<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources - PBE Team Bold</title>
    
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }

        .page-logo {
            width: 140px;
            height: 140px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .page-header-text {
            flex: 1;
        }

        .page-title {
            font-family: 'EB Garamond', 'Crimson Pro', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: color 0.2s ease;
        }

        .back-link:hover { color: var(--accent); }

        .section {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px var(--shadow);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .section-icon {
            width: 64px;
            height: 64px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .section-title {
            font-family: 'EB Garamond', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .section-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .subfolder {
            margin-bottom: 1rem;
        }

        .subfolder-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .subfolder-header:hover {
            border-color: var(--accent);
            background: var(--bg-card);
        }

        .subfolder-icon {
            font-size: 1.25rem;
            transition: transform 0.2s ease;
        }

        .subfolder.expanded .subfolder-icon {
            transform: rotate(90deg);
        }

        .subfolder-name {
            flex: 1;
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-primary);
        }

        .subfolder-count {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .subfolder-files {
            display: none;
            margin-top: 0.5rem;
            padding-left: 2rem;
        }

        .subfolder.expanded .subfolder-files {
            display: block;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .file-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.875rem 1rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .file-item:hover {
            border-color: var(--accent);
            transform: translateX(4px);
            box-shadow: 0 2px 8px var(--shadow);
        }

        .file-type-icon {
            width: 32px;
            height: 32px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .file-info {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.125rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-meta {
            font-size: 0.8rem;
            color: var(--text-muted);
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

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            font-style: italic;
        }

        @media (max-width: 768px) {
            .container { padding: 1.5rem; }
            .page-header { flex-direction: column; text-align: center; gap: 1rem; }
            .page-title { font-size: 2rem; }
            .files-grid { grid-template-columns: 1fr; }
            .theme-toggle { top: 1rem; right: 1rem; width: 44px; height: 44px; }
        }
    </style>
</head>
<body>
    <button class="theme-toggle" id="themeToggle" title="Toggle Theme">☀️</button>

    <div class="container">
        <a href="/" class="back-link">← Back to Home</a>

        <div class="page-header">
            <img src="/pbe_team_bold_logo.png" alt="PBE Team BOLD Logo" class="page-logo">
            <div class="page-header-text">
                <h1 class="page-title">Resources</h1>
            </div>
        </div>

        <?php
        // Configuration: define categories with their folder paths and section icons
        $categories = [
            [
                'title'       => 'Study Guides',
                'description' => 'Comprehensive study materials, worksheets, and reference guides.',
                'icon'        => '/icons/studyguides.png',
                'folder'      => 'downloads/study-guides',
                'extensions'  => ['pdf', 'docx', 'doc']
            ],
            [
                'title'       => 'Flashcard Sets',
                'description' => 'Printable flashcard sets for memorization and quick review.',
                'icon'        => '/icons/flashcardsets.png',
                'folder'      => 'downloads/flashcards',
                'extensions'  => ['pdf', 'docx']
            ],
            [
                'title'       => 'Misc Documents',
                'description' => 'Spreadsheets, data exports, and supplemental reference files.',
                'icon'        => '/icons/miscdocs.png',
                'folder'      => 'downloads/misc',
                'extensions'  => ['csv', 'xlsx', 'pdf', 'docx']
            ],
            [
                'title'       => 'Extra Materials',
                'description' => 'Additional team resources, training docs, and supplemental content.',
                'icon'        => '/icons/etcmaterials.png',
                'folder'      => 'downloads/extra',
                'extensions'  => ['pdf', 'docx', 'csv', 'pptx']
            ]
        ];

        // Map file extensions to icon paths
        $fileTypeIcons = [
            'pdf'  => '/icons/pdf.svg',
            'docx' => '/icons/docx.svg',
            'doc'  => '/icons/docx.svg',
            'csv'  => '/icons/csv.svg',
            'xlsx' => '/icons/xlsx.svg',
            'xls'  => '/icons/xlsx.svg',
            'pptx' => '/icons/pptx.svg',
            'ppt'  => '/icons/pptx.svg',
        ];

        function formatFileSize(int $bytes): string {
            if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
            if ($bytes >= 1024)    return number_format($bytes / 1024, 0) . ' KB';
            return $bytes . ' B';
        }

        foreach ($categories as $cat):
            $rootFiles = [];
            $subfolders = [];

            if (is_dir($cat['folder'])) {
                foreach (array_diff(scandir($cat['folder']), ['.', '..']) as $item) {
                    $itemPath = $cat['folder'] . '/' . $item;
                    
                    // Check if it's a directory (subfolder)
                    if (is_dir($itemPath)) {
                        $subfolderFiles = [];
                        foreach (array_diff(scandir($itemPath), ['.', '..']) as $file) {
                            $filePath = $itemPath . '/' . $file;
                            if (!is_file($filePath)) continue;
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (!in_array($ext, $cat['extensions'], true)) continue;
                            $subfolderFiles[] = [
                                'name' => $file,
                                'path' => $filePath,
                                'size' => filesize($filePath),
                                'ext'  => $ext,
                            ];
                        }
                        if (count($subfolderFiles) > 0) {
                            usort($subfolderFiles, fn($a, $b) => strcmp($a['name'], $b['name']));
                            $subfolders[] = [
                                'name' => $item,
                                'files' => $subfolderFiles
                            ];
                        }
                    }
                    // It's a file in the root
                    else if (is_file($itemPath)) {
                        $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                        if (!in_array($ext, $cat['extensions'], true)) continue;
                        $rootFiles[] = [
                            'name' => $item,
                            'path' => $itemPath,
                            'size' => filesize($itemPath),
                            'ext'  => $ext,
                        ];
                    }
                }
                usort($rootFiles, fn($a, $b) => strcmp($a['name'], $b['name']));
                usort($subfolders, fn($a, $b) => strcmp($a['name'], $b['name']));
            }
        ?>
        <div class="section">
            <div class="section-header">
                <img src="<?= htmlspecialchars($cat['icon']) ?>" alt="<?= htmlspecialchars($cat['title']) ?>" class="section-icon">
                <h2 class="section-title"><?= htmlspecialchars($cat['title']) ?></h2>
            </div>
            <p class="section-description"><?= htmlspecialchars($cat['description']) ?></p>

            <?php if (count($rootFiles) > 0 || count($subfolders) > 0): ?>
                
                <?php if (count($rootFiles) > 0): ?>
                <div class="files-grid">
                    <?php foreach ($rootFiles as $file):
                        $displayName = ucwords(str_replace(['_', '-'], ' ', pathinfo($file['name'], PATHINFO_FILENAME)));
                        $iconPath = $fileTypeIcons[$file['ext']] ?? '/icons/docx.svg';
                    ?>
                    <a href="/<?= htmlspecialchars($file['path']) ?>" class="file-item" download>
                        <img src="<?= htmlspecialchars($iconPath) ?>" alt="<?= strtoupper($file['ext']) ?>" class="file-type-icon">
                        <div class="file-info">
                            <div class="file-name"><?= htmlspecialchars($displayName) ?></div>
                            <div class="file-meta"><?= strtoupper($file['ext']) ?> &bull; <?= formatFileSize($file['size']) ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php foreach ($subfolders as $subfolder): ?>
                <div class="subfolder">
                    <div class="subfolder-header" onclick="this.parentElement.classList.toggle('expanded')">
                        <span class="subfolder-icon">▶</span>
                        <span class="subfolder-name"><?= htmlspecialchars(ucwords(str_replace(['_', '-'], ' ', $subfolder['name']))) ?></span>
                        <span class="subfolder-count"><?= count($subfolder['files']) ?> file<?= count($subfolder['files']) !== 1 ? 's' : '' ?></span>
                    </div>
                    <div class="subfolder-files">
                        <div class="files-grid">
                            <?php foreach ($subfolder['files'] as $file):
                                $displayName = ucwords(str_replace(['_', '-'], ' ', pathinfo($file['name'], PATHINFO_FILENAME)));
                                $iconPath = $fileTypeIcons[$file['ext']] ?? '/icons/docx.svg';
                            ?>
                            <a href="/<?= htmlspecialchars($file['path']) ?>" class="file-item" download>
                                <img src="<?= htmlspecialchars($iconPath) ?>" alt="<?= strtoupper($file['ext']) ?>" class="file-type-icon">
                                <div class="file-info">
                                    <div class="file-name"><?= htmlspecialchars($displayName) ?></div>
                                    <div class="file-meta"><?= strtoupper($file['ext']) ?> &bull; <?= formatFileSize($file['size']) ?></div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php else: ?>
            <div class="empty-state">No files available in this section yet.</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
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
