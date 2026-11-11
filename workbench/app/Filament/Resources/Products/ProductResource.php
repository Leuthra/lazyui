<?php

namespace Workbench\App\Filament\Resources\Products;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use Workbench\App\Enums\ProductStatus;
use Workbench\App\Models\Product;

/**
 * Kitchen sink: nearly every form field, table column, filter and action type.
 */
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = 'Shop';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Product::query()->where('stock', '<', 10)->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Group::make()->columnSpan(2)->schema([
                Section::make('Details')
                    ->description('The basics customers see first.')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                        TextInput::make('sku')->label('SKU')->required()->prefix('#')->unique(ignoreRecord: true),
                        Select::make('category_id')->relationship('category', 'name')->searchable()->preload(),
                        TextInput::make('price')->numeric()->prefix('$')->suffix('USD')->required(),
                        TextInput::make('stock')->numeric()->minValue(0)->suffixIcon(Heroicon::OutlinedCube),
                        RichEditor::make('description')->columnSpanFull(),
                    ]),

                Tabs::make('More')->tabs([
                    Tab::make('Variants')->icon(Heroicon::OutlinedSquares2x2)->schema([
                        Repeater::make('variants')
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('price')->numeric()->prefix('+$'),
                                TextInput::make('stock')->numeric(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->reorderable(),
                    ]),
                    Tab::make('Content')->icon(Heroicon::OutlinedDocumentText)->badge(2)->schema([
                        Builder::make('content')->blocks([
                            Block::make('heading')->icon(Heroicon::OutlinedH1)->schema([
                                TextInput::make('text')->required(),
                                Select::make('level')->options(['h2' => 'H2', 'h3' => 'H3'])->default('h2'),
                            ]),
                            Block::make('paragraph')->icon(Heroicon::OutlinedBars3BottomLeft)->schema([
                                MarkdownEditor::make('text'),
                            ]),
                        ]),
                    ]),
                    Tab::make('Attributes')->icon(Heroicon::OutlinedTag)->schema([
                        KeyValue::make('attributes')->keyLabel('Attribute')->valueLabel('Value'),
                        TagsInput::make('tags')->suggestions(['new', 'sale', 'eco', 'limited', 'bundle']),
                    ]),
                ]),
            ]),

            Group::make()->schema([
                Section::make('Status')->schema([
                    ToggleButtons::make('status')->options(ProductStatus::class)->inline()->required()->default('draft'),
                    DateTimePicker::make('published_at')->native(false),
                    Toggle::make('is_featured')->label('Featured')->helperText('Shown on the home page.'),
                ]),
                Section::make('Media')->collapsible()->schema([
                    FileUpload::make('image')->image()->imageEditor(),
                    ColorPicker::make('color'),
                ]),
                Section::make('Rating')->schema([
                    Slider::make('rating')->range(minValue: 1, maxValue: 5)->step(1),
                    Radio::make('visibility')->options(['public' => 'Public', 'hidden' => 'Hidden'])->default('public')->dehydrated(false),
                    CheckboxList::make('channels')->options(['web' => 'Web shop', 'pos' => 'Point of sale', 'app' => 'Mobile app'])->columns(1)->dehydrated(false),
                    Fieldset::make('Legal')->schema([
                        Checkbox::make('terms')->label('Complies with policy')->dehydrated(false),
                        Textarea::make('notes')->rows(2)->dehydrated(false),
                    ])->columns(1),
                ]),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Section::make('Overview')->columnSpan(2)->columns(2)->schema([
                TextEntry::make('name')->weight('bold')->size('lg')->columnSpanFull(),
                TextEntry::make('sku')->label('SKU')->copyable()->badge()->color('gray'),
                TextEntry::make('category.name')->icon(Heroicon::OutlinedFolder),
                TextEntry::make('price')->money('USD'),
                TextEntry::make('stock')->numeric()->color(fn (int $state): string => $state < 10 ? 'danger' : 'success'),
                TextEntry::make('description')->html()->columnSpanFull(),
                RepeatableEntry::make('variants')->columnSpanFull()->columns(3)->schema([
                    TextEntry::make('name'),
                    TextEntry::make('price')->money('USD'),
                    TextEntry::make('stock'),
                ]),
            ]),
            Section::make('Meta')->schema([
                TextEntry::make('status')->badge(),
                IconEntry::make('is_featured')->boolean(),
                ColorEntry::make('color')->copyable(),
                TextEntry::make('tags')->badge()->separator(','),
                KeyValueEntry::make('attributes'),
                TextEntry::make('published_at')->since()->dateTimeTooltip(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('medium')
                    ->description(fn (Product $record): string => $record->sku),
                TextColumn::make('category.name')->badge()->color('gray')->sortable()->toggleable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('price')->money('USD')->sortable()->summarize([Sum::make()->money('USD'), Average::make()->money('USD')]),
                TextColumn::make('stock')->numeric()->sortable()
                    ->color(fn (int $state): string => $state < 10 ? 'danger' : 'gray')
                    ->icon(fn (int $state): ?Heroicon => $state < 10 ? Heroicon::OutlinedExclamationTriangle : null),
                IconColumn::make('is_featured')->boolean()->label('Featured')->toggleable(),
                ToggleColumn::make('is_featured')->label('Feature')->toggleable(isToggledHiddenByDefault: true),
                SelectColumn::make('rating')->options([1 => '★', 2 => '★★', 3 => '★★★', 4 => '★★★★', 5 => '★★★★★'])->toggleable(isToggledHiddenByDefault: true),
                ColorColumn::make('color')->toggleable(),
                TextColumn::make('tags')->badge()->separator(',')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('published_at')->since()->sortable()->toggleable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->groups([TableGroup::make('category.name')->label('Category'), TableGroup::make('status')])
            ->filters([
                SelectFilter::make('status')->options(ProductStatus::class)->multiple(),
                SelectFilter::make('category')->relationship('category', 'name')->searchable()->preload(),
                TernaryFilter::make('is_featured')->label('Featured'),
                Filter::make('low_stock')->label('Low stock')->toggle()
                    ->query(fn (EloquentBuilder $query): EloquentBuilder => $query->where('stock', '<', 10)),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('restock')
                        ->icon(Heroicon::OutlinedArrowPath)
                        ->color('success')
                        ->schema([TextInput::make('quantity')->numeric()->required()->default(10)])
                        ->action(function (Product $record, array $data): void {
                            $record->increment('stock', $data['quantity']);
                            Notification::make()->title('Restocked')->success()->send();
                        }),
                    Action::make('preview')
                        ->icon(Heroicon::OutlinedEye)
                        ->slideOver()
                        ->modalContent(fn (Product $record) => str($record->description ?? '')->toHtmlString())
                        ->modalSubmitAction(false),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon(Heroicon::OutlinedCube)
            ->emptyStateDescription('Create your first product to get started.');
    }

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
