<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $settings = AppSetting::current();
            $threshold = max(0, (int) $settings->batas_stok_minimum);

            $lowStockItems = collect();
            $lowStockCount = 0;

            if (Schema::hasTable('barangs')) {
                $lowStockCount = Barang::where('jumlah_stok', '<=', $threshold)->count();
                $lowStockItems = Barang::where('jumlah_stok', '<=', $threshold)
                    ->orderBy('jumlah_stok')
                    ->limit(5)
                    ->get();
            }

            $operatorName = 'Administrator Lab';
            if (Schema::hasTable('users')) {
                $operatorName = User::query()->value('name') ?? 'Administrator Lab';
            }

            $view->with('appSettings', $settings)
                ->with('lowStockCount', $lowStockCount)
                ->with('lowStockItems', $lowStockItems)
                ->with('operatorName', $operatorName);
        });
    }
}
