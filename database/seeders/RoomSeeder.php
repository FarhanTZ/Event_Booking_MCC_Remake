<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\RoomImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan tabel anak dan induk untuk eksekusi seeder yang bersih
        DB::statement('TRUNCATE TABLE room_facilities, room_images, rooms RESTART IDENTITY CASCADE');

        $roomsData = [
            // ================================================================
            // LANTAI 1
            // ================================================================
            [
                'id' => 1,
                'name' => 'Miniatur Candi Badut',
                'floor_level' => 1,
                'capacity' => 100,
                'description' => 'Area Miniatur Candi Badut. Dapat digunakan untuk kegiatan Konser atau Jamming Session. Peralatan musik dan kebutuhan pendukung dibawa sendiri oleh penyelenggara.',
                'facilities' => ['Area Terbuka / Outdoor Mini Stage'],
            ],
            [
                'id' => 2,
                'name' => 'Teras Tengah',
                'floor_level' => 1,
                'capacity' => 750,
                'description' => 'Area ini memiliki kapasitas 500 orang (standing) dan 250 orang (sitting). Seringkali digunakan untuk event diskusi, seminar, workshop, pelatihan, dll.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],
            [
                'id' => 3,
                'name' => 'Teras Utara',
                'floor_level' => 1,
                'capacity' => 750,
                'description' => 'Area outdoor utara berkapasitas 500 orang (standing) dan 250 orang (sitting). Cocok untuk showcase dan pameran komunitas.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],
            [
                'id' => 4,
                'name' => 'Teras Selatan',
                'floor_level' => 1,
                'capacity' => 750,
                'description' => 'Area outdoor selatan berkapasitas 500 orang (standing) dan 250 orang (sitting) untuk workshop dan gathering kreatif.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],

            // ================================================================
            // LANTAI 2
            // ================================================================
            [
                'id' => 5,
                'name' => 'Main Hall Lantai 2',
                'floor_level' => 2,
                'capacity' => 750,
                'description' => 'Area utama lantai 2 berkapasitas 500 orang standing dan 250 sitting. Dilengkapi panggung utama dan sistem audio-visual lengkap.',
                'facilities' => ['Screen Proyektor Besar', 'Level Panggung Utama', 'Sound System', 'Microphone'],
            ],
            [
                'id' => 6,
                'name' => 'Malang Creative City Planning Gallery (MCCGP)',
                'floor_level' => 2,
                'capacity' => 150,
                'description' => 'Galeri interaktif sejarah dan visi masa depan Kota Malang berbasis Video Mapping, Augmented Reality, Virtual Reality, dan Interactive Selfie.',
                'facilities' => ['Video Mapping Projector', 'AR/VR Display Unit', 'Interactive Screen', 'Coloring Game Station'],
            ],
            [
                'id' => 7,
                'name' => 'Studio Podcast',
                'floor_level' => 2,
                'capacity' => 14,
                'description' => 'Studio kedap suara berkapasitas 4 orang narasumber dan 10 orang di area tunggu. Dikhususkan untuk produksi podcast dan siaran digital.',
                'facilities' => ['Ruang Akustik Kedap Suara', 'AC', 'Monitor TV Preview', 'Headphone Studio 4 Set', 'Microphone Condenser Podcast'],
            ],
            [
                'id' => 8,
                'name' => 'Ruang Meeting Lantai 2',
                'floor_level' => 2,
                'capacity' => 30,
                'description' => 'Ruang rapat formal ber-AC untuk rapat koordinasi, seminar mini, dan diskusi terarah komunitas.',
                'facilities' => ['AC Central', 'Meja Rapat', 'Kursi Ergonomis 30 Unit', 'Proyektor & Layar LCD', 'Papan Tulis Portable', 'Sound System & Mic'],
            ],
            [
                'id' => 9,
                'name' => 'Open Public Space Utara Lantai 2',
                'floor_level' => 2,
                'capacity' => 750,
                'description' => 'Ruang publik komunal terbuka di sisi utara lantai 2 untuk pameran atau bazaar komunitas.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],
            [
                'id' => 10,
                'name' => 'Multifunction Room Lantai 2',
                'floor_level' => 2,
                'capacity' => 750,
                'description' => 'Ruang serbaguna fleksibel untuk berbagai format kegiatan (theater, classroom, pameran).',
                'facilities' => ['Sound System', 'Microphone Wireless', 'AC Floor'],
            ],
            [
                'id' => 11,
                'name' => 'Open Public Space Barat Lantai 2',
                'floor_level' => 2,
                'capacity' => 750,
                'description' => 'Area publik sisi barat lantai 2 untuk aktivitas komunitas dan pameran pop-up.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],
            [
                'id' => 12,
                'name' => 'Multi Purpose Area Lantai 2',
                'floor_level' => 2,
                'capacity' => 750,
                'description' => 'Area serbaguna dengan sirkulasi luas untuk workshop dan forum kreatif berskala menengah.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],
            [
                'id' => 13,
                'name' => 'Ruang Kelas Lantai 2',
                'floor_level' => 2,
                'capacity' => 60,
                'description' => 'Ruang pelatihan dan kelas intensif dengan setup classroom berkapasitas 60 peserta.',
                'facilities' => ['AC Floor', 'Meja & Kursi Belajar 60 Set', 'Proyektor & LCD', 'Papan Tulis Portable', 'Sound System & Mic'],
            ],
            [
                'id' => 14,
                'name' => 'Food Lab Lantai 2',
                'floor_level' => 2,
                'capacity' => 70,
                'description' => 'Laboratorium kuliner dan dapur produksi untuk pelatihan memasak, baking, dan standardisasi produk F&B.',
                'facilities' => ['AC Central', 'Kompor Komersial', 'Oven Baking', 'Freezer Penyimpanan', 'Mesin Packing Otomatis', 'Mesin Roasting Kopi', 'Mesin Dehydrated'],
            ],

            // ================================================================
            // LANTAI 4
            // ================================================================
            [
                'id' => 15,
                'name' => 'Coworking Space 1 Lantai 4',
                'floor_level' => 4,
                'capacity' => 10,
                'description' => 'Kantor bersama sementara bagi komunitas startup dan inkubasi bisnis kreatif.',
                'facilities' => ['AC Central', 'Meja Kerja Terpadu', 'Kursi Kerja 10 Unit', 'Whiteboard', 'Proyektor & LCD'],
            ],
            [
                'id' => 16,
                'name' => 'Studio Musik & Recording Lantai 4',
                'floor_level' => 4,
                'capacity' => 10,
                'description' => 'Studio rekaman musik audio profesional berinsulasi suara tinggi. Dikhususkan untuk produksi rekaman karya lagu.',
                'facilities' => ['Insulasi Peredam Akustik', 'Drum Kit & Ampli Gitar/Bass', 'Audio Interface Mixer', 'Microphone Recording Condenser'],
            ],
            [
                'id' => 17,
                'name' => 'Workshop Seni Lantai 4',
                'floor_level' => 4,
                'capacity' => 50,
                'description' => 'Galeri kerja dan pameran karya seni dengan pencahayaan pameran terarah.',
                'facilities' => ['Spotlight Gallery Lighting', 'Standing Panel Pameran', 'AC Central'],
            ],
            [
                'id' => 18,
                'name' => 'Multi Purpose Area Lantai 4',
                'floor_level' => 4,
                'capacity' => 750,
                'description' => 'Ruang serbaguna luas di lantai 4 untuk pelatihan dan seminar komunitas.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],
            [
                'id' => 19,
                'name' => 'Lab Komputer Lantai 4',
                'floor_level' => 4,
                'capacity' => 25,
                'description' => 'Laboratorium komputasi grafis dan pemrograman untuk pelatihan digital.',
                'facilities' => ['11 Unit Apple iMac', '16 Unit PC Windows', 'AC Central', 'Meja & Kursi Lab Komputer', 'Proyektor & Screen', 'Papan Tulis Portable'],
            ],
            [
                'id' => 20,
                'name' => 'Ruang Workshop Kriya Lantai 4',
                'floor_level' => 4,
                'capacity' => 150,
                'description' => 'Ruang praktik kerajinan tangan dan kriya (batik, tenun, rajut) berkapasitas hingga 150 orang.',
                'facilities' => ['Set Alat Membatik (Canting, Kompor, Wajan)', 'Meja Kerja Kriya', 'Sound System & Mic'],
            ],
            [
                'id' => 21,
                'name' => 'Open Public Space Utara Lantai 4',
                'floor_level' => 4,
                'capacity' => 750,
                'description' => 'Area kumpul publik lantai 4 untuk mini showcase dan diskusi santai.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],

            // ================================================================
            // LANTAI 5
            // ================================================================
            [
                'id' => 22,
                'name' => 'Coworking Space 1 Lantai 5',
                'floor_level' => 5,
                'capacity' => 10,
                'description' => 'Ruangan privat tertutup untuk meeting tertutup, daily working, dan mini conference.',
                'facilities' => ['Akses Ramp Samping', 'Whiteboard', 'LED TV 43 Inch Display', 'Set Meja Meeting 10 Kursi'],
            ],
            [
                'id' => 23,
                'name' => 'Coworking Space 2 Lantai 5',
                'floor_level' => 5,
                'capacity' => 10,
                'description' => 'Ruang kerja tim berkapasitas 10 orang dengan fasilitas presentasi monitor display.',
                'facilities' => ['Akses Ramp Samping', 'Whiteboard', 'LED TV 43 Inch Display', 'Set Meja Meeting 10 Kursi'],
            ],
            [
                'id' => 24,
                'name' => 'Amphitheater 2 Lantai 5',
                'floor_level' => 5,
                'capacity' => 100,
                'description' => 'Ruang teater bertingkat santai untuk pemutaran film indie, pemutaran video, dan bedah karya.',
                'facilities' => ['20 Unit Bean Bag', '1 Set Sofa Narasumber', 'Proyektor Sinema & Layar LCD', 'Sound System 5.1 & Mic'],
            ],
            [
                'id' => 25,
                'name' => 'Amphitheater 1 Lantai 5',
                'floor_level' => 5,
                'capacity' => 100,
                'description' => 'Ruang mini teater bertingkat untuk screening film, sharing session, dan diskusi karya kreatif.',
                'facilities' => ['20 Unit Bean Bag', '1 Set Sofa Narasumber', 'Proyektor Sinema & Layar LCD', 'Sound System 5.1 & Mic'],
            ],
            [
                'id' => 26,
                'name' => 'Studio Foto Lantai 5',
                'floor_level' => 5,
                'capacity' => 10,
                'description' => 'Studio fotografi terpadu untuk pemotretan produk UMKM, katalog fashion, dan model.',
                'facilities' => ['AC Central', 'Lighting Studio Softbox & Diffuser', 'Kamera DSLR & Tripod Stand', 'Backdrop Berbagai Warna'],
            ],
            [
                'id' => 27,
                'name' => 'Outdoor Lounge Lantai 5',
                'floor_level' => 5,
                'capacity' => 20,
                'description' => 'Area ruang terbuka di antara Amphitheater 1 dan 2 untuk jamming session akustik atau bazar mini.',
                'facilities' => ['Akses Ramp & Lift Penumpang', 'Area Luar Ruangan Semi-Covered'],
            ],
            [
                'id' => 28,
                'name' => 'Ruang Fashion Lantai 5',
                'floor_level' => 5,
                'capacity' => 50,
                'description' => 'Ruang kerja mode dan tata busana untuk pelatihan desainer dan fitting model.',
                'facilities' => ['Manekin Display Stand', 'Peralatan Fashion Design', 'Cermin Fitting Besar', 'Sound System & Mic'],
            ],

            // ================================================================
            // LANTAI 6
            // ================================================================
            [
                'id' => 29,
                'name' => 'Perpustakaan Lantai 6',
                'floor_level' => 6,
                'capacity' => 30,
                'description' => 'Area literasi hening untuk diskusi buku, riset komunitas, dan kunjungan kedinasan.',
                'facilities' => ['Meja Baca & Kursi 30 Unit', 'Layar LCD & Proyektor', 'Microphone', 'Sound System'],
            ],
            [
                'id' => 30,
                'name' => 'Open Public Space Utara Lantai 6',
                'floor_level' => 6,
                'capacity' => 750,
                'description' => 'Area terbuka lantai 6 untuk kegiatan pelatihan berskala besar dan pameran karya.',
                'facilities' => ['Sound System', 'Microphone Wireless'],
            ],

            // ================================================================
            // LANTAI 7
            // ================================================================
            [
                'id' => 31,
                'name' => 'Auditorium Utama Lantai 7',
                'floor_level' => 7,
                'capacity' => 1000,
                'description' => 'Hall utama terbesar di MCC berkapasitas 1000 orang standing untuk konser musik, konferensi nasional, wisuda, dan pameran akbar.',
                'facilities' => ['Control Room Operator', 'Ruang Transit VIP', 'Backstage Ruang Ganti', 'Proyektor Cinema / Videotron Support', 'Sound System Concert Level & Mic Wireless'],
            ],

            // ================================================================
            // LANTAI 8
            // ================================================================
            [
                'id' => 32,
                'name' => 'Rooftop Lantai 8',
                'floor_level' => 8,
                'capacity' => 100,
                'description' => 'Area atap terbuka dengan pemandangan lanskap Kota Malang untuk kebutuhan photoshoot, take video kreatif, dan golden hour session.',
                'facilities' => ['Akses Lift Service & Passenger', 'Area Terbuka Rooftop Pemandangan Kota'],
            ],
        ];

        foreach ($roomsData as $data) {
            // 1. Simpan Room
            $room = Room::create([
                'id' => $data['id'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name'] . '-' . $data['id']),
                'floor_level' => $data['floor_level'],
                'capacity' => $data['capacity'],
                'buffer_minutes' => 30,
                'is_commercial_allowed' => true,
                'base_hourly_rate' => 0.00,
                'is_active' => true,
                'description' => $data['description'],
                'supported_layouts' => ['theater', 'classroom', 'u_shape', 'standing'],
            ]);

            // 2. Simpan Fasilitas Bawaan Ruangan
            foreach ($data['facilities'] as $facilityName) {
                RoomFacility::create([
                    'room_id' => $room->id,
                    'facility_name' => trim($facilityName),
                    'quantity' => 1,
                    'unit' => 'set',
                    'condition' => 'good',
                ]);
            }

            // 3. Simpan Placeholder Gambar Katalog Utama
            RoomImage::create([
                'room_id' => $room->id,
                'image_url' => '/images/rooms/placeholder-' . $room->floor_level . '.jpg',
                'caption' => 'Tampilan Utama ' . $room->name,
                'is_primary' => true,
                'sort_order' => 1,
            ]);
        }

        // Sinkronkan Postgres Sequence agar tidak konflik pada auto-increment selanjutnya
        DB::statement("SELECT setval(pg_get_serial_sequence('rooms', 'id'), coalesce(max(id), 1)) FROM rooms;");
    }
}
