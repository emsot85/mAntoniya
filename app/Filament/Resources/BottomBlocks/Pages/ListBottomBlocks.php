<?php

namespace App\Filament\Resources\BottomBlocks\Pages;

use App\Filament\Resources\BottomBlocks\BottomBlockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBottomBlocks extends ListRecords
{
    protected static string $resource = BottomBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
