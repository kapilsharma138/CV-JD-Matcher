<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\KeywordExtractor;

class KeywordExtractorTest extends TestCase
{
    private KeywordExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = new KeywordExtractor();
    }

    /** @test */
    public function it_extracts_known_keywords_from_text(): void
    {
        $result = $this->extractor->extract("We need a PHP Laravel developer with MySQL experience");

        $terms = array_column($result['must_have'] ?? [], 'term');

        $this->assertContains('php', $terms);
        $this->assertContains('laravel', $terms);
        $this->assertContains('mysql', $terms);
    }

    /** @test */
    public function it_is_case_insensitive(): void
    {
        $lower = $this->extractor->extract("we need php laravel");
        $upper = $this->extractor->extract("WE NEED PHP LARAVEL");
        $mixed = $this->extractor->extract("We Need PHP Laravel");

        $lowerTerms = array_column($lower['must_have'] ?? [], 'term');
        $upperTerms = array_column($upper['must_have'] ?? [], 'term');
        $mixedTerms = array_column($mixed['must_have'] ?? [], 'term');

        $this->assertContains('php', $lowerTerms);
        $this->assertContains('php', $upperTerms);
        $this->assertContains('php', $mixedTerms);
    }

    /** @test */
    public function it_returns_empty_for_no_matches(): void
    {
        $result = $this->extractor->extract("We need a Java Spring developer with Hibernate");

        // Should find nothing in must_have or important for a pure Java JD
        $mustHave = $result['must_have'] ?? [];
        $terms    = array_column($mustHave, 'term');

        $this->assertNotContains('php', $terms);
        $this->assertNotContains('laravel', $terms);
    }

    /** @test */
    public function it_does_not_match_sql_inside_mysql(): void
    {
        // Before the preg_match fix, "sql" would match inside "mysql"
        // After the fix, "sql" should only match if the word "sql" appears standalone
        $result = $this->extractor->extract("We need a MySQL developer");

        $mustHave = $result['must_have'] ?? [];
        $terms    = array_column($mustHave, 'term');

        $this->assertContains('mysql', $terms);    // mysql should match
        $this->assertNotContains('sql', $terms);   // sql should NOT match inside mysql
    }

    /** @test */
    public function it_extracts_from_jd_using_master_tech_list(): void
    {
        $result = $this->extractor->extractFromJD("We need PHP Laravel Kubernetes Docker Kafka");

        $terms = array_column($result['extracted'], 'term');

        $this->assertContains('php', $terms);
        $this->assertContains('laravel', $terms);
        $this->assertContains('kubernetes', $terms);
        $this->assertContains('kafka', $terms);
    }

    /** @test */
    public function it_returns_related_terms(): void
    {
        $related = $this->extractor->getRelated();

        $this->assertIsArray($related);
        $this->assertArrayHasKey('nestjs', $related);
        $this->assertArrayHasKey('kubernetes', $related);
    }
}