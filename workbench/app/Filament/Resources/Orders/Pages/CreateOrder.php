<?php

namespace Workbench\App\Filament\Resources\Orders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Workbench\App\Filament\Resources\Orders\OrderResource;

class CreateOrder extends CreateRecord
{
    use HasWizard;

    protected static string $resource = OrderResource::class;

    public function getSteps(): array
    {
        return OrderResource::wizardSteps();
    }
}
