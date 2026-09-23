Markdown
# UNIVERSAL CLEAN CODE & CLEAN ARCHITECTURE DIRECTIVE

Dokumen ini adalah standar arsitektur perangkat lunak dan kebersihan kode (*code hygiene*) universal untuk project backend modern (khususnya PHP / Laravel, serta berlaku umum untuk ekosistem MVC/Action-based).

---

## 1. Arsitektur Berlapis (Layered Separation of Concerns)

Setiap class harus berada di lapisan tanggung jawab yang benar dengan aliran dependensi searah:

[ Presentation / Interface Layer ] (Controllers, Commands, API Resources, Requests)
│
▼ (Membawa Validated DTO / Primitives)
[ Application / Action Layer ] (Use Cases: Single-Action Classes, Orchestration)
│
▼ (Memanggil Invariant & Rules)
[ Domain & Entity Layer ] (Entities, Eloquent Models, Enums, Value Objects)
│
▼ (Dibatasi Interface / Abstraksi)
[ Infrastructure Layer ] (Databases, Caches, Queue, Storage, Third-Party SDKs)


### Aturan Ketat Setiap Lapisan

### 1.1 Presentation Layer (Controllers / Handlers)
* **Ultra-Thin Controllers:** Target maksimal 15–25 baris per method.
* Tanggung jawab terbatas hanya pada 5 langkah:
  1. Menerima request HTTP.
  2. Menjalankan otorisasi (Policy / Gate).
  3. Memvalidasi payload via dedicated request validator (misal: `FormRequest`).
  4. Meneruskan data bersih ke **Action** / **Service**.
  5. Mengembalikan response (View, JSON Resource, atau Redirect).
* **BANNED IN CONTROLLERS:**
  * Menulis query database langsung (`DB::table(...)`, `Model::where(...)`).
  * Membuka transaksi database (`DB::transaction`).
  * Logika kalkulasi bisnis, parsing data mentah, dan manipulasi upload file.

### 1.2 Application / Action Layer (Use Cases)
* Gunakan pola **Single-Action Classes**: satu class hanya menangani satu skenario bisnis (misal: `RegisterUserAction`, `ProcessOrderPaymentAction`).
* Berikan satu public method utama: `execute()` atau magic method `__invoke()`.
* Menerima strongly-typed parameters atau Data Transfer Objects (DTO), **bukan** instance Request mentah dari HTTP.
* Action mengelola:
  * Database transaction boundary.
  * Pemanggilan domain logic / models.
  * Dispatching events / jobs.

### 1.3 Domain & Entity Layer (Models & Enums)
* Model Eloquent fokus pada: schema mapping, relationships, query scopes, dan **Domain Invariants**.
* **Enkapsulasi Status Internal:** Hindari memeriksa status mentah berulang kali di luar model. Taruh di dalam model/entity:
  ```php
  // ✅ Clean: Enkapsulasi status di model
  if (! $order->isCancellable()) { ... }

  // ❌ Slop: Logika status berceceran di controller
  if ($order->status === 'draft' || ($order->status === 'pending' && $order->paid_at === null)) { ... }
1.4 Infrastructure Layer
Mengisolasi implementasi pihak ketiga (AWS S3, Payment Gateway, Redis Lock, SMS Provider).

Kelas aplikasi harus bergantung pada abstraksi (Interface/Contract), bukan implementasi library langsung.

2. Prinsip SOLID dalam Praktik Sehari-Hari
Single Responsibility Principle (SRP):

Pecah "God Service" beribu-ribu baris menjadi Action-Action kecil yang mandiri.

Open/Closed Principle (OCP):

Gunakan Strategy Pattern / Polymorphism untuk variasi proses (misal variasi perhitungan pajak atau metode diskon), bukan switch-case raksasa yang selalu diubah setiap ada fitur baru.

Liskov Substitution Principle (LSP):

Kelas turunan atau implementasi interface tidak boleh mengubah kontrak dasar atau melempar exception tak terduga yang melanggar perilaku tipe induknya.

Interface Segregation Principle (ISP):

Buat interface kecil dan terfokus. Jangan memaksa class mengimplementasikan method kosong yang tidak ia gunakan.

Dependency Inversion Principle (DIP):

Selalu inject dependensi melalui Constructor (__construct), jangan melakukan instansiasi manual (new ThirdPartyClient()) di tengah fungsi.

3. Aturan Kebersihan Kode (Code Hygiene)
3.1 Guard Clauses & Early Returns (No Arrow Anti-Pattern)
Buang sarang if-else bertingkat (deep indentation).

Selesaikan kasus gagal, validasi kondisi, dan otorisasi di baris paling atas:

PHP
// ❌ Arrow Code (Sulit dibaca & rentan bug)
public function process(User $user, Order $order)
{
    if ($user->isActive()) {
        if ($order->isPending()) {
            if ($order->total > 0) {
                // Logika utama terkubur di 4 tingkat tab
                return $this->executePayment($order);
            }
        }
    }
    return false;
}

// ✅ Guard Clauses (Rata kiri, mudah dipindai)
public function process(User $user, Order $order): PaymentResult
{
    if (! $user->isActive()) {
        throw new InactiveUserException('User is not active.');
    }

    if (! $order->isPending()) {
        throw new InvalidOrderStateException('Order is not in pending state.');
    }

    if ($order->total <= 0) {
        throw new InvalidAmountException('Order amount must be positive.');
    }

    // Happy path dieksekusi tanpa tab bersarang
    return $this->executePayment($order);
}
3.2 Penamaan yang Menyatakan Niat (Intention-Revealing Names)
Nama variabel dan fungsi harus langsung menjelaskan isi dan tujuannya:

Variabel: $activeSubscriptions (bukan $data, $res, atau $temp).

Method / Function: Gunakan kata kerja aktif (calculateFinalPrice(), markAsDelivered()).

Boolean: Gunakan prefix is, has, can, atau should ($isVerified, $hasDiscountApplied).

3.3 No Primitive Obsession (Gunakan DTO jika Parameter > 3)
Jangan membuat function dengan deretan parameter primitif yang panjang.

Kelompokkan parameter terkait ke dalam readonly DTO (Data Transfer Object) atau Value Object dengan tipe data yang ketat.

3.4 Query Hygiene (Basmi N+1 Problem)
DILARANG memanggil query database atau lazy-loading di dalam perulangan (foreach) atau di dalam template file (Blade/Twig).

Selalu definisikan Eager Loading (with(['relationA', 'relationB'])) di level query data builder.

4. Struktur Folder Universal Rekomendasi (Laravel-Compatible)
Plaintext
app/
├── Actions/                 # Single-use business cases (misal: CreateInvoiceAction.php)
├── Contracts/               # Interfaces / Abstraksi teknis
├── DataTransferObjects/     # Readonly DTO / Form state transfer
├── Enums/                   # Status, role, category values
├── Events/                  # Domain events
├── Exceptions/              # Domain-specific custom exceptions
├── Http/
│   ├── Controllers/         # Ultra-thin controllers
│   ├── Requests/            # FormRequests & input validation
│   └── Resources/           # API JSON transformers
├── Listeners/               # Asynchronous / Event handlers
├── Models/                  # Eloquent entities & scopes
├── Policies/                # Authorization logic
└── Services/                # Infrastructure / external integrations