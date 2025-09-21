<?php

namespace App\Filament\Resources\BottomBlocks;

use BackedEnum;
use Filament\Tables\Table;
use App\Models\BottomBlock;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Resources\BottomBlocks\Pages\EditBottomBlock;
use App\Filament\Resources\BottomBlocks\Pages\ViewBottomBlock;
use App\Filament\Resources\BottomBlocks\Pages\ListBottomBlocks;
use App\Filament\Resources\BottomBlocks\Pages\CreateBottomBlock;
use App\Filament\Resources\BottomBlocks\Schemas\BottomBlockForm;
use App\Filament\Resources\BottomBlocks\Tables\BottomBlocksTable;
use App\Filament\Resources\BottomBlocks\Schemas\BottomBlockInfolist;

class BottomBlockResource extends Resource
{
    protected static ?string $model = BottomBlock::class;

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

    public static function infolist(Schema $schema): Schema
    {
        return BottomBlockInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // return BottomBlocksTable::configure($table);
         return $table->columns([
            TextColumn::make('id'),
            TextColumn::make('created_at')->dateTime(),
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
            'index' => ListBottomBlocks::route('/'),
            'create' => CreateBottomBlock::route('/create'),
            'view' => ViewBottomBlock::route('/{record}'),
            'edit' => EditBottomBlock::route('/{record}/edit'),
        ];
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
            'image',
            'videos',
            'buttons',
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

        // Картинка
        'image' =>
            FileUpload::make("$locale.$field")
                ->image()
                ->label('Изображение')
                ->directory('images/block')
                ->imageEditor()
                ->deleteUploadedFileUsing(fn($file) => Storage::disk('public')->delete($file))
                ->maxSize(1024),

        // Кнопки (JSON)
        'buttons' =>
            Repeater::make("$locale.$field")
                ->label('Кнопки')
                ->schema([
                    TextInput::make('title')->label('Заголовок')->required(),
                    TextInput::make('url')->label('Ссылка')->required(),
                ])
                ->default([])
                ->columnSpanFull()
                ->afterStateHydrated(function ($component, $state, $record) use ($locale, $field) {
                    $value = $record?->translate($locale)?->$field;
                    $component->state(is_array($value) ? $value : []);
                })
                ->dehydrateStateUsing(fn($state) => $state ?: []),

        // Видео (JSON)
        'videos' =>
            Repeater::make("$locale.$field")
                ->label('Видео')
                ->schema([
                    Select::make('platform')
                        ->options(
                            collect(\App\Enums\PlatformVideosEnum::cases())
                                ->mapWithKeys(fn($case) => [$case->value => ucfirst($case->name)])
                        )
                        ->required(),
                    TextInput::make('url')->label('URL')->required(),
                ])
                ->default([])
                ->reorderable()
                ->collapsible()
                ->columnSpanFull()
                ->afterStateHydrated(function ($component, $state, $record) use ($locale, $field) {
                    $value = $record?->translate($locale)?->$field;
                    $component->state(is_array($value) ? $value : []);
                })
                ->dehydrateStateUsing(fn($state) => $state ?: []),

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


    protected static function makeTranslatableField1(string $locale, string $field)
    {
        $isRich = in_array($field, ['content', 'extra_field_1', 'extra_field_2', 'extra_field_3', 'extra_field_4']);

        return match ($field) {
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

            'image' =>
               
                FileUpload::make("$locale.$field")
                    ->image()
                    ->label('Изображение')
                    ->directory('images/block')
                    ->imageEditor()
                    // ->preserveFilenames()
                    ->deleteUploadedFileUsing(fn($file) => Storage::disk('public')->delete($file))
                    ->maxSize(1024),

            'buttons' =>

                Repeater::make("$locale.$field")
                    ->label('Кнопки')
                    ->schema([
                        TextInput::make('title')->label('Заголовок')->required(),
                        TextInput::make('url')->label('Ссылка')->required(),
                    ])
                    ->default([])
                    ->columnSpanFull()
                    ->afterStateHydrated(function ($component, $state, $record) use ($locale, $field) {
                        $value = $record?->translate($locale)?->$field;
                        // если null → ставим []
                        $component->state($value ?? []);
                    })
                    ->dehydrateStateUsing(fn($state) => $state ?: []),


            'videos' =>
                Repeater::make("$locale.$field")
                    ->label('Видео')
                    ->schema([
                        Select::make('platform')
                             ->options(
                                collect(\App\Enums\PlatformVideosEnum::cases())
                                    ->mapWithKeys(fn($case) => [$case->value => ucfirst($case->name)])
                            )
                            ->required(),
                        TextInput::make('url')
                            ->label('URL')
                            ->required(),
                    ])
                    ->default([])
                    ->reorderable()
                    ->collapsible()
                    ->columnSpanFull()
                    ->afterStateHydrated(function ($component, $state, $record) use ($locale, $field) {
                        $value = $record?->translate($locale)?->$field;
                        // если null → ставим []
                        $component->state($value ?? []);
                    })
                    ->dehydrateStateUsing(fn($state) => $state ?: []),

            default =>
                TextInput::make("$locale.$field")
                    ->label(ucfirst(str_replace('_', ' ', $field)))
                    ->required(false)
                    ->default(fn($record) => $record?->translate($locale)?->$field ?? '')
                    ->afterStateHydrated(function ($component, $set, $record) use ($locale, $field) {
                        $value = $record?->translate($locale)?->$field;
                        $set("$locale.$field", $value ?? null);
                    })
                    ->dehydrateStateUsing(fn($state) => $state ?? ''),
        };
    }
}
