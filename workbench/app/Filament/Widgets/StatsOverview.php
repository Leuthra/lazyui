<?php

namespace Workbench\App\Filament\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Workbench\App\Models\Order;
use Workbench\App\Models\Product;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Revenue', '$'.number_format((float) Order::query()->sum('total')))
                ->description('12% increase')->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])->color('success'),
            Stat::make('Orders', Order::query()->count())
                ->description('3% decrease')->descriptionIcon(Heroicon::ArrowTrendingDown)
                ->chart([4, 6, 5, 7, 4, 3, 2, 3])->color('danger'),
            Stat::make('Products', Product::query()->count())
                ->description(Product::query()->where('stock', '<', 10)->count().' low on stock')
                ->descriptionIcon(Heroicon::ExclamationTriangle)->color('warning'),
        ];
    }
}
