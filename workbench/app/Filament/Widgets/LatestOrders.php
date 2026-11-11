<?php

namespace Workbench\App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Workbench\App\Models\Order;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest())
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('number'),
                TextColumn::make('customer'),
                TextColumn::make('status')->badge(),
                TextColumn::make('total')->money('USD'),
                TextColumn::make('created_at')->since(),
            ]);
    }
}
