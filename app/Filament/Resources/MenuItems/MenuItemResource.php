<?php

namespace App\Filament\Resources\MenuItems;

use BackedEnum;
use App\Models\Menu;
use App\Models\Page;
use App\Models\MenuItem;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\MenuItems\Pages\ListMenuItems;
use App\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use App\Filament\Resources\MenuItems\Schemas\MenuItemForm;
use App\Filament\Resources\MenuItems\Tables\MenuItemsTable;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'MenuItem';

    protected static ?int $navigationSort = 60;

    // protected static ?string $navigationGroup = 'Контент';

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Tabs::make('MenuItemTabs')
                ->tabs([
                    Tab::make('Основное')
                        ->schema([
                            Select::make('menu_id')
                                ->label('Меню')
                                ->options(Menu::all()->pluck('slug', 'id'))
                                ->required()
                                ->searchable(),

                            Select::make('parent_id')
                                ->label('Родительский пункт')
                                ->options(MenuItem::all()->pluck('title', 'id'))
                                ->searchable()
                                ->nullable(),

                            Select::make('page_id')
                                ->label('Привязанная страница')
                                ->options(Page::all()->pluck('title', 'id'))
                                ->searchable()
                                ->nullable(),

                            TextInput::make('sort_order')
                                ->numeric()
                                ->default(0)
                                ->label('Сортировка'),
                        ]),

                    Tab::make('Переводы')
                        ->schema([
                            Tabs::make('Translations')
                                ->tabs(self::getTranslationTabs()),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menu.name')
                    ->label('Меню')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Заголовок')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('url')
                    ->label('URL')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuItems::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'edit' => EditMenuItem::route('/{record}/edit'),
        ];
    }

 

    /**
     * Генерация вкладок для переводов
     */
    protected static function getTranslationTabs(): array
    {
        $locales = collect(config('translatable.locales'))
            ->mapWithKeys(fn($locale) => [$locale => strtoupper($locale)])
            ->toArray();

        $fields = ['title', 'url'];

        $tabs = [];
         foreach ($locales as $locale => $label) {
           
            $fieldsForLocale = array_map(
                fn ($field) => self::makeTranslatableField($locale, $field),
                $fields
            );

            $tabs[] = Tab::make($label)
                ->schema(array_merge(
                    [
                        Toggle::make("$locale.auto_translate")
                            ->label('Автоматический перевод')
                            ->default(false)
                            ->helperText('Если включено — данные будут перезаписываться при сохранении')
                            ->columnSpanFull(),
                    ],
                    $fieldsForLocale
                ));
        }

        return $tabs;
    }

    /**
     * Хелпер для переведённых полей
     */
    protected static function makeTranslatableField(string $locale, string $field)
    {
        return TextInput::make("$locale.$field")
            ->label(ucfirst($field))
            ->default(fn($record) => $record?->translate($locale)?->$field)
            ->afterStateHydrated(fn($state, $set, $record) => $set("$locale.$field", $record?->translate($locale)?->$field))
            ->dehydrateStateUsing(fn($state) => $state);
    }
}
