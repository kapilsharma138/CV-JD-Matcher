<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScoreCV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:score-c-v';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }

    // protected $signature = 'cv:score {--jd= : Path to job description file}
    //                                 {--cv= : Path to CV file (optional)}';

    // public function handle(KeywordExtractor $extractor, CVScorer $scorer, SuggestionEngine $suggester)
    // {
    //     $jdText = file_get_contents($this->option('jd'));
    //     $cvText = file_get_contents($this->option('cv') ?? base_path('data/kapil-cv.txt'));

    //     $jdKeywords = $extractor->extract($jdText);
    //     $cvKeywords = $extractor->extract($cvText);
    //     $result     = $scorer->score($jdKeywords, $cvKeywords);
    //     $suggestions= $suggester->suggest($result['missing']);

    //     // Pretty CLI output using Laravel's $this->table() and $this->info()
    // }
}
