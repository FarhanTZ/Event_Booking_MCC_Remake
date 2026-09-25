<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredRevisionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-expired-revisions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan otomatis permohonan booking berstatus revised yang telah melewati tenggat 24 jam dan melepaskan slot jadwal.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredBookings = Booking::query()
            ->where('status', BookingStatus::REVISED)
            ->whereNotNull('revision_deadline')
            ->where('revision_deadline', '<=', now())
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('Tidak ada permohonan revisi yang kedaluwarsa.');
            return self::SUCCESS;
        }

        $count = 0;

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {
                // 1. Ubah status menjadi cancelled
                $booking->update([
                    'status' => BookingStatus::CANCELLED,
                    'version' => $booking->version + 1,
                ]);

                // 2. Buka kembali seluruh slot jadwal ruangan untuk publik
                $booking->slots()->update([
                    'is_active' => false,
                ]);

                // 3. Catat riwayat pembatalan otomatis
                BookingApproval::query()->create([
                    'booking_id' => $booking->id,
                    'action_by' => $booking->user_id,
                    'decision' => 'cancelled',
                    'notes' => 'Permohonan dibatalkan otomatis oleh sistem MCC karena telah melewati masa tenggang perbaikan 24 jam.',
                    'created_at' => now(),
                ]);
            });

            $count++;
            $this->warn("Permohonan {$booking->booking_code} dibatalkan otomatis karena melewati batas waktu revisi.");
        }

        $this->info("Total {$count} permohonan revisi berhasil dibatalkan dan slot jadwalnya telah dibuka kembali.");

        return self::SUCCESS;
    }
}
