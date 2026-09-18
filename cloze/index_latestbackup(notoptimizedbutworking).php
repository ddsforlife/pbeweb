<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBE Cloze Study</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    
    <?php 
    $version = '2.0.0';
    echo "<meta name='app-version' content='$version'>";
    
    // Function to scan folders and return book list
    function getBooksData($baseDir = 'bible') {
        $books = [];
        
        if (!is_dir($baseDir)) {
            return $books;
        }
        
        // Scan all subdirectories
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
    
    // Get chapters from CSV
    function getChaptersFromCSV($filePath) {
        if (!file_exists($filePath)) {
            return [];
        }
        
        $lines = file($filePath, FILE_SKIP_EMPTY_LINES);
        $chapters = [];
        
        $startIndex = 0;
        if (count($lines) > 0 && stripos($lines[0], 'book') !== false) {
            $startIndex = 1;
        }
        
        for ($i = $startIndex; $i < count($lines); $i++) {
            $parts = str_getcsv($lines[$i]);
            if (count($parts) >= 2) {
                $chapter = (int)$parts[1];
                if (!in_array($chapter, $chapters)) {
                    $chapters[] = $chapter;
                }
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
            --bg-primary: #FBFAF6;
            --bg-secondary: #FFFFFF;
            --bg-card: #FFFFFF;
            --text-primary: #0D153D;
            --text-secondary: #24306A;
            --text-muted: #5B647A;
            --accent: #DFB757;
            --accent-hover: #CFA23E;
            --border: #E7E1D0;
            --shadow: rgba(2, 7, 49, 0.10);
            --shadow-hover: rgba(2, 7, 49, 0.16);
            --correct: #22C55E;
            --incorrect: #EF4444;
            --current-blank: #DFB757;
            --completed-blank: #22C55E;
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
        }

        [data-theme="warm"] {
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
        }

        [data-theme="accent"] {
            --bg-primary: #F3F6FF;
            --bg-secondary: #E6EDFF;
            --bg-card: #FFFFFF;
            --text-primary: #0D153D;
            --text-secondary: #2A3C7D;
            --text-muted: #6573A8;
            --accent: #0E44B9;
            --accent-hover: #0A3593;
            --border: #C9D6FF;
            --shadow: rgba(14, 68, 185, 0.12);
            --shadow-hover: rgba(14, 68, 185, 0.18);
        }

        [data-theme="candy"] {
            --bg-primary: #FFF5F5;
            --bg-secondary: #FFE5E5;
            --bg-card: #FFFFFF;
            --text-primary: #2A0B0B;
            --text-secondary: #6A1D1D;
            --text-muted: #9B5A5A;
            --accent: #E63946;
            --accent-hover: #D62828;
            --border: #FFD0D0;
            --shadow: rgba(230, 57, 70, 0.10);
            --shadow-hover: rgba(230, 57, 70, 0.18);
        }

        [data-theme="sky"] {
            --bg-primary: #EFF6FF;
            --bg-secondary: #DCEBFF;
            --bg-card: #FFFFFF;
            --text-primary: #051879;
            --text-secondary: #233E9A;
            --text-muted: #6577B6;
            --accent: #0E44B9;
            --accent-hover: #1252D4;
            --border: #BFD5FF;
            --shadow: rgba(5, 24, 121, 0.10);
            --shadow-hover: rgba(5, 24, 121, 0.16);
        }

        [data-theme="jungle"] {
            --bg-primary: #F7F8FC;
            --bg-secondary: #ECEFF8;
            --bg-card: #FFFFFF;
            --text-primary: #020731;
            --text-secondary: #0D153D;
            --text-muted: #6B7285;
            --accent: #DFB757;
            --accent-hover: #F0D07C;
            --border: #D6DAE8;
            --shadow: rgba(13, 21, 61, 0.10);
            --shadow-hover: rgba(13, 21, 61, 0.16);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0.5rem 2rem 2rem 2rem; /* Small space above header, normal sides/bottom */
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem; /* Reduced from 1.5rem */
            padding-bottom: 0.5rem; /* Reduced from 1rem */
            border-bottom: 2px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo h1 {
            font-family: 'EB Garamond', 'Crimson Pro', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .logo-icon {
            width: 144px;
            height: 144px;
            object-fit: contain;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-family: 'IBM Plex Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent);
            color: var(--text-primary);
            box-shadow: 0 2px 8px var(--shadow);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 16px var(--shadow-hover);
            transform: translateY(-1px);
        }

        /* Dark accent button fix */
        [data-theme="warm"] .btn-primary,
        [data-theme="candy"] .btn-primary,
        [data-theme="accent"] .btn-primary,
        [data-theme="sky"] .btn-primary {
            color: white;
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 2px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--accent);
            background: var(--bg-secondary);
            transform: translateY(-1px);
        }

        .btn-icon {
            width: 48px;
            height: 48px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-card);
            border: 2px solid var(--border);
        }

        .btn-icon:hover {
            border-color: var(--accent);
            background: var(--bg-secondary);
        }

        /* Theme Selector */
        .theme-selector {
            position: relative;
        }

        .theme-button {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            border: 2px solid var(--border);
            background: var(--bg-card);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            transition: all 0.2s ease;
        }

        .theme-button:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .theme-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 0.5rem;
            min-width: 180px;
            box-shadow: 0 8px 32px var(--shadow);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        .theme-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .theme-option {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .theme-option:hover {
            background: var(--bg-secondary);
        }

        .theme-option.active {
            background: var(--accent);
            color: var(--text-primary);
        }

        [data-theme="warm"] .theme-option.active,
        [data-theme="candy"] .theme-option.active,
        [data-theme="accent"] .theme-option.active,
        [data-theme="sky"] .theme-option.active {
            color: white;
        }

        .theme-color {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid var(--border);
        }

        /* Book Selection Screen */
        #bookSelection {
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Introduction Text */
        /* Standalone Logo */
        .standalone-logo {
            width: 335px;
            height: 335px;
            object-fit: contain;
            margin: 0 auto 0.5rem auto;
            display: block;
        }

        .intro-text {
            max-width: 800px;
            margin: 0 auto 1.5rem auto;
            padding: 1.5rem 1.5rem;
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 12px;
            text-align: center;
        }

        .intro-title {
            font-family: 'EB Garamond', 'Crimson Pro', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            margin: 0 0 1rem 0;
        }

        .intro-text p {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-secondary);
            margin: 0;
        }

        .testament-section {
            margin-bottom: 2rem;
        }

        .testament-header {
            font-family: 'EB Garamond', serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.4rem; /* Reduced from 1rem (60% reduction) */
            padding-bottom: 0.2rem; /* Reduced from 0.5rem (60% reduction) */
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        .book-accordion {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            overflow: hidden;
        }

        .book-accordion:hover {
            border-color: var(--accent);
        }

        .book-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            cursor: pointer;
            user-select: none;
        }

        .book-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .book-name {
            font-family: 'EB Garamond', serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .book-info {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }

        .book-actions {
            display: flex;
            gap: 0.5rem;
        }

        .book-action-btn {
            padding: 0.35rem 0.7rem;
            font-size: 0.75rem;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            color: var(--text-secondary);
            cursor: pointer;
            font-weight: 600;
        }

        .book-action-btn:hover {
            background: var(--accent);
            color: var(--text-primary);
            border-color: var(--accent);
        }

        [data-theme="warm"] .book-action-btn:hover,
        [data-theme="candy"] .book-action-btn:hover,
        [data-theme="accent"] .book-action-btn:hover,
        [data-theme="sky"] .book-action-btn:hover {
            color: white;
        }

        .book-content {
            display: none;
        }

        .book-accordion.expanded .book-content {
            display: block;
        }

        .chapters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(45px, 1fr));
            gap: 0.5rem;
            padding: 1rem 1.25rem;
            background: var(--bg-secondary);
        }

        .chapter-pill {
            padding: 0.5rem;
            text-align: center;
            border: 2px solid var(--border);
            border-radius: 8px;
            background: var(--bg-card);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            user-select: none;
        }

        .chapter-pill:hover {
            border-color: var(--accent);
        }

        .chapter-pill.selected {
            background: var(--accent);
            color: var(--text-primary);
            border-color: var(--accent);
        }

        [data-theme="warm"] .chapter-pill.selected,
        [data-theme="candy"] .chapter-pill.selected,
        [data-theme="accent"] .chapter-pill.selected,
        [data-theme="sky"] .chapter-pill.selected {
            color: white;
        }

        /* Selection Summary */
        .selection-summary {
            position: sticky;
            bottom: 1rem;
            background: var(--bg-card);
            border: 2px solid var(--accent);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 4px 16px var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .selection-text {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .selection-count {
            color: var(--accent);
            font-weight: 700;
        }

        /* Verse Selection (Advanced Mode) */
        /* Study Screen */
        #studyScreen {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .study-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .study-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .verse-reference {
            font-family: 'EB Garamond', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            flex: 1;
        }

        .chapter-progress {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .mini-map {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .mini-map-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--border);
            transition: all 0.2s ease;
        }

        .mini-map-dot.completed {
            background: var(--completed-blank);
        }

        .mini-map-dot.current {
            background: var(--current-blank);
            transform: scale(1.3);
        }

        .passage-container {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 24px var(--shadow);
            min-height: 400px;
        }

        .passage-text {
            font-family: 'EB Garamond', serif;
            font-size: 1.5rem;
            line-height: 2;
            color: var(--text-primary);
            letter-spacing: 0.01em;
        }

        /* Verse styling */
        .verse-block {
            display: block;
            margin-bottom: 0.5rem; /* Minimal spacing between verses */
        }

        .verse-number {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--accent);
            margin-right: 0.25rem;
            display: inline;
        }

        /* Mobile: Superscript verse numbers to save space */
        @media (max-width: 768px) {
            .verse-number {
                font-size: 0.9rem;
                vertical-align: super;
                line-height: 0;
            }
        }

        .word {
            display: inline;
        }

        .blank {
            display: inline-block;
            position: relative;
            padding: 0;
            margin: 0 2px;
            border-bottom: 2px solid var(--border);
            font-family: 'IBM Plex Sans', monospace;
            font-weight: 600;
            font-size: 1.4rem;
            cursor: pointer;
            transition: border-color 0.2s ease;
            text-align: center;
            vertical-align: baseline;
            line-height: 1.0;
        }

        .blank.current {
            border-bottom-color: var(--current-blank);
        }

        .blank.completed {
            border-bottom-color: var(--completed-blank);
            color: var(--completed-blank);
            cursor: default;
        }

        .blank.incorrect {
            border-bottom-color: var(--incorrect);
        }

        /* Dark mode: Brighter underlines and colors that POP */
        [data-theme="dark"] .blank {
            border-bottom-color: #505050; /* Brighter gray for unselected clozes */
        }

        [data-theme="dark"] .blank.current {
            border-bottom-color: #FFD700; /* Bright yellow for selected cloze */
        }

        [data-theme="dark"] .blank.completed {
            border-bottom-color: #10B981; /* Bright green */
        }

        [data-theme="dark"] .letter.correct {
            color: #10B981; /* Bright vibrant green */
        }

        [data-theme="dark"] .letter.incorrect {
            color: #FF5555; /* Bright vibrant red */
        }

        .blank-input {
            display: inline-block;
            text-align: center;
            font-family: 'IBM Plex Sans', monospace;
            font-size: 1.4rem;
            font-weight: 600;
            padding: 0;
            line-height: 0.95;
        }

        .letter {
            display: inline;
        }

        .letter.correct {
            color: var(--correct);
        }

        .letter.incorrect {
            color: var(--incorrect);
        }

        .letter.hint {
            color: var(--text-muted);
            opacity: 0.6;
        }

        /* Study Controls - Multiple Layouts */
        
        /* Top Toolbar (Always visible) */
        .study-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--bg-card);
            border-bottom: 2px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            gap: 1rem;
            z-index: 900;
            box-shadow: 0 2px 8px var(--shadow);
        }

        /* Non-sticky variant for floating mode */
        .study-toolbar.non-sticky {
            position: static;
            box-shadow: none;
        }

        .toolbar-btn {
            padding: 0.6rem 1.2rem;
            border: 2px solid var(--border);
            border-radius: 10px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .toolbar-btn:hover {
            background: var(--accent);
            border-color: var(--accent);
            transform: translateY(-1px);
        }

        [data-theme="warm"] .toolbar-btn:hover,
        [data-theme="candy"] .toolbar-btn:hover,
        [data-theme="accent"] .toolbar-btn:hover,
        [data-theme="sky"] .toolbar-btn:hover {
            color: white;
        }

        .toolbar-spacer {
            flex: 1;
        }

        /* Make top toolbar layout match bottom bar */
        .study-toolbar .toolbar-btn:first-child {
            margin-right: auto; /* Push Exit to left */
        }

        .study-toolbar .toolbar-btn:last-child {
            margin-left: auto; /* Push Menu to right */
        }

        /* Floating Action Buttons (Default) */
        .floating-controls {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            z-index: 900;
        }

        .fab {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: none;
            background: var(--accent);
            color: var(--text-primary);
            font-size: 1.75rem;
            cursor: pointer;
            box-shadow: 0 4px 16px var(--shadow-hover);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fab:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 24px var(--shadow-hover);
        }

        .fab:active {
            transform: scale(0.95);
        }

        [data-theme="warm"] .fab,
        [data-theme="candy"] .fab,
        [data-theme="accent"] .fab,
        [data-theme="sky"] .fab {
            color: white;
        }

        /* Sticky Bottom Controls (Alternative) */
        .sticky-bottom-controls {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--bg-card);
            border-top: 2px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            gap: 1rem;
            z-index: 900;
            box-shadow: 0 -2px 8px var(--shadow);
        }

        .sticky-bottom-controls .toolbar-btn:first-child {
            margin-right: auto; /* Push Exit to left */
        }

        .sticky-bottom-controls .toolbar-btn:last-child {
            margin-left: auto; /* Push Menu to right */
        }

        /* Keyboard Hints (Minimal mode) */
        .keyboard-hints {
            position: fixed;
            top: 80px;
            right: 2rem;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            z-index: 900;
            box-shadow: 0 4px 16px var(--shadow);
        }

        .keyboard-hint {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .keyboard-hint:last-child {
            margin-bottom: 0;
        }

        .keyboard-hints-close {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            width: 24px;
            height: 24px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .keyboard-hints-close:hover {
            color: var(--text-primary);
            transform: scale(1.1);
        }

        /* iPad Minimal Mode Menu Button */
        .ipad-minimal-menu-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid var(--border);
            background: var(--accent);
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 900;
            box-shadow: 0 4px 12px var(--shadow);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ipad-minimal-menu-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px var(--shadow-hover);
        }

        [data-theme="warm"] .ipad-minimal-menu-btn,
        [data-theme="candy"] .ipad-minimal-menu-btn,
        [data-theme="accent"] .ipad-minimal-menu-btn,
        [data-theme="sky"] .ipad-minimal-menu-btn {
            color: white;
        }

        /* Unified Study Menu/Settings Modal */
        .modal-unified {
            max-width: 600px;
        }

        .modal-tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid var(--border);
            margin-bottom: 2rem;
        }

        .modal-tab {
            flex: 1;
            padding: 1rem;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-tab:hover {
            color: var(--text-primary);
            background: var(--bg-secondary);
        }

        .modal-tab.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        .modal-tab-content {
            display: none;
        }

        .modal-tab-content.active {
            display: block;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1.5rem;
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .menu-item:hover {
            background: var(--accent);
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        [data-theme="warm"] .menu-item:hover,
        [data-theme="candy"] .menu-item:hover,
        [data-theme="accent"] .menu-item:hover,
        [data-theme="sky"] .menu-item:hover {
            color: white;
        }

        .menu-icon {
            font-size: 2rem;
        }

        .menu-label {
            font-weight: 600;
            font-size: 0.95rem;
            text-align: center;
            color: var(--text-primary);
        }

        /* Ensure dark mode menu items have readable text */
        [data-theme="dark"] .menu-item {
            color: var(--text-primary);
        }

        [data-theme="dark"] .menu-item:hover {
            color: var(--text-primary);
        }

        /* Adjust study container for toolbar */
        .study-container {
            padding-top: 70px;
        }

        /* Remove padding when toolbar is non-sticky (floating mode) */
        .study-toolbar.non-sticky ~ .study-container,
        body:has(.study-toolbar.non-sticky) .study-container {
            padding-top: 0;
        }

        .progress-bar-container {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 12px;
            height: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* Sticky progress bar - adapts to control layout */
        .progress-bar-container.sticky {
            position: sticky;
            z-index: 800;
            margin: 0;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        /* Top Bar Mode: Stick to bottom of top toolbar */
        .progress-bar-container.sticky-top {
            top: 60px; /* Height of top toolbar */
        }

        /* Bottom Bar Mode: Stick to top of bottom toolbar */
        .progress-bar-container.sticky-bottom {
            bottom: 60px; /* Height of bottom toolbar */
            position: fixed;
            left: 0;
            right: 0;
        }

        /* Floating/Minimal Mode: Stick to very top of page */
        .progress-bar-container.sticky-floating {
            top: 0;
        }

        /* Mobile: Adjust for smaller toolbar */
        @media (max-width: 768px) {
            .progress-bar-container.sticky-top {
                top: 50px; /* Smaller mobile toolbar */
            }
            
            .progress-bar-container.sticky-bottom {
                bottom: 50px; /* Smaller mobile toolbar */
            }
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent-hover));
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 10px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 24px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            font-family: 'EB Garamond', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 2rem;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-value {
            font-family: 'EB Garamond', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .missed-words {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .missed-words-title {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.9rem;
        }

        .missed-word-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .missed-word {
            background: var(--bg-card);
            border: 2px solid var(--incorrect);
            color: var(--incorrect);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Settings Modal */
        .settings-section {
            margin-bottom: 2rem;
        }

        .settings-section-title {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.9rem;
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 12px;
            margin-bottom: 0.75rem;
        }

        .setting-label {
            font-weight: 500;
            color: var(--text-primary);
        }

        .setting-description {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        /* Toggle Switch */
        .toggle {
            position: relative;
            width: 52px;
            height: 28px;
            background: var(--border);
            border-radius: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .toggle.active {
            background: var(--accent);
        }

        .toggle-slider {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .toggle.active .toggle-slider {
            transform: translateX(24px);
        }

        /* Difficulty Selector */
        .difficulty-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-top: 1rem;
        }

        /* Answer Mode Selector */
        .answer-mode-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border);
        }

        .answer-mode-label {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .answer-mode-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .answer-mode-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--bg-card);
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .answer-mode-btn:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .answer-mode-btn.active {
            border-color: var(--accent);
            background: var(--accent);
            color: white;
        }

        .answer-mode-icon {
            font-size: 1.5rem;
        }

        /* Difficulty Options - Made Smaller */
        .difficulty-option {
            padding: 0.75rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--bg-card);
        }

        .difficulty-option:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .difficulty-option.active {
            border-color: var(--accent);
            background: var(--accent);
            color: var(--text-primary);
        }

        [data-theme="warm"] .difficulty-option.active,
        [data-theme="candy"] .difficulty-option.active,
        [data-theme="accent"] .difficulty-option.active,
        [data-theme="sky"] .difficulty-option.active {
            color: white;
        }

        .difficulty-name {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .difficulty-percent {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Control Style Selector */
        .control-style-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .control-style-option {
            padding: 1.5rem 1rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--bg-secondary);
        }

        .control-style-option:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .control-style-option.active {
            border-color: var(--accent);
            background: var(--accent);
            color: var(--text-primary);
        }

        [data-theme="warm"] .control-style-option.active,
        [data-theme="candy"] .control-style-option.active,
        [data-theme="accent"] .control-style-option.active,
        [data-theme="sky"] .control-style-option.active {
            color: white;
        }

        .control-style-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .control-style-name {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .control-style-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .control-style-option.active .control-style-desc {
            color: inherit;
            opacity: 0.9;
        }

        /* Theme Grid */
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .theme-option-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .theme-option-card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .theme-option-card.active {
            border-color: var(--accent);
            background: var(--accent);
        }

        /* Always use white text on active cards since they have colored backgrounds */
        .theme-option-card.active .theme-name {
            color: white !important;
        }

        /* Dark mode active card needs special handling */
        [data-theme="dark"] .theme-option-card.active {
            color: white;
        }

        .theme-preview-bars {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            border: 2px solid var(--border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .theme-bar {
            flex: 1;
            width: 100%;
        }

        .theme-bar:first-child {
            border-radius: 6px 6px 0 0;
        }

        .theme-bar:last-child {
            border-radius: 0 0 6px 6px;
        }

        .theme-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Option A: Force readable colors on all theme cards regardless of current theme */
        /* Dark theme card always gets light text */
        .theme-option-card[data-theme="dark"] .theme-name {
            color: #F7F5EE !important;
        }

        /* All light theme cards always get dark text */
        .theme-option-card[data-theme="light"] .theme-name,
        .theme-option-card[data-theme="warm"] .theme-name,
        .theme-option-card[data-theme="accent"] .theme-name,
        .theme-option-card[data-theme="candy"] .theme-name,
        .theme-option-card[data-theme="sky"] .theme-name,
        .theme-option-card[data-theme="jungle"] .theme-name {
            color: #0D153D !important;
        }

        /* Fix 2: When IN dark mode, make light theme card names white for visibility */
        [data-theme="dark"] .theme-option-card[data-theme="light"] .theme-name,
        [data-theme="dark"] .theme-option-card[data-theme="warm"] .theme-name,
        [data-theme="dark"] .theme-option-card[data-theme="accent"] .theme-name,
        [data-theme="dark"] .theme-option-card[data-theme="candy"] .theme-name,
        [data-theme="dark"] .theme-option-card[data-theme="sky"] .theme-name,
        [data-theme="dark"] .theme-option-card[data-theme="jungle"] .theme-name {
            color: #FFFFFF !important;
        }

        .difficulty-percent {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .difficulty-option.active .difficulty-percent {
            opacity: 0.8;
        }

        [data-theme="warm"] .difficulty-option.active .difficulty-percent,
        [data-theme="candy"] .difficulty-option.active .difficulty-percent,
        [data-theme="accent"] .difficulty-option.active .difficulty-percent,
        [data-theme="sky"] .difficulty-option.active .difficulty-percent {
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-buttons .btn {
            flex: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
            }

            .logo h1 {
                font-size: 1.5rem;
            }

            .chapters-grid {
                grid-template-columns: repeat(auto-fill, minmax(45px, 1fr));
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .passage-container {
                padding: 1.5rem;
            }

            .passage-text {
                font-size: 1.25rem;
            }

            .modal-content {
                padding: 2rem;
            }

            .difficulty-grid {
                grid-template-columns: 1fr;
            }

            .theme-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .verse-range-selector {
                grid-template-columns: 1fr;
            }

            .selection-summary {
                bottom: 1rem;
                flex-direction: column;
                text-align: center;
            }

            /* Change 3: Smaller toolbar buttons on mobile */
            .study-toolbar,
            .sticky-bottom-controls {
                height: 50px;
                padding: 0 1rem;
            }

            .toolbar-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
            }

            /* Adjust study container padding for smaller mobile toolbar */
            .study-container {
                padding-top: 60px !important;
            }

            /* Change 4: Smaller difficulty modal elements on mobile */
            .difficulty-option {
                padding: 0.5rem;
            }

            .difficulty-name {
                font-size: 0.85rem;
            }

            .difficulty-percent {
                font-size: 0.7rem;
            }
            
            /* Answer mode section mobile styles */
            .answer-mode-btn {
                padding: 0.75rem;
                font-size: 0.85rem;
            }
            
            .answer-mode-icon {
                font-size: 1.25rem;
            }
            
            .answer-mode-text {
                font-size: 0.85rem;
            }
                font-size: 0.75rem;
            }

            .modal-header {
                font-size: 1.5rem;
            }

            /* Change 5: Hide floating and minimal control layouts on mobile */
            .control-style-option[data-style="floating"],
            .control-style-option[data-style="minimal"] {
                display: none;
            }
        }

        /* Print Styles */
        @media print {
            /* Explicitly hide ALL UI elements */
            .header,
            .modal,
            .modal-content,
            #difficultyModal,
            #statsModal,
            #studyMenuModal,
            #bookSelection,
            .study-toolbar,
            #studyToolbar,
            .sticky-bottom-controls,
            #stickyBottomControls,
            .floating-controls,
            #floatingControls,
            .keyboard-hints,
            .mini-map,
            #miniMap,
            .progress-bar-container,
            #progressBarContainer,
            .progress-bar,
            #progressBar,
            .ipad-minimal-menu-btn,
            .chapter-progress,
            #chapterProgress,
            .modal-tabs,
            .modal-tab,
            .modal-tab-content,
            .action-buttons,
            button,
            .btn,
            .fab,
            .toolbar-btn {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                height: 0 !important;
                width: 0 !important;
                overflow: hidden !important;
            }
            
            /* Ensure body and containers are clean */
            body {
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            #studyScreen {
                display: block !important;
                visibility: visible !important;
                background: white !important;
            }
            
            .study-container {
                display: block !important;
                visibility: visible !important;
                padding: 1rem !important;
                margin: 0 !important;
                background: white !important;
            }
            
            .study-header {
                display: block !important;
                visibility: visible !important;
                margin-bottom: 1rem !important;
                background: white !important;
            }
            
            .verse-reference {
                display: block !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
                font-size: 18pt !important;
                font-weight: bold !important;
                margin-bottom: 1rem !important;
                border: none !important;
            }
            
            .passage-container {
                display: block !important;
                visibility: visible !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
            }
            
            .passage-text {
                display: block !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
                font-size: 14pt !important;
                line-height: 1.8 !important;
            }
            
            .verse-block {
                display: block !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
                margin-bottom: 0.5rem !important;
            }
            
            .verse-number {
                display: inline !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
                font-weight: bold !important;
                font-size: 14pt !important;
            }
            
            .word {
                display: inline !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
            }
            
            .blank {
                display: inline-block !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
                border-bottom: 2px solid black !important;
                padding: 0 !important;
            }
            
            .blank * {
                display: inline !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
            }
            
            .blank-input {
                display: inline !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
            }
            
            /* Print filled - show answers with underline */
            .blank.print-filled {
                display: inline-block !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
                text-decoration: underline !important;
                text-decoration-color: black !important;
                border-bottom: none !important;
                padding: 0 !important;
            }
            
            .blank.print-filled * {
                display: inline !important;
                visibility: visible !important;
                color: black !important;
                background: white !important;
            }
        }
                padding: 0;
                line-height: 0.95;
            }

            .blank.print-filled .letter {
                color: black !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header" style="border-bottom: none; padding-bottom: 0; margin-bottom: 0;">
        </header>

        <!-- Book Selection Screen -->
        <div id="bookSelection">
            <!-- Logo -->
            <img src="pbe_team_bold_logo.png" alt="Logo" class="standalone-logo">
            
            <!-- Introduction -->
            <div class="intro-text">
                <h1 class="intro-title">PBE Cloze Study</h1>
                <p>Fill in the blanks to test your Bible memory! Words are hidden (blank) from Scripture passages, and your job is to remember them. Choose your chapter(s) below to start reviewing!</p>
            </div>

            <!-- Old Testament -->
            <div class="testament-section">
                <h2 class="testament-header">— Old Testament —</h2>
                <div id="oldTestamentBooks"></div>
            </div>

            <!-- New Testament -->
            <div class="testament-section">
                <h2 class="testament-header">— New Testament —</h2>
                <div id="newTestamentBooks"></div>
            </div>

            <!-- Selection Summary -->
            <div class="selection-summary">
                <div class="selection-text">
                    <span class="selection-count" id="selectionCount">0 chapters selected</span>
                    <span id="selectionBooks"></span>
                </div>
                <button class="btn btn-primary" id="startStudyBtn">Start Study →</button>
            </div>
        </div>

        <!-- Study Screen -->
        <div id="studyScreen">
            <div class="study-container">
                <div class="study-header">
                    <div class="verse-reference" id="verseReference"></div>
                    <div class="chapter-progress" id="chapterProgress"></div>
                </div>

                <div class="mini-map" id="miniMap"></div>
                
                <div class="progress-bar-container" id="progressBarContainer">
                    <div class="progress-bar" id="progressBar"></div>
                </div>

                <div class="passage-container" id="passageContainer">
                    <div class="passage-text" id="passageText"></div>
                </div>

                <!-- Hidden input for mobile keyboard in type mode -->
                <input type="text" id="mobileKeyboardInput" style="position: absolute; left: -9999px; opacity: 0;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

                <!-- Floating Action Buttons (Default) -->
                <div class="floating-controls" id="floatingControls">
                    <button class="fab fab-hint" id="hintBtnFab" title="Show Hint (Ctrl+H)">💡</button>
                    <button class="fab fab-reveal" id="revealBtnFab" title="Reveal Word (Space)">👁️</button>
                </div>

                <!-- Top Toolbar -->
                <div class="study-toolbar" id="studyToolbar">
                    <button class="toolbar-btn" id="exitStudyTop">← Exit</button>
                    <button class="toolbar-btn" id="hintBtnTop">💡 Hint</button>
                    <button class="toolbar-btn" id="revealBtnTop">👁️ Reveal</button>
                    <button class="toolbar-btn" id="menuBtn">☰ Menu</button>
                </div>

                <!-- Sticky Bottom Controls (Alternative layout) -->
                <div class="sticky-bottom-controls" id="stickyBottomControls" style="display: none;">
                    <button class="toolbar-btn" id="exitStudyBottom">← Exit</button>
                    <button class="toolbar-btn" id="hintBtnBottom">💡 Hint</button>
                    <button class="toolbar-btn" id="revealBtnBottom">👁️ Reveal</button>
                    <button class="toolbar-btn" id="menuBtnBottom">☰ Menu</button>
                </div>

                <!-- Keyboard Shortcuts Hint (Minimal mode) -->
                <div class="keyboard-hints" id="keyboardHints" style="display: none;">
                    <button class="keyboard-hints-close" id="closeKeyboardHints">×</button>
                    <div class="keyboard-hint">Space: Reveal</div>
                    <div class="keyboard-hint">Ctrl+H: Hint</div>
                    <div class="keyboard-hint">Esc: Menu</div>
                </div>

                <!-- iPad Minimal Mode Menu Button -->
                <button class="ipad-minimal-menu-btn" id="ipadMinimalMenuBtn" style="display: none;">☰</button>
            </div>
        </div>

        <!-- Unified Study Menu/Settings Modal -->
        <div class="modal" id="studyMenuModal">
            <div class="modal-content modal-unified">
                <div class="modal-tabs">
                    <button class="modal-tab active" data-tab="menu">Actions</button>
                    <button class="modal-tab" data-tab="settings">Options</button>
                    <button class="modal-tab" data-tab="theme">Theme</button>
                </div>

                <!-- Menu Tab -->
                <div class="modal-tab-content active" id="menuTab">
                    <div class="menu-grid">
                        <button class="menu-item" id="printBlankBtn">
                            <span class="menu-icon">🖨️</span>
                            <span class="menu-label">Print Blank</span>
                        </button>
                        <button class="menu-item" id="printFilledBtn">
                            <span class="menu-icon">📄</span>
                            <span class="menu-label">Print Filled</span>
                        </button>
                        <button class="menu-item" id="restartBtn">
                            <span class="menu-icon">🔄</span>
                            <span class="menu-label">Restart Chapter</span>
                        </button>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="modal-tab-content" id="settingsTab">
                    <div class="settings-section">
                        <div class="settings-section-title">Difficulty Level</div>
                        <div class="difficulty-grid">
                            <div class="difficulty-option active" data-difficulty="10">
                                <div class="difficulty-name">Easy</div>
                                <div class="difficulty-percent">10% blanked</div>
                            </div>
                            <div class="difficulty-option" data-difficulty="25">
                                <div class="difficulty-name">Medium</div>
                                <div class="difficulty-percent">25% blanked</div>
                            </div>
                            <div class="difficulty-option" data-difficulty="40">
                                <div class="difficulty-name">Hard</div>
                                <div class="difficulty-percent">40% blanked</div>
                            </div>
                            <div class="difficulty-option" data-difficulty="60">
                                <div class="difficulty-name">Challenge</div>
                                <div class="difficulty-percent">60% blanked</div>
                            </div>
                            <div class="difficulty-option" data-difficulty="80">
                                <div class="difficulty-name">Extreme</div>
                                <div class="difficulty-percent">80% blanked</div>
                            </div>
                            <div class="difficulty-option" data-difficulty="100">
                                <div class="difficulty-name">Impossible</div>
                                <div class="difficulty-percent">100% blanked</div>
                            </div>
                        </div>
                    </div>

                    <div class="settings-section">
                        <div class="settings-section-title">Study Options</div>
                        
                        <div class="setting-item">
                            <div>
                                <div class="setting-label">Case Sensitive</div>
                                <div class="setting-description">Require proper capitalization for deity names (God, Lord, Jesus, etc.)</div>
                            </div>
                            <div class="toggle" id="caseSensitiveToggle">
                                <div class="toggle-slider"></div>
                            </div>
                        </div>
                    </div>

                    <div class="settings-section">
                        <div class="settings-section-title">Control Layout</div>
                        <div class="setting-description" style="margin-bottom: 1rem; color: var(--text-muted);">Choose how Reveal and Hint buttons appear during study</div>
                        
                        <div class="control-style-grid">
                            <div class="control-style-option" data-style="floating">
                                <div class="control-style-icon">🎯</div>
                                <div class="control-style-name">Floating</div>
                                <div class="control-style-desc">Bottom-right corner</div>
                            </div>
                            <div class="control-style-option active" data-style="top">
                                <div class="control-style-icon">⬆️</div>
                                <div class="control-style-name">Top Bar</div>
                                <div class="control-style-desc">Fixed toolbar at top</div>
                            </div>
                            <div class="control-style-option" data-style="bottom">
                                <div class="control-style-icon">⬇️</div>
                                <div class="control-style-name">Bottom Bar</div>
                                <div class="control-style-desc">Sticky bottom toolbar</div>
                            </div>
                            <div class="control-style-option" data-style="minimal">
                                <div class="control-style-icon">⌨️</div>
                                <div class="control-style-name">Minimal</div>
                                <div class="control-style-desc">Keyboard shortcuts only</div>
                            </div>
                        </div>
                        
                        <div class="keyboard-shortcuts-inline" style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-secondary); border-radius: 12px; border: 2px solid var(--border);">
                            <div style="font-weight: 600; margin-bottom: 0.75rem; font-size: 0.95rem;">⌨️ Keyboard Shortcuts</div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: var(--text-secondary);">Reveal word</span>
                                    <kbd style="padding: 0.25rem 0.5rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px; font-family: monospace; font-size: 0.85rem;">Space</kbd>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: var(--text-secondary);">Show hint</span>
                                    <kbd style="padding: 0.25rem 0.5rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px; font-family: monospace; font-size: 0.85rem;">Ctrl+H</kbd>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: var(--text-secondary);">Open menu</span>
                                    <kbd style="padding: 0.25rem 0.5rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px; font-family: monospace; font-size: 0.85rem;">Esc</kbd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Theme Tab -->
                <div class="modal-tab-content" id="themeTab">
                    <div class="settings-section">
                        <div class="settings-section-title">Color Themes</div>
                        <div class="setting-description" style="margin-bottom: 1.5rem; color: var(--text-muted);">Choose a color scheme for the study interface</div>
                        
                        <div class="theme-grid">
                            <div class="theme-option-card" data-theme="light">
                                <div class="theme-preview-bars">
                                    <div class="theme-bar" style="background: #FBFAF6;"></div>
                                    <div class="theme-bar" style="background: #FFFFFF;"></div>
                                    <div class="theme-bar" style="background: #DFB757;"></div>
                                </div>
                                <div class="theme-name">Classic</div>
                            </div>
                            <div class="theme-option-card" data-theme="dark">
                                <div class="theme-preview-bars">
                                    <div class="theme-bar" style="background: #000000;"></div>
                                    <div class="theme-bar" style="background: #0A0A0A;"></div>
                                    <div class="theme-bar" style="background: #D20702;"></div>
                                </div>
                                <div class="theme-name">Dark</div>
                            </div>
                            <div class="theme-option-card" data-theme="warm">
                                <div class="theme-preview-bars">
                                    <div class="theme-bar" style="background: #FFFBF4;"></div>
                                    <div class="theme-bar" style="background: #FDF3E0;"></div>
                                    <div class="theme-bar" style="background: #D20702;"></div>
                                </div>
                                <div class="theme-name">Warm</div>
                            </div>
                            <div class="theme-option-card" data-theme="accent">
                                <div class="theme-preview-bars">
                                    <div class="theme-bar" style="background: #F3F6FF;"></div>
                                    <div class="theme-bar" style="background: #E6EDFF;"></div>
                                    <div class="theme-bar" style="background: #0E44B9;"></div>
                                </div>
                                <div class="theme-name">Accent</div>
                            </div>
                            <div class="theme-option-card" data-theme="candy">
                                <div class="theme-preview-bars">
                                    <div class="theme-bar" style="background: #FFF5F5;"></div>
                                    <div class="theme-bar" style="background: #FFE5E5;"></div>
                                    <div class="theme-bar" style="background: #E63946;"></div>
                                </div>
                                <div class="theme-name">Candy</div>
                            </div>
                            <div class="theme-option-card" data-theme="sky">
                                <div class="theme-preview-bars">
                                    <div class="theme-bar" style="background: #EFF6FF;"></div>
                                    <div class="theme-bar" style="background: #DCEBFF;"></div>
                                    <div class="theme-bar" style="background: #0E44B9;"></div>
                                </div>
                                <div class="theme-name">Sky</div>
                            </div>
                            <div class="theme-option-card" data-theme="jungle">
                                <div class="theme-preview-bars">
                                    <div class="theme-bar" style="background: #F7F8FC;"></div>
                                    <div class="theme-bar" style="background: #ECEFF8;"></div>
                                    <div class="theme-bar" style="background: #DFB757;"></div>
                                </div>
                                <div class="theme-name">Jungle</div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-secondary" id="closeMenuBtn" style="margin-top: 2rem; width: 100%;">Close</button>
            </div>
        </div>

        <!-- Difficulty Selection Modal -->
        <div class="modal active" id="difficultyModal" style="display: none;">
            <div class="modal-content" style="max-width: 550px;">
                <h2 class="modal-header">Choose Difficulty Level</h2>
                
                <!-- Answer Mode Selector -->
                <div class="answer-mode-section">
                    <div class="answer-mode-label">Answer Mode:</div>
                    <div class="answer-mode-buttons">
                        <button class="answer-mode-btn active" data-mode="type">
                            <span class="answer-mode-icon">⌨️</span>
                            <span class="answer-mode-text">Type Answers</span>
                        </button>
                        <button class="answer-mode-btn" data-mode="reveal">
                            <span class="answer-mode-icon">👆</span>
                            <span class="answer-mode-text">Tap to Reveal</span>
                        </button>
                    </div>
                </div>
                
                <div class="difficulty-grid">
                    <div class="difficulty-option active" data-difficulty="10">
                        <div class="difficulty-name">Easy</div>
                        <div class="difficulty-percent">10% blanked</div>
                    </div>
                    <div class="difficulty-option" data-difficulty="25">
                        <div class="difficulty-name">Medium</div>
                        <div class="difficulty-percent">25% blanked</div>
                    </div>
                    <div class="difficulty-option" data-difficulty="40">
                        <div class="difficulty-name">Hard</div>
                        <div class="difficulty-percent">40% blanked</div>
                    </div>
                    <div class="difficulty-option" data-difficulty="60">
                        <div class="difficulty-name">Challenge</div>
                        <div class="difficulty-percent">60% blanked</div>
                    </div>
                    <div class="difficulty-option" data-difficulty="80">
                        <div class="difficulty-name">Extreme</div>
                        <div class="difficulty-percent">80% blanked</div>
                    </div>
                    <div class="difficulty-option" data-difficulty="100">
                        <div class="difficulty-name">Impossible</div>
                        <div class="difficulty-percent">100% blanked</div>
                    </div>
                </div>

                <button class="btn btn-primary" id="startWithDifficulty" style="margin-top: 2rem; width: 100%;">Begin Study →</button>
            </div>
        </div>

        <!-- Stats Modal -->
        <div class="modal" id="statsModal">
            <div class="modal-content">
                <h2 class="modal-header">📊 Chapter Complete!</h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value" id="accuracyStat">0%</div>
                        <div class="stat-label">Accuracy</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="timeStat">0:00</div>
                        <div class="stat-label">Time</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="correctStat">0</div>
                        <div class="stat-label">Correct</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="incorrectStat">0</div>
                        <div class="stat-label">Incorrect</div>
                    </div>
                </div>

                <div class="missed-words" id="missedWordsContainer" style="display: none;">
                    <div class="missed-words-title">Words You Missed</div>
                    <div class="missed-word-list" id="missedWordsList"></div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-secondary" id="backToSelection">← Start Over</button>
                    <button class="btn btn-primary" id="continueStudy">Next Chapter →</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Biblical Order - 66 Books
        const BIBLICAL_ORDER = {
            'Old Testament': [
                'Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy',
                'Joshua', 'Judges', 'Ruth', '1 Samuel', '2 Samuel',
                '1 Kings', '2 Kings', '1 Chronicles', '2 Chronicles',
                'Ezra', 'Nehemiah', 'Esther', 'Job', 'Psalms', 'Proverbs',
                'Ecclesiastes', 'Song of Solomon', 'Isaiah', 'Jeremiah', 'Lamentations',
                'Ezekiel', 'Daniel', 'Hosea', 'Joel', 'Amos',
                'Obadiah', 'Jonah', 'Micah', 'Nahum', 'Habakkuk',
                'Zephaniah', 'Haggai', 'Zechariah', 'Malachi'
            ],
            'New Testament': [
                'Matthew', 'Mark', 'Luke', 'John', 'Acts',
                'Romans', '1 Corinthians', '2 Corinthians', 'Galatians', 'Ephesians',
                'Philippians', 'Colossians', '1 Thessalonians', '2 Thessalonians',
                '1 Timothy', '2 Timothy', 'Titus', 'Philemon', 'Hebrews',
                'James', '1 Peter', '2 Peter', '1 John', '2 John',
                '3 John', 'Jude', 'Revelation'
            ]
        };

        // State
        const state = {
            selectedChapters: new Map(), // bookName -> Set of chapter numbers
            currentChapterList: [], // Ordered list of {book, chapter} to study
            currentChapterIndex: 0,
            currentVerses: [],
            blanks: [],
            currentBlankIndex: 0,
            startTime: null,
            correctCount: 0,
            incorrectCount: 0,
            missedWords: [],
            chapterStats: [], // Stats for each chapter
            settings: {
                difficulty: 10,
                caseSensitive: false,
                controlStyle: 'top', // floating, top, bottom, minimal - default to top
                inputMode: (window.innerWidth <= 768) ? 'reveal' : 'type' // Mobile default: reveal, Desktop default: type
            },
            lastStudied: null
        };

        // Theological words
        const THEOLOGICAL_WORDS = new Set([
            'god', 'lord', 'jesus', 'christ', 'spirit', 'holy', 'father', 'son',
            'salvation', 'grace', 'faith', 'righteousness', 'covenant', 'mercy',
            'love', 'glory', 'heaven', 'kingdom', 'prophet', 'disciple', 'apostle',
            'messiah', 'savior', 'redeemer', 'shepherd', 'lamb', 'almighty',
            'yahweh', 'jehovah', 'emanuel', 'emmanuel', 'blessed', 'praise',
            'worship', 'prayer', 'repentance', 'forgiveness', 'eternal', 'resurrection'
        ]);

        // Deity names - words that should be case-sensitive when case sensitivity is enabled
        const DEITY_NAMES = new Set([
            'god', 'lord', 'jesus', 'christ', 'spirit', 'father', 'son',
            'messiah', 'savior', 'redeemer', 'shepherd', 'lamb', 'almighty',
            'yahweh', 'jehovah', 'emanuel', 'emmanuel', 'holy'
        ]);

        const ARTICLES_PREPOSITIONS = new Set([
            'a', 'an', 'the', 'of', 'to', 'in', 'for', 'on', 'at', 'by',
            'with', 'from', 'and', 'or', 'but', 'is', 'are', 'was', 'were',
            'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did'
        ]);

        // DOM
        const DOM = {
            header: document.querySelector('.header'),
            bookSelection: document.getElementById('bookSelection'),
            studyScreen: document.getElementById('studyScreen'),
            oldTestamentBooks: document.getElementById('oldTestamentBooks'),
            newTestamentBooks: document.getElementById('newTestamentBooks'),
            selectionCount: document.getElementById('selectionCount'),
            selectionBooks: document.getElementById('selectionBooks'),
            passageText: document.getElementById('passageText'),
            passageContainer: document.getElementById('passageContainer'),
            verseReference: document.getElementById('verseReference'),
            chapterProgress: document.getElementById('chapterProgress'),
            progressBar: document.getElementById('progressBar'),
            progressBarContainer: document.getElementById('progressBarContainer'),
            miniMap: document.getElementById('miniMap'),
            statsModal: document.getElementById('statsModal'),
            studyMenuModal: document.getElementById('studyMenuModal'),
            difficultyModal: document.getElementById('difficultyModal'),
            floatingControls: document.getElementById('floatingControls'),
            studyToolbar: document.getElementById('studyToolbar'),
            stickyBottomControls: document.getElementById('stickyBottomControls'),
            keyboardHints: document.getElementById('keyboardHints'),
            studyContainer: document.querySelector('.study-container'),
            ipadMinimalMenuBtn: document.getElementById('ipadMinimalMenuBtn')
        };

        // Initialize
        function init() {
            initTheme();
            loadSettings();
            loadLastStudied();
            renderBooks();
            initEvents();
        }

        // Theme
        function initTheme() {
            const savedTheme = localStorage.getItem('bibleClozeTheme') || 'warm';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeSelection(savedTheme);
        }

        function updateThemeSelection(theme) {
            document.querySelectorAll('.theme-option-card').forEach(opt => {
                opt.classList.toggle('active', opt.dataset.theme === theme);
            });
        }

        // Settings
        function loadSettings() {
            const saved = localStorage.getItem('bibleClozeSettings');
            if (saved) {
                state.settings = { ...state.settings, ...JSON.parse(saved) };
            }
            applySettings();
        }

        function saveSettings() {
            localStorage.setItem('bibleClozeSettings', JSON.stringify(state.settings));
        }

        function applySettings() {
            document.querySelectorAll('.difficulty-option').forEach(opt => {
                opt.classList.toggle('active', parseInt(opt.dataset.difficulty) === state.settings.difficulty);
            });

            document.getElementById('caseSensitiveToggle').classList.toggle('active', state.settings.caseSensitive);

            // Apply control style
            document.querySelectorAll('.control-style-option').forEach(opt => {
                opt.classList.toggle('active', opt.dataset.style === state.settings.controlStyle);
            });
            applyControlStyle();
        }

        function applyControlStyle() {
            let style = state.settings.controlStyle;
            
            // Change 5: On mobile, force top bar if floating or minimal is selected
            const isMobile = window.innerWidth <= 768;
            if (isMobile && (style === 'floating' || style === 'minimal')) {
                style = 'top';
                state.settings.controlStyle = 'top';
                saveSettings();
            }
            
            // Detect iPad (tablet range: 768px - 1024px)
            const isIPad = window.innerWidth > 768 && window.innerWidth <= 1224;
            
            // Hide all control layouts first
            DOM.floatingControls.style.display = 'none';
            DOM.studyToolbar.style.display = 'none';
            DOM.stickyBottomControls.style.display = 'none';
            DOM.keyboardHints.style.display = 'none';
            DOM.ipadMinimalMenuBtn.style.display = 'none'; // Hide iPad menu button by default
            
            // Remove all progress bar sticky classes
            DOM.progressBarContainer.classList.remove('sticky', 'sticky-top', 'sticky-bottom', 'sticky-floating');
            
            // Show selected layout
            if (style === 'floating') {
                // Floating: Show FABs + non-sticky top toolbar
                DOM.floatingControls.style.display = 'flex';
                DOM.studyToolbar.style.display = 'flex';
                DOM.studyToolbar.classList.add('non-sticky');
                if (DOM.studyContainer) DOM.studyContainer.style.paddingTop = '0';
                
                // Sticky progress bar at very top
                DOM.progressBarContainer.classList.add('sticky', 'sticky-floating');
            } else if (style === 'top') {
                // Top bar: Show sticky toolbar with all buttons
                DOM.studyToolbar.style.display = 'flex';
                DOM.studyToolbar.classList.remove('non-sticky');
                if (DOM.studyContainer) DOM.studyContainer.style.paddingTop = '80px';
                
                // Sticky progress bar below top toolbar
                DOM.progressBarContainer.classList.add('sticky', 'sticky-top');
            } else if (style === 'bottom') {
                // Bottom bar: Only show bottom bar (no top toolbar)
                DOM.stickyBottomControls.style.display = 'flex';
                DOM.studyToolbar.classList.remove('non-sticky');
                if (DOM.studyContainer) DOM.studyContainer.style.paddingTop = '0';
                
                // Sticky progress bar above bottom toolbar
                DOM.progressBarContainer.classList.add('sticky', 'sticky-bottom');
            } else if (style === 'minimal') {
                // Minimal: Only keyboard hints (no toolbars)
                DOM.keyboardHints.style.display = 'block';
                DOM.studyToolbar.classList.remove('non-sticky');
                if (DOM.studyContainer) DOM.studyContainer.style.paddingTop = '0';
                
                // Sticky progress bar at very top
                DOM.progressBarContainer.classList.add('sticky', 'sticky-floating');
                
                // Change 1: Show iPad menu button in minimal mode on iPad only
                if (isIPad) {
                    DOM.ipadMinimalMenuBtn.style.display = 'flex';
                }
            }
        }

        // Last Studied
        function loadLastStudied() {
            const saved = localStorage.getItem('bibleClozeLastStudied');
            if (saved) {
                state.lastStudied = JSON.parse(saved);
            }
        }

        function saveLastStudied() {
            const chaptersArray = Array.from(state.selectedChapters.entries()).map(([book, chapters]) => ({
                book,
                chapters: Array.from(chapters)
            }));
            localStorage.setItem('bibleClozeLastStudied', JSON.stringify(chaptersArray));
        }

        // Render Books
        function renderBooks() {
            ['Old Testament', 'New Testament'].forEach(testament => {
                const container = testament === 'Old Testament' ? DOM.oldTestamentBooks : DOM.newTestamentBooks;
                const books = BIBLICAL_ORDER[testament];
                
                container.innerHTML = books.map(bookName => {
                    const bookData = BOOKS_DATA[bookName];
                    if (!bookData) return '';
                    
                    return `
                        <div class="book-accordion" data-book="${bookName}">
                            <div class="book-header" onclick="toggleBook('${bookName}')">
                                <div class="book-title">
                                    <div>
                                        <div class="book-name">${bookName} <span class="book-info">(${bookData.totalChapters} chapters)</span></div>
                                    </div>
                                </div>
                                <div class="book-actions" onclick="event.stopPropagation()">
                                    <button class="book-action-btn" onclick="selectAllChapters('${bookName}')">All</button>
                                    <button class="book-action-btn" onclick="selectNoneChapters('${bookName}')">None</button>
                                </div>
                            </div>
                            <div class="book-content">
                                <div class="chapters-grid">
                                    ${bookData.chapters.map(ch => `
                                        <div class="chapter-pill" data-book="${bookName}" data-chapter="${ch}" onclick="toggleChapter('${bookName}', ${ch})">
                                            ${ch}
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            });
        }

        // Toggle Book Accordion
        function toggleBook(bookName) {
            const accordion = document.querySelector(`.book-accordion[data-book="${bookName}"]`);
            accordion.classList.toggle('expanded');
        }
        window.toggleBook = toggleBook;

        // Chapter Selection
        function toggleChapter(bookName, chapter) {
            if (!state.selectedChapters.has(bookName)) {
                state.selectedChapters.set(bookName, new Set());
            }
            
            const chapters = state.selectedChapters.get(bookName);
            if (chapters.has(chapter)) {
                chapters.delete(chapter);
                if (chapters.size === 0) {
                    state.selectedChapters.delete(bookName);
                }
            } else {
                chapters.add(chapter);
            }
            
            updateChapterPillUI(bookName, chapter);
            updateSelectionSummary();
        }
        window.toggleChapter = toggleChapter;

        function updateChapterPillUI(bookName, chapter) {
            const pill = document.querySelector(`.chapter-pill[data-book="${bookName}"][data-chapter="${chapter}"]`);
            if (pill) {
                const isSelected = state.selectedChapters.has(bookName) && state.selectedChapters.get(bookName).has(chapter);
                pill.classList.toggle('selected', isSelected);
            }
        }

        function selectAllChapters(bookName) {
            const bookData = BOOKS_DATA[bookName];
            if (!bookData) return;
            
            state.selectedChapters.set(bookName, new Set(bookData.chapters));
            
            bookData.chapters.forEach(ch => updateChapterPillUI(bookName, ch));
            updateSelectionSummary();
        }
        window.selectAllChapters = selectAllChapters;

        function selectNoneChapters(bookName) {
            state.selectedChapters.delete(bookName);
            
            const bookData = BOOKS_DATA[bookName];
            if (bookData) {
                bookData.chapters.forEach(ch => updateChapterPillUI(bookName, ch));
            }
            updateSelectionSummary();
        }
        window.selectNoneChapters = selectNoneChapters;

        // Selection Summary
        function updateSelectionSummary() {
            let totalChapters = 0;
            const bookCount = state.selectedChapters.size;
            
            state.selectedChapters.forEach(chapters => {
                totalChapters += chapters.size;
            });
            
            DOM.selectionCount.textContent = `${totalChapters} chapter${totalChapters !== 1 ? 's' : ''} selected`;
            
            if (bookCount > 0) {
                DOM.selectionBooks.textContent = ` from ${bookCount} book${bookCount !== 1 ? 's' : ''}`;
            } else {
                DOM.selectionBooks.textContent = '';
            }
        }

        // Start Study
        document.getElementById('startStudyBtn').addEventListener('click', () => {
            if (state.selectedChapters.size === 0) {
                alert('Please select at least one chapter to study.');
                return;
            }
            
            // Show difficulty selection modal
            DOM.difficultyModal.style.display = 'flex';
            
            // Update active difficulty option in modal
            document.querySelectorAll('#difficultyModal .difficulty-option').forEach(opt => {
                opt.classList.toggle('active', parseInt(opt.dataset.difficulty) === state.settings.difficulty);
            });
            
            // Update active answer mode button
            document.querySelectorAll('.answer-mode-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.mode === state.settings.inputMode);
            });
        });

        // Start with selected difficulty
        document.getElementById('startWithDifficulty').addEventListener('click', () => {
            // Hide difficulty modal
            DOM.difficultyModal.style.display = 'none';
            
            // Build ordered chapter list in biblical order
            state.currentChapterList = [];
            
            ['Old Testament', 'New Testament'].forEach(testament => {
                BIBLICAL_ORDER[testament].forEach(bookName => {
                    if (state.selectedChapters.has(bookName)) {
                        const chapters = Array.from(state.selectedChapters.get(bookName)).sort((a, b) => a - b);
                        chapters.forEach(chapter => {
                            state.currentChapterList.push({ book: bookName, chapter });
                        });
                    }
                });
            });
            
            state.currentChapterIndex = 0;
            state.chapterStats = [];
            
            saveLastStudied();
            startChapter();
        });

        // Start Chapter
        async function startChapter() {
            if (state.currentChapterIndex >= state.currentChapterList.length) {
                showFinalStats();
                return;
            }
            
            const { book, chapter } = state.currentChapterList[state.currentChapterIndex];
            const bookData = BOOKS_DATA[book];
            
            // Load verses for this chapter
            try {
                const response = await fetch(bookData.path);
                const text = await response.text();
                const allVerses = parseCSV(text);
                state.currentVerses = allVerses.filter(v => v.chapter === chapter);
                
                state.startTime = Date.now();
                state.correctCount = 0;
                state.incorrectCount = 0;
                state.missedWords = [];
                state.currentBlankIndex = 0;
                
                // Build passage
                const passageText = state.currentVerses.map(v => v.text.trim()).join(' ');
                state.blanks = createBlanks(passageText);
                
                // Update UI
                DOM.verseReference.textContent = `${book} ${chapter}`;
                DOM.chapterProgress.textContent = `Chapter ${state.currentChapterIndex + 1} of ${state.currentChapterList.length}`;
                
                renderMiniMap();
                renderPassage();
                
                DOM.bookSelection.style.display = 'none';
                DOM.studyScreen.style.display = 'block';
                DOM.header.style.display = 'none'; // Hide header during study
                
                setTimeout(() => focusBlank(0), 100);
            } catch (error) {
                console.error('Error loading chapter:', error);
                alert('Error loading chapter. Please try again.');
            }
        }

        // Parse CSV
        function parseCSV(text) {
            const lines = text.trim().split('\n');
            const verses = [];
            
            const startIndex = lines[0].toLowerCase().includes('book') ? 1 : 0;
            
            for (let i = startIndex; i < lines.length; i++) {
                const parts = lines[i].match(/(?:\"([^\"]*)\"|([^,]+))(?:,|$)/g).map(p => p.replace(/^"|"$/g, '').replace(/,$/, ''));
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

        // Mini Map
        function renderMiniMap() {
            if (state.currentChapterList.length <= 1) {
                // Single chapter/verse study - hide mini-map
                DOM.miniMap.style.display = 'none';
                return;
            }
            
            DOM.miniMap.style.display = 'flex';
            DOM.miniMap.innerHTML = state.currentChapterList.map((_, i) => {
                let className = 'mini-map-dot';
                if (i < state.currentChapterIndex) className += ' completed';
                if (i === state.currentChapterIndex) className += ' current';
                return `<div class="${className}"></div>`;
            }).join('');
        }

        // Create Blanks
        function createBlanks(text) {
            const blanks = [];
            let globalWordIndex = 0; // Track position across entire passage
            
            // Process each verse individually
            state.currentVerses.forEach(verse => {
                const verseText = verse.text.trim();
                const words = verseText.split(/\s+/);
                const verseWordCount = words.length;
                
                // Calculate blanks needed for THIS verse
                // Always round up, minimum 1 blank per verse
                const targetBlanks = Math.max(1, Math.ceil(verseWordCount * (state.settings.difficulty / 100)));
                
                // Score each word in THIS verse
                const scoredWords = words.map((word, localIndex) => {
                    let score = 0;
                    const cleanWord = word.toLowerCase().replace(/[^a-z]/g, '');
                    
                    // Skip very short words at low difficulty
                    if (cleanWord.length < 2 && state.settings.difficulty < 100) {
                        return { word, localIndex, globalIndex: globalWordIndex + localIndex, score: -1000 };
                    }
                    
                    // Quality scoring (Option A)
                    if (THEOLOGICAL_WORDS.has(cleanWord)) score += 100; // Theological terms
                    score += cleanWord.length * 2; // Longer words
                    if (word[0] === word[0].toUpperCase() && word[0] !== word[0].toLowerCase()) score += 30; // Proper nouns
                    if (/[qxz]/i.test(cleanWord)) score += 20; // Unusual letters
                    if (ARTICLES_PREPOSITIONS.has(cleanWord)) score -= 50; // Penalize articles
                    
                    return { word, localIndex, globalIndex: globalWordIndex + localIndex, score };
                });
                
                // Filter out invalid words and sort by score
                const validWords = scoredWords.filter(w => w.score > -1000);
                
                if (validWords.length === 0) {
                    // Edge case: verse has no valid words (very rare)
                    globalWordIndex += verseWordCount;
                    return;
                }
                
                // Option A + C: Quality scoring + Even distribution
                // Divide verse into sections and pick best word from each section
                const selectedWords = selectWordsEvenly(validWords, targetBlanks);
                
                // Add blanks for this verse
                selectedWords.forEach(wordData => {
                    const word = wordData.word;
                    const cleanWord = word.replace(/[^a-zA-Z]/g, '');
                    const leadingPunct = word.match(/^[^a-zA-Z]+/)?.[0] || '';
                    const trailingPunct = word.match(/[^a-zA-Z]+$/)?.[0] || '';
                    
                    blanks.push({
                        index: wordData.globalIndex,
                        word: cleanWord,
                        originalWord: word,
                        leadingPunct,
                        trailingPunct,
                        userInput: '',
                        completed: false,
                        correct: false,
                        hintShown: false
                    });
                });
                
                // Move global index forward
                globalWordIndex += verseWordCount;
            });
            
            return blanks.sort((a, b) => a.index - b.index);
        }
        
        // Helper function: Select words evenly distributed across verse
        function selectWordsEvenly(validWords, targetBlanks) {
            if (validWords.length === 0) return [];
            if (targetBlanks >= validWords.length) {
                // Need all valid words
                return validWords;
            }
            
            // Divide verse into sections
            const sectionSize = validWords.length / targetBlanks;
            const selectedWords = [];
            
            for (let i = 0; i < targetBlanks; i++) {
                // Define section boundaries
                const sectionStart = Math.floor(i * sectionSize);
                const sectionEnd = Math.floor((i + 1) * sectionSize);
                
                // Get words in this section
                const sectionWords = validWords.filter(w => 
                    w.localIndex >= sectionStart && w.localIndex < sectionEnd
                );
                
                if (sectionWords.length > 0) {
                    // Pick the highest-scoring word from this section
                    const bestWord = sectionWords.reduce((best, current) => 
                        current.score > best.score ? current : best
                    );
                    selectedWords.push(bestWord);
                } else {
                    // Edge case: empty section, pick from adjacent section
                    const fallbackWords = validWords.filter(w => !selectedWords.includes(w));
                    if (fallbackWords.length > 0) {
                        selectedWords.push(fallbackWords[0]);
                    }
                }
            }
            
            return selectedWords;
        }

        // Render Passage
        function renderPassage() {
            let html = '';
            let globalWordIndex = 0; // Track word position across all verses
            
            // Process each verse separately
            state.currentVerses.forEach((verse, verseIdx) => {
                const verseText = verse.text.trim();
                const words = verseText.split(/\s+/);
                const blankIndices = new Set(state.blanks.map(b => b.index));
                
                // Add verse number and container
                html += `<div class="verse-block">`;
                html += `<span class="verse-number">${verse.verse}</span> `;
                
                // Process words in this verse
                words.forEach((word, wordIdx) => {
                    if (blankIndices.has(globalWordIndex)) {
                        const blank = state.blanks.find(b => b.index === globalWordIndex);
                        const blankIndex = state.blanks.indexOf(blank);
                        const isCurrent = blankIndex === state.currentBlankIndex;
                        const isCompleted = blank.completed;
                        
                        let classes = 'blank';
                        if (isCurrent) classes += ' current';
                        if (isCompleted) classes += ' completed';
                        
                        // Calculate width based on word length (approx 20px per character for safe spacing)
                        const wordLength = blank.word.length || 3;
                        const blankWidth = Math.max(wordLength * 20, 50); // Minimum 50px
                        
                        let displayText = '';
                        if (isCompleted) {
                            // Show the completed word
                            displayText = blank.userInput || blank.word;
                        } else if (blank.hintShown) {
                            // Show hint: first and last letter in muted color
                            const first = blank.word[0] || '';
                            const last = blank.word.length > 1 ? blank.word[blank.word.length - 1] : '';
                            const middle = '&nbsp;'.repeat(Math.max(blank.word.length - 2, 1));
                            displayText = `<span class="letter hint">${first}</span>${middle}<span class="letter hint">${last}</span>`;
                        } else if (isCurrent && blank.userInput) {
                            // Show what user has typed with color coding (only if they've typed something)
                            displayText = renderLetters(blank);
                        } else {
                            // Show empty space (no dashes/underscores)
                            displayText = '&nbsp;'.repeat(blank.word.length || 3);
                        }
                        
                        // Include punctuation around the blank with dynamic width
                        html += blank.leadingPunct;
                        html += `<span class="${classes}" style="width: ${blankWidth}px;" data-blank-index="${blankIndex}" onclick="handleBlankClick(event, ${blankIndex})" ontouchend="handleBlankTouch(event, ${blankIndex})"><span class="blank-input">${displayText}</span></span>`;
                        html += blank.trailingPunct;
                        html += ' ';
                    } else {
                        html += `<span class="word">${word}</span> `;
                    }
                    
                    globalWordIndex++;
                });
                
                html += `</div>`; // Close verse-block
            });
            
            DOM.passageText.innerHTML = html;
            updateProgressBar();
        }

        function renderLetters(blank) {
            // Only apply case sensitivity to deity names when setting is enabled
            const isDeityName = DEITY_NAMES.has(blank.word.toLowerCase());
            const shouldBeCaseSensitive = state.settings.caseSensitive && isDeityName;
            
            const target = shouldBeCaseSensitive ? blank.word : blank.word.toLowerCase();
            const input = shouldBeCaseSensitive ? blank.userInput : blank.userInput.toLowerCase();
            
            let html = '';
            for (let i = 0; i < target.length; i++) {
                if (i < input.length) {
                    const letter = blank.userInput[i];
                    const isCorrect = input[i] === target[i];
                    const className = isCorrect ? 'letter correct' : 'letter incorrect';
                    html += `<span class="${className}">${letter}</span>`;
                } else {
                    // Empty space for remaining letters
                    html += '<span class="letter">&nbsp;</span>';
                }
            }
            
            return html;
        }

        function updateProgressBar() {
            const completed = state.blanks.filter(b => b.completed).length;
            const total = state.blanks.length;
            const percent = (completed / total) * 100;
            DOM.progressBar.style.width = `${percent}%`;
        }

        function focusBlank(index) {
            if (index < 0 || index >= state.blanks.length) return;
            
            state.currentBlankIndex = index;
            renderPassage();
            
            const blankElement = document.querySelector(`.blank[data-blank-index="${index}"]`);
            if (blankElement) {
                blankElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function jumpToBlank(index) {
            if (!state.blanks[index].completed) {
                focusBlank(index);
            }
        }
        window.jumpToBlank = jumpToBlank;

        // Handle blank click (desktop/mouse)
        function handleBlankClick(event, index) {
            // Check if this is a touch device
            if (event.touches || event.changedTouches || 'ontouchstart' in window) {
                return; // Let handleBlankTouch handle it
            }
            
            const blank = state.blanks[index];
            
            // MODE: Type Answers
            if (state.settings.inputMode === 'type') {
                // Desktop typing mode - just jump to the blank
                if (!blank.completed) {
                    jumpToBlank(index);
                }
            } 
            // MODE: Tap to Reveal
            else {
                // Desktop reveal mode - toggle reveal/hide
                if (!blank.completed && index === state.currentBlankIndex) {
                    revealAndContinue();
                } else if (blank.completed) {
                    // Toggle hide if already revealed
                    blank.completed = false;
                    blank.userInput = '';
                    state.currentBlankIndex = index;
                    renderPassage();
                } else {
                    focusBlank(index);
                }
            }
        }
        window.handleBlankClick = handleBlankClick;

        // Handle blank touch (mobile/tablet)
        function handleBlankTouch(event, index) {
            event.preventDefault();
            const blank = state.blanks[index];
            
            // MODE: Type Answers
            if (state.settings.inputMode === 'type') {
                // Mobile typing mode - focus hidden input to trigger keyboard
                if (!blank.completed) {
                    state.currentBlankIndex = index;
                    renderPassage();
                    
                    // Focus hidden input to trigger mobile keyboard
                    const mobileInput = document.getElementById('mobileKeyboardInput');
                    mobileInput.value = blank.userInput || '';
                    mobileInput.focus();
                    
                    // Handle input from mobile keyboard
                    mobileInput.oninput = () => {
                        const currentBlank = state.blanks[state.currentBlankIndex];
                        currentBlank.userInput = mobileInput.value;
                        renderPassage();
                    };
                    
                    // Handle Enter key on mobile
                    mobileInput.onkeydown = (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            checkAnswer();
                            mobileInput.blur();
                        }
                    };
                }
            } 
            // MODE: Tap to Reveal
            else {
                // Mobile reveal mode - toggle reveal/hide
                if (!blank.completed && index === state.currentBlankIndex) {
                    revealAndContinue();
                } else if (blank.completed) {
                    // Toggle hide if already revealed
                    blank.completed = false;
                    blank.userInput = '';
                    state.currentBlankIndex = index;
                    renderPassage();
                } else {
                    focusBlank(index);
                }
            }
        }
        window.handleBlankTouch = handleBlankTouch;

        // Keyboard Input
        document.addEventListener('keydown', (e) => {
            // Close modals with Escape
            if (e.key === 'Escape') {
                if (DOM.statsModal.classList.contains('active')) {
                    DOM.statsModal.classList.remove('active');
                    return;
                }
                if (DOM.studyMenuModal.classList.contains('active')) {
                    DOM.studyMenuModal.classList.remove('active');
                    return;
                }
                // If in study mode and no modals open, open menu
                if (DOM.studyScreen.style.display === 'block') {
                    openStudyMenu();
                    return;
                }
                return;
            }
            
            if (DOM.studyScreen.style.display !== 'block') return;
            if (state.currentBlankIndex >= state.blanks.length) return;
            
            const blank = state.blanks[state.currentBlankIndex];
            
            // Ctrl/Cmd + H for hint - requires modifier to avoid conflict with typing
            if ((e.key === 'h' || e.key === 'H') && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();
                showHint();
                return;
            }
            
            if (blank.completed) {
                // Navigate between blanks with arrows
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    let prev = state.currentBlankIndex - 1;
                    while (prev >= 0 && state.blanks[prev].completed) prev--;
                    if (prev >= 0) focusBlank(prev);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    let next = state.currentBlankIndex + 1;
                    while (next < state.blanks.length && state.blanks[next].completed) next++;
                    if (next < state.blanks.length) focusBlank(next);
                }
                return;
            }
            
            // Regular typing - any letter key without modifiers
            if (e.key.length === 1 && /[a-zA-Z]/.test(e.key) && !e.ctrlKey && !e.metaKey && !e.altKey) {
                e.preventDefault();
                // Remove hint when user starts typing
                if (blank.hintShown) {
                    blank.hintShown = false;
                }
                blank.userInput += e.key;
                checkAnswer();
            } else if (e.key === 'Backspace') {
                e.preventDefault();
                blank.userInput = blank.userInput.slice(0, -1);
                // Re-show hint if user deletes all input and hint was shown before
                renderPassage();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (blank.userInput) {
                    revealAndContinue();
                }
            } else if (e.key === ' ') {
                e.preventDefault();
                revealAndContinue();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                let prev = state.currentBlankIndex - 1;
                while (prev >= 0 && state.blanks[prev].completed) prev--;
                if (prev >= 0) focusBlank(prev);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                let next = state.currentBlankIndex + 1;
                while (next < state.blanks.length && state.blanks[next].completed) next++;
                if (next < state.blanks.length) focusBlank(next);
            }
        });

        // Check Answer
        function checkAnswer() {
            const blank = state.blanks[state.currentBlankIndex];
            
            // Only apply case sensitivity to deity names when setting is enabled
            const isDeityName = DEITY_NAMES.has(blank.word.toLowerCase());
            const shouldBeCaseSensitive = state.settings.caseSensitive && isDeityName;
            
            const target = shouldBeCaseSensitive ? blank.word : blank.word.toLowerCase();
            const input = shouldBeCaseSensitive ? blank.userInput : blank.userInput.toLowerCase();
            
            renderPassage();
            
            if (input.length >= target.length) {
                if (input === target) {
                    blank.completed = true;
                    blank.correct = true;
                    state.correctCount++;
                    
                    // Immediate navigation - no delay
                    const nextIndex = state.currentBlankIndex + 1;
                    if (nextIndex < state.blanks.length) {
                        // Move to next blank
                        focusBlank(nextIndex);
                    } else {
                        // All blanks completed - finish chapter
                        console.log('All blanks completed, showing stats modal');
                        finishChapter();
                    }
                } else {
                    // Fuzzy matching - show hint if close
                    const distance = levenshteinDistance(input, target);
                    if (distance <= 1 && input.length >= target.length - 1) {
                        blank.hintShown = true;
                        renderPassage();
                    }
                }
            }
        }

        function levenshteinDistance(a, b) {
            const matrix = [];
            
            for (let i = 0; i <= b.length; i++) {
                matrix[i] = [i];
            }
            
            for (let j = 0; j <= a.length; j++) {
                matrix[0][j] = j;
            }
            
            for (let i = 1; i <= b.length; i++) {
                for (let j = 1; j <= a.length; j++) {
                    if (b.charAt(i - 1) === a.charAt(j - 1)) {
                        matrix[i][j] = matrix[i - 1][j - 1];
                    } else {
                        matrix[i][j] = Math.min(
                            matrix[i - 1][j - 1] + 1,
                            matrix[i][j - 1] + 1,
                            matrix[i - 1][j] + 1
                        );
                    }
                }
            }
            
            return matrix[b.length][a.length];
        }

        // Reveal Answer - Multiple buttons depending on layout
        document.getElementById('revealBtnFab').addEventListener('click', revealAndContinue); // Floating
        document.getElementById('revealBtnTop').addEventListener('click', revealAndContinue); // Top bar
        document.getElementById('revealBtnBottom').addEventListener('click', revealAndContinue); // Bottom bar

        function revealAndContinue() {
            const blank = state.blanks[state.currentBlankIndex];
            if (blank.completed) return;
            
            blank.completed = true;
            blank.correct = false;
            blank.userInput = blank.word;
            state.incorrectCount++;
            state.missedWords.push(blank.word);
            
            renderPassage();
            
            // Immediate navigation - no delay
            const nextIndex = state.currentBlankIndex + 1;
            if (nextIndex < state.blanks.length) {
                // Move to next blank
                focusBlank(nextIndex);
            } else {
                // All blanks completed - finish chapter
                console.log('All blanks completed via reveal, showing stats modal');
                finishChapter();
            }
        }

        // Hint - Multiple buttons depending on layout
        function showHint() {
            const blank = state.blanks[state.currentBlankIndex];
            if (!blank.completed && !blank.hintShown) {
                blank.hintShown = true;
                blank.userInput = ''; // Clear any typed input so hint shows immediately
                renderPassage();
            }
        }
        document.getElementById('hintBtnFab').addEventListener('click', showHint); // Floating
        document.getElementById('hintBtnTop').addEventListener('click', showHint); // Top bar
        document.getElementById('hintBtnBottom').addEventListener('click', showHint); // Bottom bar

        // Finish Chapter
        function finishChapter() {
            const totalTime = Math.floor((Date.now() - state.startTime) / 1000);
            const minutes = Math.floor(totalTime / 60);
            const seconds = totalTime % 60;
            
            const total = state.correctCount + state.incorrectCount;
            const accuracy = total > 0 ? Math.round((state.correctCount / total) * 100) : 0;
            
            // Save chapter stats
            const { book, chapter } = state.currentChapterList[state.currentChapterIndex];
            state.chapterStats.push({
                book,
                chapter,
                accuracy,
                time: totalTime,
                correct: state.correctCount,
                incorrect: state.incorrectCount,
                missedWords: [...state.missedWords]
            });
            
            // Show stats
            document.getElementById('accuracyStat').textContent = `${accuracy}%`;
            document.getElementById('timeStat').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            document.getElementById('correctStat').textContent = state.correctCount;
            document.getElementById('incorrectStat').textContent = state.incorrectCount;
            
            if (state.missedWords.length > 0) {
                document.getElementById('missedWordsContainer').style.display = 'block';
                document.getElementById('missedWordsList').innerHTML = state.missedWords
                    .map(word => `<span class="missed-word">${word}</span>`)
                    .join('');
            } else {
                document.getElementById('missedWordsContainer').style.display = 'none';
            }
            
            // Update continue button text based on whether there are more chapters
            const continueBtn = document.getElementById('continueStudy');
            if (state.currentChapterIndex < state.currentChapterList.length - 1) {
                continueBtn.textContent = 'Next Chapter →';
                continueBtn.style.display = 'flex';
            } else {
                continueBtn.textContent = '← All Done!';
                continueBtn.style.display = 'flex';
            }
            
            // Show modal after 2.5 second delay so user can see final answer and read complete passage
            setTimeout(() => {
                DOM.statsModal.classList.add('active');
            }, 2500);
        }

        // Continue to next chapter
        document.getElementById('continueStudy').addEventListener('click', () => {
            DOM.statsModal.classList.remove('active');
            state.currentChapterIndex++;
            
            if (state.currentChapterIndex < state.currentChapterList.length) {
                // More chapters to study
                startChapter();
            } else {
                // All chapters complete - return to selection
                DOM.studyScreen.style.display = 'none';
                DOM.bookSelection.style.display = 'block';
                
                // Show completion message
                const totalChapters = state.chapterStats.length;
                const avgAccuracy = Math.round(
                    state.chapterStats.reduce((sum, stat) => sum + stat.accuracy, 0) / totalChapters
                );
                alert(`🎉 Study Session Complete!\n\nStudied ${totalChapters} chapter${totalChapters > 1 ? 's' : ''}\nAverage Accuracy: ${avgAccuracy}%\n\nGreat work!`);
            }
        });

        // Back to Selection
        document.getElementById('backToSelection').addEventListener('click', () => {
            DOM.statsModal.classList.remove('active');
            DOM.studyScreen.style.display = 'none';
            DOM.bookSelection.style.display = 'block';
        });

        // Restart
        document.getElementById('restartBtn').addEventListener('click', () => {
            if (confirm('Restart this chapter?')) {
                // Don't change the index, just restart the current chapter
                startChapter();
            }
        });

        // Exit
        function exitStudy() {
            if (confirm('Exit study session?')) {
                DOM.studyScreen.style.display = 'none';
                DOM.bookSelection.style.display = 'block';
                DOM.header.style.display = 'flex'; // Show header again
            }
        }
        document.getElementById('exitStudyTop').addEventListener('click', exitStudy); // Top toolbar
        document.getElementById('exitStudyBottom').addEventListener('click', exitStudy); // Bottom bar

        // Close keyboard hints (minimal mode)
        document.getElementById('closeKeyboardHints').addEventListener('click', () => {
            DOM.keyboardHints.style.display = 'none';
        });

        // Show keyboard hints (from settings)
        // Menu button
        function openStudyMenu() {
            DOM.studyMenuModal.classList.add('active');
        }
        document.getElementById('menuBtn').addEventListener('click', openStudyMenu); // Top toolbar
        document.getElementById('menuBtnBottom').addEventListener('click', openStudyMenu); // Bottom bar
        DOM.ipadMinimalMenuBtn.addEventListener('click', openStudyMenu); // iPad minimal mode button
        
        // Close menu button
        document.getElementById('closeMenuBtn').addEventListener('click', () => {
            DOM.studyMenuModal.classList.remove('active');
        });

        // Tab switching in unified modal
        document.querySelectorAll('.modal-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active from all tabs and contents
                document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.modal-tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active to clicked tab and corresponding content
                tab.classList.add('active');
                const tabName = tab.dataset.tab;
                document.getElementById(tabName + 'Tab').classList.add('active');
            });
        });

        // Control Style Selection
        document.querySelectorAll('.control-style-option').forEach(option => {
            option.addEventListener('click', () => {
                state.settings.controlStyle = option.dataset.style;
                saveSettings();
                applySettings();
            });
        });

        // Print Functions
        document.getElementById('printBlankBtn').addEventListener('click', () => {
            // Close menu modal before printing
            DOM.studyMenuModal.classList.remove('active');
            
            // Print with ALL blanks empty - default print style handles this
            setTimeout(() => window.print(), 100);
        });

        document.getElementById('printFilledBtn').addEventListener('click', () => {
            // Close menu modal before printing
            DOM.studyMenuModal.classList.remove('active');
            
            // Store current state
            const currentHTML = DOM.passageText.innerHTML;
            
            // Build printable HTML with verse-by-verse structure
            let printHTML = '';
            let globalWordIndex = 0;
            
            state.currentVerses.forEach((verse, verseIdx) => {
                const verseText = verse.text.trim();
                const words = verseText.split(/\s+/);
                const blankIndices = new Set(state.blanks.map(b => b.index));
                
                // Add verse number and container
                printHTML += `<div class="verse-block">`;
                printHTML += `<span class="verse-number">${verse.verse}</span> `;
                
                // Process words in this verse
                words.forEach((word, wordIdx) => {
                    if (blankIndices.has(globalWordIndex)) {
                        const blank = state.blanks.find(b => b.index === globalWordIndex);
                        const wordLength = blank.word.length || 3;
                        const blankWidth = Math.max(wordLength * 20, 50);
                        
                        // Show correct answer with underline
                        printHTML += blank.leadingPunct;
                        printHTML += `<span class="blank print-filled" style="width: ${blankWidth}px;"><span class="blank-input">${blank.word}</span></span>`;
                        printHTML += blank.trailingPunct;
                        printHTML += ' ';
                    } else {
                        printHTML += `<span class="word">${word}</span> `;
                    }
                    globalWordIndex++;
                });
                
                printHTML += `</div>`; // Close verse-block
            });
            
            // Temporarily update display
            DOM.passageText.innerHTML = printHTML;
            
            // Trigger print
            setTimeout(() => {
                window.print();
                
                // Restore original state after print dialog
                setTimeout(() => {
                    DOM.passageText.innerHTML = currentHTML;
                }, 500);
            }, 100);
        });

        document.querySelectorAll('.difficulty-option').forEach(option => {
            option.addEventListener('click', () => {
                const newDifficulty = parseInt(option.dataset.difficulty);
                
                // Check if currently in a study session
                if (DOM.studyScreen.style.display === 'block' && state.blanks.length > 0) {
                    // Warn user that current progress will be lost
                    if (confirm('Changing difficulty will restart the current chapter and your progress will be lost. Continue?')) {
                        state.settings.difficulty = newDifficulty;
                        saveSettings();
                        applySettings();
                        // Restart the current chapter immediately
                        startChapter();
                    }
                } else {
                    // Not in study mode, just change the setting
                    state.settings.difficulty = newDifficulty;
                    saveSettings();
                    applySettings();
                }
            });
        });

        // Answer Mode Selection
        document.querySelectorAll('.answer-mode-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const mode = btn.dataset.mode;
                state.settings.inputMode = mode;
                saveSettings();
                
                // Update active button
                document.querySelectorAll('.answer-mode-btn').forEach(b => {
                    b.classList.toggle('active', b.dataset.mode === mode);
                });
            });
        });

        function setupToggle(id, setting) {
            document.getElementById(id).addEventListener('click', function() {
                state.settings[setting] = !state.settings[setting];
                this.classList.toggle('active', state.settings[setting]);
                saveSettings();
                applySettings();
            });
        }

        setupToggle('caseSensitiveToggle', 'caseSensitive');

        // Theme Events
        // Theme selection in modal
        document.querySelectorAll('.theme-option-card').forEach(option => {
            option.addEventListener('click', () => {
                const theme = option.dataset.theme;
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('bibleClozeTheme', theme);
                // Update active state
                document.querySelectorAll('.theme-option-card').forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
            });
        });

        // Click outside modals to close
        [DOM.statsModal, DOM.studyMenuModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });

        // Difficulty modal - click outside to close
        DOM.difficultyModal.addEventListener('click', (e) => {
            if (e.target === DOM.difficultyModal) {
                DOM.difficultyModal.style.display = 'none';
            }
        });

        // Init Events
        function initEvents() {
            // Already done above
        }

        // Start
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
