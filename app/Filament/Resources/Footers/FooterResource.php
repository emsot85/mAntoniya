<?php

namespace App\Filament\Resources\Footers;

use BackedEnum;
use App\Models\Footer;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Footers\Pages\EditFooter;
use App\Filament\Resources\Footers\Pages\ListFooters;
use App\Filament\Resources\Footers\Pages\CreateFooter;
use App\Filament\Resources\Footers\Schemas\FooterForm;
use App\Filament\Resources\Footers\Tables\FootersTable;

class FooterResource extends Resource
{
    protected static ?string $model = Footer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
         return $schema->columns(1)->components([
            Tabs::make('BottomBlockTabs')->columns(1)->components([
                Tab::make('Переводы')->columns(1)->components([
                    Tabs::make('Translations')->components(self::getTranslationTabs()),
                ]),               
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return FootersTable::configure($table);
    }

     protected static function getTranslationTabs(): array
    {
        $locales = collect(config('translatable.locales'))
            ->mapWithKeys(fn ($locale) => [$locale => strtoupper($locale)])
            ->toArray();

        $fields = [
            'title',
            'content',
            'extra_field_1',
            'extra_field_2',
            'extra_field_3',
            'extra_field_4',
        ];

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

    protected static function makeTranslatableField(string $locale, string $field)
    {
        return match ($field) {
            // Текстовые поля с RichEditor
            'content', 'extra_field_1', 'extra_field_2', 'extra_field_3', 'extra_field_4' =>
                RichEditor::make("$locale.$field")
                    ->label(ucfirst(str_replace('_', ' ', $field)))
                    ->columnSpanFull()
                    ->required(false)
                    ->afterStateHydrated(function ($component, $set, $record) use ($locale, $field) {
                        $value = $record?->translate($locale)?->$field;
                        $set("$locale.$field", is_string($value) ? $value : null);
                    })
                    ->dehydrateStateUsing(fn($state) => is_string($state) ? $state : ''),
           

            // Все остальные — строки
            default =>
                TextInput::make("$locale.$field")
                    ->label(ucfirst(str_replace('_', ' ', $field)))
                    ->required(false)
                    ->afterStateHydrated(function ($component, $set, $record) use ($locale, $field) {
                        $value = $record?->translate($locale)?->$field;
                        $set("$locale.$field", is_string($value) ? $value : null);
                    })
                    ->dehydrateStateUsing(fn($state) => is_string($state) ? $state : ''),
        };
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
            'index' => ListFooters::route('/'),
            'create' => CreateFooter::route('/create'),
            'edit' => EditFooter::route('/{record}/edit'),
        ];
    }
}
