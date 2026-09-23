-- ============================================================================
-- 1. EXTENSIONS & ENUMS
-- ============================================================================
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "btree_gist";

CREATE TYPE user_role_enum AS ENUM (
    'user',
    'marketing',
    'front_office',
    'duty_officer',
    'super_admin'
);

-- Tipe Akun Registrasi Publik MCC
CREATE TYPE account_type_enum AS ENUM (
    'instansi',
    'komunitas',
    'sekolah'
);

CREATE TYPE booking_lifecycle_enum AS ENUM (
    'draft',
    'awaiting_marketing',
    'revised',
    'awaiting_payment',
    'confirmed',
    'checked_in',
    'completed',
    'rejected',
    'cancelled'
);

CREATE TYPE clearance_status_enum AS ENUM (
    'pending',
    'cleared',
    'incident_detected',
    'abandoned_clean',
    'abandoned_incident',
    'resolved'
);

CREATE TYPE marketing_decision_enum AS ENUM (
    'approved',
    'revised',
    'rejected'
);

CREATE TYPE incident_category_enum AS ENUM (
    'cleanliness',
    'property_damage',
    'equipment_loss'
);


-- ============================================================================
-- 2. TABEL PENGGUNA (users) - LENGKAP DENGAN 3 TIPE AKUN
-- ============================================================================
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    role user_role_enum NOT NULL DEFAULT 'user',
    account_type account_type_enum NOT NULL DEFAULT 'komunitas',
    
    -- Identitas Personal & Kontak Akun
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone_number VARCHAR(25) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    
    -- Identitas Lembaga / Komunitas / Sekolah
    institution_name VARCHAR(150) NOT NULL,
    logo_url VARCHAR(500) NULL,
    address TEXT NOT NULL,
    description TEXT NULL,
    social_media JSONB NOT NULL DEFAULT '{}'::jsonb, -- {"facebook": "", "instagram": "", "tiktok": "", "youtube": "", "twitter": ""}

    -- Khusus Tipe Akun "Instansi" (Null untuk Komunitas & Sekolah)
    website VARCHAR(255) NULL,
    category VARCHAR(100) NULL,             -- Agregator, Usaha/Bisnis, Lembaga Pemerintah, dll.
    sub_sector VARCHAR(100) NULL,           -- Aplikasi, Game, Kriya, Musik, Film, DKV, dll.
    city VARCHAR(100) NULL,                 -- Kota Malang, Kab Malang, Kota Batu
    district VARCHAR(100) NULL,             -- Kecamatan
    
    -- Status Akun
    is_suspended BOOLEAN NOT NULL DEFAULT FALSE,
    suspension_reason TEXT NULL,
    suspended_at TIMESTAMP NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_phone ON users(phone_number);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_account_type ON users(account_type);
CREATE INDEX idx_users_sub_sector ON users(sub_sector);
CREATE INDEX idx_users_suspended ON users(is_suspended) WHERE is_suspended = TRUE;


-- ============================================================================
-- 3. MASTER RUANGAN & ATRIBUT VISUAL
-- ============================================================================
CREATE TABLE rooms (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    floor_level INT NOT NULL,
    capacity INT NOT NULL,
    room_size_sqm NUMERIC(6, 2) NULL,
    buffer_minutes INT NOT NULL DEFAULT 30,
    is_commercial_allowed BOOLEAN NOT NULL DEFAULT TRUE,
    base_hourly_rate NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    description TEXT NOT NULL,
    supported_layouts JSONB NOT NULL DEFAULT '["theater", "classroom", "u_shape"]'::jsonb,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_rooms_floor ON rooms(floor_level);
CREATE INDEX idx_rooms_active ON rooms(is_active);

CREATE TABLE room_images (
    id BIGSERIAL PRIMARY KEY,
    room_id INT NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
    image_url VARCHAR(500) NOT NULL,
    caption VARCHAR(150) NULL,
    is_primary BOOLEAN NOT NULL DEFAULT FALSE,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_room_images_room ON room_images(room_id);

CREATE TABLE room_facilities (
    id BIGSERIAL PRIMARY KEY,
    room_id INT NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
    facility_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit VARCHAR(20) NOT NULL DEFAULT 'unit',
    condition VARCHAR(50) NOT NULL DEFAULT 'good',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_room_facilities_room ON room_facilities(room_id);


-- ============================================================================
-- 4. TRANSAKSI UTAMA (bookings)
-- ============================================================================
CREATE TABLE bookings (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    booking_code VARCHAR(30) UNIQUE NOT NULL,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    
    status booking_lifecycle_enum NOT NULL DEFAULT 'draft',
    clearance_status clearance_status_enum NOT NULL DEFAULT 'pending',
    
    pricing_scheme VARCHAR(20) NOT NULL DEFAULT 'subsidy',
    nominal_amount NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
    payment_notes TEXT NULL,
    paid_at TIMESTAMP NULL,
    confirmed_by UUID NULL REFERENCES users(id) ON DELETE RESTRICT,

    checkin_token VARCHAR(64) UNIQUE NULL,
    checked_in_at TIMESTAMP NULL,
    checked_in_by UUID NULL REFERENCES users(id) ON DELETE RESTRICT,

    completed_at TIMESTAMP NULL,
    completed_by UUID NULL REFERENCES users(id) ON DELETE RESTRICT,

    revision_deadline TIMESTAMP NULL,
    version INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_clearance ON bookings(clearance_status);
CREATE INDEX idx_bookings_code ON bookings(booking_code);
CREATE INDEX idx_bookings_checkin_token ON bookings(checkin_token);


-- ============================================================================
-- 5. ENGINE ANTI-BENTROK DATABASE (booking_slots + TRIGGER)
-- ============================================================================
CREATE TABLE booking_slots (
    id BIGSERIAL PRIMARY KEY,
    booking_id UUID NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    room_id INT NOT NULL REFERENCES rooms(id) ON DELETE RESTRICT,
    
    event_start_at TIMESTAMP NOT NULL,
    event_end_at TIMESTAMP NOT NULL,
    operational_range TSRANGE NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    
    CONSTRAINT no_overlapping_room_slots 
    EXCLUDE USING gist (
        room_id WITH =,
        operational_range WITH &&
    ) WHERE (is_active = TRUE),

    CONSTRAINT check_valid_event_time CHECK (event_start_at < event_end_at)
);

CREATE INDEX idx_booking_slots_range ON booking_slots USING gist(operational_range);
CREATE INDEX idx_booking_slots_room_event ON booking_slots(room_id, event_start_at, event_end_at);

CREATE OR REPLACE FUNCTION trg_calculate_operational_range()
RETURNS TRIGGER AS $$
DECLARE
    v_buffer INT;
BEGIN
    SELECT buffer_minutes INTO v_buffer FROM rooms WHERE id = NEW.room_id;
    IF v_buffer IS NULL THEN
        v_buffer := 30;
    END IF;

    NEW.operational_range := tsrange(
        NEW.event_start_at, 
        NEW.event_end_at + (v_buffer || ' minutes')::INTERVAL, 
        '[)'
    );
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_set_operational_range
BEFORE INSERT OR UPDATE OF event_start_at, event_end_at, room_id
ON booking_slots
FOR EACH ROW
EXECUTE FUNCTION trg_calculate_operational_range();


-- ============================================================================
-- 6. DETAIL ACARA & HISTORICAL SNAPSHOT (event_details)
-- ============================================================================
CREATE TABLE event_details (
    id BIGSERIAL PRIMARY KEY,
    booking_id UUID UNIQUE NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    event_name VARCHAR(200) NOT NULL,
    event_category VARCHAR(100) NOT NULL,
    estimated_participants INT NOT NULL,
    event_description TEXT NOT NULL,
    requested_facilities JSONB NOT NULL DEFAULT '[]'::jsonb,
    proposal_file_url VARCHAR(500) NOT NULL,
    
    pic_name_snapshot VARCHAR(150) NOT NULL,
    pic_phone_snapshot VARCHAR(25) NOT NULL,
    institution_name_snapshot VARCHAR(150) NOT NULL,
    
    tos_agreed_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================================
-- 7. AUDIT TRAIL MARKETING (booking_approvals)
-- ============================================================================
CREATE TABLE booking_approvals (
    id BIGSERIAL PRIMARY KEY,
    booking_id UUID NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    action_by UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    decision marketing_decision_enum NOT NULL,
    notes TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_approvals_booking ON booking_approvals(booking_id);


-- ============================================================================
-- 8. PELAPORAN INSIDEN LAPANGAN (room_incidents & incident_photos)
-- ============================================================================
CREATE TABLE room_incidents (
    id BIGSERIAL PRIMARY KEY,
    booking_id UUID NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    reported_by_do UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    category incident_category_enum NOT NULL,
    item_name VARCHAR(150) NULL,
    description TEXT NOT NULL,
    is_resolved BOOLEAN NOT NULL DEFAULT FALSE,
    resolution_notes TEXT NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_incidents_booking ON room_incidents(booking_id);

CREATE TABLE incident_photos (
    id BIGSERIAL PRIMARY KEY,
    incident_id BIGINT NOT NULL REFERENCES room_incidents(id) ON DELETE CASCADE,
    photo_url VARCHAR(500) NOT NULL,
    caption VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_photos_incident ON incident_photos(incident_id);


-- ============================================================================
-- 9. KUSTODIAN FISIK JAMINAN KTP (ktp_vault_logs)
-- ============================================================================
CREATE TABLE ktp_vault_logs (
    id BIGSERIAL PRIMARY KEY,
    booking_id UUID NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    ktp_holder_name VARCHAR(150) NOT NULL,
    nik_number VARCHAR(255) NOT NULL,
    ktp_photo_scan_url VARCHAR(500) NULL,
    
    received_by_fo UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    retained_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    returned_by_fo UUID NULL REFERENCES users(id) ON DELETE RESTRICT,
    returned_at TIMESTAMP NULL,
    
    unclaimed_box_id VARCHAR(50) NULL,
    notes TEXT NULL
);

CREATE INDEX idx_ktp_logs_booking ON ktp_vault_logs(booking_id);


┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                            MCC EVENT RESERVATION SYSTEM - PRODUCTION ERD                                               │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

    [ users ]                                                    [ rooms ]
    ├── PK  id : UUID                                            ├── PK  id : INT (SERIAL)
    ├──     role : user_role_enum                                ├──     name : VARCHAR(100)
    ├──     full_name : VARCHAR(150)                             ├──     slug : VARCHAR(100) (UQ)
    ├──     email : VARCHAR(150) (UQ)                            ├──     floor_level : INT (1-8)
    ├──     phone_number : VARCHAR(25)                           ├──     capacity : INT
    ├──     institution_type : VARCHAR(50)                       ├──     room_size_sqm : NUMERIC(6,2)
    ├──     institution_name : VARCHAR(150)                      ├──     buffer_minutes : INT (Default: 30)
    ├──     password_hash : VARCHAR(255)                         ├──     base_hourly_rate : NUMERIC(12,2)
    ├──     is_suspended : BOOLEAN                               ├──     supported_layouts : JSONB
    └──     suspended_at : TIMESTAMP                             ├──     is_commercial_allowed : BOOLEAN
                │ 1                                              └──     is_active : BOOLEAN
                │                                                    │ 1           │ 1
                │                                          ┌─────────┘             └─────────┐
                │                                          │ n                               │ n
                │                                  [ room_images ]                   [ room_facilities ]
                │                                  ├── PK  id : BIGINT               ├── PK  id : BIGINT
                │                                  ├── FK  room_id : INT             ├── FK  room_id : INT
                │                                  ├──     image_url : VARCHAR(500)  ├──     facility_name : VARCHAR(100)
                │                                  ├──     caption : VARCHAR(150)    ├──     quantity : INT
                │                                  └──     is_primary : BOOLEAN      └──     condition : VARCHAR(50)
                │                                                                            │ 1
                │ n                                                                          │
    ┌───────────┴──────────────────────────────────────────────────────┐                     │
    │                              bookings                            │                     │
    ├──────────────────────────────────────────────────────────────────┤                     │
    │ PK  id : UUID                                                    │                     │
    │ FK  user_id : UUID ──────────────────────────────► (users.id)    │                     │
    │     booking_code : VARCHAR(30) (UQ)              │ (Kode Tiket)  │                     │
    │     status : booking_lifecycle_enum              │ (Siklus Event)│                     │
    │     clearance_status : clearance_status_enum     │ (Inspeksi DO) │                     │
    │     pricing_scheme : VARCHAR(20)                 │ (Subsidy/Comm)│                     │
    │     nominal_amount : NUMERIC(12,2)               │               │                     │
    │     payment_notes : TEXT                         │ (Offline WA)  │                     │
    │     paid_at : TIMESTAMP                          │               │                     │
    │ FK  confirmed_by : UUID ─────────────────────────► (users.id)    │                     │
    │     checkin_token : VARCHAR(64) (UQ)             │ (Token QR)    │                     │
    │     checked_in_at : TIMESTAMP                    │               │                     │
    │ FK  checked_in_by : UUID ────────────────────────► (users.id)    │                     │
    │     completed_at : TIMESTAMP                     │               │                     │
    │ FK  completed_by : UUID ─────────────────────────► (users.id)    │                     │
    │     revision_deadline : TIMESTAMP                │ (Grace Period)│                     │
    │     version : INT                                │ (Optimistic)  │                     │
    └───────┬──────────────┬──────────────┬──────────────┬─────────────┘                     │
            │ 1            │ 1            │ 1            │ 1                                 │
            │              │              │              │                                   │
            │ 1            │ n            │ n            │ n                                 │
            ▼              ▼              ▼              ▼                                   │
  ┌──────────────────┐ ┌───────────────┐ ┌────────────────┐ ┌───────────────────┐           │
  │  event_details   │ │ booking_slots │ │booking_approval│ │  room_incidents   │           │
  ├──────────────────┤ ├───────────────┤ ├────────────────┤ ├───────────────────┤           │
  │ PK id : BIGINT   │ │ PK id : BIGINT│ │ PK id : BIGINT │ │ PK id : BIGINT    │           │
  │ FK booking_id    │ │ FK booking_id │ │ FK booking_id  │ │ FK booking_id     │           │
  │    event_name    │ │ FK room_id ───┼─┼────────────────┼─┼───────────────────┼───────────┘
  │    category      │ │    start_at   │ │ FK action_by   │ │ FK reported_by_do │
  │    participants  │ │    end_at     │ │    decision    │ │    category       │
  │    requested_fac │ │  operat_range │ │    notes       │ │    item_name      │
  │    proposal_url  │ │    (TSRANGE)  │ └────────────────┘ │    description    │
  │    pic_name_snap │ │    is_active  │   [Auto Trigger]   │    is_resolved    │
  │    pic_phone_snap│ └───────────────┘                    │    resolved_at    │
  │    inst_name_snap│   [&& Anti-Bentrok]                  └─────────┬─────────┘
  │    tos_agreed_at │                                                │ 1
  └──────────────────┘                                                │
                                                                      │ n
  ┌──────────────────────────────────────┐                            ▼
  │            ktp_vault_logs            │                  ┌───────────────────┐
  ├──────────────────────────────────────┤                  │  incident_photos  │
  │ PK id : BIGINT                       │                  ├───────────────────┤
  │ FK booking_id : UUID                 │                  │ PK id : BIGINT    │
  │    ktp_holder_name : VARCHAR(150)    │                  │ FK incident_id    │
  │    nik_number : VARCHAR(255) (Enc)   │                  │    photo_url      │
  │    ktp_photo_scan_url: VARCHAR(500)  │                  │    caption        │
  │ FK received_by_fo : UUID             │                  └───────────────────┘
  │    retained_at : TIMESTAMP           │
  │ FK returned_by_fo : UUID             │
  │    returned_at : TIMESTAMP           │
  │    unclaimed_box_id : VARCHAR(50)    │
  │    notes : TEXT                      │
  └──────────────────────────────────────┘