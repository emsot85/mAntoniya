<?php

namespace App\Filament\Resources\Categories;

use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ViewCategory;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Filament\Resources\Categories\Schemas\CategoryInfolist;
    

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 50;

    public static function infolist(Schema $schema): Schema
    {
        return CategoryInfolist::configure($schema);
    }
    
    // Английский (en) EvidenceКитайский (zh)证据 (zhèngjù)Арабский (ar)أدلة (adilla)Немецкий (de)BeweiseФранцузский (fr)PreuvesИспанский (es)PruebasТурецкий (tr)KanıtlarТаджикский (tg)Далелҳо (dalehho)Татарский (tt)Дәлилләр (däliller)Японский (ja)証拠 (shōko)Грузинский (ka)მტკიცებები (mtk’itsebebi)

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected static ?string $navigationGroup = 'Статьи';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('CategoryTabs')
                ->tabs([
                    Tab::make('Основное')
                        ->components([
                            Select::make('parent_id')
                                ->label('Родительская категория')
                                ->options(
                                    \App\Models\Category::all()->pluck('title_for_filter', 'id')
                                )
                                ->searchable()
                                ->nullable(),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->unique(ignoreRecord: true),
                        ]),
                    Tab::make('Переводы')
                        ->schema([
                            Tabs::make('Translations')
                                ->tabs(self::getTranslationTabs()),
                        ]),
                ]),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Название'),
                Tables\Columns\TextColumn::make('slug')->label('Slug'),
                Tables\Columns\TextColumn::make('parent.title')->label('Родительская категория'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Фильтр по родителю')
                    ->options(Category::all()->pluck('title', 'id')),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    protected static function getTranslationTabs(): array
    {
        $locales = config('translatable.locales', ['ru', 'en', 'zh']);
        $fields = ['title'];

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
         return TextInput::make("{$locale}.{$field}")
        ->label(ucfirst($field))
        ->default(fn ($record) => $record?->translate($locale)?->{$field})
        ->afterStateHydrated(
            fn ($state, $set, $record) => $set(
                "{$locale}.{$field}",
                $record?->translate($locale)?->{$field}
            )
        )
        ->dehydrateStateUsing(fn ($state) => $state);

        // return TextInput::make("$locale.$field")
        //     ->label(ucfirst($field))
        //     ->default(fn ($record) => $record?->translate($locale, false)?->$field)
        //     ->dehydrateStateUsing(fn ($state) => $state);
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
