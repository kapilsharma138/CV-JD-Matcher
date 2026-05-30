<?php

namespace App\Services;

class CVScorer
{
    public function score(array $jdKeywords, array $cvKeywords): array
    {
        // Flatten CV keywords into a simple list of terms for easy lookup
        $cvTerms = $this->flatten($cvKeywords);

        $matched        = [];
        $missing        = [];
        $totalPossible  = 0;
        $totalMatched   = 0;

        // ADDED: empty JD guard
        if (empty($jdKeywords)) {
            return [
                'score'          => 0,
                'matched'        => [],
                'missing'        => [],
                'message'        => 'No recognisable tech keywords found in this JD.',
                'total_possible' => 0,
                'total_matched'  => 0,
            ];
        }

        foreach ($jdKeywords as $category => $terms) {
            foreach ($terms as $item) {
                $term   = $item['term'];
                $weight = $item['weight'];

                $totalPossible += $weight;

                if (in_array($term, $cvTerms)) {
                    $matched[]      = ['term' => $term, 'category' => $category, 'weight' => $weight];
                    $totalMatched  += $weight;
                } else {
                    $missing[]      = ['term' => $term, 'category' => $category, 'weight' => $weight];
                }
            }
        }

        $score = $totalPossible > 0
            ? round(($totalMatched / $totalPossible) * 100, 1)
            : 0;

        return [
            'score'          => $score,
            'matched'        => $matched,
            'missing'        => $missing,
            'total_possible' => $totalPossible,
            'total_matched'  => $totalMatched,
        ];
    }

    private function flatten(array $keywords): array
    {
        $terms = [];
        foreach ($keywords as $category => $items) {
            foreach ($items as $item) {
                $terms[] = $item['term'];
            }
        }
        return $terms;
    }
}