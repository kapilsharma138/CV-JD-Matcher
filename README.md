# CV–JD Matcher

> A Laravel tool that scores how well your CV matches a job description — built and used during an active job search.

Paste a job description. It scans it against a master tech term list, finds every relevant keyword, checks how many appear in your CV, and returns a match score with a breakdown of what's missing and how to address each gap.

---

## How it works

```
Job Description (text)
        ↓
KeywordExtractor — scans against 200+ tech terms master list
        ↓
Found: [php, laravel, docker, kubernetes, kafka, linux...]
        ↓
CVScorer — compares JD keywords vs CV keywords (weighted)
        ↓
Score = matched weight / total possible weight × 100
        ↓
SuggestionEngine — for each missing keyword:
    → adjacent skill found → "You have Docker, mention it as adjacent to Kubernetes"
    → genuine gap         → "Flag as learning"
    → unlisted            → "Research if common in PHP roles"
        ↓
JSON response → rendered in Blade UI
```

---

## Results on a real JD

Tested against a Senior Backend Engineer JD (PHP, Laravel, Docker, Kubernetes, Linux, Kafka):

| Keyword | In CV | Weight | Points |
|---|---|---|---|
| PHP | ✅ | 3 | 3/3 |
| Laravel | ✅ | 3 | 3/3 |
| Docker | ✅ | 2 | 2/2 |
| AWS | ✅ | 2 | 2/2 |
| Kubernetes | ❌ | 1 | 0/1 |
| Linux | ❌ | 1 | 0/1 |
| Kafka | ❌ | 1 | 0/1 |
| **Score** | | **13 pts possible** | **10/13 = 76.9%** |

Missing terms: Kubernetes (adjacent: Docker) · Linux (adjacent: AWS EC2) · Kafka (genuine gap)

---

## What this demonstrates

**1. Service class architecture**
Four single-responsibility service classes — `KeywordExtractor`, `CVScorer`, `SuggestionEngine` each do exactly one job. The controller wires them together via Laravel's dependency injection container.

**2. Algorithm design**
Weighted keyword scoring — must-have terms (PHP, Laravel) score 3 points, important terms (AWS, Docker) score 2, nice-to-have terms score 1. Missing a must-have costs more than missing a nice-to-have. Designed and implemented without a library.

**3. Word boundary matching**
`preg_match('/\b' . preg_quote($term) . '\b/')` prevents false positives — "sql" won't match inside "mysql", "php" won't match inside "phpstorm". A real bug found during testing, fixed with a deliberate solution.

**4. Auto-extraction from JD**
The tool doesn't rely solely on a fixed dictionary. `extractFromJD()` scans the JD against a master tech term list (200+ terms) — works on any job description without predicting keywords in advance.

**5. PHPUnit test coverage**
12 unit tests, 28 assertions across two test classes. Core scoring logic is fully tested in isolation — not the framework, the algorithm.

**6. Laravel dependency injection**
Services are bound in `AppServiceProvider` and injected via constructor — not `new`'d inside methods. Clean, testable, production-grade pattern.

---

## Endpoints

```
GET  /           — web UI (paste JD, see results)
POST /score      — JSON API (returns score, matched, missing, suggestions)
```

### Sample API response

```json
{
    "score": 86.4,
    "matched": [
        { "term": "php",        "category": "extracted", "weight": 3 },
        { "term": "laravel",    "category": "extracted", "weight": 3 },
        { "term": "javascript", "category": "extracted", "weight": 3 },
        { "term": "docker",     "category": "extracted", "weight": 2 }
    ],
    "missing": [
        { "term": "linux",  "category": "extracted", "weight": 1 },
        { "term": "oracle", "category": "extracted", "weight": 1 }
    ],
    "suggestions": [
        {
            "term": "linux",
            "type": "adjacent",
            "message": "You have AWS EC2 (Linux instances) — mention it as adjacent to linux"
        },
        {
            "term": "oracle",
            "type": "unlisted",
            "message": "Not in your profile — research if it appears often in PHP roles"
        }
    ],
    "total": 10
}
```

---

## Architecture

```
cv-jd-matcher/
├── app/
│   ├── Console/Commands/          ← (planned: CLI interface)
│   ├── Http/Controllers/
│   │   └── ScoreController.php    ← receives request, wires services, returns JSON
│   ├── Providers/
│   │   └── AppServiceProvider.php ← binds SuggestionEngine with KeywordExtractor
│   └── Services/
│       ├── KeywordExtractor.php   ← scans text, finds tech terms
│       ├── CVScorer.php           ← weighted scoring algorithm
│       └── SuggestionEngine.php   ← gap analysis and adjacent skill suggestions
├── data/
│   ├── keywords.php               ← weighted keyword dictionary (your rulebook)
│   ├── tech-terms.php             ← master tech term list (200+ terms, auto-extract)
│   └── kapil-cv.txt               ← CV as plain text (pre-loaded in UI)
├── resources/views/scorer/
│   └── index.blade.php            ← web UI
└── tests/Unit/
    ├── KeywordExtractorTest.php   ← 6 tests
    └── CVScorerTest.php           ← 6 tests
```

---

## PHPUnit tests

```bash
php artisan test

# PASS  Tests\Unit\CVScorerTest
# ✓ it scores perfect match as 100
# ✓ it scores zero when nothing matches
# ✓ it returns missing keywords correctly
# ✓ it uses weighted scoring
# ✓ it handles empty jd keywords
# ✓ it returns correct matched count
#
# PASS  Tests\Unit\KeywordExtractorTest
# ✓ it extracts known keywords from text
# ✓ it is case insensitive
# ✓ it returns empty for no matches
# ✓ it does not match sql inside mysql
# ✓ it extracts from jd using master tech list
# ✓ it returns related terms
#
# Tests: 12 passed (28 assertions)
```

Tests cover: keyword extraction accuracy · case insensitivity · word boundary false positives · weighted score calculation · empty input handling · missing keyword detection

---

## Run locally

```bash
git clone https://github.com/kapilsharma138/cv-jd-matcher
cd cv-jd-matcher
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Open `http://localhost:8000` — paste any tech job description, click Score.

No database required. No API keys. No external services.

---

## Key design decisions

**Why weighted scoring over simple keyword count?**
Missing PHP in a PHP role is more damaging than missing Kafka. Weights reflect real-world importance — must-have skills penalise the score more than nice-to-have ones.

**Why rule-based instead of NLP/LLM?**
I evaluated adding an LLM API layer for contextual scoring. Chose the deterministic approach for cost (free, no API key), reliability (no rate limits, no latency), and transparency (same input always gives same output). The same tradeoff production systems make when budget and predictability matter.

**Why two extraction methods?**
`extract()` uses the weighted dictionary — good when you want weights to reflect importance. `extractFromJD()` uses the master tech list — good when you don't know what the JD will contain. Using the right method in the right place is a deliberate architectural choice.

---

## Next

- [ ] PHPUnit attribute syntax update (`#[Test]` instead of `/** @test */`)
- [ ] CLI interface — `php artisan cv:score --jd=job.txt`
- [ ] Frequency scoring — "PHP" appearing 5 times in JD scores higher than once
- [ ] Deploy to AWS EC2 — live public URL

---

*Part of a backend engineering portfolio.*
*Previous: [Query Benchmark API](https://github.com/kapilsharma138/Query-Benchmark-API) — 97.52% query improvement, Laravel + MySQL + Redis + AWS EC2*

*[Kapil Sharma](https://linkedin.com/in/kapil-sharma-7665a7b0) · [GitHub](https://github.com/kapilsharma138)*
