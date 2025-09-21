<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Services\YandexTranslateService;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Articles\ArticleResource;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

      protected function mutateFormDataBeforeCreate(array $data): array
    {
        $translator = app(YandexTranslateService::class);
        $locales = config('translatable.locales');
  $fields = ['title', 'description', 'extra_field_1', 'extra_field_2', 'extra_field_3', 'meta_title', 'meta_description'];

        // Переводим только если заполнен русский язык
        if (!empty($data['ru']['title']) || !empty($data['ru']['description'])) {
            foreach ($locales as $locale) {
                if ($locale === 'ru') continue;
                // проверяем чекбокс
                if (!($data[$locale]['auto_translate'] ?? false)) {
                    continue; // пропускаем, если выключен перевод
                }

                if (!empty($data['ru']['title'])) {
                    try {
                        $data[$locale]['title'] = $translator->translate($data['ru']['title'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['title'] = $data['ru']['title']; // или оставьте пустым
                    }
                }


                if (!empty($data['ru']['description'])) {
                    try {
                        $data[$locale]['description'] = $translator->translate($data['ru']['description'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['description'] = $data['ru']['description']; // или оставьте пустым
                    }
                }

                if (!empty($data['ru']['extra_field_1'])) {
                    try {
                        $data[$locale]['extra_field_1'] = $translator->translate($data['ru']['extra_field_1'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['extra_field_1'] = $data['ru']['extra_field_1']; // или оставьте пустым
                    }
                }

                if (!empty($data['ru']['extra_field_2'])) {
                     try {
                        $data[$locale]['extra_field_2'] = $translator->translate($data['ru']['extra_field_2'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['extra_field_2'] = $data['ru']['extra_field_2']; // или оставьте пустым
                    }
                }

                 if (!empty($data['ru']['extra_field_3'])) {
                    try {
                        $data[$locale]['extra_field_3'] = $translator->translate($data['ru']['extra_field_3'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['extra_field_3'] = $data['ru']['extra_field_3']; // или оставьте пустым
                    }
                }

                 if (!empty($data['ru']['meta_title'])) {
                    try {
                        $data[$locale]['meta_title'] = $translator->translate($data['ru']['meta_title'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['meta_title'] = $data['ru']['meta_title']; // или оставьте пустым
                    }
                }    

                  if (!empty($data['ru']['meta_description'])) {
                    try {
                        $data[$locale]['meta_description'] = $translator->translate($data['ru']['meta_description'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['meta_description'] = $data['ru']['meta_description']; // или оставьте пустым
                    }
                }    

            }
        }

        return $data;
    }
    
}
