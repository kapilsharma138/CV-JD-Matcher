<?php

namespace App\Services;

class SuggestionEngine
{
    private array $related;

    public function __construct(KeywordExtractor $extractor)
    {
        $this->related = $extractor->getRelated();
    }

    public function suggest(array $missing): array
    {
        $suggestions = [];

        foreach ($missing as $item) {
            $term = $item['term'];

            if (array_key_exists($term, $this->related)) {
                $adjacent = $this->related[$term];

                $suggestions[] = [
                    'term'       => $term,
                    'type'       => $adjacent ? 'adjacent' : 'gap',
                    'message'    => $adjacent
                        ? "You have {$adjacent} — mention it as adjacent to {$term}"
                        : "Genuine gap — consider flagging as learning",
                ];
            } else {
                $suggestions[] = [
                    'term'    => $term,
                    'type'    => 'unlisted',
                    'message' => "Not in your profile — research if it appears often in PHP roles",
                ];
            }
        }

        return $suggestions;
    }
}