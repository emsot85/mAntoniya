<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\YandexTranslateService;
use Filament\Notifications\Notification;
use Astrotomic\Translatable\Translatable;
use Filament\Forms\Components\FileUpload;

class Backup extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected string $view = 'filament.pages.backup';
    protected static ?string $navigationLabel = 'Бэкап контента';

    protected static ?int $navigationSort = 100;

    // 🔹 Livewire property для FileUpload
    public $backupFile;

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('backupFile')
                ->label('Файл импорта (JSON)')
                ->directory('backups/tmp')
                ->disk('local')
                ->acceptedFileTypes(['application/json'])
                ->required(),
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Экспорт в backup')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportData')
                ->requiresConfirmation(),

            Actions\Action::make('import')
                ->label('Импорт backup')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('backupFile')
                        ->label('Файл бэкапа (JSON)')
                        ->directory('backups/tmp')
                        ->disk('local')
                        ->acceptedFileTypes(['application/json'])
                        ->required(),
                ])
                ->modalHeading('Импорт данных')
                ->modalDescription('⚠️ Текущие данные будут удалены и заменены содержимым из файла.')
                ->modalSubmitActionLabel('Импортировать')
                ->action(fn(array $data) => $this->importData($data)),

            Actions\Action::make('import_articles')
                ->label('Импорт Статей из Вордпресс')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('backupFile')
                        ->label('Файл бэкапа (JSON)')
                        ->directory('backups/tmp')
                        ->disk('local')
                        ->acceptedFileTypes(['application/json'])
                        ->required(),
                ])
                ->modalHeading('Импорт данных')
                ->modalDescription('⚠️ Текущие данные будут удалены и заменены содержимым из файла.')
                ->modalSubmitActionLabel('Импортировать')
                ->action(fn(array $data) => $this->importSimpleArticles($data)),

        ];
    }

    public function exportData()
    {
            $models = [
                \App\Models\Category::class,
                \App\Models\Article::class,
                \App\Models\Page::class,
                \App\Models\BottomBlock::class,
                \App\Models\Footer::class,
                \App\Models\Menu::class => ['items.translations'],
            ];

            $data = [];

            foreach ($models as $key => $value) {
                if (is_string($key)) {
                    $modelClass = $key;
                    $relations = $value;
                } else {
                    $modelClass = $value;
                    $relations = ['translations'];
                }

                $pluralKey = Str::plural(Str::camel(class_basename($modelClass)));

                $items = $modelClass::with($relations)->get()->map(function ($model) use ($relations) {
                    $arr = $model->toArray();

                    // Для моделей с Translatable формируем translations отдельно
                    if (in_array(Translatable::class, class_uses_recursive($model))) {
                    $arr['translations'] = [];
                    foreach ($model->translations as $t) {
                        $tArr = $t->toArray();

                        // Приводим JSON-поля к массиву
                        if (method_exists($t, 'getCasts')) {
                            foreach ($t->getCasts() as $field => $type) {
                                if ($type === 'array' && isset($tArr[$field]) && is_string($tArr[$field])) {
                                    $tArr[$field] = json_decode($tArr[$field], true);
                                }
                            }
                        }

                        $arr['translations'][] = $tArr;
                    }

                    // Удаляем переводимые атрибуты с корня модели
                    foreach ($model->translatedAttributes as $attr) {
                        unset($arr[$attr]);
                    }
                }

                // Для остальных связей (например Menu->items)
                foreach ($relations as $rel) {
                    if (isset($arr[$rel])) {
                        $arr[$rel] = collect($arr[$rel])->map(function ($relItem) use ($model, $rel) {
                            $relArr = $relItem;

                            $related = $model->$rel()->getRelated();

                            if (method_exists($related, 'getCasts')) {
                                foreach ($related->getCasts() as $f => $type) {
                                    if ($type === 'array' && isset($relArr[$f]) && is_string($relArr[$f])) {
                                        $relArr[$f] = json_decode($relArr[$f], true);
                                    }
                                }
                            }

                            return $relArr;
                        })->all();
                    }
                }

                return $arr;
            });

            $data[$pluralKey] = $items;
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.json';
        Storage::disk('local')->put("backups/{$filename}", $json);

        return response()->streamDownload(fn() => print($json), $filename, [
            'Content-Type' => 'application/json',
        ]);
    }


    public function importData(array $state): void
    {
        \Log::info("=== START IMPORT ===");
        \Log::info("STATE KEYS: " . implode(', ', array_keys($state)));
        \Log::info("STATE categories count: " . (isset($state['categories']) ? count($state['categories']) : 'null'));

         $file = $state['backupFile'] ?? null;

        if (!$file) {
            Notification::make()
                ->title('Файл не выбран')
                ->danger()
                ->send();
            return;
        }

        // читаем JSON
        $json = Storage::disk('local')->get($file);
        $state = json_decode($json, true);

        if (!$state) {
            Notification::make()
                ->title('Файл повреждён или пуст')
                ->danger()
                ->send();
            return;
        }

         \Log::info("STATE111 categories count: " . (isset($state['categories']) ? count($state['categories']) : 'null'));


        // авто-бэкап перед импортом
        $this->createAutoBackup();

      
       
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            \App\Models\Category::truncate();
            \App\Models\CategoryTranslation::truncate();

            \App\Models\Article::truncate();
            \App\Models\ArticleTranslation::truncate();

            \App\Models\Page::truncate();
            \App\Models\PageTranslation::truncate();

            \App\Models\Menu::truncate();
            \App\Models\MenuItem::truncate();
            \App\Models\MenuItemTranslation::truncate();

            \App\Models\BottomBlock::truncate();
            \App\Models\BottomBlockTranslation::truncate();

            \App\Models\Footer::truncate();
            \App\Models\FooterTranslation::truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');


            $idMap = [
                'pages'        => [],
                'categories'   => [],
                'menus'        => [],
                'menu_items'   => [],
                'articles'     => [],
                'bottomBlocks' => [],
                'footers'      => [],
            ];

            $normalizeDate = function ($value) {
                if (empty($value)) return now();
                try {
                    return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    \Log::warning("Invalid date: " . $value);
                    return now();
                }
            };

            $createWithTranslations = function ($modelClass, $items, $mapKey) use (&$idMap, $normalizeDate) {
                \Log::info("Importing {$mapKey}: " . count($items));

                foreach ($items as $data) {
                    try {
                        $oldId = $data['id'] ?? null;
                        $translations = $data['translations'] ?? [];
                        unset($data['id'], $data['translations']);

                        if (isset($data['created_at'])) $data['created_at'] = $normalizeDate($data['created_at']);
                        if (isset($data['updated_at'])) $data['updated_at'] = $normalizeDate($data['updated_at']);

                        /** @var \Illuminate\Database\Eloquent\Model $model */
                        $model = $modelClass::create($data);
                        $idMap[$mapKey][$oldId] = $model->id;

                        \Log::info("  Created {$mapKey} old={$oldId} new={$model->id}");
                        \Log::info("Translations for {$mapKey} old={$oldId}: " . json_encode($translations, JSON_UNESCAPED_UNICODE));


                        foreach ($translations as $t) {
                            unset($t['id'], $t[$mapKey . '_id'], $t['created_at'], $t['updated_at']);
                            unset($t['category_id']); // внешний ключ запишется через связь

                            if (empty($t['locale'])) {
                                \Log::error("❌ Нет locale в переводе {$mapKey}: " . json_encode($t, JSON_UNESCAPED_UNICODE));
                                continue;
                            }

                            $model->translations()->updateOrCreate(
                                ['locale' => $t['locale']],
                                $t
                            );
                        }

                    } catch (\Exception $e) {
                        \Log::error("Error importing {$mapKey}: " . $e->getMessage(), ['data' => $data]);
                    }
                }
            };

            try {
                $createWithTranslations(\App\Models\Category::class, $state['categories'] ?? [], 'categories');
                $createWithTranslations(\App\Models\Page::class, $state['pages'] ?? [], 'pages');
                $createWithTranslations(\App\Models\Article::class, $state['articles'] ?? [], 'articles');
                $createWithTranslations(\App\Models\BottomBlock::class, $state['bottomBlocks'] ?? [], 'bottomBlocks');
                $createWithTranslations(\App\Models\Footer::class, $state['footers'] ?? [], 'footers');

                foreach ($state['menus'] ?? [] as $menuData) {
                    try {
                        $oldMenuId = $menuData['id'] ?? null;
                        $items = $menuData['items'] ?? [];
                        unset($menuData['id'], $menuData['items']);

                        if (isset($menuData['created_at'])) $menuData['created_at'] = $normalizeDate($menuData['created_at']);
                        if (isset($menuData['updated_at'])) $menuData['updated_at'] = $normalizeDate($menuData['updated_at']);

                        $menu = \App\Models\Menu::create($menuData);
                        $idMap['menus'][$oldMenuId] = $menu->id;

                        \Log::info("Menu old={$oldMenuId} new={$menu->id}");

                        foreach ($items as $itemData) {
                            $oldItemId = $itemData['id'] ?? null;
                            $translations = $itemData['translations'] ?? [];
                            unset($itemData['id'], $itemData['translations']);

                            $itemData['menu_id'] = $idMap['menus'][$itemData['menu_id']] ?? null;
                            $itemData['page_id'] = $idMap['pages'][$itemData['page_id']] ?? null;
                            $parentId = $itemData['parent_id'] ?? null;
                            $itemData['parent_id'] = null;

                            $item = $menu->items()->create($itemData);
                            $idMap['menu_items'][$oldItemId] = $item->id;

                            \Log::info("  MenuItem old={$oldItemId} new={$item->id} (parent={$parentId})");

                            foreach ($translations as $t) {
                                unset($t['id'], $t['menu_item_id'], $t['created_at'], $t['updated_at']);
                                $item->translations()->updateOrCreate(
                                    ['locale' => $t['locale'] ?? 'xx'],
                                    $t
                                );
                            }

                            if ($parentId) {
                                $pendingParents[$item->id] = $parentId;
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error importing menu: " . $e->getMessage(), ['menu' => $menuData]);
                    }
                }

                if (!empty($pendingParents)) {
                    foreach ($pendingParents as $newId => $oldParentId) {
                        if (isset($idMap['menu_items'][$oldParentId])) {
                            \App\Models\MenuItem::where('id', $newId)->update([
                                'parent_id' => $idMap['menu_items'][$oldParentId],
                            ]);
                            \Log::info("Updated parent_id for item {$newId}");
                        }
                    }
                }

                \Log::info("=== IMPORT COMPLETE ===");
                Notification::make()->title('Импорт выполнен')->success()->send();

            } catch (\Exception $e) {
                \Log::error("FATAL Import error: " . $e->getMessage());
                throw $e;
            }

    }



    public function importSimpleArticles(array $data): void
    {
        $file = $data['backupFile'] ?? null;

        if (!$file) {
            Notification::make()
                ->title('Файл не выбран')
                ->danger()
                ->send();
            return;
        }

        $json = Storage::disk('local')->get($file);
        $articles = json_decode($json, true);

        if (!$articles) {
            Notification::make()
                ->title('Файл повреждён или пуст')
                ->danger()
                ->send();
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // // \App\Models\ArticleTranslation::query()->delete();
        // // \App\Models\Article::query()->delete();
        // \App\Models\ArticleTranslation::truncate();
        // \App\Models\Article::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $batchSize = 5;
        $batches = array_chunk($articles, $batchSize);
        $translator = new YandexTranslateService();

        foreach ($batches as $batch) {
            DB::transaction(function () use ($articles, $translator) {
                $i = 0;
                // $translator = new YandexTranslateService();
                foreach ($articles as $item) {
                    $i++;
                    $slug = Str::slug($item['title']);
                    // Проверяем, существует ли статья с таким slug
                    $existingArticle = \App\Models\Article::where('slug', $slug)->first();
                    if ($existingArticle) {
                        \Log::info("Статья существует:",[
                            'slug'=>$slug,
                            'title'=>$item['title'],
                        ]);
                        continue; // Пропускаем, если статья уже существует
                    }
                    // Создаём новую статью
                    $article = \App\Models\Article::create([
                        'uuid' => (string) Str::uuid(),
                        'slug' => $slug,
                        'category_id' => 1,
                        'status' => 'published',
                        'image' => null,
                        'meta_title' => $item['title'],
                        'title' => $item['title'],
                        'description' => $item['content'],
                    ]);
                    // Добавляем переводы
                    $article->translations()->updateOrCreate(
                        ['locale' => 'ru'],
                        [
                            'title' => $item['title'],
                            'description' => $item['content'],
                        ]
                    );

                    $locales = [
                        'en',
                        'zh',
                        'ar',  // арабский
                        'de',  // немецкий
                        'fr',  // французский
                        'es',  // испанский
                        'tr',  // турецкий
                        'tg',  // таджикский
                        'tt',  // татарский
                        'ja',  // японский
                        'ka',  // грузинский
                    ];

                    foreach ($locales as $locale) {
                        $article->translations()->updateOrCreate(
                            ['locale' => $locale],
                            [
                                'title' => $translator->translate($item['title'], $locale),
                                'description' => $translator->translate($item['content'], $locale),
                            ]
                        );
                    }
                }
            });
            sleep(1); // Пауза между пакетами
        }


        // DB::transaction(function () use ($articles) {

        //     $i = 0;

        //     $translator = new YandexTranslateService();

        //     foreach ($articles as $item) {
        //         $i++;
        //         // Создаём статью
        //         $article = \App\Models\Article::create([
        //             'uuid'        => (string) Str::uuid(),
        //             'slug'        => Str::slug($item['title']) . $i,
        //             'category_id' => 1,
        //             'status'      => 'published',
        //             'image'       => null,
        //             'meta_title'       => $item['title'], // чтобы в основной таблице тоже было
        //             'title'            => $item['title'],
        //             'description'      => $item['content'],
        //         ]);

        //         // // Добавляем переводы для ru/en/zh
        //         // foreach (['ru', 'en', 'zh'] as $locale) {
        //         //     $article->translations()->updateOrCreate(
        //         //         ['locale' => $locale], // уникальное сочетание
        //         //         [
        //         //             'title'       => $item['title'],
        //         //             'meta_title'  => $item['title'],
        //         //             'description' => $item['content'],
        //         //             'extra_field_1' => null,
        //         //             'extra_field_2' => null,
        //         //             'extra_field_3' => null,
        //         //         ]
        //         //     );
        //         // }

        //         // ru перевод
        //         $article->translations()->updateOrCreate(
        //             ['locale' => 'ru'],
        //             [
        //                 'title'       => $item['title'],
        //                 // 'meta_title'  => $item['title'],
        //                 'description' => $item['content'],
        //             ]
        //         );

        //         // en / zh переводы
        //         $locales = [
        //             'en',
        //             'zh',
        //             'ar',  // арабский
        //             'de',  // немецкий
        //             'fr',  // французский
        //             'es',  // испанский
        //             'tr',  // турецкий
        //             'tg',  // таджикский
        //             'tt',  // татарский
        //             'ja',  // японский
        //             'ka',  // грузинский
        //         ];
        //         foreach ($locales as $locale) {
        //             $article->translations()->updateOrCreate(
        //                 ['locale' => $locale],
        //                 [
        //                     'title'       => $translator->translate($item['title'], $locale),
        //                     // 'meta_title'  => $translator->translate($item['title'], $locale),
        //                     'description' => $translator->translate($item['content'], $locale),
        //                 ]
        //             );
        //         }
        //     }
        // });

        Storage::disk('local')->delete($file);

        Notification::make()
            ->title('Импорт статей выполнен')
            ->success()
            ->send();
    }



    private function createAutoBackup(): void
    {
        $models = [
            'categories'    => \App\Models\Category::with('translations')->get(),
            'articles'      => \App\Models\Article::with('translations')->get(),
            'pages'         => \App\Models\Page::with('translations')->get(),
            'bottomBlocks'  => \App\Models\BottomBlock::with('translations')->get(),
            'footers'       => \App\Models\Footer::with('translations')->get(),
            'menus'         => \App\Models\Menu::with(['items.translations'])->get(),
        ];

        // прогоняем модели через ->toArray(), чтобы casts применились
        $data = [];
        foreach ($models as $key => $collection) {
            $data[$key] = $collection->toArray();
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'auto-backup_' . now()->format('Y-m-d_H-i-s') . '.json';
        Storage::disk('local')->put("backups/{$filename}", $json);
    }

}
