<?php

namespace App\Filament\Resources\Menus;

use BackedEnum;
use App\Models\Menu;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ViewMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Schemas\MenuForm;
use App\Filament\Resources\Menus\Tables\MenusTable;
use App\Filament\Resources\Menus\Schemas\MenuInfolist;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    // protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Tabs::make('MenuTabs')->tabs([

                Tab::make('Переводы')
                    ->schema([
                        Tabs::make('Translations')
                            ->tabs(self::getTranslationTabs()),
                    ]),

                // Tab::make('Основное')
                //     ->schema([]),
            ]),
        ]);
    }

    protected static function getTranslationTabs(): array
    {
        $locales = collect(config('translatable.locales'))
            ->mapWithKeys(fn ($locale) => [$locale => strtoupper($locale)])
            ->toArray();

        $fields = ['name'];

        $tabs = [];
        foreach ($locales as $locale => $label) {
            $tabs[] = Tab::make($label)
                ->schema(array_map(
                    fn($field) => self::makeTranslatableField($locale, $field),
                    $fields
                ));
        }

        return $tabs;
    }

    protected static function makeTranslatableField(string $locale, string $field)
    {
        return TextInput::make("$locale.$field")
            ->label(ucfirst($field))
            ->default(fn($record) => $record?->translate($locale)?->$field)
            ->afterStateHydrated(fn($state, $set, $record) => $set("$locale.$field", $record?->translate($locale)?->$field))
            ->dehydrateStateUsing(fn ($state) => $state);
    }


    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'view' => ViewMenu::route('/{record}'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
