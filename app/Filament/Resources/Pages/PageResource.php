<?php

namespace App\Filament\Resources\Pages;

use BackedEnum;
use App\Models\Page;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;
use Symfony\Component\ErrorHandler\Debug;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Schemas\PageForm;
use App\Filament\Resources\Pages\Tables\PagesTable;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // protected static ?string $recordTitleAttribute = 'Admin';

    public static function form(Schema $form): Schema
    {
        return $form->columns(1)->components([
            Tabs::make('PageTabs')->columns(1)->components([
                Tab::make('Переводы')->columns(1)->components([
                    Tabs::make('Translations')->components(self::getTranslationTabs()),
                ]),

                Tab::make('Настройки')
                    ->components([
                        TextInput::make('slug')
                            ->label('Slug')
                            ->unique(ignoreRecord: true)
                            // ->required()
                            ->disabled(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                            ->afterStateHydrated(fn($state, $set, $record) => $set('slug', $record?->slug))
                            ->dehydrateStateUsing(function ($state, $record, $get) {
                                if ($record?->exists) {
                                    return $state;
                                }
                                $title = $get('ru.title') ?: $get('en.title');
                                return Str::slug($title ?? Str::uuid());
                            }),

                        Toggle::make('is_active')
                            ->label('Активна')
                            ->default(true),
                    ]),
            ]),


        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('slug')->searchable()->sortable(),
            IconColumn::make('is_active')->boolean(),
            TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                // EditAction::make(),
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }

    /**
     * Генерация вкладок для языков
     */
    protected static function getTranslationTabs(): array
    {
        $locales = collect(config('translatable.locales'))
            ->mapWithKeys(fn($locale) => [$locale => strtoupper($locale)])
            ->toArray();

        $fields = [
            'title',
            'description',
            'content',
            'extra_field_1',
            'extra_field_2',
            'extra_field_3',
            'meta_title',
            'meta_description',
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

    /**
     * Хелпер для генерации поля перевода
     */
    protected static function makeTranslatableField(string $locale, string $field)
    {
        // Поля, которые должны быть WYSIWYG
        $isRichEditor = in_array($field, [
            'description',
            'content',
            'extra_field_1',
            'extra_field_2',
            'extra_field_3',
            'meta_description',
        ]);

        return $isRichEditor
            // ? RichEditor::make("$locale.$field") // редактор с HTML
            // ->label('Описание')
            // ->columnSpanFull()
            // ->required(false)

              ? RichEditor::make("$locale.$field")
                            ->label(ucfirst(str_replace('_', ' ', $field)))
                             ->toolbarButtons([
                                    ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                    ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                    ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                    ['table', 'attachFiles'], // + customBlocks / mergeTags, если используются
                                    ['undo', 'redo'],
                                ])
                            // ->disableToolbarButtons(['strike', 'h-1', 'h-2']) // можно отключить ненужные кнопки
                            // ->disableXssProtection() // обязательно, иначе Filament всё экранирует
                            ->columnSpanFull()
                            ->required(false)
                            ->default(fn($record) => $record?->translate($locale)?->$field)
                            ->afterStateHydrated(function ($component, $set, $record) use ($locale, $field) {
                                $value = $record?->translate($locale)?->$field;
                                $set("$locale.$field", is_string($value) ? $value : null);
                            })
                            ->dehydrateStateUsing(fn($state) => is_string($state) ? $state : '')

            : TextInput::make("$locale.$field")
                ->label(ucfirst(str_replace('_', ' ', $field))) 
                ->afterStateHydrated(function ($component, $set, $record) use ($locale, $field) {
                        $value = $record?->translate($locale)?->$field;
                        $set("$locale.$field", is_string($value) ? $value : null);
                    })
                ->dehydrateStateUsing(fn($state) => is_string($state) ? $state : '');

        // return $component;
            // ->label(ucfirst(str_replace('_', ' ', $field)))
            // // ->default(fn($record) => $record?->translate($locale)?->$field ?? '')
            //    ->afterStateHydrated(function ($component, $set, $record) use ($locale, $field) {
            //         $value = $record?->translate($locale)?->$field;
            //         $set("$locale.$field", is_string($value) ? $value : null);
            //     })
            //     ->dehydrateStateUsing(fn($state) => is_string($state) ? $state : '');
            //  ->afterStateHydrated(function ($component, $set, $record) use ($locale, $field) {
            //             $value = $record?->translate($locale)?->$field;
            //             $set("$locale.$field", is_string($value) ? $value.'' : '');
            //         })
            //         ->dehydrateStateUsing(fn($state) => is_string($state) ? $state.'_' : '_');
    }
}
