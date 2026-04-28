<?php

namespace App\Filament\Resources\DigitalNoteResource\Pages;

use App\Filament\Resources\DigitalNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDigitalNote extends EditRecord
{
    protected static string $resource = DigitalNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
