<?php

namespace App\Filament\Resources\Pages\Pages;

use Filament\Actions\DeleteAction;
use App\Services\YandexTranslateService;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Pages\PageResource;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $translator = app(YandexTranslateService::class);
        $locales = config('translatable.locales');

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

                if (!empty($data['ru']['content'])) {
                    try {
                        $data[$locale]['content'] = $translator->translate($data['ru']['content'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['content'] = $data['ru']['content']; // или оставьте пустым
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
