<?php

namespace App\Services;

class CVScorer
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function score(array $jdKeywords, array $cvKeywords): array
    {
        // Your algorithm:
        // - For each keyword in JD: is it in CV?
        // - Yes → add its weight to matched score
        // - No  → add to missing list
        // - Score = matched_weight / total_possible_weight * 100

        // Returns:
        return [
            'score'   => 84,
            'matched' => ['php', 'laravel'],
            'missing' => ['nestjs', 'graphql'],
        ];
    }
}
