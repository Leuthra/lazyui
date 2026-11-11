<?php

namespace Workbench\App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Workbench\App\Enums\OrderStatus;
use Workbench\App\Models\Order;

class StatusChart extends ChartWidget
{
    protected ?string $heading = 'Orders by status';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $counts = collect(OrderStatus::cases())->map(fn (OrderStatus $s): int => Order::query()->where('status', $s)->count());

        return [
            'datasets' => [['label' => 'Orders', 'data' => $counts->all(), 'backgroundColor' => ['#3b82f6', '#f59e0b', '#10b981', '#22c55e', '#ef4444']]],
            'labels' => collect(OrderStatus::cases())->map->getLabel()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
