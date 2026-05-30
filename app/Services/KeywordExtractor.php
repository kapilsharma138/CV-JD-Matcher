<?php

namespace App\Services;

class KeywordExtractor
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function extract(string $text): array
    {
        $text = strtolower($text);
        $keywords = require base_path('data/keywords.php');
        $found = [];

        foreach ($keywords as $category => $terms) {
            if ($category === 'related') continue;
            foreach ($terms as $term => $weight) {
                if (str_contains($text, $term)) {
                    $found[$category][] = ['term' => $term, 'weight' => $weight];
                }
            }
        }
        return $found;
    }
}
