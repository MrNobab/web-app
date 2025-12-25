<?php

namespace App\Filament\TopAdmin\Resources\LicenseResource\Pages;

use App\Filament\TopAdmin\Resources\LicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLicenses extends ListRecords
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
