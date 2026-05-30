<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CV–JD Matcher</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #F9FAFB;
            color: #111827;
            min-height: 100vh;
            padding: 32px 20px;
        }

        .container { max-width: 900px; margin: 0 auto; }

        .header { margin-bottom: 28px; }
        .header h1 { font-size: 22px; font-weight: 600; color: #111827; }
        .header p  { font-size: 14px; color: #6B7280; margin-top: 5px; }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        label {
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .05em;
            display: block;
            margin-bottom: 7px;
        }

        textarea {
            width: 100%;
            height: 180px;
            padding: 12px 14px;
            font-size: 13px;
            font-family: inherit;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            background: #fff;
            color: #111827;
            resize: vertical;
            line-height: 1.55;
            transition: border-color .15s;
        }
        textarea:focus { outline: none; border-color: #1B4FD8; }

        .score-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: #111827;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin-bottom: 24px;
            transition: opacity .15s;
        }
        .score-btn:hover   { opacity: .85; }
        .score-btn:disabled { opacity: .4; cursor: not-allowed; }

        /* results */
        #results { display: none; }
        #results.visible { display: block; }

        .metrics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .metric {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 14px 16px;
            text-align: center;
        }
        .metric-n { font-size: 24px; font-weight: 600; color: #111827; }
        .metric-l { font-size: 12px; color: #6B7280; margin-top: 3px; }

        .bar-wrap {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 16px;
        }
        .bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #6B7280;
            margin-bottom: 10px;
        }
        .bar-bg {
            height: 10px;
            background: #F3F4F6;
            border-radius: 5px;
            overflow: hidden;
        }
        .bar-fill {
            height: 10px;
            border-radius: 5px;
            background: #1D9E75;
            transition: width .7s ease;
        }

        .two-lists {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .list-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 16px 18px;
        }
        .list-title {
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 12px;
        }

        .pill {
            display: inline-block;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 20px;
            margin: 3px 3px 3px 0;
        }
        .pill-match { background: #F0FDF4; color: #166534; }
        .pill-miss  { background: #FEF2F2; color: #991B1B; }
        .pill-cat   {
            font-size: 10px;
            padding: 1px 5px;
            border-radius: 3px;
            background: rgba(0,0,0,.06);
            color: inherit;
            margin-left: 3px;
        }

        .sugg-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 16px;
        }
        .sugg-title {
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 12px;
        }
        .sugg-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 9px 0;
            border-bottom: 1px solid #F3F4F6;
            font-size: 13px;
        }
        .sugg-row:last-child { border-bottom: none; }
        .sugg-term { font-weight: 600; min-width: 100px; color: #111827; }
        .sugg-msg  { color: #6B7280; line-height: 1.5; flex: 1; }
        .badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
            white-space: nowrap;
            flex-shrink: 0;
            font-weight: 500;
        }
        .badge-adj { background: #FFFBEB; color: #92400E; }
        .badge-gap { background: #FEF2F2; color: #991B1B; }
        .badge-ok  { background: #F0FDF4; color: #166534; }

        .api-btn {
            width: 100%;
            padding: 10px;
            font-size: 13px;
            border-radius: 8px;
            border: 1px solid #E5E7EB;
            background: #F9FAFB;
            color: #374151;
            cursor: pointer;
            transition: background .15s;
        }
        .api-btn:hover { background: #F3F4F6; }

        .loading { color: #6B7280; font-size: 13px; padding: 8px 0; }
        .error   { color: #991B1B; font-size: 13px; padding: 8px 0; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>CV–JD Matcher</h1>
        <p>Paste a job description — score how well your CV matches it and see exactly what's missing.</p>
    </div>

    <div class="two-col">
        <div>
            <label>Job description</label>
            <textarea id="jd" placeholder="Paste the full job description here..."></textarea>
        </div>
        <div>
            <label>Your CV (pre-loaded — edit if needed)</label>
            <textarea id="cv-override">{{ file_get_contents(base_path('data/kapil-cv.txt')) }}</textarea>
        </div>
    </div>

    <button class="score-btn" id="score-btn" onclick="runScore()">
        &#9654; Score my CV match
    </button>

    <div id="status"></div>

    <div id="results">

        <div class="metrics">
            <div class="metric">
                <div class="metric-n" id="m-score">—</div>
                <div class="metric-l">match score</div>
            </div>
            <div class="metric">
                <div class="metric-n" id="m-matched">—</div>
                <div class="metric-l">keywords matched</div>
            </div>
            <div class="metric">
                <div class="metric-n" id="m-missing">—</div>
                <div class="metric-l">keywords missing</div>
            </div>
            <div class="metric">
                <div class="metric-n" id="m-total">—</div>
                <div class="metric-l">total in JD</div>
            </div>
        </div>

        <div class="bar-wrap">
            <div class="bar-label">
                <span>Match strength</span>
                <span id="bar-pct">0%</span>
            </div>
            <div class="bar-bg">
                <div class="bar-fill" id="bar-fill" style="width:0%"></div>
            </div>
        </div>

        <div class="two-lists">
            <div class="list-card">
                <div class="list-title">✓ Matched keywords</div>
                <div id="matched-list"></div>
            </div>
            <div class="list-card">
                <div class="list-title">✗ Missing keywords</div>
                <div id="missing-list"></div>
            </div>
        </div>

        <div class="sugg-card">
            <div class="sugg-title">Suggestions</div>
            <div id="sugg-list"></div>
        </div>

        <button class="api-btn" onclick="copyJson()">Copy raw JSON response</button>

    </div>
</div>

<script>
    const catLabel = { must_have: 'must', important: 'key', good_to_have: 'nice' };

    function barColor(pct) {
        if (pct >= 80) return '#1D9E75';
        if (pct >= 60) return '#BA7517';
        return '#E24B4A';
    }

    let lastJson = null;

    async function runScore() {
        const jd  = document.getElementById('jd').value.trim();
        if (!jd) { alert('Paste a job description first'); return; }

        const btn    = document.getElementById('score-btn');
        const status = document.getElementById('status');
        btn.disabled = true;
        status.innerHTML = '<p class="loading">Scoring...</p>';
        document.getElementById('results').classList.remove('visible');

        try {
            const res = await fetch('/score', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ jd })
            });

            const data = await res.json();
            lastJson   = data;
            status.innerHTML = '';
            renderResults(data);

        } catch (e) {
            status.innerHTML = '<p class="error">Error — check the console and make sure the server is running.</p>';
        } finally {
            btn.disabled = false;
        }
    }

    function renderResults(data) {
        // Metrics
        document.getElementById('m-score').textContent   = data.score + '%';
        document.getElementById('m-matched').textContent = data.matched.length;
        document.getElementById('m-missing').textContent = data.missing.length;
        document.getElementById('m-total').textContent   = data.total;

        // Bar
        document.getElementById('bar-pct').textContent     = data.score + '%';
        const fill = document.getElementById('bar-fill');
        fill.style.width      = data.score + '%';
        fill.style.background = barColor(data.score);

        // Matched pills
        const ml = document.getElementById('matched-list');
        ml.innerHTML = data.matched.length
            ? data.matched.map(m =>
                `<span class="pill pill-match">${m.term}<span class="pill-cat">${catLabel[m.category] || ''}</span></span>`
              ).join('')
            : '<span style="font-size:13px;color:#6B7280;">Nothing matched</span>';

        // Missing pills
        const misl = document.getElementById('missing-list');
        misl.innerHTML = data.missing.length
            ? data.missing.map(m =>
                `<span class="pill pill-miss">${m.term}<span class="pill-cat">w${m.weight}</span></span>`
              ).join('')
            : '<span style="font-size:13px;color:#166534;">Perfect match — nothing missing!</span>';

        // Suggestions
        const sl = document.getElementById('sugg-list');
        if (!data.suggestions.length) {
            sl.innerHTML = '<p style="font-size:13px;color:#166534;padding:8px 0;">No gaps — strong match.</p>';
        } else {
            sl.innerHTML = data.suggestions.map(s => `
                <div class="sugg-row">
                    <span class="sugg-term">${s.term}</span>
                    <span class="badge ${s.type === 'adjacent' ? 'badge-adj' : s.type === 'gap' ? 'badge-gap' : 'badge-ok'}">${s.type}</span>
                    <span class="sugg-msg">${s.message}</span>
                </div>`).join('');
        }

        document.getElementById('results').classList.add('visible');
    }

    function copyJson() {
        if (!lastJson) return;
        navigator.clipboard.writeText(JSON.stringify(lastJson, null, 2));
        alert('JSON copied to clipboard');
    }
</script>
</body>
</html>