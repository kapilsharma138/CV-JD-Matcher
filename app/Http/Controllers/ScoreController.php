<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KeywordExtractor;
use App\Services\CVScorer;
use App\Services\SuggestionEngine;

class ScoreController extends Controller
{
    // CHANGED: inject via constructor instead of new inside method
    public function __construct(
        private KeywordExtractor $extractor,
        private CVScorer $scorer,
        private SuggestionEngine $suggester,
    ) {}

    public function index()
    {
        return view('scorer.index');
    }

    public function score(Request $request)
    {
        $request->validate(['jd' => 'required|string|min:20']);

        // CHANGED: use extractFromFile helper instead of file_get_contents in controller
        // $jdKeywords = $this->extractor->extract($request->input('jd'));
        $jdKeywords = $this->extractor->extractFromJD($request->input('jd'));
        $cvKeywords = $this->extractor->extractFromFile(base_path('data/kapil-cv.txt'));

        $result      = $this->scorer->score($jdKeywords, $cvKeywords);
        $suggestions = $this->suggester->suggest($result['missing']);

        return response()->json([
            'score'       => $result['score'],
            'matched'     => $result['matched'],
            'missing'     => $result['missing'],
            'suggestions' => $suggestions,
            'total'       => count($result['matched']) + count($result['missing']),
        ]);
    }
}