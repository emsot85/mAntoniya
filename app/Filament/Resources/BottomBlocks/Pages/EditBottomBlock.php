<?php

namespace App\Filament\Resources\BottomBlocks\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Services\YandexTranslateService;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\BottomBlocks\BottomBlockResource;

class EditBottomBlock extends EditRecord
{
    protected static string $resource = BottomBlockResource::class;

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

        // Переводим только если заполнен русский язык
        if (!empty($data['ru']['title']) || !empty($data['ru']['content'])) {
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

                 if (!empty($data['ru']['extra_field_4'])) {
                    try {
                        $data[$locale]['extra_field_4'] = $translator->translate($data['ru']['extra_field_4'], $locale);
                    } catch (\Exception $e) {
                        \Log::error("Ошибка перевода для {$locale}: " . $e->getMessage());
                        $data[$locale]['extra_field_4'] = $data['ru']['extra_field_4']; // или оставьте пустым
                    }
                }

                // buttons
                if (!empty($data['ru']['buttons']) && is_array($data['ru']['buttons'])) {
                    $data[$locale]['buttons'] = collect($data['ru']['buttons'])->map(function ($button) use ($translator, $locale) {
                        try {
                            return [
                                'title' => $translator->translate($button['title'], $locale),
                                'url'   => $button['url'],
                            ];
                        } catch (\Exception $e) {
                            \Log::error("Ошибка перевода кнопки для {$locale}: " . $e->getMessage());
                            return $button; // fallback — оставить русский вариант
                        }
                    })->toArray();
                }

            }
        }

        return $data;
    }
}
