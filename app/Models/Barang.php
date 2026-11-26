<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    public const KONDISI_OPTIONS = [
        'Baik',
        'Rusak Ringan',
        'Rusak Berat',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'jumlah_stok',
        'kondisi',
        'deskripsi',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_stok' => 'integer',
        ];
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'barang_id')->latest();
    }

    public function isOutOfStock(): bool
    {
        return $this->jumlah_stok <= 0;
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return $this->jumlah_stok > 0 && $this->jumlah_stok <= $threshold;
    }

    public function needsStockAttention(int $threshold = 5): bool
    {
        return $this->jumlah_stok <= $threshold;
    }

    /**
     * Return Tailwind badge classes corresponding to the item condition (Light & Dark mode).
     */
    public function kondisiBadgeClass(): string
    {
        return match ($this->kondisi) {
            'Baik' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30',
            'Rusak Ringan' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30',
            'Rusak Berat' => 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30',
            default => 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-500/20 dark:bg-slate-700/40 dark:text-slate-300 dark:ring-slate-600',
        };
    }

    /**
     * Return Tailwind dot indicator classes corresponding to the item condition.
     */
    public function kondisiDotClass(): string
    {
        return match ($this->kondisi) {
            'Baik' => 'bg-emerald-500 dark:bg-emerald-400',
            'Rusak Ringan' => 'bg-amber-500 dark:bg-amber-400',
            'Rusak Berat' => 'bg-rose-500 dark:bg-rose-400',
            default => 'bg-slate-400',
        };
    }

    /**
     * Return a human-readable status note for the item condition.
     */
    public function kondisiKeterangan(): string
    {
        return match ($this->kondisi) {
            'Baik' => 'Perangkat dalam kondisi normal dan siap digunakan untuk kegiatan praktikum laboratorium.',
            'Rusak Ringan' => 'Perangkat masih dapat dioperasikan namun memerlukan pengecekan atau perbaikan ringan.',
            'Rusak Berat' => 'Perangkat mengalami kerusakan berat dan perlu perbaikan menyeluruh atau penggantian.',
            default => 'Status kondisi belum dikategorikan.',
        };
    }

    /**
     * Format a model timestamp into Indonesian date and time (WIB).
     */
    public function formatWaktuIndonesia(?CarbonInterface $timestamp): string
    {
        if ($timestamp === null) {
            return '-';
        }

        return $timestamp->copy()
            ->timezone('Asia/Jakarta')
            ->locale('id')
            ->translatedFormat('l, d F Y, H:i') . ' WIB';
    }

    /**
     * Format a model timestamp into a relative Indonesian diff string.
     */
    public function formatWaktuRelatif(?CarbonInterface $timestamp): ?string
    {
        if ($timestamp === null) {
            return null;
        }

        return $timestamp->copy()
            ->timezone('Asia/Jakarta')
            ->locale('id')
            ->diffForHumans();
    }
}
