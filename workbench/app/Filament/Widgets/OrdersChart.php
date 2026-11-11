<?php

namespace Workbench\App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Orders per month';

    protected ?string $description = 'Last 12 months';

    protected static ?int $sort = 2;

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return ['week' => 'Last week', 'month' => 'Last month', 'year' => 'This year'];
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                ['label' => 'Orders', 'data' => [12, 19, 14, 22, 18, 25, 30, 27, 33, 29, 35, 41], 'fill' => 'start'],
                ['label' => 'Returns', 'data' => [2, 3, 1, 4, 2, 3, 5, 3, 4, 2, 3, 4]],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
