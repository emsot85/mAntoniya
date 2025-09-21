<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use DOMDocument;

class YandexTranslateService
{
    protected string $apiKey;
    protected string $folderId;

    public function __construct()
    {
        $this->apiKey = config('services.yandex_translate.api_key');
        $this->folderId = config('services.yandex_translate.folder_id');
    }

    public function translateHtml(string $html, string $targetLanguage): string
    {
        if (trim($html) === '') {
            return $html;
        }

        // Загружаем HTML в DOM
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Рекурсивно перевести все текстовые узлы
        $this->translateTextNodes($dom, $targetLanguage);

        return $dom->saveHTML();
    }

    protected function translateTextNodes(\DOMNode $node, string $targetLanguage): void
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE && trim($child->nodeValue) !== '') {
                $child->nodeValue = $this->translate($child->nodeValue, $targetLanguage);
            } elseif ($child->hasChildNodes()) {
                $this->translateTextNodes($child, $targetLanguage);
            }
        }
    }

    public function translate(string $text, string $targetLanguage): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Api-Key ' . $this->apiKey,
        ])->post('https://translate.api.cloud.yandex.net/translate/v2/translate', [
            'folderId' => $this->folderId,
            'texts' => [$text],
            'targetLanguageCode' => $targetLanguage,
            'sourceLanguageCode' => 'ru',
        ]);

        if ($response->failed()) {
            \Log::error('Yandex Translate error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return $text;
        }

        $result = $response->json();
        return $result['translations'][0]['text'] ?? $text;
    }
}
