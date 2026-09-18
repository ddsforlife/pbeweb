<?php
// ─── Cache-bust ───────────────────────────────────────────────────────────────
$fileVersion = md5_file(__FILE__);
header('Cache-Control: no-cache, must-revalidate');
header('ETag: "' . $fileVersion . '"');
if (
    isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
    trim($_SERVER['HTTP_IF_NONE_MATCH']) === '"' . $fileVersion . '"'
) {
    header('HTTP/1.1 304 Not Modified');
    exit;
}
// ──────────────────────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cloze Cards - PBE Team Bold</title>

    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">

    <?php
    $version = substr(md5_file(__FILE__), 0, 8);
    echo "<meta name='app-version' content='$version'>";

    function getBooksData($baseDir = 'bible') {
        $books = [];
        if (!is_dir($baseDir)) return $books;
        $categories = array_filter(glob($baseDir . '/*'), 'is_dir');
        foreach ($categories as $categoryPath) {
            $files = glob($categoryPath . '/*.csv');
            foreach ($files as $file) {
                $bookName = ucfirst(str_replace('.csv', '', basename($file)));
                $chapters = getChaptersFromCSV($file);
                $books[$bookName] = [
                    'path' => $file,
                    'chapters' => $chapters,
                    'totalChapters' => count($chapters)
                ];
            }
        }
        return $books;
    }

    function getChaptersFromCSV($filePath) {
        if (!file_exists($filePath)) return [];
        $lines = file($filePath, FILE_SKIP_EMPTY_LINES);
        $chapters = [];
        $startIndex = (count($lines) > 0 && stripos($lines[0], 'book') !== false) ? 1 : 0;
        for ($i = $startIndex; $i < count($lines); $i++) {
            $parts = str_getcsv($lines[$i]);
            if (count($parts) >= 2) {
                $chapter = (int)$parts[1];
                if (!in_array($chapter, $chapters)) $chapters[] = $chapter;
            }
        }
        sort($chapters);
        return $chapters;
    }

    $booksData = getBooksData();
    echo "<script>const BOOKS_DATA = " . json_encode($booksData) . ";</script>";
    ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-primary: #FFFBF4;
            --bg-secondary: #FDF3E0;
            --bg-card: #FFFFFF;
            --text-primary: #1A214A;
            --text-secondary: #3A3F63;
            --text-muted: #7A7F9A;
            --accent: #D20702;
            --accent-hover: #B30602;
            --border: #E8D8B6;
            --shadow: rgba(26, 33, 74, 0.10);
            --shadow-hover: rgba(26, 33, 74, 0.16);
            --correct: #059669;
            --incorrect: #DC2626;
            --current-blank: #D20702;
            --completed-blank: #059669;
        }

        [data-theme="dark"] {
            --bg-primary: #000000;
            --bg-secondary: #0A0A0A;
            --bg-card: #151515;
            --text-primary: #FFFFFF;
            --text-secondary: #E5E5E5;
            --text-muted: #A0A0A0;
            --accent: #D20702;
            --accent-hover: #B30602;
            --border: #2A2A2A;
            --shadow: rgba(0, 0, 0, 0.60);
            --shadow-hover: rgba(0, 0, 0, 0.80);
            --correct: #10B981;
            --incorrect: #EF4444;
            --current-blank: #FFD700;
            --completed-blank: #10B981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Prevent iOS double-tap zoom on all interactive elements */
        button, input, select, .pill, .blank, .mode-option, .toggle, .toggle-group-btn {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        /* Prevent iOS auto-zoom on input focus — all inputs must be >= 16px */
        @media (max-width: 768px) {
            input, select, textarea { font-size: 16px !important; }
        }

        body {
            font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container { max-width: 900px; margin: 0 auto; padding: 10px 20px 20px; }

        /* Header */
        .header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--border);
        }
        .logo-section { display: flex; align-items: center; gap: 12px; }
        .logo { height: 65px; width: auto; }
        .title-section h1 { font-family: 'IBM Plex Sans', sans-serif; font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px; }
        .title-section p { font-size: 12px; color: var(--text-muted); }
        .header-controls { display: flex; gap: 12px; align-items: center; }

        @media (max-width: 768px) {
            .logo { height: 40px; }
            .logo-section { flex: 1; }
            .title-section { display: none; }
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem; border: none; border-radius: 12px;
            font-family: 'IBM Plex Sans', sans-serif; font-size: 0.95rem; font-weight: 600;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;
        }
        .btn-primary { background: var(--accent); color: white; box-shadow: 0 2px 8px var(--shadow); }
        .btn-primary:hover { background: var(--accent-hover); box-shadow: 0 4px 16px var(--shadow-hover); transform: translateY(-1px); }
        .btn-secondary { background: var(--bg-card); color: var(--text-primary); border: 2px solid var(--border); }
        .btn-secondary:hover { border-color: var(--accent); background: var(--bg-secondary); transform: translateY(-1px); }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 8px; }
        .btn-icon { width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: var(--bg-card); border: 2px solid var(--border); cursor: pointer; font-size: 1.2rem; transition: all 0.2s ease; }
        .btn-icon:hover { border-color: var(--accent); background: var(--bg-secondary); }

        /* ─── WIZARD ─── */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        #wizardScreen { animation: fadeIn 0.4s ease; }

        .wizard-progress {
            display: flex; gap: 0.5rem; margin-bottom: 2rem; align-items: center; justify-content: center;
        }
        .wizard-dot-wrap {
            position: relative; display: flex; flex-direction: column; align-items: center;
        }
        .wizard-dot {
            width: 12px; height: 12px; border-radius: 50%;
            background: var(--border); transition: all 0.3s ease;
        }
        .wizard-dot.active { background: var(--accent); transform: scale(1.3); }
        .wizard-dot.completed { background: var(--completed-blank); cursor: pointer; }
        .wizard-dot.completed:hover { transform: scale(1.4); filter: brightness(1.15); }
        .wizard-dot-label {
            position: absolute; top: 20px; white-space: nowrap;
            font-size: 0.7rem; font-weight: 600; color: var(--text-muted);
            opacity: 0; transition: opacity 0.2s ease; pointer-events: none;
        }
        .wizard-dot-wrap:hover .wizard-dot-label,
        .wizard-dot-wrap.active .wizard-dot-label { opacity: 1; }
        .wizard-dot.active + .wizard-dot-label { color: var(--accent); }
        .wizard-dot.completed + .wizard-dot-label { color: var(--completed-blank); }

        .wizard-line { width: 30px; height: 2px; background: var(--border); }
        .wizard-line.completed { background: var(--completed-blank); }

        @media (max-width: 768px) {
            .wizard-dot-label { display: none; }
            .wizard-progress { margin-bottom: 0.5rem; }
        }

        .wizard-step-counter {
            display: none; text-align: center; font-size: 0.8rem; font-weight: 600;
            color: var(--text-muted); margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .wizard-step-counter { display: block; }
        }

        .wizard-step { display: none; animation: fadeIn 0.3s ease; }
        .wizard-step.active { display: block; }

        .wizard-step-title {
            font-family: 'EB Garamond', serif; font-size: 1.75rem; font-weight: 700;
            color: var(--text-primary); margin-bottom: 0.5rem; text-align: center;
        }
        .wizard-step-desc {
            color: var(--text-muted); text-align: center; margin-bottom: 1.5rem; font-size: 0.95rem;
        }

        .wizard-nav {
            display: flex; gap: 1rem; margin-top: 2rem; justify-content: space-between;
        }
        .wizard-nav .btn { flex: 1; justify-content: center; }

        /* Toggle Group */
        .toggle-group {
            display: flex; border: 2px solid var(--border); border-radius: 12px;
            overflow: hidden; margin-bottom: 1.5rem;
        }
        .toggle-group-btn {
            flex: 1; padding: 1rem; text-align: center; font-weight: 600;
            cursor: pointer; transition: all 0.2s ease; background: var(--bg-card);
            color: var(--text-primary); border: none; font-size: 1rem;
            font-family: 'IBM Plex Sans', sans-serif;
        }
        .toggle-group-btn.active { background: var(--accent); color: white; }
        .toggle-group-btn:not(.active):hover { background: var(--bg-secondary); }

        /* Selection Pills */
        .pill-grid {
            display: grid; gap: 0.75rem; margin-bottom: 1rem;
        }
        .pill-grid.sections { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        .pill-grid.chapters { grid-template-columns: repeat(auto-fill, minmax(52px, 1fr)); }

        .pill {
            padding: 0.75rem; text-align: center; border: 2px solid var(--border);
            border-radius: 10px; background: var(--bg-card); cursor: pointer;
            font-weight: 600; font-size: 0.95rem; user-select: none;
            transition: all 0.2s ease;
        }
        .pill:hover { border-color: var(--accent); transform: translateY(-1px); }
        .pill.selected { background: var(--accent); color: white; border-color: var(--accent); }
        .pill-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; font-weight: 500; }
        .pill.selected .pill-label { color: rgba(255,255,255,0.8); }

        .quick-actions {
            display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;
        }
        .quick-actions .btn-sm { font-size: 0.8rem; padding: 0.4rem 0.8rem; }

        /* Book group in chapter view */
        .book-group { margin-bottom: 1.25rem; }
        .book-group-title {
            font-family: 'EB Garamond', serif; font-size: 1.1rem; font-weight: 700;
            color: var(--text-secondary); margin-bottom: 0.5rem;
            padding-bottom: 0.25rem; border-bottom: 1px solid var(--border);
        }

        /* Mode Selector */
        .mode-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.75rem; margin-bottom: 1.5rem;
        }
        .mode-option {
            padding: 0.75rem; border: 2px solid var(--border); border-radius: 10px;
            background: var(--bg-card); cursor: pointer; text-align: center;
            transition: all 0.2s ease; font-size: 0.9rem;
        }
        .mode-option:hover { border-color: var(--accent); transform: translateY(-1px); }
        .mode-option.active { border-color: var(--accent); background: var(--accent); color: white; }
        .mode-name { font-weight: 700; margin-bottom: 0.15rem; }
        .mode-desc { font-size: 0.75rem; color: var(--text-muted); }
        .mode-option.active .mode-desc { color: rgba(255,255,255,0.8); }

        /* Parameter inputs */
        .param-group { margin-bottom: 1rem; }
        .param-label { font-weight: 600; margin-bottom: 0.5rem; font-size: 0.95rem; }
        .param-desc { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; }
        .param-input {
            width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--border); border-radius: 10px;
            background: var(--bg-card); color: var(--text-primary); font-size: 16px;
            font-family: 'IBM Plex Sans', sans-serif; transition: border-color 0.2s ease;
        }
        .param-input:focus { outline: none; border-color: var(--accent); }
        .param-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        /* Filter toggles */
        .filter-section {
            background: var(--bg-secondary); border: 2px solid var(--border);
            border-radius: 12px; padding: 1.25rem; margin-top: 1.5rem;
        }
        .filter-title { font-weight: 700; margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .filter-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.75rem 0; border-bottom: 1px solid var(--border);
        }
        .filter-item:last-child { border-bottom: none; }
        .filter-item-label { font-weight: 500; }
        .filter-item-desc { font-size: 0.8rem; color: var(--text-muted); }

        /* Toggle switch */
        .toggle {
            position: relative; width: 48px; min-width: 48px; height: 26px;
            background: var(--border); border-radius: 13px; cursor: pointer;
            transition: background 0.3s ease; flex-shrink: 0;
        }
        .toggle.active { background: var(--accent); }
        .toggle-slider {
            position: absolute; top: 3px; left: 3px; width: 20px; height: 20px;
            background: white; border-radius: 50%;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .toggle.active .toggle-slider { transform: translateX(22px); }

        /* Options cards */
        .option-card {
            background: var(--bg-card); border: 2px solid var(--border);
            border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem;
        }
        .option-card-title { font-weight: 700; margin-bottom: 0.75rem; }

        /* Review summary */
        .review-grid { display: grid; gap: 1rem; margin-bottom: 1.5rem; }
        .review-item {
            background: var(--bg-secondary); border: 2px solid var(--border);
            border-radius: 12px; padding: 1rem 1.25rem;
        }
        .review-label { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.25rem; }
        .review-value { font-weight: 700; font-size: 1.05rem; }

        /* ─── CARD STUDY SCREEN ─── */
        #cardStudyScreen { display: none; animation: fadeIn 0.4s ease; }

        .card-toolbar {
            position: fixed; top: 0; left: 0; right: 0; height: 56px;
            background: var(--bg-card); border-bottom: 2px solid var(--border);
            display: flex; align-items: center; padding: 0 1rem; gap: 0.75rem;
            z-index: 900; box-shadow: 0 2px 8px var(--shadow);
        }
        .toolbar-btn {
            padding: 0.5rem 1rem; border: 2px solid var(--border); border-radius: 8px;
            background: var(--bg-secondary); color: var(--text-primary); font-weight: 600;
            font-size: 0.85rem; cursor: pointer; transition: all 0.2s ease;
            font-family: 'IBM Plex Sans', sans-serif;
            touch-action: manipulation; -webkit-tap-highlight-color: transparent;
        }
        .toolbar-btn:hover { background: var(--accent); border-color: var(--accent); color: white; }
        .toolbar-spacer { flex: 1; }
        .toolbar-progress { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; white-space: nowrap; }

        .card-body { padding-top: 72px; padding-bottom: 90px; }

        /* The Card */
        .verse-card {
            background: var(--bg-card); border: 2px solid var(--border);
            border-radius: 20px; padding: 2.5rem; margin: 0 auto;
            max-width: 800px; min-height: 280px;
            box-shadow: 0 4px 24px var(--shadow);
            display: flex; flex-direction: column; justify-content: center;
        }

        .card-reference {
            font-family: 'EB Garamond', serif; font-size: 1.3rem; font-weight: 700;
            color: var(--accent); margin-bottom: 1.25rem; text-align: center;
            transition: opacity 0.3s ease;
        }
        .card-reference.hidden { opacity: 0; pointer-events: none; }
        .card-reference.revealed { animation: fadeIn 0.4s ease; }

        .card-verse-text {
            font-family: 'EB Garamond', serif; font-size: 1.5rem; line-height: 2;
            color: var(--text-primary); letter-spacing: 0.01em; text-align: center;
        }

        .word { display: inline; }

        .blank {
            display: inline-block; position: relative; padding: 0; margin: 0 2px;
            border-bottom: 2px solid var(--border);
            font-family: 'IBM Plex Sans', monospace; font-weight: 600; font-size: 1.4rem;
            cursor: pointer; transition: border-color 0.2s ease;
            vertical-align: baseline; line-height: 1.0;
        }
        .blank-sizer {
            visibility: hidden; display: inline-block; padding: 0 9px;
            white-space: nowrap; pointer-events: none; user-select: none;
            font-family: 'IBM Plex Sans', monospace; font-weight: 600; line-height: 1.0;
        }
        .blank-input {
            position: absolute; left: 0; right: 0; top: 0; bottom: 0;
            display: flex; align-items: center; justify-content: center; text-align: center;
            font-family: 'IBM Plex Sans', monospace; font-size: 1.4rem; font-weight: 600;
            padding: 0; line-height: 0.95;
        }
        .blank.current { border-bottom-color: var(--current-blank); }
        .blank.completed { border-bottom-color: var(--completed-blank); color: var(--completed-blank); cursor: default; }
        .blank.incorrect { border-bottom-color: var(--incorrect); }

        [data-theme="dark"] .blank { border-bottom-color: #505050; }
        [data-theme="dark"] .blank.current { border-bottom-color: #FFD700; border-bottom-width: 3px; }
        [data-theme="dark"] .blank.completed { border-bottom-color: #10B981; }

        .letter { display: inline; }
        .letter.correct { color: var(--correct); }
        .letter.incorrect { color: var(--incorrect); }
        .letter.hint { color: var(--text-muted); opacity: 0.6; }

        /* Card navigation */
        .card-nav {
            position: fixed; bottom: 0; left: 0; right: 0; height: 70px;
            background: var(--bg-card); border-top: 2px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            gap: 1rem; padding: 0 1.5rem; z-index: 900;
            box-shadow: 0 -2px 8px var(--shadow);
        }
        .nav-btn {
            padding: 0.75rem 2rem; border: 2px solid var(--border); border-radius: 12px;
            background: var(--bg-card); color: var(--text-primary); font-weight: 700;
            font-size: 1.1rem; cursor: pointer; transition: all 0.2s ease;
            font-family: 'IBM Plex Sans', sans-serif;
            touch-action: manipulation; -webkit-tap-highlight-color: transparent;
        }
        .nav-btn:hover { border-color: var(--accent); background: var(--bg-secondary); transform: translateY(-1px); }
        .nav-btn.primary { background: var(--accent); color: white; border-color: var(--accent); }
        .nav-btn.primary:hover { background: var(--accent-hover); }
        .nav-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
        .card-counter {
            font-weight: 700; color: var(--text-muted); font-size: 1rem;
            min-width: 80px; text-align: center;
        }

        /* Progress bar */
        .progress-bar-container {
            background: var(--bg-secondary); border: 2px solid var(--border);
            border-radius: 12px; height: 10px; overflow: hidden;
            position: fixed; top: 56px; left: 0; right: 0; z-index: 800;
            border-radius: 0; border-left: none; border-right: none;
        }
        .progress-bar {
            height: 100%; background: linear-gradient(90deg, var(--accent), var(--accent-hover));
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 10px;
        }

        /* Stats Modal */
        .modal {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
            z-index: 2000; align-items: center; justify-content: center; padding: 2rem;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: var(--bg-card); border: 2px solid var(--border); border-radius: 24px;
            padding: 2.5rem; max-width: 550px; width: 100%; max-height: 90vh;
            overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .modal-header {
            font-family: 'EB Garamond', serif; font-size: 1.75rem; font-weight: 700;
            color: var(--text-primary); margin-bottom: 1.5rem; text-align: center;
        }
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card { background: var(--bg-secondary); border: 2px solid var(--border); border-radius: 16px; padding: 1.25rem; text-align: center; }
        .stat-value { font-family: 'EB Garamond', serif; font-size: 2.25rem; font-weight: 700; color: var(--accent); margin-bottom: 0.25rem; }
        .stat-label { color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }

        .missed-words { background: var(--bg-secondary); border: 2px solid var(--border); border-radius: 16px; padding: 1.25rem; margin-bottom: 1.5rem; }
        .missed-words-title { font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.85rem; }
        .missed-word-list { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .missed-word { background: var(--bg-card); border: 2px solid var(--incorrect); color: var(--incorrect); padding: 0.4rem 0.75rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }

        .action-buttons { display: flex; gap: 1rem; margin-top: 1.5rem; }
        .action-buttons .btn { flex: 1; justify-content: center; }

        /* Quick Start */
        .quick-start-toggle {
            text-align: center; margin-bottom: 1.5rem;
        }
        .quick-start-link {
            color: var(--accent); font-weight: 600; cursor: pointer;
            text-decoration: underline; font-size: 0.95rem;
        }

        .advanced-expand {
            background: var(--bg-secondary); border: 2px solid var(--border);
            border-radius: 12px; margin-top: 1rem; overflow: hidden;
        }
        .advanced-expand-header {
            padding: 1rem 1.25rem; cursor: pointer; display: flex;
            justify-content: space-between; align-items: center; font-weight: 600;
        }
        .advanced-expand-body { display: none; padding: 0 1.25rem 1.25rem; }
        .advanced-expand.open .advanced-expand-body { display: block; }
        .advanced-expand-arrow { transition: transform 0.3s ease; }
        .advanced-expand.open .advanced-expand-arrow { transform: rotate(180deg); }

        /* Reveal Action Bar */
        .reveal-action-bar {
            position: fixed; bottom: 70px; left: 0; right: 0;
            display: flex; justify-content: center; padding: 0.75rem 1.5rem;
            background: var(--bg-primary);
            z-index: 899;
        }
        .reveal-action-btn {
            width: 100%; max-width: 500px; padding: 1rem;
            border: 2px solid var(--accent); border-radius: 14px;
            background: var(--accent); color: white;
            font-family: 'IBM Plex Sans', sans-serif; font-size: 1.15rem; font-weight: 700;
            cursor: pointer; transition: all 0.2s ease;
            box-shadow: 0 2px 12px var(--shadow);
            touch-action: manipulation; -webkit-tap-highlight-color: transparent;
        }
        .reveal-action-btn:hover { background: var(--accent-hover); transform: translateY(-1px); }
        .reveal-action-btn:active { transform: scale(0.98); }

        /* Font Size Group */
        .font-size-group { display: flex; gap: 2px; align-items: center; }
        .font-btn { padding: 0.4rem 0.5rem !important; min-width: 28px; text-align: center; }
        .font-btn[data-size="sm"] { font-size: 0.65rem !important; }
        .font-btn[data-size="md"] { font-size: 0.8rem !important; }
        .font-btn[data-size="ml"] { font-size: 0.95rem !important; }
        .font-btn[data-size="lg"] { font-size: 1.1rem !important; }
        .font-btn.active { background: var(--accent) !important; color: white !important; border-color: var(--accent) !important; }

        /* Card text sizes */
        [data-text-size="sm"] .card-verse-text { font-size: 1.15rem; }
        [data-text-size="sm"] .blank { font-size: 1.05rem; }
        [data-text-size="sm"] .blank-input { font-size: 1.05rem; }
        [data-text-size="sm"] .card-reference { font-size: 1.05rem; }

        /* md = default, uses base CSS */

        [data-text-size="ml"] .card-verse-text { font-size: 1.7rem; }
        [data-text-size="ml"] .blank { font-size: 1.6rem; }
        [data-text-size="ml"] .blank-input { font-size: 1.6rem; }
        [data-text-size="ml"] .card-reference { font-size: 1.4rem; }

        [data-text-size="lg"] .card-verse-text { font-size: 1.9rem; }
        [data-text-size="lg"] .blank { font-size: 1.8rem; }
        [data-text-size="lg"] .blank-input { font-size: 1.8rem; }
        [data-text-size="lg"] .card-reference { font-size: 1.6rem; }

        /* Hidden input for mobile */
        #mobileInput { position: absolute; left: -9999px; opacity: 0; font-size: 16px; }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .header { flex-direction: column; gap: 1rem; }
            .verse-card { padding: 1.5rem; min-height: 200px; }
            .card-verse-text { font-size: 1.2rem; }
            .blank { font-size: 1.1rem; }
            .blank-input { font-size: 1.1rem; }
            .card-toolbar { height: 48px; padding: 0 0.5rem; gap: 0.4rem; }
            .toolbar-btn { padding: 0.3rem 0.55rem; font-size: 0.75rem; border-radius: 6px; }
            .card-body { padding-top: 62px; }
            .progress-bar-container { top: 48px; }
            .card-nav { height: 60px; gap: 0.5rem; padding: 0 0.75rem; }
            .nav-btn { padding: 0.6rem 1.25rem; font-size: 0.95rem; }
            .reveal-action-bar { bottom: 60px; padding: 0.5rem 0.75rem; }
            .reveal-action-btn { font-size: 1rem; padding: 0.75rem; }
            .font-btn { padding: 0.2rem 0.35rem !important; min-width: 22px; }
            .font-btn[data-size="sm"] { font-size: 0.55rem !important; }
            .font-btn[data-size="md"] { font-size: 0.65rem !important; }
            .font-btn[data-size="ml"] { font-size: 0.8rem !important; }
            .font-btn[data-size="lg"] { font-size: 0.95rem !important; }

            /* Mobile card text: scale everything down to prevent scroll */
            .verse-card { padding: 1.25rem; min-height: auto; border-radius: 14px; }
            .card-verse-text { font-size: 1.15rem; line-height: 1.8; }
            .blank { font-size: 1.05rem; }
            .blank-input { font-size: 1.05rem; }
            .card-reference { font-size: 1.05rem; margin-bottom: 0.75rem; }
            .card-body { padding-top: 62px; padding-bottom: 75px; }

            /* Mobile text size overrides */
            [data-text-size="sm"] .card-verse-text { font-size: 0.95rem; }
            [data-text-size="sm"] .blank, [data-text-size="sm"] .blank-input { font-size: 0.85rem; }
            [data-text-size="sm"] .card-reference { font-size: 0.85rem; }

            [data-text-size="ml"] .card-verse-text { font-size: 1.3rem; }
            [data-text-size="ml"] .blank, [data-text-size="ml"] .blank-input { font-size: 1.2rem; }
            [data-text-size="ml"] .card-reference { font-size: 1.15rem; }

            [data-text-size="lg"] .card-verse-text { font-size: 1.5rem; }
            [data-text-size="lg"] .blank, [data-text-size="lg"] .blank-input { font-size: 1.4rem; }
            [data-text-size="lg"] .card-reference { font-size: 1.3rem; }
            .pill-grid.sections { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
            .mode-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
            .modal-content { padding: 1.75rem; }
            .stats-grid { grid-template-columns: 1fr; }
            .param-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header" id="mainHeader">
            <div class="logo-section">
                <a href="/"><img src="pbe_team_bold_logo.png" alt="PBE Team Bold Logo" class="logo"></a>
                <div class="title-section">
                    <h1><span id="titleResetWizard" style="cursor:pointer;">Cloze Cards</span></h1>
                    <p>Verse-by-verse fill-in-the-blank</p>
                </div>
            </div>
            <div class="header-controls">
                <button class="btn-icon" id="themeToggleHeader" title="Toggle Light/Dark">☀️</button>
            </div>
        </header>

        <!-- ═══════ WIZARD SCREEN ═══════ -->
        <div id="wizardScreen">
            <!-- Quick Start Toggle -->
            <div class="quick-start-toggle">
                <span class="quick-start-link" id="quickStartToggle">Switch to Quick Start</span>
            </div>

            <!-- Progress Dots -->
            <div class="wizard-progress" id="wizardProgress"></div>
            <div class="wizard-step-counter" id="wizardStepCounter"></div>

            <!-- STEP 1: Study Mode + Selection -->
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-title">Select What to Study</div>
                <div class="wizard-step-desc">Choose your study plan, then pick sections or chapters</div>

                <!-- Plan Selector -->
                <div class="param-group" style="margin-bottom:1.25rem;">
                    <select class="param-input" id="planSelector" style="font-weight:700;font-size:1.05rem;text-align:center;"></select>
                </div>

                <div class="toggle-group" id="studyModeToggle">
                    <button class="toggle-group-btn active" data-mode="section">By Section</button>
                    <button class="toggle-group-btn" data-mode="chapter">By Chapter</button>
                </div>

                <!-- Section Selection -->
                <div id="sectionSelection">
                    <div class="quick-actions">
                        <button class="btn btn-sm btn-secondary" onclick="selectAllPills('section')">Select All</button>
                        <button class="btn btn-sm btn-secondary" onclick="selectNonePills('section')">Clear</button>
                    </div>
                    <div class="pill-grid sections" id="sectionPills"></div>
                </div>

                <!-- Chapter Selection -->
                <div id="chapterSelection" style="display:none;">
                    <div class="quick-actions" id="chapterQuickActions"></div>
                    <div id="chapterPillsContainer"></div>
                </div>

                <div class="wizard-nav">
                    <span></span>
                    <button class="btn btn-primary" onclick="wizardNext()">Next →</button>
                </div>
            </div>

            <!-- STEP 2: Verse Selection -->
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-title">Pick Verses</div>
                <div class="wizard-step-desc">Which verses from each chapter?</div>

                <div class="mode-grid" id="verseModePicker"></div>

                <div id="verseParams"></div>

                <div class="filter-section">
                    <div class="filter-title">Verse Window</div>
                    <div class="param-desc">Limit to a range of verse numbers within each chapter</div>
                    <div class="param-row" style="margin-top:0.75rem;">
                        <div class="param-group">
                            <div class="param-label">Start at verse</div>
                            <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="verseWindowStart" value="1" min="1">
                        </div>
                        <div class="param-group">
                            <div class="param-label">End at verse</div>
                            <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="verseWindowEnd" value="999" min="1">
                        </div>
                    </div>
                </div>

                <div class="wizard-nav">
                    <button class="btn btn-secondary" onclick="wizardPrev()">← Back</button>
                    <button class="btn btn-primary" onclick="wizardNext()">Next →</button>
                </div>
            </div>

            <!-- STEP 3: Word Blanking -->
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-title">Blank Words</div>
                <div class="wizard-step-desc">How should words be blanked on each card?</div>

                <div class="mode-grid" id="wordModePicker"></div>

                <div id="wordParams"></div>

                <div class="filter-section">
                    <div class="filter-title">Word Filters</div>
                    <div class="filter-item">
                        <div>
                            <div class="filter-item-label">Min word length</div>
                            <div class="filter-item-desc">Skip words shorter than this</div>
                        </div>
                        <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="filterMinLen" value="0" min="0" style="width:70px;">
                    </div>
                    <div class="filter-item">
                        <div>
                            <div class="filter-item-label">Capitalized only</div>
                            <div class="filter-item-desc">Only blank words starting with uppercase</div>
                        </div>
                        <div class="toggle" id="filterCapsToggle"><div class="toggle-slider"></div></div>
                    </div>
                    <div class="filter-item" style="flex-direction:column;align-items:stretch;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                            <div>
                                <div class="filter-item-label">Exclude words</div>
                                <div class="filter-item-desc">Words that will never be blanked</div>
                            </div>
                            <select class="param-input" id="excludePreset" style="width:160px;">
                                <option value="none">None</option>
                                <option value="basic" selected>Basic filler</option>
                                <option value="extended">Extended filler</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <input type="text" class="param-input" id="filterExclude" value="the, a, an, and, or, but, of, to, in, for, is, it" placeholder="Comma-separated words...">
                    </div>
                    <div class="filter-item">
                        <div>
                            <div class="filter-item-label">Always include words</div>
                            <div class="filter-item-desc">Always blank these regardless of mode</div>
                        </div>
                        <input type="text" class="param-input" id="filterInclude" value="" style="width:200px;" placeholder="Lord, God...">
                    </div>
                </div>

                <div class="wizard-nav">
                    <button class="btn btn-secondary" onclick="wizardPrev()">← Back</button>
                    <button class="btn btn-primary" onclick="wizardNext()">Next →</button>
                </div>
            </div>

            <!-- STEP 4: Card Options -->
            <div class="wizard-step" data-step="4">
                <div class="wizard-step-title">Card Options</div>
                <div class="wizard-step-desc">Customize your study experience</div>

                <div class="option-card">
                    <div class="option-card-title">Answer Mode</div>
                    <div class="toggle-group" id="answerModeToggle" style="flex-wrap:wrap;">
                        <button class="toggle-group-btn" data-mode="type">⌨️ Type</button>
                        <button class="toggle-group-btn active" data-mode="both">⌨️👆 Type & Tap</button>
                        <button class="toggle-group-btn" data-mode="reveal">👆 Tap to Reveal</button>
                    </div>
                    <div class="param-desc" style="margin-top:0.5rem;text-align:center;">
                        <strong>Type:</strong> Type each word &nbsp;|&nbsp;
                        <strong>Type & Tap:</strong> Type or tap to reveal &nbsp;|&nbsp;
                        <strong>Reveal:</strong> Tap only
                    </div>
                </div>

                <div class="option-card">
                    <div class="option-card-title">Verse Reference</div>
                    <div class="mode-grid" id="refModePicker" style="margin-bottom:0;">
                        <div class="mode-option active" data-refmode="show"><div class="mode-name">Always Show</div></div>
                        <div class="mode-option" data-refmode="hide"><div class="mode-name">Always Hide</div></div>
                        <div class="mode-option" data-refmode="auto"><div class="mode-name">Reveal on Complete</div></div>
                    </div>
                </div>

                <div class="option-card">
                    <div class="option-card-title">Shuffle & Ordering</div>
                    <div class="filter-item" style="border-bottom:none;padding:0;">
                        <div>
                            <div class="filter-item-label">Shuffle cards randomly</div>
                        </div>
                        <div class="toggle active" id="shuffleToggle"><div class="toggle-slider"></div></div>
                    </div>
                    <div class="filter-item" id="noAdjChapterRow" style="display:none;padding-top:0.75rem;">
                        <div>
                            <div class="filter-item-label">No adjacent same chapter</div>
                            <div class="filter-item-desc">Keep cards from same chapter apart</div>
                        </div>
                        <div class="toggle" id="noAdjChapterToggle"><div class="toggle-slider"></div></div>
                    </div>
                    <div class="filter-item" id="noAdjSectionRow" style="display:none;padding-top:0.75rem;">
                        <div>
                            <div class="filter-item-label">No adjacent same section</div>
                            <div class="filter-item-desc">Keep cards from same section apart</div>
                        </div>
                        <div class="toggle" id="noAdjSectionToggle"><div class="toggle-slider"></div></div>
                    </div>
                </div>

                <div class="wizard-nav">
                    <button class="btn btn-secondary" onclick="wizardPrev()">← Back</button>
                    <button class="btn btn-primary" onclick="wizardNext()">Review →</button>
                </div>
            </div>

            <!-- STEP 5: Review & Start -->
            <div class="wizard-step" data-step="5">
                <div class="wizard-step-title">Ready to Study!</div>
                <div class="wizard-step-desc">Review your settings</div>

                <div class="review-grid" id="reviewGrid"></div>

                <div class="wizard-nav">
                    <button class="btn btn-secondary" onclick="wizardPrev()">← Back</button>
                    <button class="btn btn-primary" onclick="startStudy()" style="font-size:1.1rem;padding:1rem 2rem;">🚀 Begin Study</button>
                </div>
            </div>

            <!-- ═══════ QUICK START (hidden by default) ═══════ -->
            <div id="quickStartScreen" style="display:none;">
                <div class="wizard-step-title">Quick Start</div>
                <div class="wizard-step-desc">Select chapters and go — smart defaults handle the rest</div>

                <!-- Plan Selector -->
                <div class="param-group" style="margin-bottom:1.25rem;">
                    <select class="param-input" id="qsPlanSelector" style="font-weight:700;font-size:1.05rem;text-align:center;"></select>
                </div>

                <div class="toggle-group" id="qsStudyModeToggle">
                    <button class="toggle-group-btn active" data-mode="section">By Section</button>
                    <button class="toggle-group-btn" data-mode="chapter">By Chapter</button>
                </div>

                <div id="qsSectionSelection">
                    <div class="quick-actions">
                        <button class="btn btn-sm btn-secondary" onclick="selectAllPills('qs-section')">All</button>
                        <button class="btn btn-sm btn-secondary" onclick="selectNonePills('qs-section')">None</button>
                    </div>
                    <div class="pill-grid sections" id="qsSectionPills"></div>
                </div>
                <div id="qsChapterSelection" style="display:none;">
                    <div class="quick-actions" id="qsChapterQuickActions"></div>
                    <div id="qsChapterPillsContainer"></div>
                </div>

                <div class="advanced-expand" id="qsAdvanced">
                    <div class="advanced-expand-header" onclick="toggleAdvanced()">
                        <span>Advanced Options</span>
                        <span class="advanced-expand-arrow">▼</span>
                    </div>
                    <div class="advanced-expand-body">
                        <div class="param-group">
                            <div class="param-label">Verse selection</div>
                            <select class="param-input" id="qsVerseMode">
                                <option value="all">All verses (default)</option>
                                <option value="first">First X verses</option>
                                <option value="last">Last X verses</option>
                                <option value="random">Random X verses</option>
                                <option value="first_pct">First X% of verses</option>
                                <option value="last_pct">Last X% of verses</option>
                                <option value="rand_pct">Random X% of verses</option>
                            </select>
                        </div>
                        <div class="param-group" id="qsVerseParamGroup" style="display:none;">
                            <div class="param-label">Verse amount</div>
                            <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="qsVerseParam" value="5" min="1">
                            <div class="param-desc" id="qsVerseParamDesc">Number of verses or %, depending on mode</div>
                        </div>

                        <hr style="border:none;border-top:1px solid var(--border);margin:1rem 0;">

                        <div class="param-group">
                            <div class="param-label">Word blanking</div>
                            <select class="param-input" id="qsWordMode">
                                <option value="random_pct">Random % of words (default)</option>
                                <option value="first">First X words</option>
                                <option value="last">Last X words</option>
                                <option value="random">Random X words</option>
                                <option value="all">All words</option>
                            </select>
                        </div>
                        <div class="param-group">
                            <div class="param-label">Word amount</div>
                            <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="qsWordParam" value="40" min="1">
                            <div class="param-desc">% of words or count, depending on mode</div>
                        </div>

                        <hr style="border:none;border-top:1px solid var(--border);margin:1rem 0;">

                        <div class="filter-item" style="border-bottom:none;">
                            <div class="filter-item-label">Answer Mode</div>
                            <div class="toggle-group" style="width:100%;margin-bottom:0;" id="qsAnswerToggle">
                                <button class="toggle-group-btn" data-mode="type" style="padding:0.5rem;">Type</button>
                                <button class="toggle-group-btn active" data-mode="both" style="padding:0.5rem;">Type & Tap</button>
                                <button class="toggle-group-btn" data-mode="reveal" style="padding:0.5rem;">Reveal</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top:1.5rem;display:flex;gap:1rem;">
                    <button class="btn btn-secondary" id="qsBackToWizard" style="flex:1;justify-content:center;">← Wizard Mode</button>
                    <button class="btn btn-primary" id="qsStartBtn" style="flex:2;justify-content:center;font-size:1.1rem;">🚀 Start Studying</button>
                </div>
            </div>
        </div>

        <!-- ═══════ CARD STUDY SCREEN ═══════ -->
        <div id="cardStudyScreen">
            <!-- Toolbar -->
            <div class="card-toolbar" id="cardToolbar">
                <button class="toolbar-btn" id="exitStudy">← Exit</button>
                <button class="toolbar-btn" id="hintBtn">💡</button>
                <span class="toolbar-spacer"></span>
                <span class="toolbar-progress" id="toolbarProgress"></span>
                <span class="font-size-group" id="fontSizeGroup">
                    <button class="toolbar-btn font-btn" data-size="sm" title="Small">A</button>
                    <button class="toolbar-btn font-btn active" data-size="md" title="Normal">A</button>
                    <button class="toolbar-btn font-btn" data-size="ml" title="Medium-Large">A</button>
                    <button class="toolbar-btn font-btn" data-size="lg" title="Large">A</button>
                </span>
                <button class="toolbar-btn" id="themeToggleStudy">☀️</button>
            </div>

            <div class="progress-bar-container"><div class="progress-bar" id="progressBar"></div></div>

            <div class="card-body">
                <div class="verse-card" id="verseCard">
                    <div class="card-reference" id="cardReference"></div>
                    <div class="card-verse-text" id="cardVerseText"></div>
                </div>
            </div>

            <!-- Reveal Action Bar (above nav, shown in reveal/both modes) -->
            <div class="reveal-action-bar" id="revealActionBar">
                <button class="reveal-action-btn" id="revealBtnAction">👁️ Reveal Word</button>
            </div>

            <!-- Navigation -->
            <div class="card-nav" id="cardNav">
                <button class="nav-btn" id="prevCardBtn" disabled>← Prev</button>
                <span class="card-counter" id="cardCounter">1 / 1</span>
                <button class="nav-btn primary" id="nextCardBtn">Next →</button>
            </div>

            <!-- Hidden mobile input -->
            <input type="text" id="mobileInput" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
        </div>

        <!-- Stats Modal -->
        <div class="modal" id="statsModal">
            <div class="modal-content">
                <h2 class="modal-header" id="statsTitle">📊 Session Complete!</h2>
                <div id="statsBody"></div>
                <div class="action-buttons" style="flex-wrap:wrap;">
                    <button class="btn btn-secondary" onclick="backToWizard()" style="flex:1;justify-content:center;">← New Session</button>
                    <button class="btn btn-primary" onclick="restartSession()" style="flex:1;justify-content:center;">🔄 Same Settings</button>
                </div>
                <button class="btn btn-secondary" onclick="startNewWizard()" style="width:100%;margin-top:0.75rem;justify-content:center;">🧭 Start Wizard from Beginning</button>
            </div>
        </div>
    </div>

    <script>
    // ══════════════════════════════════════════════════════════════════
    //  STUDY PLANS — multi-book, multi-section study configurations
    //  To add a new plan: add an entry to STUDY_PLANS below.
    //  Each section's `entries` array can span multiple books.
    // ══════════════════════════════════════════════════════════════════
    const STUDY_PLANS = {
        'isaiah-1-33': {
            name: 'Isaiah 1–33',
            sections: [
                { id: 1, name: 'Section 1', label: 'Ch 1–5',   entries: [{ book: 'Isaiah', chapters: [1,2,3,4,5] }] },
                { id: 2, name: 'Section 2', label: 'Ch 6–10',  entries: [{ book: 'Isaiah', chapters: [6,7,8,9,10] }] },
                { id: 3, name: 'Section 3', label: 'Ch 11–16', entries: [{ book: 'Isaiah', chapters: [11,12,13,14,15,16] }] },
                { id: 4, name: 'Section 4', label: 'Ch 17–22', entries: [{ book: 'Isaiah', chapters: [17,18,19,20,21,22] }] },
                { id: 5, name: 'Section 5', label: 'Ch 23–28', entries: [{ book: 'Isaiah', chapters: [23,24,25,26,27,28] }] },
                { id: 6, name: 'Section 6', label: 'Ch 29–33', entries: [{ book: 'Isaiah', chapters: [29,30,31,32,33] }] },
            ]
        },
        'mark-letters': {
            name: 'Mark & Letters',
            sections: [
                // ── Fill in your exact section breakdown here ──
                // Each section can span multiple books. Example:
                // { id: 1, name: 'Section 1', label: 'Mark 1–4, 1 Pet 1', entries: [
                //     { book: 'Mark', chapters: [1,2,3,4] },
                //     { book: '1 Peter', chapters: [1] },
                // ]},
                // { id: 2, name: 'Section 2', label: 'Mark 5–8, 1 Pet 2', entries: [
                //     { book: 'Mark', chapters: [5,6,7,8] },
                //     { book: '1 Peter', chapters: [2] },
                // ]},

                // Placeholder — one section per book until you fill in the real breakdown:
                { id: 1, name: 'Section 1', label: 'Mark 1–4, 1 Pet 1', entries: [
                    { book: 'Mark', chapters: [1,2,3,4] },
                    { book: '1 Peter', chapters: [1] },
                ]},
                { id: 2, name: 'Section 2', label: 'Mark 5–8, 1 Pet 2–3', entries: [
                    { book: 'Mark', chapters: [5,6,7,8] },
                    { book: '1 Peter', chapters: [2,3] },
                ]},
                { id: 3, name: 'Section 3', label: 'Mark 9–12, 1 Pet 4–5', entries: [
                    { book: 'Mark', chapters: [9,10,11,12] },
                    { book: '1 Peter', chapters: [4,5] },
                ]},
                { id: 4, name: 'Section 4', label: 'Mark 13–16, 2 Pet 1', entries: [
                    { book: 'Mark', chapters: [13,14,15,16] },
                    { book: '2 Peter', chapters: [1] },
                ]},
                { id: 5, name: 'Section 5', label: '2 Pet 2–3, 1 John 1–3', entries: [
                    { book: '2 Peter', chapters: [2,3] },
                    { book: '1 John', chapters: [1,2,3] },
                ]},
                { id: 6, name: 'Section 6', label: '1 John 4–5, 2 John, 3 John', entries: [
                    { book: '1 John', chapters: [4,5] },
                    { book: '2 John', chapters: [1] },
                    { book: '3 John', chapters: [1] },
                ]},
            ]
        },
        // ── Add more plans here ──
        // 'next-study': { name: 'Next Study', sections: [...] }
    };

    const DEFAULT_PLAN = 'isaiah-1-33';

    // Derived helpers — these now read from the active plan
    function getActivePlan() { return STUDY_PLANS[state.activePlan] || STUDY_PLANS[DEFAULT_PLAN]; }
    function getActiveSections() { return getActivePlan().sections; }

    // Get all {book, chapter} tuples in the active plan
    function getAllPlanChapters() {
        const tuples = [];
        for (const sec of getActiveSections()) {
            for (const entry of sec.entries) {
                for (const ch of entry.chapters) {
                    const key = `${entry.book}|${ch}`;
                    if (!tuples.find(t => t.key === key)) {
                        tuples.push({ book: entry.book, chapter: ch, key });
                    }
                }
            }
        }
        return tuples;
    }

    // Get unique books in the active plan (preserves order)
    function getPlanBooks() {
        const books = [];
        for (const sec of getActiveSections()) {
            for (const entry of sec.entries) {
                if (!books.includes(entry.book)) books.push(entry.book);
            }
        }
        return books;
    }

    // ══════════════════════════════════════════════════════════════════
    //  VERSE SELECTION MODES
    // ══════════════════════════════════════════════════════════════════
    const VERSE_MODES = [
        { id: 'all',        name: 'All',          desc: 'Every verse',       params: [] },
        { id: 'first',      name: 'First X',      desc: 'First X verses',   params: ['x'] },
        { id: 'last',       name: 'Last X',       desc: 'Last X verses',    params: ['x'] },
        { id: 'middle',     name: 'Middle X',     desc: 'Middle X verses',  params: ['x'] },
        { id: 'random',     name: 'Random X',     desc: 'Random X verses',  params: ['x'] },
        { id: 'odd',        name: 'Odd',          desc: 'Odd-numbered',     params: [] },
        { id: 'even',       name: 'Even',         desc: 'Even-numbered',    params: [] },
        { id: 'every_n',    name: 'Every Nth',    desc: 'Every Nth verse',  params: ['n'] },
        { id: 'range',      name: 'Range',        desc: 'Verse range',      params: ['rangeStart','rangeEnd'] },
        { id: 'first_pct',  name: 'First %',      desc: 'First X%',        params: ['pct'] },
        { id: 'last_pct',   name: 'Last %',       desc: 'Last X%',         params: ['pct'] },
        { id: 'mid_pct',    name: 'Middle %',     desc: 'Middle X%',       params: ['pct'] },
        { id: 'rand_pct',   name: 'Random %',     desc: 'Random X%',       params: ['pct'] },
    ];

    // ══════════════════════════════════════════════════════════════════
    //  WORD BLANKING MODES
    // ══════════════════════════════════════════════════════════════════
    const WORD_MODES = [
        { id: 'random_pct', name: 'Random %',     desc: 'Random X%',         params: ['pct'] },
        { id: 'first_pct',  name: 'First %',      desc: 'First X%',          params: ['pct'] },
        { id: 'last_pct',   name: 'Last %',       desc: 'Last X%',           params: ['pct'] },
        { id: 'mid_pct',    name: 'Middle %',     desc: 'Middle X%',         params: ['pct'] },
        { id: 'all',        name: 'All',           desc: 'Every word',        params: [] },
        { id: 'first',      name: 'First X',      desc: 'First X words',     params: ['x'] },
        { id: 'last',       name: 'Last X',       desc: 'Last X words',      params: ['x'] },
        { id: 'middle',     name: 'Middle X',     desc: 'Middle X words',    params: ['x'] },
        { id: 'random',     name: 'Random X',     desc: 'Random X words',    params: ['x'] },
        { id: 'every_n',    name: 'Every Nth',    desc: 'Every Nth word',    params: ['n'] },
        { id: 'odd',        name: 'Odd',           desc: 'Odd words (1,3,5)', params: [] },
        { id: 'even',       name: 'Even',          desc: 'Even words (2,4,6)', params: [] },
        { id: 'custom',     name: 'Custom',        desc: 'Pick positions',     params: ['custom'] },
    ];

    // ══════════════════════════════════════════════════════════════════
    //  STATE
    // ══════════════════════════════════════════════════════════════════
    const state = {
        // Wizard
        wizardStep: 1,
        activePlan: DEFAULT_PLAN,
        studyMode: 'section', // 'section' or 'chapter'
        selectedSections: new Set(),
        selectedChapters: new Set(), // Set of "Book|Chapter" strings

        // Verse selection
        verseMode: 'all',
        verseParams: { x: 3, n: 2, pct: 50, rangeStart: 1, rangeEnd: 999 },
        verseWindowStart: 1,
        verseWindowEnd: 999,

        // Word blanking
        wordMode: 'random_pct',
        wordParams: { x: 5, n: 3, pct: 40, custom: '' },
        filters: { minLen: 0, capsOnly: false, exclude: 'the, a, an, and, or, but, of, to, in, for, is, it', include: '' },

        // Card options
        answerMode: 'both', // 'type', 'reveal', or 'both'
        refMode: 'show', // 'show', 'hide', 'auto'
        shuffle: true,
        noAdjChapter: false,
        noAdjSection: false,

        // Study session
        cards: [],        // Array of { ref, chapter, section, verseNum, text, blanks:[] }
        currentCard: 0,
        currentBlankIndex: 0,
        startTime: null,
        correctCount: 0,    // Typed correctly
        incorrectCount: 0,  // Revealed (not typed)
        missedWords: [],
        hintsUsed: 0,
    };

    // ══════════════════════════════════════════════════════════════════
    //  THEME
    // ══════════════════════════════════════════════════════════════════
    function initTheme() {
        const t = localStorage.getItem('theme') || 'light';
        applyTheme(t);
    }
    function applyTheme(t) {
        document.documentElement.setAttribute('data-theme', t === 'dark' ? 'dark' : '');
        localStorage.setItem('theme', t);
        const icon = t === 'dark' ? '🌙' : '☀️';
        document.querySelectorAll('#themeToggleHeader,#themeToggleStudy').forEach(b => b.textContent = icon);
    }
    function toggleTheme() {
        applyTheme((localStorage.getItem('theme') || 'light') === 'dark' ? 'light' : 'dark');
    }
    document.getElementById('themeToggleHeader').addEventListener('click', toggleTheme);
    document.getElementById('themeToggleStudy').addEventListener('click', toggleTheme);
    document.getElementById('titleResetWizard').addEventListener('click', () => {
        // If in study mode, confirm first
        if (document.getElementById('cardStudyScreen').style.display === 'block') {
            if (!confirm('Exit study and start over?')) return;
        }
        startNewWizard();
    });

    // ══════════════════════════════════════════════════════════════════
    //  CRYPTO-STRENGTH SHUFFLE — truly unpredictable
    // ══════════════════════════════════════════════════════════════════
    function cryptoRandom() {
        const arr = new Uint32Array(1);
        crypto.getRandomValues(arr);
        return arr[0] / (0xFFFFFFFF + 1);
    }

    // Fisher-Yates with crypto random — run multiple passes
    function shuffle(arr) {
        const a = [...arr];
        // 3 independent Fisher-Yates passes to break any residual structure
        for (let pass = 0; pass < 3; pass++) {
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(cryptoRandom() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
        }
        return a;
    }

    // Disruption pass: random swaps that maintain constraints
    function disruptOrder(arr, noAdjChap, noAdjSec) {
        const a = [...arr];
        const len = a.length;
        if (len < 4) return a;

        // Attempt many random swaps, keep only those that don't break constraints
        const attempts = len * 5;
        for (let t = 0; t < attempts; t++) {
            const i = Math.floor(cryptoRandom() * len);
            const j = Math.floor(cryptoRandom() * len);
            if (i === j) continue;

            // Test if swap would violate constraints
            const testArr = [...a];
            [testArr[i], testArr[j]] = [testArr[j], testArr[i]];

            if (meetsConstraints(testArr, i, noAdjChap, noAdjSec) &&
                meetsConstraints(testArr, j, noAdjChap, noAdjSec)) {
                [a[i], a[j]] = [a[j], a[i]];
            }
        }
        return a;
    }

    // Check if position idx in array meets adjacency constraints
    function sameChapter(a, b) { return a.book === b.book && a.chapter === b.chapter; }

    function meetsConstraints(arr, idx, noAdjChap, noAdjSec) {
        const item = arr[idx];
        if (idx > 0) {
            const prev = arr[idx - 1];
            if (noAdjChap && sameChapter(item, prev)) return false;
            if (noAdjSec && item.section === prev.section) return false;
        }
        if (idx < arr.length - 1) {
            const next = arr[idx + 1];
            if (noAdjChap && sameChapter(item, next)) return false;
            if (noAdjSec && item.section === next.section) return false;
        }
        return true;
    }

    // Constraint-aware ordering — two-phase approach
    function orderWithConstraints(items, noAdjChap, noAdjSec) {
        if (!noAdjChap && !noAdjSec) return shuffle(items);

        // Phase 1: Build a valid ordering using randomized bucket interleaving
        // Instead of greedy sequential pick, interleave shuffled chapter buckets
        const bestOf = [];
        for (let attempt = 0; attempt < 50; attempt++) {
            const result = buildInterleavedOrder(items, noAdjChap, noAdjSec);
            if (result) {
                bestOf.push(result);
                if (bestOf.length >= 5) break; // Collect a few valid orderings
            }
        }

        let ordered;
        if (bestOf.length > 0) {
            // Pick a random one from our valid orderings
            ordered = bestOf[Math.floor(cryptoRandom() * bestOf.length)];
        } else {
            // Fallback: just shuffle
            ordered = shuffle(items);
        }

        // Phase 2: Disruption pass — random constraint-safe swaps to eliminate any
        // residual pattern from the interleaving algorithm
        return disruptOrder(ordered, noAdjChap, noAdjSec);
    }

    function buildInterleavedOrder(items, noAdjChap, noAdjSec) {
        // Group by book+chapter, shuffle within each group, shuffle group order
        const buckets = new Map();
        for (const item of items) {
            const key = (item.book || '') + '|' + item.chapter;
            if (!buckets.has(key)) buckets.set(key, []);
            buckets.get(key).push(item);
        }

        // Shuffle items within each bucket
        for (const [key, arr] of buckets) {
            buckets.set(key, shuffle(arr));
        }

        // Build list of bucket keys in random order
        let bucketKeys = shuffle([...buckets.keys()]);

        const out = [];
        const cursors = new Map();
        for (const k of bucketKeys) cursors.set(k, 0);

        let remaining = items.length;
        let lastItem = null;
        let stuckCount = 0;

        while (remaining > 0 && stuckCount < 1000) {
            const available = bucketKeys.filter(k => {
                if (cursors.get(k) >= buckets.get(k).length) return false;
                const nextItem = buckets.get(k)[cursors.get(k)];
                if (lastItem) {
                    if (noAdjChap && sameChapter(nextItem, lastItem)) return false;
                    if (noAdjSec && nextItem.section === lastItem.section) return false;
                }
                return true;
            });

            if (available.length === 0) {
                const anyLeft = bucketKeys.filter(k => cursors.get(k) < buckets.get(k).length);
                if (anyLeft.length === 0) break;
                const pick = anyLeft[Math.floor(cryptoRandom() * anyLeft.length)];
                const item = buckets.get(pick)[cursors.get(pick)];
                cursors.set(pick, cursors.get(pick) + 1);
                out.push(item);
                lastItem = item;
                remaining--;
                stuckCount++;
                continue;
            }

            const pick = available[Math.floor(cryptoRandom() * available.length)];
            const item = buckets.get(pick)[cursors.get(pick)];
            cursors.set(pick, cursors.get(pick) + 1);
            out.push(item);
            lastItem = item;
            remaining--;
            stuckCount = 0;

            if (out.length % 7 === 0) bucketKeys = shuffle(bucketKeys);
        }

        if (out.length < items.length) return null;
        return out;
    }

    // ══════════════════════════════════════════════════════════════════
    //  CSV PARSER
    // ══════════════════════════════════════════════════════════════════
    function parseCSV(text) {
        const lines = text.trim().split('\n');
        const verses = [];
        const start = lines[0].toLowerCase().includes('book') ? 1 : 0;
        for (let i = start; i < lines.length; i++) {
            const m = lines[i].match(/(?:"([^"]*)"|([^,]+))(?:,|$)/g);
            if (!m) continue;
            const parts = m.map(p => p.replace(/^"|"$/g, '').replace(/,$/, ''));
            if (parts.length >= 4) {
                verses.push({
                    book: parts[0].trim(),
                    chapter: parseInt(parts[1].trim()),
                    verse: parseInt(parts[2].trim()),
                    text: parts.slice(3).join(',').trim().replace(/^"|"$/g, '')
                });
            }
        }
        return verses;
    }

    // ══════════════════════════════════════════════════════════════════
    //  VERSE FILTER ENGINE (full VBA parity)
    // ══════════════════════════════════════════════════════════════════
    function filterVerses(verses, mode, params, winStart, winEnd) {
        // Apply window first
        let v = verses.filter(vs => vs.verse >= winStart && vs.verse <= winEnd);
        if (v.length === 0) return [];

        const n = v.length;
        const x = Math.min(params.x || 3, n);
        const pctCount = (p) => Math.max(1, Math.round(n * (p || 50) / 100));

        switch (mode) {
            case 'all':       return v;
            case 'first':     return v.slice(0, x);
            case 'last':      return v.slice(-x);
            case 'middle': {
                const start = Math.max(0, Math.floor((n - x) / 2));
                return v.slice(start, start + x);
            }
            case 'random':    return shuffle(v).slice(0, x);
            case 'odd':       return v.filter((_, i) => (i + 1) % 2 === 1);
            case 'even':      return v.filter((_, i) => (i + 1) % 2 === 0);
            case 'every_n': {
                const step = params.n || 2;
                return v.filter((_, i) => (i + 1) % step === 0);
            }
            case 'range': {
                const rs = params.rangeStart || 1;
                const re = params.rangeEnd || 999;
                return v.filter(vs => vs.verse >= rs && vs.verse <= re);
            }
            case 'first_pct':  return v.slice(0, pctCount(params.pct));
            case 'last_pct':   return v.slice(-pctCount(params.pct));
            case 'mid_pct': {
                const c = pctCount(params.pct);
                const s = Math.max(0, Math.floor((n - c) / 2));
                return v.slice(s, s + c);
            }
            case 'rand_pct':   return shuffle(v).slice(0, pctCount(params.pct));
            default:           return v;
        }
    }

    // ══════════════════════════════════════════════════════════════════
    //  WORD BLANKING ENGINE (full VBA parity)
    // ══════════════════════════════════════════════════════════════════
    function cleanWord(w) { return w.replace(/[^a-zA-Z']/g, ''); }

    function getBlankIndices(words, mode, params, filters) {
        const n = words.length;
        if (n === 0) return [];

        // Build real-word list (indices of words that are actual words, not punctuation)
        const realIndices = [];
        const cleanWords = words.map(w => cleanWord(w));

        // Parse filter lists
        const excludeSet = new Set(
            (filters.exclude || '').split(',').map(w => w.trim().toLowerCase()).filter(Boolean)
        );
        const includeSet = new Set(
            (filters.include || '').split(',').map(w => w.trim().toLowerCase()).filter(Boolean)
        );

        for (let i = 0; i < n; i++) {
            const cw = cleanWords[i];
            if (cw.length === 0) continue;
            if (filters.minLen > 0 && cw.length < filters.minLen) continue;
            if (filters.capsOnly && cw[0] === cw[0].toLowerCase()) continue;
            if (excludeSet.has(cw.toLowerCase())) continue;
            realIndices.push(i);
        }

        // Always-include words (override filters)
        const forceIndices = new Set();
        if (includeSet.size > 0) {
            for (let i = 0; i < n; i++) {
                if (includeSet.has(cleanWords[i].toLowerCase())) forceIndices.add(i);
            }
        }

        const rn = realIndices.length;
        if (rn === 0 && forceIndices.size === 0) return [];

        const x = Math.min(params.x || 5, rn);
        const pctCount = (p) => Math.max(1, Math.round(rn * Math.min(p || 40, 100) / 100));

        let selected = new Set();

        switch (mode) {
            case 'all':
                realIndices.forEach(i => selected.add(i));
                break;
            case 'first':
                realIndices.slice(0, x).forEach(i => selected.add(i));
                break;
            case 'last':
                realIndices.slice(-x).forEach(i => selected.add(i));
                break;
            case 'middle': {
                const s = Math.max(0, Math.floor((rn - x) / 2));
                realIndices.slice(s, s + x).forEach(i => selected.add(i));
                break;
            }
            case 'random':
                pickNonAdjacent(realIndices, x).forEach(i => selected.add(i));
                break;
            case 'odd':
                realIndices.filter((_, i) => i % 2 === 0).forEach(i => selected.add(i));
                break;
            case 'even':
                realIndices.filter((_, i) => i % 2 === 1).forEach(i => selected.add(i));
                break;
            case 'every_n': {
                const step = params.n || 3;
                realIndices.filter((_, i) => (i + 1) % step === 0).forEach(idx => selected.add(idx));
                break;
            }
            case 'first_pct':
                realIndices.slice(0, pctCount(params.pct)).forEach(i => selected.add(i));
                break;
            case 'last_pct':
                realIndices.slice(-pctCount(params.pct)).forEach(i => selected.add(i));
                break;
            case 'mid_pct': {
                const c = pctCount(params.pct);
                const s = Math.max(0, Math.floor((rn - c) / 2));
                realIndices.slice(s, s + c).forEach(i => selected.add(i));
                break;
            }
            case 'random_pct':
                pickNonAdjacent(realIndices, pctCount(params.pct)).forEach(i => selected.add(i));
                break;
            case 'custom': {
                const positions = (params.custom || '').split(',').map(p => parseInt(p.trim())).filter(p => !isNaN(p));
                positions.forEach(p => { if (p >= 1 && p <= n) selected.add(p - 1); });
                break;
            }
        }

        // Add force-included words
        forceIndices.forEach(i => selected.add(i));

        // Post-processing: reduce adjacency ONLY for random modes
        // All positional modes (first, last, middle, odd, even, etc.) must
        // keep their exact positions — adjacency reduction would break them
        if (mode === 'random' || mode === 'random_pct') {
            selected = reduceAdjacency(selected, realIndices, n);
        }

        return [...selected].sort((a, b) => a - b);
    }

    // ── Adjacency-avoiding random picker ──
    // Picks `count` indices from `pool` (word-position indices), strongly
    // preferring non-adjacent selections. Uses sectioned picking to spread
    // blanks evenly across the verse, then fills remaining slots randomly.
    function pickNonAdjacent(pool, count) {
        if (count >= pool.length) return [...pool];
        if (count === 0) return [];

        const selected = new Set();
        const shuffled = shuffle(pool);

        // Pass 1: pick non-adjacent, spread across verse
        // Divide pool into `count` sections, pick one non-adjacent from each
        const sectionSize = pool.length / count;
        for (let i = 0; i < count && selected.size < count; i++) {
            const secStart = Math.floor(i * sectionSize);
            const secEnd = Math.floor((i + 1) * sectionSize);
            const section = shuffled.slice(secStart, secEnd);

            // Prefer candidates not adjacent to already-selected
            const nonAdj = section.filter(idx => !isAdjacentToSet(idx, selected));
            const pool2 = nonAdj.length > 0 ? nonAdj : section;

            // Pick from candidates not already selected
            const available = pool2.filter(idx => !selected.has(idx));
            if (available.length > 0) {
                selected.add(available[Math.floor(cryptoRandom() * available.length)]);
            }
        }

        // Pass 2: if we still need more, fill from remaining non-adjacent
        if (selected.size < count) {
            const remaining = shuffled.filter(idx => !selected.has(idx) && !isAdjacentToSet(idx, selected));
            for (const idx of remaining) {
                if (selected.size >= count) break;
                selected.add(idx);
            }
        }

        // Pass 3: last resort — fill from anything left (allows adjacency)
        if (selected.size < count) {
            const remaining = shuffled.filter(idx => !selected.has(idx));
            for (const idx of remaining) {
                if (selected.size >= count) break;
                selected.add(idx);
            }
        }

        return [...selected];
    }

    // ── Post-processing: swap adjacent blanks for non-adjacent alternatives ──
    // Scans selected set for pairs of adjacent word indices. For each pair,
    // tries to swap one out for a non-adjacent unused candidate from realIndices.
    function reduceAdjacency(selected, realIndices, totalWords) {
        const sel = new Set(selected);
        const sorted = [...sel].sort((a, b) => a - b);

        // Find adjacent pairs
        const adjPairs = [];
        for (let i = 0; i < sorted.length - 1; i++) {
            if (sorted[i + 1] - sorted[i] === 1) {
                adjPairs.push([sorted[i], sorted[i + 1]]);
            }
        }

        if (adjPairs.length === 0) return sel;

        // Build pool of unused real-word indices
        const unused = realIndices.filter(idx => !sel.has(idx));
        if (unused.length === 0) return sel;

        // For each adjacent pair, try to swap one member out
        const shuffledUnused = shuffle(unused);
        let uIdx = 0;

        for (const [a, b] of adjPairs) {
            if (uIdx >= shuffledUnused.length) break;

            // Decide which of the pair to swap (pick randomly)
            const victim = cryptoRandom() < 0.5 ? a : b;
            const other = victim === a ? b : a;

            // Find a replacement that is not adjacent to `other` or any other selected
            let replaced = false;
            for (let j = uIdx; j < shuffledUnused.length; j++) {
                const candidate = shuffledUnused[j];
                if (sel.has(candidate)) continue;

                // Check candidate won't be adjacent to any remaining selected index
                const tempSel = new Set(sel);
                tempSel.delete(victim);
                tempSel.add(candidate);

                if (!isAdjacentToSet(candidate, new Set([...tempSel].filter(x => x !== candidate)))) {
                    // Good swap — and verify we didn't create new adjacency for `other`
                    if (!isAdjacentToSet(other, new Set([...tempSel].filter(x => x !== other)))) {
                        sel.delete(victim);
                        sel.add(candidate);
                        // Mark this unused index as consumed
                        shuffledUnused[j] = shuffledUnused[uIdx];
                        uIdx++;
                        replaced = true;
                        break;
                    }
                }
            }
            // If no swap found, leave the adjacency — it's allowed, just not preferred
        }

        return sel;
    }

    function isAdjacentToSet(idx, set) {
        return set.has(idx - 1) || set.has(idx + 1);
    }

    function buildBlanks(verseText, mode, params, filters) {
        const words = verseText.split(/\s+/);
        const blankIndices = new Set(getBlankIndices(words, mode, params, filters));
        const blanks = [];

        words.forEach((word, i) => {
            if (blankIndices.has(i)) {
                const cw = cleanWord(word);
                const leadingPunct = word.match(/^[^a-zA-Z']+/)?.[0] || '';
                const trailingPunct = word.match(/[^a-zA-Z']+$/)?.[0] || '';

                blanks.push({
                    index: i,
                    word: cw,
                    fullWord: word,
                    leadingPunct,
                    trailingPunct,
                    userInput: '',
                    completed: false,
                    correct: false,
                    hintShown: false,
                });
            }
        });

        return blanks;
    }

    // ══════════════════════════════════════════════════════════════════
    //  WIZARD LOGIC
    // ══════════════════════════════════════════════════════════════════
    const TOTAL_STEPS = 5;
    const STEP_LABELS = ['Select', 'Verses', 'Blanks', 'Options', 'Review'];

    function renderWizardProgress() {
        const el = document.getElementById('wizardProgress');
        let html = '';
        for (let i = 1; i <= TOTAL_STEPS; i++) {
            const cls = i < state.wizardStep ? 'completed' : (i === state.wizardStep ? 'active' : '');
            const clickable = i < state.wizardStep;
            const wrapCls = i === state.wizardStep ? 'active' : '';
            html += `<div class="wizard-dot-wrap ${wrapCls}">`;
            html += `<div class="wizard-dot ${cls}" ${clickable ? `onclick="wizardJump(${i})" role="button" tabindex="0" title="${STEP_LABELS[i-1]}"` : ''}></div>`;
            html += `<span class="wizard-dot-label">${STEP_LABELS[i-1]}</span>`;
            html += `</div>`;
            if (i < TOTAL_STEPS) {
                html += `<div class="wizard-line ${i < state.wizardStep ? 'completed' : ''}"></div>`;
            }
        }
        el.innerHTML = html;

        // Mobile step counter
        const counter = document.getElementById('wizardStepCounter');
        if (counter) counter.textContent = `Step ${state.wizardStep} of ${TOTAL_STEPS} — ${STEP_LABELS[state.wizardStep - 1]}`;
    }

    function wizardJump(step) {
        // Only allow jumping to completed (previous) steps
        if (step < state.wizardStep) showWizardStep(step);
    }
    window.wizardJump = wizardJump;

    function showWizardStep(step) {
        state.wizardStep = step;
        document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
        const target = document.querySelector(`.wizard-step[data-step="${step}"]`);
        if (target) target.classList.add('active');
        renderWizardProgress();

        // Scroll so wizard dots are at top of viewport
        const dotsEl = document.getElementById('wizardProgress');
        if (dotsEl) dotsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Step 4: conditionally show constraint toggles
        if (step === 4) {
            const chapCount = getSelectedChapters().length;
            const secCount = state.selectedSections.size;
            document.getElementById('noAdjChapterRow').style.display = chapCount > 1 ? 'flex' : 'none';
            document.getElementById('noAdjSectionRow').style.display = secCount > 1 ? 'flex' : 'none';
        }

        // Step 5: build review
        if (step === 5) buildReview();
    }

    function wizardNext() {
        if (state.wizardStep === 1) {
            const chapters = getSelectedChapters();
            if (chapters.length === 0) { alert('Please select at least one section or chapter.'); return; }
        }
        if (state.wizardStep < TOTAL_STEPS) showWizardStep(state.wizardStep + 1);
    }

    function wizardPrev() {
        if (state.wizardStep > 1) showWizardStep(state.wizardStep - 1);
    }

    // ══════════════════════════════════════════════════════════════════
    //  SELECTION RENDERING (multi-book aware)
    // ══════════════════════════════════════════════════════════════════

    // Returns array of { book, chapter } tuples
    function getSelectedChapters() {
        if (state.studyMode === 'section') {
            const tuples = [];
            const seen = new Set();
            state.selectedSections.forEach(secId => {
                const sec = getActiveSections().find(s => s.id === secId);
                if (!sec) return;
                for (const entry of sec.entries) {
                    for (const ch of entry.chapters) {
                        const key = `${entry.book}|${ch}`;
                        if (!seen.has(key)) { seen.add(key); tuples.push({ book: entry.book, chapter: ch }); }
                    }
                }
            });
            return tuples;
        }
        // Chapter mode: selectedChapters stores "Book|Chapter" strings
        return [...state.selectedChapters].map(key => {
            const [book, ch] = key.split('|');
            return { book, chapter: parseInt(ch) };
        });
    }

    function getSectionForChapter(book, ch) {
        for (const sec of getActiveSections()) {
            for (const entry of sec.entries) {
                if (entry.book === book && entry.chapters.includes(ch)) return sec.id;
            }
        }
        return 0;
    }

    function renderSectionPills(containerId, prefix) {
        const el = document.getElementById(containerId);
        el.innerHTML = getActiveSections().map(s => `
            <div class="pill" data-id="${s.id}" data-type="${prefix}" onclick="togglePill(this,'${prefix}')">
                <div>${s.name}</div>
                <div class="pill-label">${s.label}</div>
            </div>
        `).join('');
    }

    function renderChapterPills(containerId, prefix) {
        const el = document.getElementById(containerId);
        const books = getPlanBooks();
        const allTuples = getAllPlanChapters();

        let html = '';
        for (const book of books) {
            const chapters = allTuples.filter(t => t.book === book);
            if (chapters.length === 0) continue;
            html += `<div class="book-group">`;
            if (books.length > 1) {
                html += `<div class="book-group-title">${book}</div>`;
            }
            html += `<div class="pill-grid chapters">`;
            for (const t of chapters) {
                html += `<div class="pill" data-id="${t.key}" data-type="${prefix}" onclick="togglePill(this,'${prefix}')">${t.chapter}</div>`;
            }
            html += `</div></div>`;
        }
        el.innerHTML = html;
    }

    function renderChapterQuickActions(containerId, prefix) {
        const el = document.getElementById(containerId);
        // Build section-based range buttons
        const sections = getActiveSections();
        let html = `<button class="btn btn-sm btn-secondary" onclick="selectAllPills('${prefix}')">All</button>`;
        html += `<button class="btn btn-sm btn-secondary" onclick="selectNonePills('${prefix}')">None</button>`;
        for (const sec of sections) {
            html += `<button class="btn btn-sm btn-secondary" onclick="selectSectionRange(${sec.id},'${prefix}')">${sec.label}</button>`;
        }
        el.innerHTML = html;
    }

    function selectSectionRange(secId, prefix) {
        const sec = getActiveSections().find(s => s.id === secId);
        if (!sec) return;
        for (const entry of sec.entries) {
            for (const ch of entry.chapters) {
                state.selectedChapters.add(`${entry.book}|${ch}`);
            }
        }
        document.querySelectorAll(`.pill[data-type="${prefix}"]`).forEach(p => {
            if (state.selectedChapters.has(p.dataset.id)) p.classList.add('selected');
        });
    }
    window.selectSectionRange = selectSectionRange;

    function togglePill(el, prefix) {
        const id = el.dataset.id;
        const isSection = prefix.includes('section');
        const set = isSection ? state.selectedSections : state.selectedChapters;
        const key = isSection ? parseInt(id) : id; // sections are int IDs, chapters are "Book|Ch" strings

        if (set.has(key)) { set.delete(key); el.classList.remove('selected'); }
        else { set.add(key); el.classList.add('selected'); }
    }

    function selectAllPills(prefix) {
        const isSection = prefix.includes('section');
        if (isSection) {
            const items = getActiveSections().map(s => s.id);
            items.forEach(id => state.selectedSections.add(id));
        } else {
            getAllPlanChapters().forEach(t => state.selectedChapters.add(t.key));
        }
        document.querySelectorAll(`.pill[data-type="${prefix}"]`).forEach(p => p.classList.add('selected'));
    }

    function selectNonePills(prefix) {
        const isSection = prefix.includes('section');
        const set = isSection ? state.selectedSections : state.selectedChapters;
        set.clear();
        document.querySelectorAll(`.pill[data-type="${prefix}"]`).forEach(p => p.classList.remove('selected'));
    }

    window.selectAllPills = selectAllPills;
    window.selectNonePills = selectNonePills;
    window.togglePill = togglePill;

    // ══════════════════════════════════════════════════════════════════
    //  MODE PICKERS
    // ══════════════════════════════════════════════════════════════════
    function renderModePicker(containerId, modes, currentMode, onSelect) {
        const el = document.getElementById(containerId);
        el.innerHTML = modes.map(m => `
            <div class="mode-option ${m.id === currentMode ? 'active' : ''}" data-mode="${m.id}">
                <div class="mode-name">${m.name}</div>
                <div class="mode-desc">${m.desc}</div>
            </div>
        `).join('');
        el.querySelectorAll('.mode-option').forEach(opt => {
            opt.addEventListener('click', () => {
                el.querySelectorAll('.mode-option').forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                onSelect(opt.dataset.mode);
            });
        });
    }

    function renderParams(containerId, modes, modeId, paramsState, prefix) {
        const mode = modes.find(m => m.id === modeId);
        const el = document.getElementById(containerId);
        if (!mode || mode.params.length === 0) { el.innerHTML = ''; return; }

        let html = '';
        mode.params.forEach(p => {
            if (p === 'x') {
                html += `<div class="param-group"><div class="param-label">How many?</div>
                    <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="${prefix}_x" value="${paramsState.x}" min="1"
                    onchange="updateParam('${prefix}','x',this.value)"></div>`;
            } else if (p === 'n') {
                html += `<div class="param-group"><div class="param-label">Every Nth (N = ?)</div>
                    <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="${prefix}_n" value="${paramsState.n}" min="2"
                    onchange="updateParam('${prefix}','n',this.value)"></div>`;
            } else if (p === 'pct') {
                html += `<div class="param-group"><div class="param-label">Percentage (1-100)</div>
                    <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="${prefix}_pct" value="${paramsState.pct}" min="1" max="100"
                    onchange="updateParam('${prefix}','pct',this.value)"></div>`;
            } else if (p === 'rangeStart') {
                html += `<div class="param-row"><div class="param-group"><div class="param-label">Start verse</div>
                    <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="${prefix}_rangeStart" value="${paramsState.rangeStart}" min="1"
                    onchange="updateParam('${prefix}','rangeStart',this.value)"></div>`;
            } else if (p === 'rangeEnd') {
                html += `<div class="param-group"><div class="param-label">End verse</div>
                    <input type="number" inputmode="numeric" pattern="[0-9]*" class="param-input" id="${prefix}_rangeEnd" value="${paramsState.rangeEnd}" min="1"
                    onchange="updateParam('${prefix}','rangeEnd',this.value)"></div></div>`;
            } else if (p === 'custom') {
                html += `<div class="param-group"><div class="param-label">Word positions (comma-separated)</div>
                    <div class="param-desc">e.g. 1, 4, 7, 10</div>
                    <input type="text" class="param-input" id="${prefix}_custom" value="${paramsState.custom || ''}"
                    onchange="updateParam('${prefix}','custom',this.value)"></div>`;
            }
        });
        el.innerHTML = html;
    }

    function updateParam(prefix, key, val) {
        const params = prefix === 'verse' ? state.verseParams : state.wordParams;
        params[key] = (key === 'custom') ? val : parseInt(val) || 0;
    }
    window.updateParam = updateParam;

    // ══════════════════════════════════════════════════════════════════
    //  REVIEW BUILDER
    // ══════════════════════════════════════════════════════════════════
    function buildReview() {
        const chapters = getSelectedChapters();
        const verseMode = VERSE_MODES.find(m => m.id === state.verseMode);
        const wordMode = WORD_MODES.find(m => m.id === state.wordMode);
        const plan = getActivePlan();

        let chLabel;
        if (state.studyMode === 'section') {
            const secs = [...state.selectedSections].sort().map(id => {
                const s = getActiveSections().find(x => x.id === id);
                return s ? `${s.name} (${s.label})` : '';
            });
            chLabel = secs.join(', ');
        } else {
            // Group by book, format as ranges
            const byBook = {};
            chapters.forEach(t => { if (!byBook[t.book]) byBook[t.book] = []; byBook[t.book].push(t.chapter); });
            const parts = Object.entries(byBook).map(([book, chs]) =>
                `${book} ${formatChapterRanges(chs.sort((a, b) => a - b))}`
            );
            chLabel = parts.join(', ');
        }

        // Build filters summary
        const filterParts = [];
        if (state.filters.minLen > 0) filterParts.push(`Min ${state.filters.minLen} letters`);
        if (state.filters.capsOnly) filterParts.push('Capitalized only');
        if (state.filters.exclude) filterParts.push(`Exclude: ${state.filters.exclude}`);
        if (state.filters.include) filterParts.push(`Always blank: ${state.filters.include}`);

        const items = [
            { label: 'Study Plan', value: plan.name },
            { label: 'Studying', value: chLabel },
            { label: 'Verse Selection', value: verseMode ? formatModeName(verseMode, state.verseParams) + formatParamSummary(state.verseParams, verseMode) : 'All' },
            { label: 'Word Blanking', value: wordMode ? formatModeName(wordMode, state.wordParams) + formatParamSummary(state.wordParams, wordMode) : 'Random 40%' },
        ];

        if (filterParts.length > 0) {
            items.push({ label: 'Word Filters', value: filterParts.join(' · ') });
        }

        items.push(
            { label: 'Answer Mode', value: state.answerMode === 'type' ? '⌨️ Type' : state.answerMode === 'both' ? '⌨️👆 Type & Tap' : '👆 Tap to Reveal' },
            { label: 'Reference', value: state.refMode === 'show' ? 'Always Show' : state.refMode === 'hide' ? 'Always Hide' : 'Reveal on Complete' },
            { label: 'Shuffle', value: state.shuffle ? 'Yes' : 'No (sequential)' },
        );

        document.getElementById('reviewGrid').innerHTML = items.map(i =>
            `<div class="review-item"><div class="review-label">${i.label}</div><div class="review-value">${i.value}</div></div>`
        ).join('');
    }

    // Format [1,2,3,5,7,8,9] → "1–3, 5, 7–9"
    function formatChapterRanges(sorted) {
        if (sorted.length === 0) return '';
        const ranges = [];
        let start = sorted[0], end = sorted[0];
        for (let i = 1; i < sorted.length; i++) {
            if (sorted[i] === end + 1) { end = sorted[i]; }
            else { ranges.push(start === end ? `${start}` : `${start}–${end}`); start = end = sorted[i]; }
        }
        ranges.push(start === end ? `${start}` : `${start}–${end}`);
        return ranges.join(', ');
    }

    function formatParamSummary(params, mode) {
        if (!mode || mode.params.length === 0) return '';
        if (mode.params.includes('pct')) return ''; // pct is embedded in formatModeName
        if (mode.params.includes('x')) return ` (${params.x})`;
        if (mode.params.includes('n')) return ` (every ${params.n})`;
        if (mode.params.includes('rangeStart')) return ` (${params.rangeStart}–${params.rangeEnd})`;
        return '';
    }

    function formatModeName(mode, params) {
        if (!mode) return '';
        // For percentage modes, embed the number: "Last %" → "Last 20%"
        if (mode.params.includes('pct')) {
            return mode.name.replace('%', `${params.pct || 50}%`);
        }
        return mode.name;
    }

    // ══════════════════════════════════════════════════════════════════
    //  STUDY SESSION
    // ══════════════════════════════════════════════════════════════════
    async function startStudy() {
        const selectedTuples = getSelectedChapters();
        if (selectedTuples.length === 0) { alert('No chapters selected.'); return; }

        // Group by book to minimize CSV fetches
        const byBook = {};
        for (const t of selectedTuples) {
            if (!byBook[t.book]) byBook[t.book] = [];
            byBook[t.book].push(t.chapter);
        }

        try {
            // Load all needed CSVs in parallel
            const bookVerses = {};
            const fetchPromises = Object.keys(byBook).map(async (bookName) => {
                const bookData = BOOKS_DATA[bookName];
                if (!bookData) {
                    console.warn(`Book "${bookName}" not found in BOOKS_DATA. Check your bible/ CSV files.`);
                    return;
                }
                const response = await fetch(bookData.path);
                const csvText = await response.text();
                bookVerses[bookName] = parseCSV(csvText);
            });
            await Promise.all(fetchPromises);

            // Build cards
            const cards = [];
            for (const { book, chapter } of selectedTuples) {
                const allVerses = bookVerses[book];
                if (!allVerses) continue;

                const chapterVerses = allVerses.filter(v => v.chapter === chapter);
                const filtered = filterVerses(
                    chapterVerses, state.verseMode, state.verseParams,
                    state.verseWindowStart, state.verseWindowEnd
                );

                for (const v of filtered) {
                    const blanks = buildBlanks(v.text, state.wordMode, state.wordParams, state.filters);
                    if (blanks.length === 0) continue;

                    cards.push({
                        ref: `${book} ${v.chapter}:${v.verse}`,
                        book: book,
                        chapter: v.chapter,
                        section: getSectionForChapter(book, v.chapter),
                        verseNum: v.verse,
                        text: v.text,
                        blanks: blanks,
                        allCompleted: false,
                    });
                }
            }

            if (cards.length === 0) {
                alert('No verses matched your selections. Try adjusting your filters.');
                return;
            }

            // Order cards
            if (state.shuffle) {
                state.cards = orderWithConstraints(cards, state.noAdjChapter, state.noAdjSection);
            } else {
                state.cards = cards;
            }

            state.currentCard = 0;
            state.currentBlankIndex = 0;
            state.startTime = Date.now();
            state.correctCount = 0;
            state.incorrectCount = 0;
            state.missedWords = [];
            state.hintsUsed = 0;

            // Show study screen
            document.getElementById('wizardScreen').style.display = 'none';
            document.getElementById('mainHeader').style.display = 'none';
            document.getElementById('cardStudyScreen').style.display = 'block';

            renderCard();
        } catch (err) {
            console.error('Error loading verses:', err);
            alert('Error loading verse data. Check console.');
        }
    }
    window.startStudy = startStudy;

    // ══════════════════════════════════════════════════════════════════
    //  CARD RENDERING
    // ══════════════════════════════════════════════════════════════════
    function renderCard() {
        const card = state.cards[state.currentCard];
        if (!card) return;

        // Reference
        const refEl = document.getElementById('cardReference');
        if (state.refMode === 'show') {
            refEl.textContent = card.ref;
            refEl.className = 'card-reference';
        } else if (state.refMode === 'hide') {
            refEl.textContent = card.ref;
            refEl.className = 'card-reference hidden';
        } else { // auto
            if (card.allCompleted) {
                refEl.textContent = card.ref;
                refEl.className = 'card-reference revealed';
            } else {
                refEl.textContent = card.ref;
                refEl.className = 'card-reference hidden';
            }
        }

        // Verse text with blanks
        const words = card.text.split(/\s+/);
        const blankMap = new Map(card.blanks.map((b, bi) => [b.index, { blank: b, blankIdx: bi }]));
        let html = '';

        words.forEach((word, i) => {
            if (blankMap.has(i)) {
                const { blank, blankIdx } = blankMap.get(i);
                const isCurrent = blankIdx === state.currentBlankIndex && !card.allCompleted;
                const isCompleted = blank.completed;

                let cls = 'blank';
                if (isCurrent) cls += ' current';
                if (isCompleted) cls += ' completed';

                let display = '';
                if (isCompleted) {
                    display = blank.userInput || blank.word;
                } else if (blank.hintShown) {
                    const first = blank.word[0] || '';
                    const last = blank.word.length > 1 ? blank.word[blank.word.length - 1] : '';
                    const mid = '&nbsp;'.repeat(Math.max(blank.word.length - 2, 1));
                    display = `<span class="letter hint">${first}</span>${mid}<span class="letter hint">${last}</span>`;
                } else if (isCurrent && blank.userInput && (state.answerMode === 'type' || state.answerMode === 'both')) {
                    display = renderTypedLetters(blank);
                }

                html += `<span style="white-space:nowrap;display:inline">${blank.leadingPunct}<span class="${cls}" data-bi="${blankIdx}" onclick="onBlankClick(${blankIdx})" ontouchend="onBlankTouch(event,${blankIdx})"><span class="blank-sizer" aria-hidden="true">${blank.word}</span><span class="blank-input">${display}</span></span>${blank.trailingPunct}</span> `;
            } else {
                html += `<span class="word">${word}</span> `;
            }
        });

        document.getElementById('cardVerseText').innerHTML = html;

        // Counter & progress
        document.getElementById('cardCounter').textContent = `${state.currentCard + 1} / ${state.cards.length}`;
        document.getElementById('toolbarProgress').textContent = `Card ${state.currentCard + 1} of ${state.cards.length}`;

        const completedCards = state.cards.filter(c => c.allCompleted).length;
        document.getElementById('progressBar').style.width = `${(completedCards / state.cards.length) * 100}%`;

        // Nav buttons
        document.getElementById('prevCardBtn').disabled = state.currentCard === 0;
        const nextBtn = document.getElementById('nextCardBtn');
        if (state.currentCard >= state.cards.length - 1 && card.allCompleted) {
            nextBtn.textContent = 'Finish ✓';
        } else {
            nextBtn.textContent = 'Next →';
        }

        // Reveal action bar: show only in reveal/both modes when card has incomplete blanks
        const revealBar = document.getElementById('revealActionBar');
        const showRevealBar = (state.answerMode === 'reveal' || state.answerMode === 'both') && !card.allCompleted;
        revealBar.style.display = showRevealBar ? 'flex' : 'none';

        // Adjust card body bottom padding for reveal bar
        const cardBody = document.querySelector('.card-body');
        const isMobile = window.innerWidth <= 768;
        cardBody.style.paddingBottom = showRevealBar
            ? (isMobile ? '130px' : '150px')
            : (isMobile ? '75px' : '90px');
    }

    function renderTypedLetters(blank) {
        const target = blank.word.toLowerCase();
        const input = blank.userInput.toLowerCase();
        let html = '';
        for (let i = 0; i < blank.word.length; i++) {
            if (i < blank.userInput.length) {
                const isCorrect = input[i] === target[i];
                html += `<span class="letter ${isCorrect ? 'correct' : 'incorrect'}">${blank.userInput[i]}</span>`;
            } else {
                html += '<span class="letter">&nbsp;</span>';
            }
        }
        return html;
    }

    // ══════════════════════════════════════════════════════════════════
    //  CARD INTERACTION
    // ══════════════════════════════════════════════════════════════════
    function onBlankClick(bi) {
        const card = state.cards[state.currentCard];
        if (card.allCompleted) return;
        const blank = card.blanks[bi];

        if (state.answerMode === 'type') {
            if (!blank.completed) { state.currentBlankIndex = bi; renderCard(); }
        } else if (state.answerMode === 'both') {
            // Hybrid: click non-current blank to jump, click current to reveal, click completed to un-reveal
            if (!blank.completed && bi === state.currentBlankIndex) {
                revealCurrentBlank();
            } else if (blank.completed) {
                blank.completed = false; blank.userInput = ''; blank.correct = false;
                state.currentBlankIndex = bi;
                card.allCompleted = false;
                renderCard();
            } else {
                state.currentBlankIndex = bi; renderCard();
            }
        } else {
            // Reveal mode
            if (!blank.completed && bi === state.currentBlankIndex) {
                revealCurrentBlank();
            } else if (blank.completed) {
                blank.completed = false; blank.userInput = '';
                state.currentBlankIndex = bi;
                card.allCompleted = false;
                renderCard();
            } else {
                state.currentBlankIndex = bi; renderCard();
            }
        }
    }
    window.onBlankClick = onBlankClick;

    function onBlankTouch(e, bi) {
        e.preventDefault();
        const card = state.cards[state.currentCard];
        if (card.allCompleted) return;

        if (state.answerMode === 'type' || state.answerMode === 'both') {
            const blank = card.blanks[bi];
            if (!blank.completed) {
                state.currentBlankIndex = bi;
                renderCard();
                const inp = document.getElementById('mobileInput');
                inp.value = blank.userInput || '';
                inp.focus();
                inp.oninput = () => {
                    const cb = state.cards[state.currentCard].blanks[state.currentBlankIndex];
                    cb.userInput = inp.value;
                    checkTypedAnswer();
                };
                inp.onkeydown = (ev) => { if (ev.key === 'Enter') { ev.preventDefault(); inp.blur(); } };
            } else if (state.answerMode === 'both') {
                // In both mode, allow un-revealing on touch too
                blank.completed = false; blank.userInput = ''; blank.correct = false;
                state.currentBlankIndex = bi;
                card.allCompleted = false;
                renderCard();
            }
        } else {
            onBlankClick(bi);
        }
    }
    window.onBlankTouch = onBlankTouch;

    // Keyboard handling
    document.addEventListener('keydown', (e) => {
        if (document.getElementById('cardStudyScreen').style.display !== 'block') return;
        const card = state.cards[state.currentCard];
        if (!card) return;
        if (card.allCompleted) {
            if (e.key === 'ArrowRight' || e.key === ' ' || e.key === 'Enter') { e.preventDefault(); nextCard(); }
            if (e.key === 'ArrowLeft') { e.preventDefault(); prevCard(); }
            return;
        }

        const blank = card.blanks[state.currentBlankIndex];
        if (!blank || blank.completed) return;

        // Hint
        if ((e.key === 'h' || e.key === 'H') && (e.ctrlKey || e.metaKey)) {
            e.preventDefault(); showHint(); return;
        }

        if (state.answerMode === 'type' || state.answerMode === 'both') {
            if (e.key.length === 1 && /[a-zA-Z'']/.test(e.key) && !e.ctrlKey && !e.metaKey && !e.altKey) {
                e.preventDefault();
                if (blank.hintShown) blank.hintShown = false;
                blank.userInput += e.key;
                checkTypedAnswer();
            } else if (e.key === 'Backspace') {
                e.preventDefault();
                blank.userInput = blank.userInput.slice(0, -1);
                renderCard();
            } else if (e.key === ' ') {
                e.preventDefault(); revealCurrentBlank();
            } else if (e.key === 'Enter') {
                e.preventDefault(); if (blank.userInput) revealCurrentBlank();
            }
        } else {
            // Reveal-only mode
            if (e.key === ' ' || e.key === 'Enter') { e.preventDefault(); revealCurrentBlank(); }
        }

        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            let prev = state.currentBlankIndex - 1;
            while (prev >= 0 && card.blanks[prev].completed) prev--;
            if (prev >= 0) { state.currentBlankIndex = prev; renderCard(); }
        }
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            let next = state.currentBlankIndex + 1;
            while (next < card.blanks.length && card.blanks[next].completed) next++;
            if (next < card.blanks.length) { state.currentBlankIndex = next; renderCard(); }
        }
    });

    function checkTypedAnswer() {
        const card = state.cards[state.currentCard];
        const blank = card.blanks[state.currentBlankIndex];
        const target = blank.word.toLowerCase();
        const input = blank.userInput.toLowerCase();

        renderCard();

        if (input.length >= target.length) {
            if (input === target) {
                blank.completed = true;
                blank.correct = true;
                state.correctCount++;
                advanceToNextBlank();
            }
        }
    }

    function revealCurrentBlank() {
        const card = state.cards[state.currentCard];
        const blank = card.blanks[state.currentBlankIndex];
        if (!blank || blank.completed) return;

        blank.completed = true;
        blank.correct = false;
        blank.userInput = blank.word;
        state.incorrectCount++;
        state.missedWords.push(`${blank.word} (${card.ref})`);

        advanceToNextBlank();
    }

    function advanceToNextBlank() {
        const card = state.cards[state.currentCard];
        if (card.blanks.every(b => b.completed)) {
            card.allCompleted = true;
            state.currentBlankIndex = 0;
            renderCard();

            // Auto-advance after short delay if not last card
            if (state.cards.every(c => c.allCompleted)) {
                setTimeout(showStats, 1200);
            }
            return;
        }

        let next = state.currentBlankIndex + 1;
        while (next < card.blanks.length && card.blanks[next].completed) next++;
        if (next >= card.blanks.length) {
            next = 0;
            while (next < state.currentBlankIndex && card.blanks[next].completed) next++;
        }
        state.currentBlankIndex = next;
        renderCard();

        // Scroll to current blank
        setTimeout(() => {
            const el = document.querySelector('.blank.current');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 50);
    }

    function showHint() {
        const card = state.cards[state.currentCard];
        if (card.allCompleted) return;
        const blank = card.blanks[state.currentBlankIndex];
        if (!blank || blank.completed || blank.hintShown) return;
        blank.hintShown = true;
        blank.userInput = '';
        state.hintsUsed++;
        renderCard();
    }

    // Navigation
    function nextCard() {
        if (state.currentCard < state.cards.length - 1) {
            state.currentCard++;
            state.currentBlankIndex = 0;
            // Find first incomplete blank
            const card = state.cards[state.currentCard];
            if (!card.allCompleted) {
                for (let i = 0; i < card.blanks.length; i++) {
                    if (!card.blanks[i].completed) { state.currentBlankIndex = i; break; }
                }
            }
            renderCard();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else if (state.cards[state.currentCard].allCompleted) {
            if (state.cards.every(c => c.allCompleted)) showStats();
        }
    }

    function prevCard() {
        if (state.currentCard > 0) {
            state.currentCard--;
            state.currentBlankIndex = 0;
            renderCard();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    document.getElementById('nextCardBtn').addEventListener('click', nextCard);
    document.getElementById('prevCardBtn').addEventListener('click', prevCard);

    // Toolbar buttons
    document.getElementById('hintBtn').addEventListener('click', showHint);
    document.getElementById('revealBtnAction').addEventListener('click', revealCurrentBlank);
    document.getElementById('exitStudy').addEventListener('click', () => {
        if (confirm('Exit study session?')) backToWizard();
    });

    // ── Prevent iOS zoom: stop toolbar/nav buttons from stealing focus ──
    // When mobile keyboard is open (mobileInput focused), tapping a button
    // causes Safari to blur→refocus→zoom. Preventing default on mousedown/
    // touchstart keeps focus on the hidden input while still firing click.
    document.querySelectorAll(
        '#cardToolbar .toolbar-btn, #cardNav .nav-btn, .reveal-action-btn, .font-btn'
    ).forEach(btn => {
        btn.addEventListener('mousedown', e => e.preventDefault());
        btn.addEventListener('touchstart', e => { /* don't preventDefault here — it blocks click on iOS */ }, { passive: true });
    });

    // For font buttons specifically, also prevent focus steal
    document.querySelectorAll('.font-btn').forEach(btn => {
        btn.addEventListener('mousedown', e => e.preventDefault());
    });

    // ══════════════════════════════════════════════════════════════════
    //  STATS
    // ══════════════════════════════════════════════════════════════════
    function showStats() {
        const elapsed = Math.floor((Date.now() - state.startTime) / 1000);
        const mins = Math.floor(elapsed / 60);
        const secs = elapsed % 60;
        const timeStr = `${mins}:${secs.toString().padStart(2, '0')}`;
        const totalBlanks = state.correctCount + state.incorrectCount;

        let html = '<div class="stats-grid">';

        if (state.answerMode === 'reveal') {
            // ── REVEAL MODE: Time, cards, hints — no accuracy ──
            document.getElementById('statsTitle').textContent = '✅ Session Complete!';
            html += statCard(timeStr, 'Study Time');
            html += statCard(state.cards.length, 'Verses Reviewed');
            html += statCard(totalBlanks, 'Words Studied');
            html += statCard(state.hintsUsed, 'Hints Used');
            html += '</div>';

        } else if (state.answerMode === 'both') {
            // ── TYPE & TAP MODE: typed vs tapped breakdown ──
            document.getElementById('statsTitle').textContent = '📊 Session Complete!';
            const typedAcc = totalBlanks > 0 ? Math.round((state.correctCount / totalBlanks) * 100) : 0;
            html += statCard(timeStr, 'Study Time');
            html += statCard(state.correctCount, 'Typed Correctly');
            html += statCard(state.incorrectCount, 'Tapped to Reveal');
            html += statCard(state.hintsUsed, 'Hints Used');
            html += '</div>';

            if (totalBlanks > 0) {
                html += `<div style="text-align:center;margin-bottom:1rem;">
                    <span style="font-size:1.1rem;font-weight:600;color:var(--text-secondary);">
                        ${typedAcc}% typed · ${state.cards.length} verses
                    </span>
                </div>`;
            }

            // Show missed words only if some were typed wrong (revealed after partial typing)
            const actualMissed = state.missedWords.filter(w => w.includes('(typed'));
            if (actualMissed.length > 0) {
                html += missedWordsBlock(actualMissed);
            }

        } else {
            // ── TYPE MODE: full accuracy stats ──
            document.getElementById('statsTitle').textContent = '📊 Session Complete!';
            const acc = totalBlanks > 0 ? Math.round((state.correctCount / totalBlanks) * 100) : 0;
            html += statCard(`${acc}%`, 'Accuracy');
            html += statCard(timeStr, 'Study Time');
            html += statCard(state.correctCount, 'Correct');
            html += statCard(state.incorrectCount, 'Missed');
            html += '</div>';

            if (state.hintsUsed > 0) {
                html += `<div style="text-align:center;margin-bottom:1rem;">
                    <span style="font-size:0.95rem;color:var(--text-muted);font-weight:600;">
                        💡 ${state.hintsUsed} hint${state.hintsUsed !== 1 ? 's' : ''} used
                    </span>
                </div>`;
            }

            if (state.missedWords.length > 0) {
                html += missedWordsBlock(state.missedWords);
            }
        }

        document.getElementById('statsBody').innerHTML = html;
        document.getElementById('statsModal').classList.add('active');
    }

    function statCard(value, label) {
        return `<div class="stat-card"><div class="stat-value">${value}</div><div class="stat-label">${label}</div></div>`;
    }

    function missedWordsBlock(words) {
        return `<div class="missed-words">
            <div class="missed-words-title">Words You Missed</div>
            <div class="missed-word-list">${words.map(w => `<span class="missed-word">${w}</span>`).join('')}</div>
        </div>`;
    }

    function startNewWizard() {
        document.getElementById('cardStudyScreen').style.display = 'none';
        document.getElementById('statsModal').classList.remove('active');
        document.getElementById('wizardScreen').style.display = 'block';
        document.getElementById('mainHeader').style.display = 'flex';
        // Reset to step 1
        state.selectedSections.clear();
        state.selectedChapters.clear();
        document.querySelectorAll('.pill.selected').forEach(p => p.classList.remove('selected'));
        showWizardStep(1);
        // Make sure wizard mode is showing (not quick start)
        const qs = document.getElementById('quickStartScreen');
        if (qs.style.display !== 'none') {
            document.getElementById('quickStartToggle').click();
        }
    }
    window.startNewWizard = startNewWizard;

    function backToWizard() {
        document.getElementById('cardStudyScreen').style.display = 'none';
        document.getElementById('statsModal').classList.remove('active');
        document.getElementById('wizardScreen').style.display = 'block';
        document.getElementById('mainHeader').style.display = 'flex';
    }
    window.backToWizard = backToWizard;

    function restartSession() {
        document.getElementById('statsModal').classList.remove('active');
        // Re-randomize blanks for each card
        state.cards.forEach(card => {
            card.blanks = buildBlanks(card.text, state.wordMode, state.wordParams, state.filters);
            card.allCompleted = false;
        });
        if (state.shuffle) {
            state.cards = orderWithConstraints(state.cards, state.noAdjChapter, state.noAdjSection);
        }
        state.currentCard = 0;
        state.currentBlankIndex = 0;
        state.startTime = Date.now();
        state.correctCount = 0;
        state.incorrectCount = 0;
        state.missedWords = [];
        state.hintsUsed = 0;
        renderCard();
    }
    window.restartSession = restartSession;

    // ══════════════════════════════════════════════════════════════════
    //  QUICK START
    // ══════════════════════════════════════════════════════════════════
    document.getElementById('quickStartToggle').addEventListener('click', () => {
        const wizard = document.querySelector('#wizardScreen > .quick-start-toggle').nextElementSibling;
        const steps = document.querySelectorAll('.wizard-step');
        const qs = document.getElementById('quickStartScreen');
        const isQS = qs.style.display !== 'none';

        if (isQS) {
            // Back to wizard
            qs.style.display = 'none';
            document.getElementById('wizardProgress').style.display = 'flex';
            steps.forEach((s, i) => s.style.display = ''); // Reset
            showWizardStep(state.wizardStep);
            document.getElementById('quickStartToggle').textContent = 'Switch to Quick Start';
        } else {
            // Show quick start
            document.getElementById('wizardProgress').style.display = 'none';
            steps.forEach(s => s.style.display = 'none');
            qs.style.display = 'block';
            document.getElementById('quickStartToggle').textContent = 'Switch to Step-by-Step Wizard';
        }
    });

    document.getElementById('qsBackToWizard').addEventListener('click', () => {
        document.getElementById('quickStartToggle').click();
    });

    document.getElementById('qsStartBtn').addEventListener('click', () => {
        // Apply quick start - verse selection
        const qsVerseMode = document.getElementById('qsVerseMode').value;
        const qsVerseParam = parseInt(document.getElementById('qsVerseParam').value) || 5;
        state.verseMode = qsVerseMode;
        if (qsVerseMode.includes('pct')) state.verseParams.pct = qsVerseParam;
        else state.verseParams.x = qsVerseParam;
        state.verseWindowStart = 1;
        state.verseWindowEnd = 999;

        // Word blanking
        const qsMode = document.getElementById('qsWordMode').value;
        const qsParam = parseInt(document.getElementById('qsWordParam').value) || 40;
        state.wordMode = qsMode;
        if (qsMode.includes('pct')) state.wordParams.pct = qsParam;
        else state.wordParams.x = qsParam;

        state.filters = { minLen: 0, capsOnly: false, exclude: 'the, a, an, and, or, but, of, to, in, for, is, it', include: '' };

        const activeAM = document.querySelector('#qsAnswerToggle .toggle-group-btn.active');
        state.answerMode = activeAM ? activeAM.dataset.mode : 'both';
        state.refMode = 'show';
        state.shuffle = true;
        state.noAdjChapter = getSelectedChapters().length > 1;
        state.noAdjSection = state.selectedSections.size > 1;

        startStudy();
    });

    // QS verse mode select — show/hide param input
    document.getElementById('qsVerseMode').addEventListener('change', function() {
        const needsParam = this.value !== 'all';
        document.getElementById('qsVerseParamGroup').style.display = needsParam ? 'block' : 'none';
        // Set smart default value
        if (this.value.includes('pct')) {
            document.getElementById('qsVerseParam').value = 50;
            document.getElementById('qsVerseParamDesc').textContent = 'Percentage of verses (1-100)';
        } else {
            document.getElementById('qsVerseParam').value = 5;
            document.getElementById('qsVerseParamDesc').textContent = 'Number of verses per chapter';
        }
    });

    function toggleAdvanced() {
        document.getElementById('qsAdvanced').classList.toggle('open');
    }
    window.toggleAdvanced = toggleAdvanced;

    // ══════════════════════════════════════════════════════════════════
    //  INIT
    // ══════════════════════════════════════════════════════════════════
    function init() {
        initTheme();

        // ── Plan selector dropdowns ──
        function populatePlanSelector(selectId) {
            const el = document.getElementById(selectId);
            el.innerHTML = Object.entries(STUDY_PLANS).map(([key, plan]) =>
                `<option value="${key}" ${key === DEFAULT_PLAN ? 'selected' : ''}>${plan.name}</option>`
            ).join('');
        }
        populatePlanSelector('planSelector');
        populatePlanSelector('qsPlanSelector');

        function switchPlan(planKey) {
            state.activePlan = planKey;
            state.selectedSections.clear();
            state.selectedChapters.clear();
            // Re-render all pill containers
            renderAllPills();
        }

        document.getElementById('planSelector').addEventListener('change', function() {
            switchPlan(this.value);
            document.getElementById('qsPlanSelector').value = this.value;
        });
        document.getElementById('qsPlanSelector').addEventListener('change', function() {
            switchPlan(this.value);
            document.getElementById('planSelector').value = this.value;
        });

        // ── Render all pills for current plan ──
        function renderAllPills() {
            renderSectionPills('sectionPills', 'section');
            renderChapterPills('chapterPillsContainer', 'chapter');
            renderChapterQuickActions('chapterQuickActions', 'chapter');

            renderSectionPills('qsSectionPills', 'qs-section');
            renderChapterPills('qsChapterPillsContainer', 'qs-chapter');
            renderChapterQuickActions('qsChapterQuickActions', 'qs-chapter');
        }
        renderAllPills();

        // Wizard mode toggles
        function setupModeToggle(groupId, onSelect, showSection, hideSection) {
            document.querySelectorAll(`#${groupId} .toggle-group-btn`).forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll(`#${groupId} .toggle-group-btn`).forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const mode = btn.dataset.mode;
                    onSelect(mode);
                    document.getElementById(showSection).style.display = mode === 'section' ? 'block' : 'none';
                    document.getElementById(hideSection).style.display = mode === 'chapter' ? 'block' : 'none';
                });
            });
        }

        setupModeToggle('studyModeToggle',
            m => state.studyMode = m,
            'sectionSelection', 'chapterSelection');
        setupModeToggle('qsStudyModeToggle',
            m => state.studyMode = m,
            'qsSectionSelection', 'qsChapterSelection');

        // Answer mode toggle
        document.querySelectorAll('#answerModeToggle .toggle-group-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('#answerModeToggle .toggle-group-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                state.answerMode = btn.dataset.mode;
            });
        });

        // Quick start answer mode
        document.querySelectorAll('#qsAnswerToggle .toggle-group-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('#qsAnswerToggle .toggle-group-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });

        // Ref mode picker
        document.querySelectorAll('#refModePicker .mode-option').forEach(opt => {
            opt.addEventListener('click', () => {
                document.querySelectorAll('#refModePicker .mode-option').forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                state.refMode = opt.dataset.refmode;
            });
        });

        // Toggle switches
        function setupToggleSwitch(id, getter, setter) {
            const el = document.getElementById(id);
            el.addEventListener('click', () => {
                const val = !getter();
                setter(val);
                el.classList.toggle('active', val);
            });
        }
        setupToggleSwitch('shuffleToggle', () => state.shuffle, v => state.shuffle = v);
        setupToggleSwitch('noAdjChapterToggle', () => state.noAdjChapter, v => state.noAdjChapter = v);
        setupToggleSwitch('noAdjSectionToggle', () => state.noAdjSection, v => state.noAdjSection = v);
        setupToggleSwitch('filterCapsToggle', () => state.filters.capsOnly, v => state.filters.capsOnly = v);

        // Filter inputs
        document.getElementById('filterMinLen').addEventListener('change', function() { state.filters.minLen = parseInt(this.value) || 0; });
        document.getElementById('filterExclude').addEventListener('input', function() { state.filters.exclude = this.value; });
        document.getElementById('filterInclude').addEventListener('change', function() { state.filters.include = this.value; });
        document.getElementById('verseWindowStart').addEventListener('change', function() { state.verseWindowStart = parseInt(this.value) || 1; });
        document.getElementById('verseWindowEnd').addEventListener('change', function() { state.verseWindowEnd = parseInt(this.value) || 999; });

        // Exclude word presets
        const EXCLUDE_PRESETS = {
            none: '',
            basic: 'the, a, an, and, or, but, of, to, in, for, is, it',
            extended: 'the, a, an, and, or, but, of, to, in, for, is, it, was, were, has, had, have, are, be, been, he, she, his, her, with, by, at, on, from, not, that, this, which, who, its, them, they, their, will, shall',
            custom: '',
        };
        document.getElementById('excludePreset').addEventListener('change', function() {
            const val = this.value;
            const input = document.getElementById('filterExclude');
            if (val === 'custom') {
                input.value = '';
                input.focus();
            } else {
                input.value = EXCLUDE_PRESETS[val];
            }
            state.filters.exclude = input.value;
        });
        // If user manually edits the text field, switch dropdown to 'custom'
        document.getElementById('filterExclude').addEventListener('focus', function() {
            const preset = document.getElementById('excludePreset');
            const currentPresetVal = EXCLUDE_PRESETS[preset.value];
            if (this.value !== currentPresetVal) preset.value = 'custom';
        });

        // Mode pickers
        renderModePicker('verseModePicker', VERSE_MODES, state.verseMode, (mode) => {
            state.verseMode = mode;
            renderParams('verseParams', VERSE_MODES, mode, state.verseParams, 'verse');
        });
        renderParams('verseParams', VERSE_MODES, state.verseMode, state.verseParams, 'verse');

        renderModePicker('wordModePicker', WORD_MODES, state.wordMode, (mode) => {
            state.wordMode = mode;
            renderParams('wordParams', WORD_MODES, mode, state.wordParams, 'word');
        });
        renderParams('wordParams', WORD_MODES, state.wordMode, state.wordParams, 'word');

        // Wizard progress
        renderWizardProgress();

        // Font size buttons
        document.querySelectorAll('.font-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.font-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const size = btn.dataset.size;
                document.getElementById('cardStudyScreen').dataset.textSize = size;
                localStorage.setItem('clozeCardsFontSize', size);
            });
        });
        const savedFont = localStorage.getItem('clozeCardsFontSize') || 'md';
        document.querySelectorAll('.font-btn').forEach(b => b.classList.toggle('active', b.dataset.size === savedFont));
        document.getElementById('cardStudyScreen').dataset.textSize = savedFont;

        // Click outside modal to close
        document.getElementById('statsModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('statsModal')) {
                document.getElementById('statsModal').classList.remove('active');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
