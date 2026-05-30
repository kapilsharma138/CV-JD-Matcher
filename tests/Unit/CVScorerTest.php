<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CVScorer;

class CVScorerTest extends TestCase
{
    private CVScorer $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scorer = new CVScorer();
    }

    /** @test */
    public function it_scores_perfect_match_as_100(): void
    {
        $jd = [
            'extracted' => [
                ['term' => 'php',    'weight' => 3],
                ['term' => 'laravel','weight' => 3],
            ]
        ];

        $cv = [
            'must_have' => [
                ['term' => 'php',    'weight' => 3],
                ['term' => 'laravel','weight' => 3],
            ]
        ];

        $result = $this->scorer->score($jd, $cv);

        $this->assertEquals(100.0, $result['score']);
        $this->assertEmpty($result['missing']);
    }

    /** @test */
    public function it_scores_zero_when_nothing_matches(): void
    {
        $jd = [
            'extracted' => [
                ['term' => 'java',  'weight' => 3],
                ['term' => 'spring','weight' => 3],
            ]
        ];

        $cv = [
            'must_have' => [
                ['term' => 'php',    'weight' => 3],
                ['term' => 'laravel','weight' => 3],
            ]
        ];

        $result = $this->scorer->score($jd, $cv);

        $this->assertEquals(0, $result['score']);
        $this->assertCount(2, $result['missing']);
    }

    /** @test */
    public function it_returns_missing_keywords_correctly(): void
    {
        $jd = [
            'extracted' => [
                ['term' => 'php',        'weight' => 3],
                ['term' => 'kubernetes', 'weight' => 1],
            ]
        ];

        $cv = [
            'must_have' => [
                ['term' => 'php', 'weight' => 3],
            ]
        ];

        $result = $this->scorer->score($jd, $cv);

        $missingTerms = array_column($result['missing'], 'term');

        $this->assertContains('kubernetes', $missingTerms);
        $this->assertNotContains('php', $missingTerms);
    }

    /** @test */
    public function it_uses_weighted_scoring(): void
    {
        // JD wants php(3) + kafka(1) = 4 points total
        // CV only has php → 3/4 = 75%
        $jd = [
            'extracted' => [
                ['term' => 'php',  'weight' => 3],
                ['term' => 'kafka','weight' => 1],
            ]
        ];

        $cv = [
            'must_have' => [
                ['term' => 'php', 'weight' => 3],
            ]
        ];

        $result = $this->scorer->score($jd, $cv);

        $this->assertEquals(75.0, $result['score']);
    }

    /** @test */
    public function it_handles_empty_jd_keywords(): void
    {
        $result = $this->scorer->score([], []);

        $this->assertEquals(0, $result['score']);
        $this->assertArrayHasKey('message', $result);
    }

    /** @test */
    public function it_returns_correct_matched_count(): void
    {
        $jd = [
            'extracted' => [
                ['term' => 'php',    'weight' => 3],
                ['term' => 'laravel','weight' => 3],
                ['term' => 'aws',    'weight' => 2],
            ]
        ];

        $cv = [
            'must_have' => [
                ['term' => 'php',    'weight' => 3],
                ['term' => 'laravel','weight' => 3],
            ],
            'important' => [
                ['term' => 'aws', 'weight' => 2],
            ]
        ];

        $result = $this->scorer->score($jd, $cv);

        $this->assertCount(3, $result['matched']);
        $this->assertEmpty($result['missing']);
    }
}