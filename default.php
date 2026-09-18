<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBE Team Bold</title>
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .site-logo {
            width: 340px;
            height: 290px;
            margin: 0 auto 0rem;
            object-fit: contain;
        }

        .site-title {
            font-family: 'EB Garamond', 'Crimson Pro', serif;
            font-size: 2.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .site-subtitle {
            font-size: 1.15rem;
            color: var(--text-secondary);
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .tool-card {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 1rem 1rem 1.25rem;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px var(--shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .tool-card:hover {
            border-color: var(--accent);
            box-shadow: 0 8px 24px var(--shadow-hover);
            transform: translateY(-4px);
        }

        .tool-icon {
            width: 150px;
            margin-bottom: 0.25rem;
            object-fit: contain;
        }

        .tool-title {
            font-family: 'EB Garamond', serif;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .tool-description {
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.45;
        }

        .downloads-section {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 1.25rem;
            text-align: center;
            box-shadow: 0 2px 8px var(--shadow);
        }

        .downloads-title {
            font-family: 'EB Garamond', serif;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            color: var(--text-primary);
        }

        .downloads-description {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.75rem;
            background: var(--accent);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px var(--shadow);
        }

        .btn:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 16px var(--shadow-hover);
            transform: translateY(-2px);
        }

        .theme-toggle {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 48px;
            height: 48px;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.2s ease;
            z-index: 100;
        }

        .theme-toggle:hover {
            border-color: var(--accent);
            transform: scale(1.05);
        }

        @media (max-width: 1024px) and (min-width: 769px) {
            .tool-icon { width: 130px; }
            .tools-grid { gap: 1rem; }
            .tool-card { padding: 0.85rem 0.85rem 1rem; }
        }

        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .site-title { font-size: 2rem; }
            .tools-grid { grid-template-columns: repeat(2, 1fr); gap: 0.6rem; }
            .tool-card { padding: 0.6rem 0.5rem 0.75rem; }
            .tool-icon { width: 90px; margin-bottom: 0.15rem; }
            .tool-title { font-size: 1.1rem; margin-bottom: 0.15rem; }
            .tool-description { font-size: 0.75rem; line-height: 1.35; }
            .downloads-section { padding: 1rem; }
            .downloads-title { font-size: 1.1rem; }
            .downloads-description { font-size: 0.8rem; margin-bottom: 0.75rem; }
            .btn { padding: 0.65rem 1.5rem; font-size: 0.85rem; }
            .theme-toggle { top: 0.75rem; right: 0.75rem; width: 40px; height: 40px; font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    <button class="theme-toggle" id="themeToggle" title="Toggle Theme">☀️</button>

    <div class="container">
        <div class="header">
            <img src="/pbe_team_bold_logo.png" alt="PBE Team BOLD Logo" class="site-logo">
        </div>

        <div class="tools-grid">
            <a href="/study/" class="tool-card">
                <img src="/icons/biblestudy.png" alt="Bible Study" class="tool-icon">
                <h2 class="tool-title">Bible Study</h2>
                <p class="tool-description">Read, listen and memorize PBE verses!</p>
            </a>

            <a href="/cards/" class="tool-card">
                <img src="/icons/flashcards.png" alt="Flashcards" class="tool-icon">
                <h2 class="tool-title">Flashcards</h2>
                <p class="tool-description">Basic flashcards as well as use the 3-pile system and type-your-answer.</p>
            </a>

            <a href="/cloze/" class="tool-card">
                <img src="/icons/clozestudy.png" alt="Cloze Study" class="tool-icon">
                <h2 class="tool-title">Cloze Study</h2>
                <p class="tool-description">Fill in the blanks, by clicking or typing.</p>
            </a>

            <a href="/cloze/cloze-cards.php" class="tool-card">
                <img src="/icons/clozecards.png" alt="Cloze Cards" class="tool-icon">
                <h2 class="tool-title">Cloze Cards</h2>
                <p class="tool-description">Verse-by-verse cloze cards with sections, chapters, and advanced options.</p>
            </a>
        </div>

        <div class="downloads-section">
            <h2 class="downloads-title">Resources</h2>
            <p class="downloads-description">Access printable study materials, worksheets, and reference documents for your team.</p>
            <a href="/downloads.php" class="btn">View Downloads →</a>
        </div>

    </div>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;

        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);

        themeToggle.addEventListener('click', () => {
            const current = html.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(next);
        });

        function updateThemeIcon(theme) {
            themeToggle.textContent = theme === 'dark' ? '☀️' : '🌙';
        }
    </script>
</body>
</html>
