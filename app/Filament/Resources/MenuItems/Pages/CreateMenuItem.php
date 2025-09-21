<?php

namespace App\Filament\Resources\MenuItems\Pages;

use App\Services\YandexTranslateService;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MenuItems\MenuItemResource;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;

      protected function mutateFormDataBeforeCreate(array $data): array
    {
        $translator = app(YandexTranslateService::class);
        $locales = config('translatable.locales');

        // Переводим только если заполнен русский язык
        if (!empty($data['ru']['title']) || !empty($data['ru']['url'])) {
            foreach ($locales as $locale) {
                if ($locale === 'ru') continue;
                // проверяем чекбокс
                // if (!($data[$locale]['auto_translate'] ?? false)) {
                //     continue; // пропускаем, если выключен перевод
                // }

                if (!empty($data['ru']['title'])) {
                    try {
                        $data[$locale]['title'] = $translator->translate($data['ru']['title'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['title'] = $data['ru']['title']; // или оставьте пустым
                    }
                }

                if (!empty($data['ru']['url'])) {
                    try {
                        $data[$locale]['url'] = $data['ru']['url'];
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['urlle'] = $data['ru']['url']; // или оставьте пустым
                    }
                }
            }
        }

        return $data;
    }
}
