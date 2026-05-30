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


    public function extractFromJD(string $jdText): array
    {
        $jdText   = strtolower($jdText);
        $techTerms = require base_path('data/tech-terms.php');
        $found    = [];

        foreach ($techTerms as $term) {
            if (preg_match('/\b' . preg_quote($term, '/') . '\b/', $jdText)) {
                $found[] = [
                    'term'   => $term,
                    'weight' => $this->getWeight($term),
                ];
            }
        }

        return ['extracted' => $found];
    }

    private function getWeight(string $term): int
    {
        foreach ($this->keywords as $category => $terms) {
            if ($category === 'related') continue;
            if (array_key_exists($term, $terms)) {
                return $terms[$term]; // use your defined weight if exists
            }
        }
        return 1; // default weight for unknown terms
    }
}