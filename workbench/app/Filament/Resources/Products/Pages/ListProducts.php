<?php

namespace Workbench\App\Filament\Resources\Products\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Workbench\App\Enums\ProductStatus;
use Workbench\App\Filament\Resources\Products\ProductResource;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            ...collect(ProductStatus::cases())->mapWithKeys(fn (ProductStatus $status): array => [
                $status->value => Tab::make($status->getLabel())
                    ->badge(fn (): int => ProductResource::getModel()::query()->where('status', $status)->count())
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', $status)),
            ])->all(),
        ];
    }
}
