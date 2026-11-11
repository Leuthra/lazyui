<?php

namespace Workbench\App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Custom page: schema content, a form, notifications, confirmation modal, callouts, empty state.
 */
class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    /** @var array<string, mixed> */
    public ?array $data = ['site_name' => 'LazyUI Shop', 'currency' => 'USD', 'maintenance' => false];

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            TextInput::make('site_name')->required(),
            Select::make('currency')->options(['USD' => 'US Dollar', 'EUR' => 'Euro', 'IDR' => 'Rupiah'])->native(false),
            Toggle::make('maintenance')->label('Maintenance mode'),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Callout::make('Heads up')->description('Changes apply to every storefront immediately.')->warning(),
            Tabs::make()->tabs([
                Tab::make('General')->icon(Heroicon::OutlinedAdjustmentsHorizontal)->schema([
                    Section::make('Store')->description('Name, currency and availability.')->schema([
                        Form::make([$this->getSchema('form')])
                            ->id('settings')
                            ->livewireSubmitHandler('save')
                            ->footer([Actions::make([
                                Action::make('save')->submit('save'),
                                Action::make('reset')->color('gray')->requiresConfirmation()->action(fn () => $this->notify('Reset', 'warning')),
                            ])]),
                    ]),
                ]),
                Tab::make('Notifications')->icon(Heroicon::OutlinedBell)->schema([
                    Actions::make([
                        Action::make('success')->color('success')->action(fn () => $this->notify('Saved successfully', 'success')),
                        Action::make('warning')->color('warning')->action(fn () => $this->notify('Check your input', 'warning')),
                        Action::make('danger')->color('danger')->action(fn () => $this->notify('Something went wrong', 'danger')),
                        Action::make('info')->color('info')->action(fn () => $this->notify('Just so you know', 'info')),
                    ]),
                ]),
                Tab::make('Integrations')->icon(Heroicon::OutlinedPuzzlePiece)->schema([
                    EmptyState::make('No integrations yet')
                        ->description('Connect a payment provider to start taking orders.')
                        ->icon(Heroicon::OutlinedPuzzlePiece)
                        ->footer([Action::make('connect')->icon(Heroicon::OutlinedPlus)]),
                ]),
            ]),
        ]);
    }

    public function save(): void
    {
        $this->form->getState();
        $this->notify('Settings saved', 'success');
    }

    protected function notify(string $title, string $status): void
    {
        Notification::make()->title($title)->body('This is how notifications look.')->status($status)->send();
    }
}
