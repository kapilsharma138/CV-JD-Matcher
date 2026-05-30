<?php

namespace App\Services;

class KeywordExtractor
{
    private array $keywords;
    private static ?array $cache = null;

    public function __construct()
    {
        if (self::$cache === null) {
            self::$cache = require base_path('data/keywords.php');
        }
        $this->keywords = self::$cache;
    }

    public function extract(string $text): array
    {
        $text  = strtolower($text);
        $found = [];

        foreach ($this->keywords as $category => $terms) {

            if ($category === 'related') continue;

            foreach ($terms as $term => $weight) {
                // CHANGED: word boundary check instead of str_contains
                if (preg_match('/\b' . preg_quote(strtolower($term), '/') . '\b/', $text)) {
                    $found[$category][] = [
                        'term'   => $term,
                        'weight' => $weight,
                    ];
                }
            }
        }

        return $found;
    }

    // ADDED: helper for file-based extraction
    public function extractFromFile(string $path): array
    {
        $text = file_get_contents($path);
        return $this->extract($text);
    }

    public function getRelated(): array
    {
        return $this->keywords['related'] ?? [];
    }
}