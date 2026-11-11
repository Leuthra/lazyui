<?php

namespace Workbench\App\Filament\Resources\Orders;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;
use Workbench\App\Enums\OrderStatus;
use Workbench\App\Models\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 3;

    /** @return array<Step> */
    public static function wizardSteps(): array
    {
        return [
            Step::make('Customer')->icon(Heroicon::OutlinedUser)->description('Who is ordering')->schema([
                TextInput::make('customer')->required(),
                TextInput::make('email')->email()->required(),
                Textarea::make('address'),
            ]),
            Step::make('Items')->icon(Heroicon::OutlinedShoppingCart)->schema([
                Repeater::make('items')->schema([
                    TextInput::make('product')->required(),
                    TextInput::make('quantity')->numeric()->default(1),
                ])->columns(2)->defaultItems(1),
            ]),
            Step::make('Review')->icon(Heroicon::OutlinedCheckBadge)->schema([
                Callout::make('Almost done')->description('Check the details before placing the order.')->info(),
                TextInput::make('number')->required()->default(fn (): string => 'ORD-'.random_int(20000, 99999)),
                TextInput::make('total')->numeric()->prefix('$')->required(),
                Select::make('status')->options(OrderStatus::class)->default('new')->required(),
            ]),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order')->columns(3)->schema([
                TextEntry::make('number')->copyable(),
                TextEntry::make('status')->badge(),
                TextEntry::make('total')->money('USD'),
                TextEntry::make('customer'),
                TextEntry::make('email')->icon(Heroicon::OutlinedEnvelope),
                TextEntry::make('created_at')->dateTime(),
                TextEntry::make('address')->columnSpanFull()->placeholder('No address'),
            ]),
            RepeatableEntry::make('items')->columns(2)->schema([
                TextEntry::make('product'),
                TextEntry::make('quantity'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->weight('medium'),
                TextColumn::make('customer')->searchable()->description(fn (Order $record): string => $record->email),
                TextColumn::make('status')->badge(),
                TextColumn::make('total')->money('USD')->sortable(),
                TextColumn::make('created_at')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([SelectFilter::make('status')->options(OrderStatus::class)])
            ->recordActions([
                ViewAction::make(),
                Action::make('ship')
                    ->icon(Heroicon::OutlinedTruck)
                    ->requiresConfirmation()
                    ->modalDescription('The customer will be notified.')
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Processing)
                    ->action(fn (Order $record) => $record->update(['status' => OrderStatus::Shipped, 'shipped_at' => now()])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
