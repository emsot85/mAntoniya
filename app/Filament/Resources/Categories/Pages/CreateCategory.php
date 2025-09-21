<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Services\YandexTranslateService;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Categories\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

     protected function mutateFormDataBeforeCreate(array $data): array
    {
        $translator = app(YandexTranslateService::class);
        $locales = config('translatable.locales');

        // Переводим только если заполнено русское название
        if (!empty($data['ru']['title'])) {
            foreach ($locales as $locale) {
                if ($locale === 'ru') continue;

                if (!empty($data['ru']['title'])) {
                    try {
                        $data[$locale]['title'] = $translator->translate($data['ru']['title'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['title'] = $data['ru']['title']; // или оставьте пустым
                    }
                }
            }
        }

        return $data;
    }
    
}
