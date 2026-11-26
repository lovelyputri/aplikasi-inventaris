<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    public const AKTIVITAS_TYPES = [
        'Tambah Barang',
        'Perbarui Barang',
        'Hapus Barang',
        'Pengaturan',
    ];

    protected $fillable = [
        'user_id',
        'user_name',
        'aktivitas',
        'barang_id',
        'kode_barang',
        'nama_barang',
        'keterangan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an inventory or settings activity entry.
     */
    public static function record(
        string $aktivitas,
        string $namaBarang,
        ?string $kodeBarang = null,
        ?int $barangId = null,
        ?string $keterangan = null,
        ?User $actor = null
    ): self {
        $user = $actor ?? Auth::user() ?? User::query()->first();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Administrator Lab',
            'aktivitas' => $aktivitas,
            'barang_id' => $barangId,
            'kode_barang' => $kodeBarang,
            'nama_barang' => $namaBarang,
            'keterangan' => $keterangan,
        ]);
    }

    public function aktivitasBadgeClass(): string
    {
        return match ($this->aktivitas) {
            'Tambah Barang' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30',
            'Perbarui Barang' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/15 dark:text-blue-300 dark:ring-blue-500/30',
            'Hapus Barang' => 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30',
            'Pengaturan' => 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/20 dark:bg-indigo-500/15 dark:text-indigo-300 dark:ring-indigo-500/30',
            default => 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-500/20 dark:bg-slate-700/40 dark:text-slate-300 dark:ring-slate-600',
        };
    }

    public function formatWaktuIndonesia(?CarbonInterface $timestamp = null): string
    {
        $ts = $timestamp ?? $this->created_at;
        if ($ts === null) {
            return '-';
        }

        return $ts->copy()
            ->timezone('Asia/Jakarta')
            ->locale('id')
            ->translatedFormat('d F Y, H:i:s') . ' WIB';
    }

    public function formatWaktuRelatif(?CarbonInterface $timestamp = null): ?string
    {
        $ts = $timestamp ?? $this->created_at;
        if ($ts === null) {
            return null;
        }

        return $ts->copy()
            ->timezone('Asia/Jakarta')
            ->locale('id')
            ->diffForHumans();
    }
}
