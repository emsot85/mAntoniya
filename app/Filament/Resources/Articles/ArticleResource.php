<?php

namespace App\Filament\Resources\Articles;

use BackedEnum;
use App\Models\Tag;
use Filament\Forms;
use Filament\Tables;
use App\Models\Article;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ViewArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Schemas\ArticleForm;
use App\Filament\Resources\Articles\ArticleResource\Pages;
use App\Filament\Resources\Articles\Schemas\ArticleInfolist;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function infolist(Schema $schema): Schema
    {
        return ArticleInfolist::configure($schema);
    }

    public static function form(Schema $form): Schema
    {
        $categories = Category::all()->mapWithKeys(fn($c) => [$c->id => $c->title]);
        $tags = Tag::all()->mapWithKeys(fn($t) => [$t->id => $t->title]);

        return $form->schema([
            Tabs::make('ArticleTabs')
                ->tabs([
                    Tab::make('Основное')
                        ->schema([

                            Select::make('category_id')
                                ->label('Категория')
                                ->options($categories)
                                ->searchable(),

                            Select::make('tags')
                                ->label('Теги')
                                ->multiple()
                                ->options($tags)
                                ->preload(),

                            FileUpload::make('image')
                                ->image()
                                ->label('Изображение')
                                ->directory('images/articles')
                                ->imageEditor()
                                // ->preserveFilenames()
                                ->deleteUploadedFileUsing(fn($file) => Storage::disk('public')->delete($file))
                                ->maxSize(1024),

                            TextInput::make('views')
                                ->label('Просмотры')
                                ->numeric()
                                ->default(0)
                                ->disabled(),

                            Select::make('status')
                                ->label('Статус')
                                ->options([
                                    'draft' => 'Черновик',
                                    'published' => 'Опубликован',
                                    'archived' => 'Архив',
                                ])
                                ->default('draft')
                                ->searchable(),

                            TextInput::make('slug')
                                ->label('Slug')
                                ->unique(ignoreRecord: true)
                                ->disabled(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                ->afterStateHydrated(function ($state, $set, $record) {
                                    // при редактировании подставляем текущий slug
                                    if ($record?->slug) {
                                        $set('slug', $record->slug);
                                    }
                                })
                                ->dehydrateStateUsing(function ($state, $record, $get) {
                                    // Если slug уже заполнен — оставляем его
                                    if (!empty($state)) {
                                        return $state;
                                    }

                                    // Генерируем из заголовка (сначала ru, затем en)
                                    $title = $get('ru.title') ?: $get('en.title') ?: $get('title');
                                    if ($title) {
                                        return \Illuminate\Support\Str::slug($title);
                                    }

                                    // fallback — UUID
                                    return (string) \Illuminate\Support\Str::uuid();
                                }),
                        ]),

                    Tab::make('Переводы')
                        ->components([
                            Tabs::make('Translations')
                                ->components(self::getTranslationTabs()),
                        ]),

                    Tab::make('Видео')
                        ->schema([
                            Repeater::make('videos')
                                ->schema([
                                    Select::make('platform')
                                        ->label('Платформа')
                                        ->options(
                                            collect(\App\Enums\PlatformVideosEnum::cases())
                                                ->mapWithKeys(fn($case) => [$case->value => ucfirst($case->name)])
                                        )
                                        ->required(),
                                    TextInput::make('url')
                                        ->label('URL')
                                        ->url()
                                        ->required(),
                                ])
                                ->reorderable()
                                ->collapsible(),
                        ]),

                    Tab::make('Кнопки')
                        ->schema([
                            Repeater::make('buttons')
                                ->schema([
                                    TextInput::make('label')->label('Название'),
                                    TextInput::make('url')->label('URL')->url(),
                                    ColorPicker::make('color')->label('Цвет'),

                                ])
                                ->reorderable()
                                ->collapsible(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')->searchable(),
                // Tables\Columns\TextColumn::make('category.title')->label('Категория'), // сейчас не работает
                // Tables\Columns\TextColumn::make('tags.title')->label('Теги')->badge(), // сейчас не работает
                Tables\Columns\TextColumn::make('views')->label('Просмотры')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i'),
            ])
            ->filters([ // сейчас не работает закоментил
                // Tables\Filters\SelectFilter::make('category')->options(Category::all()->pluck('title', 'id'))->label('Категория'),

                // Filter::make('tags')
                //     ->query(fn($query, $data) => $query->whereHas('tags', fn($q) => $q->whereTranslation('title', $data['value'])))
                //     ->form([
                //         Forms\Components\Select::make('value')
                //             ->options(\App\Models\Tag::all()->pluck('title', 'id'))
                //             ->multiple()
                //             ->searchable(),
                //     ])
            ])
            ->actions([
                EditAction::make(),
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
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'view' => ViewArticle::route('/{record}'),
            'edit' => EditArticle::route('/{record}/edit'),
        ];
    }

    /**
     * Генерация вкладок для языков.
     */
    protected static function getTranslationTabs(): array
    {
        $locales = collect(config('translatable.locales'))
            ->mapWithKeys(fn($locale) => [$locale => strtoupper($locale)])
            ->toArray();

        $fields = ['title', 'description', 'extra_field_1', 'extra_field_2', 'extra_field_3', 'meta_title', 'meta_description'];

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
     * Хелпер для генерации поля перевода.
     */
    protected static function makeTranslatableField(string $locale, string $field)
    {
        $isRich = in_array($field, ['description', 'content', 'extra_field_1', 'extra_field_2', 'extra_field_3', 'meta_description']);

        $component = $isRich
            ? RichEditor::make("$locale.$field")
            ->label(ucfirst(str_replace('_', ' ', $field)))
            ->columnSpanFull()
            ->required(false)
            : TextInput::make("$locale.$field")
            ->label(ucfirst(str_replace('_', ' ', $field)))
            ->required(false);

        return $component
            ->default(fn($record) => $record?->translate($locale)?->$field)
            ->afterStateHydrated(function ($component, $set, $record) use ($locale, $field) {
                $value = $record?->translate($locale)?->$field;
                $set("$locale.$field", $value);
            })
            ->dehydrateStateUsing(fn($state) => $state);
    }
}
