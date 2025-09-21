<?php

namespace App\Filament\Resources\Categories\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Services\YandexTranslateService;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Categories\CategoryResource;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $translator = app(YandexTranslateService::class);
        $locales = config('translatable.locales');

        // Переводим только если заполнено русское название
        if (!empty($data['ru']['title'])) {
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
            }
        }

        return $data;
    }
}
