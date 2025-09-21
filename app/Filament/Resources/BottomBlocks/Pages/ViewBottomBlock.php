<?php

namespace App\Filament\Resources\BottomBlocks\Pages;

use App\Filament\Resources\BottomBlocks\BottomBlockResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBottomBlock extends ViewRecord
{
    protected static string $resource = BottomBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
