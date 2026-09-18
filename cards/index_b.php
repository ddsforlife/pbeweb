<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBE Flashcards</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <?php 
    $version = '3.7.2'; // Mobile typing: centered like desktop
    echo "<meta name='app-version' content='$version'>";
    
    // Function to scan folders and return deck list with categories
    function getDecksWithCategories($baseDir = 'flashcards') {
        $decks = [];
        
        if (!is_dir($baseDir)) {
            return $decks;
        }
        
        // Get all subdirectories (categories)
        $categories = array_filter(glob($baseDir . '/*'), 'is_dir');
        
        // If no subdirectories, treat files in base as "Uncategorized"
        if (empty($categories)) {
            $files = glob($baseDir . '/*.csv');
            foreach ($files as $file) {
                $cardInfo = countCardsInCSV($file);
                $decks[] = [
                    'file' => basename($file),
                    'path' => $file,
                    'category' => 'Uncategorized',
                    'cardCount' => $cardInfo['count'],
                    'hasTypeable' => $cardInfo['hasTypeable']
                ];
            }
        } else {
            // Scan each category folder
            foreach ($categories as $categoryPath) {
                $category = basename($categoryPath);
                $files = glob($categoryPath . '/*.csv');
                
                foreach ($files as $file) {
                    $cardInfo = countCardsInCSV($file);
                    $decks[] = [
                        'file' => basename($file),
                        'path' => $file,
                        'category' => $category,
                        'cardCount' => $cardInfo['count'],
                        'hasTypeable' => $cardInfo['hasTypeable']
                    ];
                }
            }
        }
        
        return $decks;
    }
    
    // Helper function to count cards in a CSV file
    function countCardsInCSV($filePath) {
        if (!file_exists($filePath)) {
            return ['count' => 0, 'hasTypeable' => false];
        }
        
        $lines = file($filePath, FILE_SKIP_EMPTY_LINES);
        $count = count($lines);
        $hasTypeable = false;
        
        // Check if first line looks like a header (contains "front" and "back")
        $startLine = 0;
        if ($count > 0) {
            $firstLine = strtolower($lines[0]);
            if (strpos($firstLine, 'front') !== false && strpos($firstLine, 'back') !== false) {
                $count--;
                $startLine = 1;
            }
        }
        
        // Check for typeable flag in 4th column
        for ($i = $startLine; $i < count($lines); $i++) {
            $parts = str_getcsv($lines[$i]);
            if (isset($parts[3]) && strtolower(trim($parts[3])) === 't') {
                $hasTypeable = true;
                break;
            }
        }
        
        return ['count' => max(0, $count), 'hasTypeable' => $hasTypeable];
    }
    
    $decksData = getDecksWithCategories();
    echo "<script>const DECKS_DATA = " . json_encode($decksData) . ";</script>";
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>        :root, [data-theme="light"] {
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
            --bg-primary: #131318;
            --bg-secondary: #1C1C24;
            --bg-card: #22222C;
            --text-primary: #EEEDF2;
            --text-secondary: #C5C4CC;
            --text-muted: #8887A0;
            --accent: #E85650;
            --accent-hover: #D4403A;
            --border: #353542;
            --shadow: rgba(0, 0, 0, 0.40);
            --shadow-hover: rgba(0, 0, 0, 0.55);
        }
* {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-y: auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--border);
            animation: fadeIn 0.6s ease;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            height: 65px;
            width: auto;
        }

        .title-section h1 {
            font-family: 'IBM Plex Sans', -apple-system, sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .title-section p {
            font-size: 12px;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .logo {
                height: 35px;
            }

            .logo-section {
                flex: 1;
            }
            
            .title-section {
                display: none;
            }
        }

        .header-controls {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .header-search {
            width: 280px;
        }

        @media (max-width: 768px) {
            .header-search {
                width: 200px;
            }
        }

        .global-settings-btn {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 50%;
            padding: 0;
            cursor: pointer;
            font-size: 22px;
            color: var(--text-primary);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
        }

        .global-settings-btn:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
            transform: rotate(90deg);
            box-shadow: 0 4px 12px var(--shadow-hover);
        }

        /* Deck Selection */
        .deck-selection {
            animation: fadeIn 0.6s ease 0.3s both;
            padding-bottom: 100px; /* Space for footer when active */
        }

        .deck-selection-header {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Search and Filter Controls */
        .deck-controls {
            max-width: 900px;
            margin: 0 auto 30px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .search-bar {
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 14px 20px 14px 48px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            background: var(--bg-card);
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 20%, transparent);
        }

        .search-bar::before {
            content: "🔍";
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            opacity: 0.5;
        }

        .filter-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .category-filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            flex: 1;
        }

        .category-btn {
            padding: 8px 16px;
            border: 2px solid var(--border);
            border-radius: 20px;
            background: var(--bg-card);
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .category-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .category-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .category-btn .count {
            opacity: 0.7;
            font-size: 12px;
            margin-left: 4px;
        }

        .sort-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sort-control label {
            font-size: 14px;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .sort-control select {
            padding: 8px 12px;
            border: 2px solid var(--border);
            border-radius: 8px;
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .sort-control select:focus {
            outline: none;
            border-color: var(--accent);
        }

        /* Recent Studied Section */
        .recent-studied-section {
            max-width: 900px;
            margin: 0 auto 20px;
            padding: 20px;
            background: var(--bg-secondary);
            border-radius: 16px;
            border: 2px solid var(--border);
        }

        .recent-studied-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .recent-studied-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .clear-all-btn {
            padding: 6px 14px;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .clear-all-btn:hover {
            background: #e57373;
            border-color: #e57373;
            color: white;
        }

        .recent-studied-items {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .recent-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 12px;
            transition: all 0.2s ease;
            position: relative;
        }

        .recent-item:hover {
            border-color: var(--accent);
        }

        .recent-item-info {
            flex: 1;
            margin-right: 12px;
        }

        .recent-item-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .recent-item-details {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .recent-item-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .recent-item-button {
            padding: 8px 16px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .recent-item-button:hover {
            background: var(--accent-hover);
            transform: scale(1.05);
        }

        .recent-item-remove {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            color: var(--text-muted);
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .recent-item-remove:hover {
            background: #e57373;
            border-color: #e57373;
            color: white;
            transform: scale(1.1);
        }

        .deck-stats {
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 12px;
        }

        .deck-selection-controls {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 16px;
        }

        .deck-selection-controls button {
            padding: 8px 16px;
            font-size: 0.9rem;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .deck-selection-controls button:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }
            
            .category-filters {
                width: 100%;
                justify-content: center;
            }
            
            .sort-control {
                width: 100%;
                justify-content: center;
            }
        }

        .decks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .deck-card {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 20px;
            padding: 35px 28px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .deck-card .category-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            padding: 5px 12px;
            background: var(--accent);
            color: white;
            font-size: 11px;
            font-weight: 600;
            border-radius: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.95;
            z-index: 1;
        }

        .deck-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px var(--shadow-hover);
            border-color: var(--accent);
        }

        .deck-card.selected {
            border-color: var(--accent);
            background: var(--bg-secondary);
        }

        .deck-card h3 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .deck-card .card-count {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .deck-card .card-count-badge {
            position: absolute;
            bottom: 16px;
            right: 16px;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .deck-card .card-count-badge::before {
            content: "📚 ";
            opacity: 0.7;
        }

        .deck-checkbox {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 24px;
            height: 24px;
            accent-color: var(--accent);
            cursor: pointer;
            z-index: 10;
        }

        .deck-gear-btn {
            position: absolute;
            bottom: 15px;
            left: 15px;
            width: 36px;
            height: 36px;
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .deck-gear-btn:hover {
            background: var(--accent);
            border-color: var(--accent);
            transform: rotate(90deg);
        }

        .study-still-learning-btn {
            margin-top: 12px;
            padding: 8px 12px;
            background: #ffe5e5;
            border: 2px solid #d47574;
            border-radius: 8px;
            color: #d47574;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: none;
            text-align: center;
        }

        .study-still-learning-btn:hover {
            background: #d47574;
            color: white;
            transform: translateY(-1px);
        }

        .study-still-learning-btn.visible {
            display: block;
        }

        .deck-pile-info {
            margin-top: 12px;
            display: flex;
            gap: 12px;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .deck-pile-info span {
            padding: 4px 8px;
            border-radius: 6px;
            background: var(--bg-secondary);
        }

        .deck-finished-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 3000;
        }

        .deck-finished-modal.active {
            display: flex;
        }

        .deck-finished-content {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .deck-finished-content h2 {
            font-family: 'Crimson Pro', serif;
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .deck-finished-content p {
            color: var(--text-secondary);
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .deck-finished-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* Pile Selection Screen */
        .pile-selection-screen {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--bg-primary);
            z-index: 1000;
            overflow-y: auto;
            padding: 40px 20px;
        }

        .pile-selection-screen.active {
            display: block;
        }

        .pile-selection-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .pile-selection-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .pile-selection-header h2 {
            font-family: 'Crimson Pro', serif;
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .pile-selection-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .pile-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .pile-option {
            background: var(--bg-card);
            border: 3px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pile-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--shadow);
        }

        .pile-option.pile1 {
            border-color: #e57373;
        }

        .pile-option.pile2 {
            border-color: #ffd54f;
        }

        .pile-option.pile3 {
            border-color: #81c784;
        }

        .pile-option.all {
            border-color: var(--accent);
            background: var(--bg-secondary);
        }

        .pile-option-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .pile-option-icon {
            font-size: 2rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .pile-option.pile1 .pile-option-icon {
            background: #ffebee;
            color: #c62828;
        }

        .pile-option.pile2 .pile-option-icon {
            background: #fff9c4;
            color: #f57f17;
        }

        .pile-option.pile3 .pile-option-icon {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .pile-option.all .pile-option-icon {
            background: var(--accent);
            color: white;
        }

        .pile-option-text h3 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.3rem;
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }

        .pile-option-text p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .pile-option-count {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .pile-selection-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 30px;
        }

        .pile-shuffle-option {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 12px;
        }

        .pile-shuffle-option label {
            cursor: pointer;
            user-select: none;
        }

        @media (max-width: 768px) {
            .pile-selection-header h2 {
                font-size: 1.5rem;
            }

            .pile-option {
                padding: 15px;
            }

            .pile-option-icon {
                width: 40px;
                height: 40px;
                font-size: 1.5rem;
            }

            .pile-option-text h3 {
                font-size: 1.1rem;
            }

            .pile-option-count {
                font-size: 1.5rem;
            }
        }

        /* Settings Panel */
        /* Multi-Deck Footer */
        .multi-deck-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-card);
            border-top: 2px solid var(--border);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -4px 12px var(--shadow);
            z-index: 100;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .multi-deck-info {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .multi-deck-info span {
            color: var(--accent);
            font-size: 1.3rem;
        }

        .multi-deck-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .shuffle-toggle-small {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
        }

        .shuffle-toggle-small:hover {
            border-color: var(--accent);
            background: var(--bg-primary);
        }

        .shuffle-toggle-small input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
            cursor: pointer;
        }

        .shuffle-toggle-small span {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .multi-deck-footer {
                flex-direction: column;
                gap: 12px;
                padding: 12px 15px;
            }

            .multi-deck-controls {
                width: 100%;
                justify-content: space-between;
            }
        }

        /* Study Screen */
        .study-screen {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            background: var(--bg-primary);
            z-index: 1000;
        }

        .study-layout {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* Progress Indicator - Top Left */
        .progress-indicator {
            position: fixed;
            top: 15px;
            left: 15px;
            background: var(--bg-card);
            padding: 8px 16px;
            border-radius: 20px;
            border: 2px solid var(--border);
            box-shadow: 0 2px 8px var(--shadow);
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            z-index: 200;
        }

        /* Flashcard Wrapper - Goes to top */
        .flashcard-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 10px 90px 10px;
            overflow: hidden;
            position: relative;
        }

        .flashcard-container {
            perspective: 1000px;
            width: 100%;
            max-width: 900px;
            height: 100%;
            position: relative;
        }

        .flashcard {
            position: relative;
            width: 100%;
            height: 100%;
            cursor: pointer;
            transform-style: preserve-3d;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .flashcard.flipped {
            transform: rotateX(180deg);
        }

        .flashcard-wrapper.slide-out {
            animation: slideOut 0.25s ease forwards;
        }

        .flashcard-wrapper.slide-in {
            animation: slideIn 0.25s ease forwards;
        }

        @keyframes slideOut {
            0% { transform: translateX(0); opacity: 1; }
            40% { opacity: 0; }
            100% { transform: translateX(-20px); opacity: 0; }
        }

        @keyframes slideIn {
            0% { transform: translateX(20px); opacity: 0; }
            60% { opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 20px;
            padding: 95px 20px 20px;
            box-shadow: 0 10px 40px var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow-y: auto;
        }

        .card-face.front {
            transform: rotateX(0deg);
        }

        .card-face.back {
            transform: rotateX(180deg);
        }

        .card-title {
            position: absolute;
            top: 15px;
            left: 20px;
            right: 20px;
            font-family: 'Crimson Pro', serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-secondary);
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border);
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title-text {
            flex: 1;
            text-align: center;
        }

        .card-title-progress {
            position: absolute;
            left: 0;
            font-size: 1.1rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .card-progress-counters {
            position: absolute;
            top: 60px;
            left: 20px;
            right: 20px;
            display: none;
            justify-content: space-between;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .card-progress-counters.active {
            display: flex;
        }

        .still-learning-count {
            color: #d47574;
        }

        .know-count {
            color: #5a9b8e;
        }

        /* Pile Display - single line under title */
        .pile-display {
            position: absolute;
            top: 60px;
            left: 10px;
            right: 10px;
            display: none;
            justify-content: stretch;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .pile-display.active {
            display: flex;
        }

        .pile-item {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 6px;
            background: var(--bg-secondary);
            border: 1.5px solid var(--border);
            flex: 1;
            white-space: nowrap;
        }

        .pile-item.pile1 {
            border-color: #e57373;
            background: #ffebee;
            color: #c62828;
        }

        .pile-item.pile2 {
            border-color: #ffd54f;
            background: #fff9c4;
            color: #f57f17;
        }

        .pile-item.pile3 {
            border-color: #81c784;
            background: #e8f5e9;
            color: #2e7d32;
        }

        .pile-label {
            font-size: 0.7rem;
            opacity: 0.85;
        }

        .pile-count {
            font-size: 0.85rem;
            font-weight: 700;
        }

        .card-content {
            font-family: 'Crimson Pro', serif;
            font-size: clamp(1.2rem, 2.5vw, 1.6rem);
            line-height: 1.5;
            color: var(--text-primary);
            max-width: 700px;
            transition: font-size 0.3s ease;
        }

        /* Text Size Options - 7 sizes, "large" is default */
        body[data-text-size="tiny"] .card-content {
            font-size: clamp(0.9rem, 1.8vw, 1.2rem);
        }

        body[data-text-size="tiny"] .card-reference {
            font-size: 0.75rem;
        }

        body[data-text-size="small"] .card-content {
            font-size: clamp(1.1rem, 2.2vw, 1.4rem);
        }

        body[data-text-size="small"] .card-reference {
            font-size: 0.85rem;
        }

        body[data-text-size="medium"] .card-content {
            font-size: clamp(1.3rem, 2.8vw, 1.7rem);
        }

        body[data-text-size="medium"] .card-reference {
            font-size: 1rem;
        }

        body[data-text-size="large"] .card-content {
            font-size: clamp(1.6rem, 3.2vw, 2.1rem);
        }

        body[data-text-size="large"] .card-reference {
            font-size: 1.15rem;
        }

        body[data-text-size="extra-large"] .card-content {
            font-size: clamp(1.9rem, 3.8vw, 2.5rem);
        }

        body[data-text-size="extra-large"] .card-reference {
            font-size: 1.3rem;
        }

        body[data-text-size="huge"] .card-content {
            font-size: clamp(2.2rem, 4.4vw, 2.9rem);
        }

        body[data-text-size="huge"] .card-reference {
            font-size: 1.5rem;
        }

        body[data-text-size="massive"] .card-content {
            font-size: clamp(2.6rem, 5vw, 3.4rem);
        }

        body[data-text-size="massive"] .card-reference {
            font-size: 1.7rem;
        }

        .card-reference {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 2px solid var(--border);
            font-family: 'DM Sans', sans-serif;
            font-size: 1.15rem;
            color: var(--text-muted);
            font-style: italic;
            transition: font-size 0.3s ease;
        }

        /* Professional Bottom Control Bar */
        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-card);
            border-top: 2px solid var(--border);
            padding: 12px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            box-shadow: 0 -4px 12px var(--shadow);
            z-index: 200;
        }

        .bottom-bar-section {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 140px; /* Equal width for left and right sections */
        }

        .bottom-bar-section:first-child {
            justify-content: flex-start;
        }

        .bottom-bar-section:last-child {
            justify-content: flex-end;
        }

        .bottom-bar-section.center {
            flex: 1;
            justify-content: center;
            min-width: auto;
        }

        /* When pile mode is active, navigation fills full width */
        .bottom-bar.pile-active .bottom-bar-section.center .navigation {
            width: 100%;
        }

        /* Match gear/back button height to pile buttons in pile mode */
        .bottom-bar.pile-active .bottom-controls {
            align-items: stretch;
        }

        .bottom-bar.pile-active .bottom-controls .btn,
        .bottom-bar.pile-active .bottom-controls .settings-btn {
            height: 60px;
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 0;
        }

        /* Mobile: Ensure all buttons fit on one line */
        @media (max-width: 768px) {
            .bottom-bar-section {
                min-width: 0;
                flex-shrink: 1;
            }
            
            .bottom-bar-section:first-child {
                flex: 0 0 auto;
            }
            
            .bottom-bar-section:last-child {
                flex: 0 0 auto;
            }
        }

        /* Navigation inside bottom bar */
        .navigation {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .nav-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--bg-primary);
            border: 2px solid var(--border);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
            user-select: none;
        }

        @media (hover: hover) {
            .nav-btn:hover:not(:disabled) {
                transform: translateY(-2px);
                background: var(--accent);
                color: white;
                border-color: var(--accent);
            }

            /* Pile buttons: very subtle lift, keep their own colors */
            .navigation.pile-mode .nav-btn:hover:not(:disabled) {
                transform: translateY(-2px);
                filter: brightness(0.92);
                background: initial;
                color: initial;
                border-color: initial;
            }

            .navigation.pile-mode.style1 #pile1Btn:hover {
                background: #ffebee;
                border-color: #e57373;
                color: #c62828;
            }
            .navigation.pile-mode.style1 #pile2Btn:hover {
                background: #fff9c4;
                border-color: #ffd54f;
                color: #f57f17;
            }
            .navigation.pile-mode.style1 #pile3Btn:hover {
                background: #e8f5e9;
                border-color: #81c784;
                color: #2e7d32;
            }
        }

        .nav-btn:focus {
            outline: none;
        }

        .nav-btn:active {
            transform: scale(0.95);
        }

        .nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* Pile Mode - 3 Buttons (full width) */
        .navigation.pile-mode {
            gap: 8px;
            width: 100%;
            flex: 1;
        }

        .navigation.pile-mode .nav-btn {
            flex: 1;
            height: 60px;
            border-radius: 12px;
            font-size: 0.9rem;
            flex-direction: column;
            padding: 6px 4px;
            gap: 2px;
            width: auto;
            min-width: 0;
        }

        /* Button Style 1: Emoji + Text */
        .navigation.pile-mode.style1 #pile1Btn {
            background: #ffebee;
            border-color: #e57373;
            color: #c62828;
        }

        .navigation.pile-mode.style1 #pile2Btn {
            background: #fff9c4;
            border-color: #ffd54f;
            color: #f57f17;
        }

        .navigation.pile-mode.style1 #pile3Btn {
            background: #e8f5e9;
            border-color: #81c784;
            color: #2e7d32;
        }

        /* Button Style 2: Icons Only */
        .navigation.pile-mode.style2 #pile1Btn {
            background: #ff5252;
            border-color: #d32f2f;
            color: white;
        }

        .navigation.pile-mode.style2 #pile2Btn {
            background: #ffc107;
            border-color: #ffa000;
            color: white;
        }

        .navigation.pile-mode.style2 #pile3Btn {
            background: #4caf50;
            border-color: #388e3c;
            color: white;
        }

        /* Button Style 3: Minimalist */
        .navigation.pile-mode.style3 .nav-btn {
            background: var(--bg-card);
            border-width: 3px;
        }

        .navigation.pile-mode.style3 #pile1Btn {
            border-color: #e57373;
            color: #c62828;
        }

        .navigation.pile-mode.style3 #pile2Btn {
            border-color: #ffd54f;
            color: #f57f17;
        }

        .navigation.pile-mode.style3 #pile3Btn {
            border-color: #81c784;
            color: #2e7d32;
        }

        /* Button Style 4: Modern Gradient */
        .navigation.pile-mode.style4 #pile1Btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            border: none;
            color: white;
        }

        .navigation.pile-mode.style4 #pile2Btn {
            background: linear-gradient(135deg, #ffd93d, #f6c23e);
            border: none;
            color: white;
        }

        .navigation.pile-mode.style4 #pile3Btn {
            background: linear-gradient(135deg, #6bcf7f, #51cf66);
            border: none;
            color: white;
        }

        /* Type Answer Mode */
        .type-answer-area {
            width: 100%;
            max-width: 500px;
            padding-top: 12px;
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .type-answer-area.active {
            display: flex;
        }

        /* Overlay pattern: colored mirror text behind transparent input */
        .type-answer-input-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0;
        }

        .type-answer-mirror {
            position: absolute;
            left: 0;
            top: 0;
            right: 48px;
            bottom: 0;
            padding: 10px 14px;
            font-size: 1.05rem;
            font-family: 'DM Sans', sans-serif;
            line-height: 1.5;
            text-align: center;
            pointer-events: none;
            white-space: pre-wrap;
            word-break: break-word;
            overflow: hidden;
            border: 2px solid transparent;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .type-answer-mirror .char-correct {
            color: #2e7d32;
            font-weight: 600;
        }

        .type-answer-mirror .char-wrong {
            color: #c62828;
            font-weight: 600;
            text-decoration: underline;
            text-decoration-color: #e57373;
        }

        .type-answer-input {
            flex: 1;
            min-width: 0;
            padding: 10px 14px;
            border: 2px solid var(--border);
            border-right: none;
            border-radius: 10px 0 0 10px;
            font-size: 1.05rem;
            line-height: 1.5;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-secondary);
            color: transparent;
            caret-color: transparent;
            text-align: center;
            transition: border-color 0.2s ease;
        }

        .type-answer-input::placeholder {
            color: var(--text-muted);
            opacity: 0.5;
        }

        /* When nothing typed yet, show placeholder (input color doesn't matter) */
        .type-answer-input:placeholder-shown {
            color: transparent;
        }

        .type-answer-input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .type-answer-input.correct {
            border-color: #81c784;
        }

        .type-answer-input.wrong {
            border-color: #e57373;
        }

        .type-answer-input.submitted {
            color: var(--text-primary);
            border-right-width: 2px;
            border-right-style: solid;
            border-radius: 10px;
        }

        .type-answer-submit {
            width: 48px;
            height: 100%;
            min-height: 44px;
            background: var(--accent);
            color: white;
            border: 2px solid var(--accent);
            border-radius: 0 10px 10px 0;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .type-answer-submit:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
        }

        .type-answer-result {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 6px;
            display: none;
        }

        .type-answer-result.show {
            display: inline-block;
        }

        .type-answer-result.perfect {
            color: #2e7d32;
            background: #e8f5e9;
        }

        .type-answer-result.partial {
            color: #f57f17;
            background: #fff9c4;
        }

        .type-answer-result.incorrect {
            color: #c62828;
            background: #ffebee;
        }

        /* ── Typing Mode Layout Overrides ── */
        /* Desktop/tablet: content can shrink for type area, but stays centered */
        body.typing-active .card-content {
            flex-shrink: 1;
            overflow-y: auto;
            min-height: 0;
        }

        body.typing-active .type-answer-area {
            padding-bottom: 4px;
            flex-shrink: 0;
        }

        /* Mobile only: input sizing */
        @media (max-width: 768px) {
            .type-answer-input {
                font-size: 1rem;
                padding: 9px 12px;
            }

            .type-answer-mirror {
                font-size: 1rem;
                padding: 9px 12px;
            }
        }

        /* Track Progress Toggle - Compact */
        .track-progress-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-primary);
            padding: 8px 12px;
            border-radius: 20px;
            border: 2px solid var(--border);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .track-progress-toggle:hover {
            border-color: var(--accent);
        }

        .track-progress-toggle label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-primary);
            cursor: pointer;
            user-select: none;
            margin: 0;
            white-space: nowrap;
        }

        .toggle-switch {
            position: relative;
            width: 40px;
            height: 22px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--border);
            transition: 0.3s;
            border-radius: 22px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        input:checked + .toggle-slider {
            background-color: var(--accent);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(18px);
        }

        /* Bottom Controls */
        .bottom-controls {
            display: flex;
            gap: 6px;
        }

        .btn {
            padding: 10px 14px;
            border: 2px solid var(--border);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
        }

        .settings-btn {
            width: 38px;
            height: 38px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1rem;
        }

        /* Shortcut Hint Bar */
        .shortcut-hint-bar {
            position: fixed;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%) translateY(100%);
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 12px 20px;
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--text-secondary);
            transition: transform 0.3s ease, opacity 0.3s ease;
            z-index: 99;
            box-shadow: 0 4px 12px var(--shadow);
            max-width: 90%;
            opacity: 0;
        }

        .shortcut-hint-bar.active {
            display: flex;
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .shortcut-hint-bar.hiding {
            transform: translateX(-50%) translateY(100%);
            opacity: 0;
        }

        /* Swipe Feedback */
        .flashcard-wrapper.swiping-left::after,
        .flashcard-wrapper.swiping-right::after {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            font-weight: 700;
            opacity: 0.7;
            z-index: 10;
        }

        .flashcard-wrapper.swiping-left::after {
            content: '✗ Still Learning';
            left: 20px;
            color: #d47574;
        }

        .flashcard-wrapper.swiping-right::after {
            content: '✓ Know';
            right: 20px;
            color: #5a9b8e;
        }

        /* Focus Button */
        .focus-btn {
            position: fixed;
            bottom: 80px;
            right: 15px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent);
            color: white;
            border: 2px solid var(--accent);
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 4px 16px var(--shadow-hover);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .study-screen .focus-btn {
            display: flex;
        }

        .focus-btn.active {
            background: var(--accent-hover);
        }

        body.focus-mode .bottom-bar,
        body.focus-mode .progress-indicator {
            display: none !important;
        }

        @media (min-width: 769px) {
            .focus-btn {
                display: none !important;
            }
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
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-content h2 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        /* Settings Sections */
        .settings-section {
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--border);
        }

        .settings-section:last-of-type {
            border-bottom: none;
            margin-bottom: 20px;
        }

        .settings-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .setting-group {
            margin-bottom: 16px;
        }

        .setting-group:last-child {
            margin-bottom: 0;
        }

        .setting-group label {
            display: block;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .setting-description {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 6px;
            line-height: 1.4;
        }

        .danger-btn {
            background: #e57373 !important;
            color: white !important;
            border-color: #e57373 !important;
        }

        .danger-btn:hover {
            background: #d32f2f !important;
            border-color: #d32f2f !important;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
            justify-content: flex-end;
        }

        /* Study Options Modal */
        .study-options-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .study-option-btn {
            display: grid;
            grid-template-columns: 40px 1fr;
            gap: 16px;
            align-items: center;
            padding: 16px 20px;
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            width: 100%;
            min-height: 72px;
            font-family: 'DM Sans', sans-serif;
        }

        .study-option-btn:hover {
            background: var(--bg-card);
            border-color: var(--accent);
            transform: translateX(4px);
        }

        .study-option-btn.danger {
            border-color: #e57373;
        }

        .study-option-btn.danger:hover {
            border-color: #d32f2f;
            background: rgba(229, 115, 115, 0.1);
        }

        .option-icon {
            font-size: 28px;
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            font-family: "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji", sans-serif;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        .option-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            min-width: 0;
        }

        .option-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-primary);
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .option-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .theme-selector-inline {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .theme-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid var(--border);
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px var(--shadow);
        }

        .theme-btn:hover {
            transform: scale(1.1);
        }

        .theme-btn.active {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 20%, transparent);
        }

        .theme-btn[data-theme="light"] { background: linear-gradient(135deg, #FFFBF4, #FDF3E0); }
        .theme-btn[data-theme="dark"] { background: linear-gradient(135deg, #1C1C24, #22222C); }


        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .bottom-bar {
                padding: 10px;
                flex-wrap: wrap;
            }
            
            .bottom-bar-section {
                gap: 8px;
            }
            
            .bottom-bar-section.center {
                order: -1;
                width: 100%;
                justify-content: center;
                margin-bottom: 8px;
            }
            
            .track-progress-toggle {
                padding: 6px 10px;
            }
            
            .track-progress-toggle label {
                font-size: 0.75rem;
            }
            
            .toggle-switch {
                width: 36px;
                height: 20px;
            }
            
            .toggle-slider:before {
                height: 14px;
                width: 14px;
            }
            
            input:checked + .toggle-slider:before {
                transform: translateX(16px);
            }
            
            .nav-btn {
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
            }
            
            /* Pile mode buttons - full width on mobile */
            .navigation.pile-mode {
                gap: 6px;
            }
            
            .navigation.pile-mode .nav-btn {
                height: 54px;
                font-size: 0.8rem;
                padding: 4px 2px;
            }
            
            .btn {
                padding: 8px 10px;
                font-size: 0.75rem;
            }
            
            .bottom-controls {
                gap: 5px;
            }
            
            .settings-btn {
                width: 32px;
                height: 32px;
                padding: 6px;
                font-size: 0.9rem;
            }

            /* Match gear/back to pile button height on mobile */
            .bottom-bar.pile-active .bottom-controls .btn,
            .bottom-bar.pile-active .bottom-controls .settings-btn {
                height: 54px;
                width: 38px;
                border-radius: 10px;
            }
            
            .bottom-bar {
                padding: 10px 12px;
                gap: 8px;
            }
            
            .card-face {
                padding: 50px 15px 15px;
            }
            
            .card-title {
                font-size: 0.95rem;
            }
            
            .card-title-progress {
                font-size: 0.75rem;
            }
            
            .card-content {
                font-size: 1.1rem;
            }
            
            .progress-indicator {
                top: 10px;
                left: 10px;
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            
            .decks-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .deck-card {
                padding: 20px 14px;
                border-radius: 10px;
            }
            
            .deck-card h3 {
                font-size: 1rem;
            }
            
            .deck-card .card-count {
                font-size: 0.8rem;
            }
            
            .deck-checkbox {
                width: 18px;
                height: 18px;
            }
            
            .shortcut-hint-bar {
                font-size: 0.75rem;
                padding: 8px 14px;
                bottom: 90px;
            }
        }

        .loading {
            text-align: center;
            padding: 20px 20px;
            color: var(--text-muted);
        }
    </style>
</head>
<body data-theme="light">
    <button class="focus-btn" id="focusBtn">Focus</button>

    <div class="container">
        <div class="deck-selection" id="deckSelection">
            <header>
                <div class="logo-section">
                    <a href="/"><img src="pbe_team_bold_logo.png" alt="PBE Team Bold Logo" class="logo"></a>
                    <div class="title-section">
                        <h1>Flashcards</h1>
                        <p>Learn, review, master</p>
                    </div>
                </div>
                
                <div class="header-controls">
                    <div class="search-bar header-search">
                        <input type="text" id="deckSearch" name="deckSearch" placeholder="Search decks...">
                    </div>
                    <button class="global-settings-btn" id="globalSettingsBtn" title="Global Settings">⚙️</button>
                </div>
            </header>

            <div class="deck-controls">
                <div class="filter-row">
                    <div class="category-filters" id="categoryFilters">
                        <!-- Categories will be populated by JavaScript -->
                    </div>
                    
                    <div class="sort-control">
                        <label for="sortDecks">Sort:</label>
                        <select id="sortDecks">
                            <option value="alpha">A-Z</option>
                            <option value="recent">Recently Used</option>
                            <option value="progress">Progress</option>
                        </select>
                    </div>
                </div>
                
                <!-- Recent Studied Section -->
                <div class="recent-studied-section" id="recentStudiedSection" style="display: none;">
                    <div class="recent-studied-header">
                        <h3>Recently Studied</h3>
                        <button class="clear-all-btn" id="clearAllRecentBtn">Clear All</button>
                    </div>
                    <div class="recent-studied-items" id="recentStudiedItems">
                        <!-- Recent items will be populated by JavaScript -->
                    </div>
                </div>
                
                <div class="deck-stats" id="deckStats">
                    <!-- Stats will be populated by JavaScript -->
                </div>
                
                <div class="deck-selection-controls" id="deckSelectionControls" style="display: none;">
                    <button id="selectAllDecksBtn">Select All</button>
                    <button id="selectNoneDecksBtn">Select None</button>
                </div>
            </div>

            <div id="decksContainer" class="loading">Loading decks...</div>
            
            <div class="multi-deck-footer" id="multiDeckActions" style="display: none;">
                <div class="multi-deck-info">
                    <span id="selectedDeckCount">0</span> decks selected <span id="selectedCardCount"></span>
                </div>
                <div class="multi-deck-controls">
                    <button class="btn" id="clearSelectionBtn">Clear</button>
                    <button class="btn btn-primary" id="studySelectedBtn">Study Selected</button>
                </div>
            </div>
        </div>

        <div class="study-screen" id="studyScreen">
            <div class="study-layout">
                <div class="flashcard-wrapper" id="flashcardWrapper">
                    <div class="flashcard-container">
                        <div class="flashcard" id="flashcard">
                            <div class="card-face front">
                                <div class="card-title">
                                    <span class="card-title-progress" id="cardProgressFront"></span>
                                    <span class="card-title-text" id="cardTitleFront"></span>
                                </div>
                                <div class="card-progress-counters" id="cardProgressCountersFront">
                                    <div class="still-learning-count"><span id="stillLearningFront">0</span> Still Learning</div>
                                    <div class="know-count"><span id="knowFront">0</span> Know</div>
                                </div>
                                <div class="pile-display" id="pileDisplayFront">
                                    <div class="pile-item pile1">
                                        <span class="pile-label">Don't Know</span>
                                        <span class="pile-count" id="pile1CountFront">0</span>
                                    </div>
                                    <div class="pile-item pile2">
                                        <span class="pile-label">Learning</span>
                                        <span class="pile-count" id="pile2CountFront">0</span>
                                    </div>
                                    <div class="pile-item pile3">
                                        <span class="pile-label">Know</span>
                                        <span class="pile-count" id="pile3CountFront">0</span>
                                    </div>
                                </div>
                                <div class="card-content" id="questionText"></div>
                                <div class="type-answer-area" id="typeAnswerArea">
                                    <div class="type-answer-input-wrapper">
                                        <div class="type-answer-mirror" id="typeAnswerMirror"></div>
                                        <input type="text" class="type-answer-input" id="typeAnswerInput" placeholder="Type your answer..." autocomplete="off" autocapitalize="off" spellcheck="false">
                                        <button class="type-answer-submit" id="typeAnswerSubmit">✓</button>
                                    </div>
                                    <div class="type-answer-result" id="typeAnswerResult"></div>
                                </div>
                            </div>
                            <div class="card-face back">
                                <div class="card-title">
                                    <span class="card-title-progress" id="cardProgressBack"></span>
                                    <span class="card-title-text" id="cardTitleBack"></span>
                                </div>
                                <div class="card-progress-counters" id="cardProgressCountersBack">
                                    <div class="still-learning-count"><span id="stillLearningBack">0</span> Still Learning</div>
                                    <div class="know-count"><span id="knowBack">0</span> Know</div>
                                </div>
                                <div class="pile-display" id="pileDisplayBack">
                                    <div class="pile-item pile1">
                                        <span class="pile-label">Don't Know</span>
                                        <span class="pile-count" id="pile1CountBack">0</span>
                                    </div>
                                    <div class="pile-item pile2">
                                        <span class="pile-label">Learning</span>
                                        <span class="pile-count" id="pile2CountBack">0</span>
                                    </div>
                                    <div class="pile-item pile3">
                                        <span class="pile-label">Know</span>
                                        <span class="pile-count" id="pile3CountBack">0</span>
                                    </div>
                                </div>
                                <div class="card-content" id="answerText"></div>
                                <div class="card-reference" id="referenceText"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shortcut-hint-bar" id="shortcutHintBar" style="display: none;"></div>

                <div class="bottom-bar">
                    <div class="bottom-bar-section">
                        <!-- Pile mode is now in settings -->
                    </div>

                    <div class="bottom-bar-section center">
                        <div class="navigation" id="navigation">
                            <button class="nav-btn" id="prevBtn">←</button>
                            <button class="nav-btn" id="nextBtn">→</button>
                            <button class="nav-btn" id="pile1Btn" style="display: none;">❌<br>Don't Know</button>
                            <button class="nav-btn" id="pile2Btn" style="display: none;">📚<br>Learning</button>
                            <button class="nav-btn" id="pile3Btn" style="display: none;">✅<br>Know</button>
                        </div>
                    </div>

                    <div class="bottom-bar-section">
                        <div class="bottom-controls">
                            <button class="btn settings-btn" id="studySettingsBtn">⚙️</button>
                            <button class="btn btn-primary" id="backToDecksBtn">←</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Settings Modal -->
    <div class="modal" id="globalSettingsModal">
        <div class="modal-content" style="max-width: 550px;">
            <h2>⚙️ Global Settings</h2>
            
            <!-- Appearance Section -->
            <div class="settings-section">
                <h3 class="settings-section-title">🎨 Appearance</h3>
                <div class="setting-group">
                    <label>Theme:</label>
                    <div class="theme-selector-inline">
                        <button class="theme-btn active" data-theme="light"></button>
                        <button class="theme-btn" data-theme="dark"></button>
                    </div>
                </div>
                <div class="setting-group">
                    <label>Text Size:</label>
                    <select id="textSizeSelect" class="btn" style="width: 100%; padding: 12px;">
                        <option value="tiny">Tiny</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large" selected>Large (Default)</option>
                        <option value="extra-large">Extra Large</option>
                        <option value="huge">Huge</option>
                        <option value="massive">Massive</option>
                    </select>
                </div>
            </div>
            
            <!-- Pile Mode Settings Section -->
            <div class="settings-section">
                <h3 class="settings-section-title">📚 Pile Mode Settings</h3>
                <div class="setting-group">
                    <label>Pile Button Style:</label>
                    <select id="pileButtonStyleSelect" class="btn" style="width: 100%; padding: 12px;">
                        <option value="style1" selected>Emoji + Text</option>
                        <option value="style2">Solid Colors</option>
                        <option value="style3">Minimalist</option>
                        <option value="style4">Modern Gradient</option>
                    </select>
                    <p class="setting-description">Choose how pile buttons appear when using pile mode</p>
                </div>
                <div class="setting-group">
                    <label>Keyboard Shortcuts:</label>
                    <select id="pileKeyboardSelect" class="btn" style="width: 100%; padding: 12px;">
                        <option value="keys123" selected>1, 2, 3</option>
                        <option value="arrows">←, ↓, →</option>
                        <option value="qwe">Q, W, E</option>
                        <option value="asd">A, S, D</option>
                    </select>
                    <p class="setting-description">Keyboard shortcuts for sorting cards into piles</p>
                </div>
            </div>
            
            <!-- Recent Studied Section -->
            <div class="settings-section">
                <h3 class="settings-section-title">🕐 Recent Studied</h3>
                <div class="setting-group">
                    <label>Number of Recent Items:</label>
                    <select id="recentCountSelect" class="btn" style="width: 100%; padding: 12px;">
                        <option value="3" selected>3 (Default)</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                    </select>
                    <p class="setting-description">How many recent study sessions to show on the deck page</p>
                </div>
                <div class="setting-group">
                    <button class="btn" id="clearRecentStudiedBtn" style="width: 100%;">Clear Recent History</button>
                </div>
            </div>
            
            <!-- Other Section -->
            <div class="settings-section">
                <h3 class="settings-section-title">🔧 Other</h3>
                <div class="setting-group">
                    <button class="btn danger-btn" id="clearAllDecksBtn" style="width: 100%;">Clear All Progress</button>
                    <p class="setting-description">⚠️ This will delete all pile data for all decks</p>
                </div>
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-primary" id="closeGlobalSettingsBtn">Close</button>
            </div>
        </div>
    </div>

    <!-- Deck Settings Modal -->
    <div class="modal" id="deckSettingsModal">
        <div class="modal-content">
            <h2>⚙️ <span id="deckSettingsTitle">Deck Settings</span></h2>
            <div class="setting-group">
                <label>Clear Progress:</label>
                <button class="btn" id="clearCurrentDeckBtn" style="margin-top: 10px; width: 100%;">Clear This Deck</button>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 8px;">
                    This will reset all pile data for this deck
                </p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" id="closeDeckSettingsBtn">Close</button>
            </div>
        </div>
    </div>

    <!-- Study Settings Modal (in study view) -->
    <div class="modal" id="settingsModal">
        <div class="modal-content">
            <h2>⚙️ Study Settings</h2>
            <div class="setting-group">
                <label>Theme:</label>
                <div class="theme-selector-inline">
                    <button class="theme-btn active" data-theme="light"></button>
                    <button class="theme-btn" data-theme="dark"></button>
                </div>
            </div>
            <div class="setting-group">
                <label>Text Size:</label>
                <select id="textSizeSelectStudy" class="btn" style="width: 100%; padding: 12px;">
                    <option value="tiny">Tiny</option>
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large" selected>Large (Default)</option>
                    <option value="extra-large">Extra Large</option>
                    <option value="huge">Huge</option>
                    <option value="massive">Massive</option>
                </select>
            </div>
            <div class="setting-group">
                <label>Clear Progress:</label>
                <button class="btn" id="clearCurrentDeckBtnStudy" style="margin-top: 10px; width: 100%;">Clear This Deck</button>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 8px;">
                    This will reset all pile data for this deck
                </p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" id="closeSettingsBtn">Close</button>
            </div>
        </div>
    </div>

    <div class="pile-selection-screen" id="pileSelectionScreen">
        <div class="pile-selection-content">
            <div class="pile-selection-header">
                <h2>📚 Choose What to Study</h2>
                <p id="pileSelectionDeckName"></p>
            </div>

            <div class="pile-options">
                <div class="pile-option all" id="studyAllPiles">
                    <div class="pile-option-info">
                        <div class="pile-option-icon">📚</div>
                        <div class="pile-option-text">
                            <h3>Study All Cards</h3>
                            <p>Review all piles together</p>
                        </div>
                    </div>
                    <div class="pile-option-count" id="allPilesCount">0</div>
                </div>

                <div class="pile-option pile1" id="studyPile1">
                    <div class="pile-option-info">
                        <div class="pile-option-icon">❌</div>
                        <div class="pile-option-text">
                            <h3>Don't Know</h3>
                            <p>Cards you're still learning</p>
                        </div>
                    </div>
                    <div class="pile-option-count" id="pile1SelectCount">0</div>
                </div>

                <div class="pile-option pile2" id="studyPile2">
                    <div class="pile-option-info">
                        <div class="pile-option-icon">📚</div>
                        <div class="pile-option-text">
                            <h3>Learning</h3>
                            <p>Cards you're getting better at</p>
                        </div>
                    </div>
                    <div class="pile-option-count" id="pile2SelectCount">0</div>
                </div>

                <div class="pile-option pile3" id="studyPile3">
                    <div class="pile-option-info">
                        <div class="pile-option-icon">✅</div>
                        <div class="pile-option-text">
                            <h3>Know</h3>
                            <p>Cards you've mastered</p>
                        </div>
                    </div>
                    <div class="pile-option-count" id="pile3SelectCount">0</div>
                </div>
            </div>

            <div class="pile-selection-actions">
                <div class="pile-shuffle-option">
                    <input type="checkbox" id="pileShuffleCheck" name="pileShuffleCheck">
                    <label for="pileShuffleCheck">Shuffle cards</label>
                </div>
                <button class="btn btn-primary" id="backToDecksFromPileSelect">← Back to Decks</button>
            </div>
        </div>
    </div>

    <div class="deck-finished-modal" id="deckFinishedModal">
        <div class="deck-finished-content">
            <h2>🎉 Deck Complete!</h2>
            <p id="deckFinishedMessage">Great job! You've reviewed all cards.</p>
            <div class="deck-finished-actions">
                <button class="btn btn-primary" id="studyStillLearningFinished">Study Still Learning Cards</button>
                <button class="btn" id="restartDeckFinished">Restart Deck</button>
                <button class="btn" id="backToDecksFinished">Back to Decks</button>
            </div>
        </div>
    </div>

    <!-- Study Options Modal -->
    <div class="modal" id="studyOptionsModal">
        <div class="modal-content" style="max-width: 400px;">
            <h2>📚 <span id="studyOptionsTitle">Study Options</span></h2>
            <div class="study-options-list">
                <button class="study-option-btn" id="resumeStudyBtn" style="display: none;">
                    <span class="option-icon">▶️</span>
                    <div class="option-text">
                        <div class="option-title">Resume</div>
                        <div class="option-subtitle">Continue where you left off</div>
                    </div>
                </button>
                <button class="study-option-btn" id="studyLinearBtn">
                    <span class="option-icon">📖</span>
                    <div class="option-text">
                        <div class="option-title">Study</div>
                        <div class="option-subtitle">Linear order from beginning</div>
                    </div>
                </button>
                <button class="study-option-btn" id="studyShuffledBtn">
                    <span class="option-icon">🔀</span>
                    <div class="option-text">
                        <div class="option-title">Study Shuffled</div>
                        <div class="option-subtitle">Random order</div>
                    </div>
                </button>
                <button class="study-option-btn" id="studyWithPilesBtn">
                    <span class="option-icon">📚</span>
                    <div class="option-text">
                        <div class="option-title">Study with Piles</div>
                        <div class="option-subtitle">Sort into know/learning/don't know</div>
                    </div>
                </button>
                <button class="study-option-btn" id="studyWithTypingBtn" style="display: none;">
                    <span class="option-icon">⌨️</span>
                    <div class="option-text">
                        <div class="option-title">Study with Typing</div>
                        <div class="option-subtitle">Type answers & auto-sort into piles</div>
                    </div>
                </button>
                <button class="study-option-btn danger" id="clearProgressBtn" style="display: none;">
                    <span class="option-icon">🗑️</span>
                    <div class="option-text">
                        <div class="option-title">Clear Progress</div>
                        <div class="option-subtitle">Reset pile data for this deck</div>
                    </div>
                </button>
            </div>
            <button class="btn" id="closeStudyOptionsBtn" style="width: 100%; margin-top: 16px;">Cancel</button>
        </div>
    </div>

    <script>
        <?php
        echo "\n        console.log('Found " . count($decksData) . " decks:', DECKS_DATA);";
        ?>

        // Process DECKS_DATA into usable format
        const availableDecks = DECKS_DATA.map(d => d.file); // For backward compatibility
        const deckCategories = {};
        const deckPaths = {};
        
        DECKS_DATA.forEach(deck => {
            if (!deckCategories[deck.category]) {
                deckCategories[deck.category] = [];
            }
            deckCategories[deck.category].push(deck.file);
            deckPaths[deck.file] = deck.path;
        });

        // State for filtering and sorting
        let activeCategory = 'All';
        let searchQuery = '';
        let sortMode = 'alpha'; // alpha, recent, progress
        
        const state = {
            currentDeck: null,
            currentDeckForSettings: null, // Track which deck's settings we're editing
            selectedDecks: [],
            pendingStudyDecks: [], // Decks waiting for study option selection
            cards: [],
            allCards: [],
            totalCards: 0,
            currentIndex: 0,
            flipped: false,
            deckCache: {}, // Cache for pre-loaded deck data: { filename: { csv, cards, timestamp } }
            preloadInProgress: new Set(), // Track which decks are currently being pre-loaded
            settings: {
                shuffle: false,
                pileMode: false, // Now determined at study time, not deck-level
                shortcutHintSeen: false,
                textSize: 'large',
                pileButtonStyle: 'style1', // style1, style2, style3, style4
                pileKeyboardShortcuts: 'keys123', // keys123, arrows, qwe, asd
                recentStudiedCount: 3 // How many recent items to show
            },
            pileData: {
                pile1: [], // Don't Know (red)
                pile2: [], // Learning (yellow)
                pile3: []  // Know (green)
            },
            currentPile: 'all', // 'all', 'pile1', 'pile2', 'pile3'
            currentStudyMode: 'linear', // 'linear', 'shuffled', 'piles', 'typing'
            focusModeActive: false,
            typeMode: false,
            typeSubmitted: false,
            lastTypeResult: null
        };

        const DOM = {
            deckSelection: document.getElementById('deckSelection'),
            studyScreen: document.getElementById('studyScreen'),
            flashcard: document.getElementById('flashcard'),
            flashcardWrapper: document.getElementById('flashcardWrapper'),
            navigation: document.getElementById('navigation'),
            questionText: document.getElementById('questionText'),
            answerText: document.getElementById('answerText'),
            referenceText: document.getElementById('referenceText'),
            shortcutHintBar: document.getElementById('shortcutHintBar'),
            typeAnswerArea: document.getElementById('typeAnswerArea'),
            typeAnswerInput: document.getElementById('typeAnswerInput'),
            typeAnswerMirror: document.getElementById('typeAnswerMirror'),
            typeAnswerSubmit: document.getElementById('typeAnswerSubmit'),
            typeAnswerResult: document.getElementById('typeAnswerResult')
        };

        function parseFileName(filename) {
            let name = filename.replace('.csv', '').replace(/[-_]/g, ' ').replace(/([a-zA-Z])(\d)/g, '$1 $2');
            
            // Smart capitalization: detect acronyms
            return name.split(' ').map(word => {
                const trimmed = word.trim();
                if (!trimmed) return '';
                
                // If word is 2-5 characters, all letters, AND all lowercase (likely acronym like pbe, abg, vbs)
                if (trimmed.length >= 2 && trimmed.length <= 5 && /^[a-z]+$/.test(trimmed)) {
                    return trimmed.toUpperCase();
                }
                
                // Otherwise, title case (first letter uppercase, rest lowercase)
                return trimmed.charAt(0).toUpperCase() + trimmed.slice(1).toLowerCase();
            }).join(' ');
        }

        // Recent Studied Functions
        function getRecentStudied() {
            const recent = localStorage.getItem('recentStudied');
            return recent ? JSON.parse(recent) : [];
        }

        function saveRecentStudied(decks, studyMode) {
            const recent = getRecentStudied();
            const deckKey = Array.isArray(decks) ? decks.sort().join(',') : decks;
            
            // Remove if already exists
            const filtered = recent.filter(item => item.deckKey !== deckKey);
            
            // Add to beginning
            filtered.unshift({
                deckKey: deckKey,
                decks: Array.isArray(decks) ? decks : [decks],
                studyMode: studyMode,
                timestamp: Date.now()
            });
            
            // Keep only configured number
            const maxCount = state.settings.recentStudiedCount || 3;
            const trimmed = filtered.slice(0, maxCount);
            
            localStorage.setItem('recentStudied', JSON.stringify(trimmed));
            loadRecentStudied();
        }

        function loadRecentStudied() {
            const recent = getRecentStudied();
            const container = document.getElementById('recentStudiedItems');
            const section = document.getElementById('recentStudiedSection');
            
            if (recent.length === 0) {
                section.style.display = 'none';
                return;
            }
            
            section.style.display = 'block';
            container.innerHTML = '';
            
            const maxCount = state.settings.recentStudiedCount || 3;
            recent.slice(0, maxCount).forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'recent-item';
                
                // Use smart deck name formatting (same logic as formatDeckName)
                const deckNames = formatDeckNames(item.decks);
                const studyModeLabel = item.studyMode === 'piles' ? '📚 Piles' : 
                                      item.studyMode === 'shuffled' ? '🔀 Shuffled' : 
                                      item.studyMode === 'typing' ? '⌨️ Typing' : '📖 Linear';
                
                // Calculate total cards
                let totalCards = 0;
                item.decks.forEach(file => {
                    const deckData = DECKS_DATA.find(d => d.file === file);
                    if (deckData) totalCards += deckData.cardCount || 0;
                });
                
                itemDiv.innerHTML = `
                    <div class="recent-item-info">
                        <div class="recent-item-name">${deckNames}</div>
                        <div class="recent-item-details">${studyModeLabel} • ${totalCards} cards</div>
                    </div>
                    <div class="recent-item-actions">
                        <button class="recent-item-button">Study</button>
                        <button class="recent-item-remove" title="Remove from recent">×</button>
                    </div>
                `;
                
                itemDiv.querySelector('.recent-item-button').onclick = (e) => {
                    e.stopPropagation();
                    openStudyOptionsModal(item.decks);
                };
                
                itemDiv.querySelector('.recent-item-remove').onclick = (e) => {
                    e.stopPropagation();
                    removeRecentItem(item.decks);
                };
                
                container.appendChild(itemDiv);
            });
        }

        function clearRecentStudied() {
            if (confirm('Clear all recently studied history?')) {
                localStorage.removeItem('recentStudied');
                loadRecentStudied();
            }
        }

        function removeRecentItem(decks) {
            const recent = getRecentStudied();
            const deckKey = Array.isArray(decks) ? decks.sort().join(',') : decks;
            
            const filtered = recent.filter(item => {
                const itemKey = item.decks.sort().join(',');
                return itemKey !== deckKey;
            });
            
            localStorage.setItem('recentStudied', JSON.stringify(filtered));
            loadRecentStudied();
        }

        // Get unified pile data key for deck(s)
        function getPileDataKey(decks) {
            const deckArray = Array.isArray(decks) ? decks : [decks];
            return 'pileData_' + deckArray.sort().join(',');
        }

        // Open study options modal
        function openStudyOptionsModal(decks) {
            state.pendingStudyDecks = Array.isArray(decks) ? decks : [decks];
            
            // Start pre-loading decks immediately for instant loading when user clicks a study mode
            preloadDecks(state.pendingStudyDecks);
            
            const modal = document.getElementById('studyOptionsModal');
            const title = document.getElementById('studyOptionsTitle');
            
            // Set title
            if (state.pendingStudyDecks.length === 1) {
                title.textContent = parseFileName(state.pendingStudyDecks[0]);
            } else {
                title.textContent = `${state.pendingStudyDecks.length} Decks`;
            }
            
            // Check if there's existing progress (pile data)
            const pileKey = getPileDataKey(state.pendingStudyDecks);
            const existingPileData = localStorage.getItem(pileKey);
            const hasProgress = existingPileData !== null;
            
            // Show/hide resume and clear progress buttons
            document.getElementById('resumeStudyBtn').style.display = hasProgress ? 'grid' : 'none';
            document.getElementById('clearProgressBtn').style.display = hasProgress ? 'grid' : 'none';
            
            // Show typing button only if any selected deck has typeable cards
            const hasTypeableCards = state.pendingStudyDecks.some(file => {
                const deckData = DECKS_DATA.find(d => d.file === file);
                return deckData && deckData.hasTypeable;
            });
            document.getElementById('studyWithTypingBtn').style.display = hasTypeableCards ? 'grid' : 'none';
            
            modal.classList.add('active');
        }

        function closeStudyOptionsModal() {
            document.getElementById('studyOptionsModal').classList.remove('active');
            state.pendingStudyDecks = [];
        }

        function parseCSV(csv) {
            const lines = csv.split('\n').filter(l => l.trim());
            return lines.map((line, index) => {
                const parts = [];
                let current = '', inQuotes = false;
                for (let i = 0; i < line.length; i++) {
                    const c = line[i], next = line[i + 1];
                    if (c === '"') {
                        if (inQuotes && next === '"') { current += '"'; i++; }
                        else inQuotes = !inQuotes;
                    } else if (c === ',' && !inQuotes) {
                        parts.push(current.trim());
                        current = '';
                    } else current += c;
                }
                parts.push(current.trim());
                
                if (parts.length >= 2) {
                    // Create deterministic ID from question content + index
                    const question = parts[0];
                    const answer = parts[1];
                    // Use first 30 chars of question + first 30 of answer + index for unique ID
                    const idBase = (question.substring(0, 30) + answer.substring(0, 30) + index).replace(/\s+/g, '_');
                    return {
                        question: question,
                        answer: answer,
                        reference: parts[2] || '',
                        typeable: (parts[3] || '').trim().toLowerCase() === 't',
                        id: idBase
                    };
                }
                return null;
            }).filter(Boolean);
        }

        function shuffleArray(arr) {
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
        }

        // ─── Type Answer Mode ───────────────────────────────────────
        function normalizeAnswer(str) {
            return str
                .toLowerCase()
                .replace(/[''ʼ`]/g, '')       // strip smart/curly apostrophes
                .replace(/'/g, '')              // strip straight apostrophe
                .replace(/[""]/g, '')           // strip smart quotes
                .replace(/[.,;:!?—–\-]/g, '')  // strip punctuation
                .replace(/\s+/g, ' ')           // collapse whitespace
                .trim();
        }

        function compareAnswers(typed, correct) {
            const normTyped = normalizeAnswer(typed);
            const normCorrect = normalizeAnswer(correct);
            
            if (normTyped === normCorrect) return { score: 1.0, label: 'perfect' };
            
            // Word-by-word comparison for partial scoring
            const typedWords = normTyped.split(' ').filter(w => w);
            const correctWords = normCorrect.split(' ').filter(w => w);
            
            if (typedWords.length === 0) return { score: 0, label: 'incorrect' };
            
            let matchedWords = 0;
            const usedIndices = new Set();
            
            typedWords.forEach(tw => {
                for (let i = 0; i < correctWords.length; i++) {
                    if (!usedIndices.has(i) && tw === correctWords[i]) {
                        matchedWords++;
                        usedIndices.add(i);
                        break;
                    }
                }
            });
            
            const score = matchedWords / Math.max(correctWords.length, 1);
            
            if (score >= 0.9) return { score, label: 'perfect' };
            if (score >= 0.5) return { score, label: 'partial' };
            return { score, label: 'incorrect' };
        }

        function renderTypingFeedback(typed, correctAnswer) {
            const mirror = DOM.typeAnswerMirror;
            
            if (!typed) {
                mirror.innerHTML = '';
                return;
            }
            
            const normCorrectChars = normalizeAnswer(correctAnswer).split('');
            const normTypedChars = normalizeAnswer(typed).split('');
            const typedChars = typed.split('');
            
            let html = '';
            for (let i = 0; i < typedChars.length; i++) {
                const normT = normalizeAnswer(typed.substring(0, i + 1));
                const normTLen = normT.length;
                const matchChar = normCorrectChars[normTLen - 1];
                const typedNormChar = normTypedChars[normTLen - 1];
                
                if (matchChar !== undefined && typedNormChar === matchChar) {
                    html += `<span class="char-correct">${escapeHtml(typedChars[i])}</span>`;
                } else {
                    html += `<span class="char-wrong">${escapeHtml(typedChars[i])}</span>`;
                }
            }
            
            mirror.innerHTML = html;
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, c => map[c]);
        }

        function submitTypedAnswer() {
            if (state.typeSubmitted) return;
            state.typeSubmitted = true;
            
            const card = state.cards[state.currentIndex];
            const typed = DOM.typeAnswerInput.value;
            const result = compareAnswers(typed, card.answer);
            
            // Store result for reference
            state.lastTypeResult = result;
            
            // Show result badge
            const resultEl = DOM.typeAnswerResult;
            resultEl.className = 'type-answer-result show ' + result.label;
            
            if (result.label === 'perfect') {
                resultEl.textContent = '✅ Correct!';
                DOM.typeAnswerInput.classList.add('correct');
            } else if (result.label === 'partial') {
                resultEl.textContent = `📚 Partial (${Math.round(result.score * 100)}%)`;
                DOM.typeAnswerInput.classList.add('wrong');
            } else {
                resultEl.textContent = '❌ Incorrect';
                DOM.typeAnswerInput.classList.add('wrong');
            }
            
            // Make typed text visible (no longer transparent)
            DOM.typeAnswerInput.classList.add('submitted');
            DOM.typeAnswerInput.disabled = true;
            DOM.typeAnswerMirror.innerHTML = '';
            DOM.typeAnswerSubmit.style.display = 'none';
            
            // Delay before auto-flip to reveal answer (1500ms)
            setTimeout(() => {
                flipCard();
                
                // Silently sort into correct pile (no advance)
                const cardId = card.id;
                state.pileData.pile1 = state.pileData.pile1.filter(id => id !== cardId);
                state.pileData.pile2 = state.pileData.pile2.filter(id => id !== cardId);
                state.pileData.pile3 = state.pileData.pile3.filter(id => id !== cardId);
                
                if (result.label === 'perfect') {
                    state.pileData.pile3.push(cardId);
                } else if (result.label === 'partial') {
                    state.pileData.pile2.push(cardId);
                } else {
                    state.pileData.pile1.push(cardId);
                }
                
                savePileData();
                updatePileCounters();
                
                // Show pile buttons so user can manually advance
                // (tapping a pile re-sorts + advances)
                updateNavigationButtons();
            }, 1500); // Auto-flip delay in ms
        }

        function resetTypeAnswerUI() {
            DOM.typeAnswerInput.value = '';
            DOM.typeAnswerInput.disabled = false;
            DOM.typeAnswerInput.classList.remove('correct', 'wrong', 'submitted');
            DOM.typeAnswerMirror.innerHTML = '';
            DOM.typeAnswerResult.className = 'type-answer-result';
            DOM.typeAnswerSubmit.style.display = 'flex';
            state.typeSubmitted = false;
        }

        function isCurrentCardTypeable() {
            if (!state.typeMode || !state.cards.length) return false;
            return state.cards[state.currentIndex].typeable === true;
        }

        function deckHasTypeableCards(cards) {
            return cards.some(c => c.typeable === true);
        }
        // ─── End Type Answer Mode ───────────────────────────────────

        function smartShuffle(cards) {
            // Group cards by deck
            const deckGroups = {};
            cards.forEach(card => {
                const deckName = card.deckName || 'unknown';
                if (!deckGroups[deckName]) deckGroups[deckName] = [];
                deckGroups[deckName].push(card);
            });

            // Shuffle each deck's cards
            Object.values(deckGroups).forEach(group => shuffleArray(group));

            // Smart interleaving - distribute cards evenly
            const result = [];
            const deckNames = Object.keys(deckGroups);
            const maxCards = Math.max(...Object.values(deckGroups).map(g => g.length));
            
            for (let i = 0; i < maxCards; i++) {
                for (const deckName of deckNames) {
                    if (deckGroups[deckName][i]) {
                        result.push(deckGroups[deckName][i]);
                    }
                }
            }

            // Apply no-more-than-2-in-a-row rule
            for (let i = 2; i < result.length; i++) {
                if (result[i].deckName === result[i-1].deckName && 
                    result[i].deckName === result[i-2].deckName) {
                    // Find a different card to swap with
                    for (let j = i + 1; j < result.length; j++) {
                        if (result[j].deckName !== result[i].deckName) {
                            [result[i], result[j]] = [result[j], result[i]];
                            break;
                        }
                    }
                }
            }

            return result;
        }

        function loadDecks() {
            console.log('loadDecks() called');
            console.log('availableDecks:', availableDecks);
            console.log('Protocol:', window.location.protocol);
            
            const container = document.getElementById('decksContainer');
            
            // Check if file is opened with file:// protocol
            if (window.location.protocol === 'file:') {
                container.innerHTML = '<div style="text-align:center;padding:40px;"><p style="color:red;font-weight:bold;">⚠️ PHP Not Executing</p><p>This file must be served through a web server (localhost, Apache, nginx, etc.)</p><p style="margin-top:15px;font-size:0.9rem;">Opening directly from file system won\'t work.</p></div>';
                return;
            }
            
            if (!availableDecks) {
                container.innerHTML = '<div style="text-align:center;padding:40px;"><p style="color:red;font-weight:bold;">Error: availableDecks is undefined!</p><p>Check browser console for details.</p></div>';
                return;
            }
            if (!availableDecks.length) {
                container.innerHTML = '<div style="text-align:center;padding:40px;"><p style="font-size:1.2rem;margin-bottom:10px;">📂 No decks found</p><p style="color:var(--text-muted);">Place CSV files in the <code>flashcards/</code> folder</p></div>';
                return;
            }
            
            // Build category filters (only once)
            buildCategoryFilters();
            
            // Filter decks based on active category and search
            let filteredDecks = filterDecks();
            
            // Sort decks
            filteredDecks = sortDecks(filteredDecks);
            
            // Update stats
            updateDeckStats(filteredDecks.length, availableDecks.length);
            
            // Build deck grid
            const grid = document.createElement('div');
            grid.className = 'decks-grid';
            
            filteredDecks.forEach(file => {
                const deckData = DECKS_DATA.find(d => d.file === file);
                const category = deckData ? deckData.category : 'Uncategorized';
                
                const card = document.createElement('div');
                card.className = 'deck-card';
                if (state.selectedDecks.includes(file)) card.classList.add('selected');
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'deck-checkbox';
                checkbox.checked = state.selectedDecks.includes(file);
                checkbox.onclick = e => { e.stopPropagation(); toggleDeckSelection(file); };
                
                // Add category badge
                const categoryBadge = document.createElement('div');
                categoryBadge.className = 'category-badge';
                categoryBadge.textContent = category;
                
                // Add card count badge
                const cardCount = deckData ? deckData.cardCount : 0;
                const cardCountBadge = document.createElement('div');
                cardCountBadge.className = 'card-count-badge';
                cardCountBadge.textContent = `${cardCount} cards`;
                
                // Check if pile mode is enabled for this deck
                const pileModeEnabled = localStorage.getItem(`pileMode_${file}`) === 'true';
                let extraInfo = '';
                
                if (pileModeEnabled) {
                    // Show pile counts
                    const pileDataKey = `pileData_${file}`;
                    const pileData = localStorage.getItem(pileDataKey);
                    
                    if (pileData) {
                        try {
                            const data = JSON.parse(pileData);
                            const pile1Count = data.pile1?.length || 0;
                            const pile2Count = data.pile2?.length || 0;
                            const pile3Count = data.pile3?.length || 0;
                            
                            if (pile1Count > 0 || pile2Count > 0 || pile3Count > 0) {
                                extraInfo = `<div class="deck-pile-info">
                                    <span style="color: #e57373;">❌ ${pile1Count}</span>
                                    <span style="color: #ffd54f;">📚 ${pile2Count}</span>
                                    <span style="color: #81c784;">✅ ${pile3Count}</span>
                                </div>`;
                            }
                        } catch (e) {}
                    }
                }
                
                card.innerHTML = `<h3>${parseFileName(file)}</h3><p class="card-count">Click to study</p>${extraInfo}`;
                card.insertBefore(checkbox, card.firstChild);
                card.appendChild(categoryBadge);
                card.appendChild(cardCountBadge);
                card.onclick = e => {
                    if (e.target !== checkbox && 
                        !e.target.classList.contains('category-badge') &&
                        !e.target.classList.contains('card-count-badge')) {
                        if (state.selectedDecks.length > 0) {
                            toggleDeckSelection(file);
                        } else {
                            openStudyOptionsModal([file]);
                        }
                    }
                };
                grid.appendChild(card);
            });
            
            if (filteredDecks.length === 0) {
                container.innerHTML = '<div style="text-align:center;padding:40px;"><p style="font-size:1.2rem;margin-bottom:10px;">🔍 No decks found</p><p style="color:var(--text-muted);">Try adjusting your search or filters</p></div>';
            } else {
                container.innerHTML = '';
                container.appendChild(grid);
            }
            
            // Show/hide deck selection controls
            const deckSelectionControls = document.getElementById('deckSelectionControls');
            if (deckSelectionControls) {
                deckSelectionControls.style.display = filteredDecks.length > 0 ? 'flex' : 'none';
            }
            
            document.getElementById('multiDeckActions').style.display = state.selectedDecks.length ? 'flex' : 'none';
            const countEl = document.getElementById('selectedDeckCount');
            if (countEl) countEl.textContent = state.selectedDecks.length;
            
            // Calculate total card count for selected decks
            const cardCountEl = document.getElementById('selectedCardCount');
            if (cardCountEl && state.selectedDecks.length > 0) {
                let totalCards = 0;
                state.selectedDecks.forEach(file => {
                    const deckData = DECKS_DATA.find(d => d.file === file);
                    if (deckData && deckData.cardCount) {
                        totalCards += deckData.cardCount;
                    }
                });
                cardCountEl.textContent = `(${totalCards} cards)`;
            } else if (cardCountEl) {
                cardCountEl.textContent = '';
            }
        }

        function buildCategoryFilters() {
            const container = document.getElementById('categoryFilters');
            if (container.dataset.built) return; // Only build once
            
            container.innerHTML = '';
            
            // Add "All" button
            const allBtn = document.createElement('button');
            allBtn.className = 'category-btn active';
            allBtn.innerHTML = `All <span class="count">(${availableDecks.length})</span>`;
            allBtn.onclick = () => {
                activeCategory = 'All';
                updateCategoryButtons();
                loadDecks();
            };
            container.appendChild(allBtn);
            
            // Add button for each category
            const sortedCategories = Object.keys(deckCategories).sort();
            sortedCategories.forEach(category => {
                const count = deckCategories[category].length;
                const btn = document.createElement('button');
                btn.className = 'category-btn';
                btn.innerHTML = `${category} <span class="count">(${count})</span>`;
                btn.dataset.category = category;
                btn.onclick = () => {
                    activeCategory = category;
                    updateCategoryButtons();
                    loadDecks();
                };
                container.appendChild(btn);
            });
            
            container.dataset.built = 'true';
        }

        function updateCategoryButtons() {
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active');
                if ((activeCategory === 'All' && btn.textContent.startsWith('All')) ||
                    (btn.dataset.category === activeCategory)) {
                    btn.classList.add('active');
                }
            });
        }

        function filterDecks() {
            let decks = [...availableDecks];
            
            // Filter by category
            if (activeCategory !== 'All') {
                decks = deckCategories[activeCategory] || [];
            }
            
            // Filter by search query
            if (searchQuery) {
                const query = searchQuery.toLowerCase();
                decks = decks.filter(file => {
                    const name = parseFileName(file).toLowerCase();
                    return name.includes(query);
                });
            }
            
            return decks;
        }

        function naturalSort(a, b) {
            // Natural sorting: handles numbers properly (e.g., "Isaiah 2" before "Isaiah 10")
            const aStr = parseFileName(a);
            const bStr = parseFileName(b);
            
            // Split into chunks of text and numbers
            const aParts = aStr.match(/(\d+|\D+)/g) || [];
            const bParts = bStr.match(/(\d+|\D+)/g) || [];
            
            for (let i = 0; i < Math.max(aParts.length, bParts.length); i++) {
                const aPart = aParts[i] || '';
                const bPart = bParts[i] || '';
                
                // Check if both parts are numbers
                const aNum = parseInt(aPart);
                const bNum = parseInt(bPart);
                
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    // Compare as numbers
                    if (aNum !== bNum) return aNum - bNum;
                } else {
                    // Compare as strings
                    const comparison = aPart.localeCompare(bPart);
                    if (comparison !== 0) return comparison;
                }
            }
            
            return 0;
        }

        function sortDecks(decks) {
            const recentlyUsed = JSON.parse(localStorage.getItem('recentlyUsed') || '{}');
            
            switch (sortMode) {
                case 'alpha':
                    return decks.sort((a, b) => naturalSort(a, b));
                
                case 'recent':
                    return decks.sort((a, b) => {
                        const aTime = recentlyUsed[a] || 0;
                        const bTime = recentlyUsed[b] || 0;
                        return bTime - aTime; // Most recent first
                    });
                
                case 'progress':
                    return decks.sort((a, b) => {
                        const aProgress = getDeckProgress(a);
                        const bProgress = getDeckProgress(b);
                        
                        // If progress is different, sort by progress (highest first)
                        if (bProgress !== aProgress) {
                            return bProgress - aProgress;
                        }
                        
                        // If progress is the same, use natural sort as secondary sort
                        return naturalSort(a, b);
                    });
                
                default:
                    return decks;
            }
        }

        function getDeckProgress(file) {
            const pileModeEnabled = localStorage.getItem(`pileMode_${file}`) === 'true';
            if (!pileModeEnabled) return 0;
            
            const pileData = localStorage.getItem(`pileData_${file}`);
            if (!pileData) return 0;
            
            try {
                const data = JSON.parse(pileData);
                const pile3Count = data.pile3?.length || 0;
                const totalCards = (data.pile1?.length || 0) + (data.pile2?.length || 0) + pile3Count;
                return totalCards > 0 ? (pile3Count / totalCards) * 100 : 0;
            } catch (e) {
                return 0;
            }
        }

        function updateDeckStats(showing, total) {
            const statsEl = document.getElementById('deckStats');
            if (showing === total) {
                statsEl.textContent = `Showing all ${total} decks`;
            } else {
                statsEl.textContent = `Showing ${showing} of ${total} decks`;
            }
        }

        function trackRecentlyUsed(deckNames) {
            const recentlyUsed = JSON.parse(localStorage.getItem('recentlyUsed') || '{}');
            const timestamp = Date.now();
            
            deckNames.forEach(name => {
                recentlyUsed[name] = timestamp;
            });
            
            localStorage.setItem('recentlyUsed', JSON.stringify(recentlyUsed));
        }

        function toggleDeckSelection(file) {
            const idx = state.selectedDecks.indexOf(file);
            
            idx > -1 ? state.selectedDecks.splice(idx, 1) : state.selectedDecks.push(file);
            
            // Pre-load the deck when selected for instant loading later
            if (idx === -1) {
                preloadDeck(file);
            }
            
            loadDecks();
        }

        // Pre-load deck data in the background for instant loading
        async function preloadDeck(file) {
            // Skip if already cached or currently loading
            if (state.deckCache[file] || state.preloadInProgress.has(file)) {
                return;
            }
            
            state.preloadInProgress.add(file);
            
            try {
                const filePath = deckPaths[file] || `flashcards/${file}`;
                const response = await fetch(filePath);
                const csv = await response.text();
                const cards = parseCSV(csv);
                cards.forEach(c => c.deckName = parseFileName(file));
                
                // Cache the parsed cards
                state.deckCache[file] = {
                    cards: cards,
                    timestamp: Date.now()
                };
                
                console.log(`✓ Pre-loaded deck: ${file} (${cards.length} cards)`);
            } catch (error) {
                console.error(`Failed to pre-load deck: ${file}`, error);
            } finally {
                state.preloadInProgress.delete(file);
            }
        }
        
        // Pre-load multiple decks
        async function preloadDecks(files) {
            const promises = files.map(file => preloadDeck(file));
            await Promise.all(promises);
        }
        
        // Background pre-loading of recently used decks on page load
        function backgroundPreloadRecentDecks() {
            const recent = getRecentStudied();
            if (recent.length > 0) {
                // Get unique deck files from recent items
                const recentDecks = new Set();
                recent.forEach(item => {
                    item.decks.forEach(deck => recentDecks.add(deck));
                });
                
                // Pre-load up to 10 most recent decks in background
                const decksToPreload = Array.from(recentDecks).slice(0, 10);
                console.log(`🔄 Background pre-loading ${decksToPreload.length} recent decks...`);
                
                // Start pre-loading after a short delay to not interfere with page load
                setTimeout(() => {
                    preloadDecks(decksToPreload);
                }, 500);
            }
        }

        async function loadDeck(files, studyMode = 'linear') {
            if (!Array.isArray(files)) files = [files];
            state.currentDeck = files.length === 1 ? files[0] : files.join(',');
            state.currentIndex = 0;
            state.flipped = false;
            state.allCards = [];
            state.currentStudyMode = studyMode;
            
            // Track recently used decks
            trackRecentlyUsed(files);
            
            // Set shuffle based on study mode
            state.settings.shuffle = (studyMode === 'shuffled');
            
            // Set pile mode based on study mode (typing uses piles under the hood)
            state.settings.pileMode = (studyMode === 'piles' || studyMode === 'typing');
            
            // Set type mode
            state.typeMode = (studyMode === 'typing');
            document.body.classList.toggle('typing-active', state.typeMode);
            
            // Load pile data using unified key
            loadPileData();

            // Load cards - use cache when available for instant loading
            for (const file of files) {
                let cards;
                
                // Check if deck is cached
                if (state.deckCache[file]) {
                    // Use cached data - instant loading!
                    cards = JSON.parse(JSON.stringify(state.deckCache[file].cards)); // Deep clone
                    console.log(`⚡ Used cached data for: ${file}`);
                } else {
                    // Not cached - fetch normally
                    const filePath = deckPaths[file] || `flashcards/${file}`;
                    const response = await fetch(filePath);
                    const csv = await response.text();
                    cards = parseCSV(csv);
                    cards.forEach(c => c.deckName = parseFileName(file));
                    
                    // Cache for next time
                    state.deckCache[file] = {
                        cards: JSON.parse(JSON.stringify(cards)),
                        timestamp: Date.now()
                    };
                    console.log(`📥 Loaded and cached: ${file}`);
                }
                
                state.allCards.push(...cards);
            }

            // Initialize new cards to pile1 (Don't Know) - only for pile mode
            if (state.settings.pileMode) {
                const sortedIds = new Set([...state.pileData.pile1, ...state.pileData.pile2, ...state.pileData.pile3]);
                state.allCards.forEach(card => {
                    if (!sortedIds.has(card.id) && !state.pileData.pile1.includes(card.id)) {
                        state.pileData.pile1.push(card.id);
                        sortedIds.add(card.id); // Add to set to prevent double-adding
                    }
                });
                
                // Clean up any duplicate IDs that might have crept in
                state.pileData.pile1 = [...new Set(state.pileData.pile1)];
                state.pileData.pile2 = [...new Set(state.pileData.pile2)];
                state.pileData.pile3 = [...new Set(state.pileData.pile3)];
                
                savePileData();
            }

            state.cards = [...state.allCards];
            state.totalCards = state.cards.length;
            
            // Use smart shuffle for multiple decks or if shuffled mode
            if (state.settings.shuffle) {
                if (files.length > 1) {
                    state.cards = smartShuffle(state.cards);
                } else {
                    shuffleArray(state.cards);
                }
            }
            
            state.selectedDecks = [];
            
            // Save to recent studied AFTER deck is loaded
            saveRecentStudied(files, studyMode);
            
            // If pile mode is on, show pile selection screen
            if (state.settings.pileMode) {
                showPileSelectionScreen();
            } else {
                showStudyScreen();
                loadProgress();
                loadSettings(); // Sync study view modal with current deck settings
                displayCard();
            }
        }

        function filterCards() {
            // No longer used in pile mode - keeping for compatibility
            state.cards = [...state.allCards];
        }

        function showStudyScreen() {
            DOM.deckSelection.style.display = 'none';
            DOM.studyScreen.style.display = 'block';
        }

        function displayCard(direction = 'none') {
            if (!state.cards.length) return;
            
            // Immediately hide hint bar completely
            const hintBar = DOM.shortcutHintBar;
            if (hintBar.classList.contains('active')) {
                hintBar.classList.remove('active');
                hintBar.classList.add('hiding');
                setTimeout(() => {
                    hintBar.classList.remove('hiding');
                    hintBar.style.display = 'none';
                }, 300);
            }
            
            if (direction !== 'none') {
                DOM.flashcardWrapper.classList.add('slide-out');
                setTimeout(() => {
                    DOM.flashcard.classList.remove('flipped');
                    state.flipped = false;
                }, 50);
            } else {
                DOM.flashcard.classList.remove('flipped');
                state.flipped = false;
            }
            
            setTimeout(() => {
                const card = state.cards[state.currentIndex];
                const deckName = formatDeckName();
                const actualPosition = state.totalCards - state.cards.length + state.currentIndex + 1;
                const progress = `${actualPosition}/${state.totalCards}`;
                
                document.getElementById('cardTitleFront').textContent = deckName;
                document.getElementById('cardTitleBack').textContent = deckName;
                document.getElementById('cardProgressFront').textContent = progress;
                document.getElementById('cardProgressBack').textContent = progress;
                DOM.questionText.textContent = card.question;
                DOM.answerText.textContent = card.answer;
                DOM.referenceText.textContent = card.reference;

                // Type answer mode
                resetTypeAnswerUI();
                if (state.typeMode && card.typeable) {
                    DOM.typeAnswerArea.classList.add('active');
                    // Focus input after animation
                    setTimeout(() => DOM.typeAnswerInput.focus(), direction !== 'none' ? 300 : 50);
                } else {
                    DOM.typeAnswerArea.classList.remove('active');
                }

                DOM.flashcard.classList.remove('flipped');
                state.flipped = false;

                // Update pile counters if in pile mode
                if (state.settings.pileMode) {
                    updatePileCounters();
                    document.querySelectorAll('.pile-display').forEach(el => el.classList.add('active'));
                    document.querySelectorAll('.card-progress-counters').forEach(el => el.classList.remove('active'));
                } else {
                    document.querySelectorAll('.pile-display').forEach(el => el.classList.remove('active'));
                }

                updateNavigationButtons();
                saveProgress();

                if (direction !== 'none') {
                    DOM.flashcardWrapper.classList.remove('slide-out');
                    DOM.flashcardWrapper.classList.add('slide-in');
                    setTimeout(() => DOM.flashcardWrapper.classList.remove('slide-in'), 250);
                }
            }, direction !== 'none' ? 250 : 0);
        }

        function saveProgress() {
            if (state.currentDeck && state.cards.length > 0) {
                localStorage.setItem(`progress_${state.currentDeck}`, JSON.stringify({
                    currentIndex: state.currentIndex,
                    timestamp: Date.now()
                }));
            }
        }

        function studyStillLearning() {
            // Filter to only still learning cards
            state.cards = state.allCards.filter(c => state.trackingData.stillLearning.includes(c.id));
            
            if (state.cards.length === 0) {
                alert('No cards marked as "Still Learning"!');
                return;
            }
            
            state.totalCards = state.cards.length;
            state.currentIndex = 0;
            state.flipped = false;
            displayCard();
        }

        function loadProgress() {
            if (!state.currentDeck) return;
            
            const saved = localStorage.getItem(`progress_${state.currentDeck}`);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    // Only resume if less than 7 days old
                    if (Date.now() - data.timestamp < 7 * 24 * 60 * 60 * 1000) {
                        if (data.currentIndex < state.cards.length) {
                            if (confirm(`Resume from card ${data.currentIndex + 1}?`)) {
                                state.currentIndex = data.currentIndex;
                            }
                        }
                    }
                } catch (e) {
                    console.error('Error loading progress:', e);
                }
            }
        }

        function formatDeckName() {
            if (!state.currentDeck.includes(',')) {
                return parseFileName(state.currentDeck);
            }
            
            // Smart combining for multiple decks
            const decks = state.currentDeck.split(',').map(d => parseFileName(d));
            
            return formatDeckNames(state.currentDeck.split(','));
        }
        
        function formatDeckNames(deckFiles) {
            // Helper function to format an array of deck file names smartly
            if (deckFiles.length === 1) {
                return parseFileName(deckFiles[0]);
            }
            
            const decks = deckFiles.map(d => parseFileName(d));
            
            // Group by book name
            const groups = {};
            decks.forEach(name => {
                const parts = name.match(/^(.*?)(\d+)$/);
                if (parts) {
                    const book = parts[1].trim();
                    const num = parseInt(parts[2]);
                    if (!groups[book]) groups[book] = [];
                    groups[book].push(num);
                } else {
                    if (!groups[name]) groups[name] = [];
                }
            });
            
            // Format each group
            const formatted = Object.entries(groups).map(([book, nums]) => {
                if (nums.length === 0) return book;
                nums.sort((a, b) => a - b);
                
                // Check if consecutive
                let ranges = [];
                let start = nums[0];
                let prev = nums[0];
                
                for (let i = 1; i < nums.length; i++) {
                    if (nums[i] === prev + 1) {
                        prev = nums[i];
                    } else {
                        ranges.push(start === prev ? `${start}` : `${start}-${prev}`);
                        start = nums[i];
                        prev = nums[i];
                    }
                }
                ranges.push(start === prev ? `${start}` : `${start}-${prev}`);
                
                return `${book} ${ranges.join(', ')}`;
            });
            
            return formatted.join(' & ');
        }

        function updatePileCounters() {
            const pile1Count = state.pileData.pile1.length;
            const pile2Count = state.pileData.pile2.length;
            const pile3Count = state.pileData.pile3.length;
            
            // Update card displays
            ['Front', 'Back'].forEach(side => {
                document.getElementById(`pile1Count${side}`).textContent = pile1Count;
                document.getElementById(`pile2Count${side}`).textContent = pile2Count;
                document.getElementById(`pile3Count${side}`).textContent = pile3Count;
            });
            
            // Update pile selection screen
            document.getElementById('pile1SelectCount').textContent = pile1Count;
            document.getElementById('pile2SelectCount').textContent = pile2Count;
            document.getElementById('pile3SelectCount').textContent = pile3Count;
            document.getElementById('allPilesCount').textContent = pile1Count + pile2Count + pile3Count;
        }

        function updateNavigationButtons() {
            const prev = document.getElementById('prevBtn');
            const next = document.getElementById('nextBtn');
            const pile1Btn = document.getElementById('pile1Btn');
            const pile2Btn = document.getElementById('pile2Btn');
            const pile3Btn = document.getElementById('pile3Btn');
            const bottomBar = document.querySelector('.bottom-bar');
            
            if (state.settings.pileMode) {
                // Hide arrow buttons, show pile buttons
                prev.style.display = 'none';
                next.style.display = 'none';
                pile1Btn.style.display = 'flex';
                pile2Btn.style.display = 'flex';
                pile3Btn.style.display = 'flex';
                bottomBar.classList.add('pile-active');
                
                // In type mode for typeable cards, hide pile buttons (auto-sort handles it)
                if (state.typeMode && isCurrentCardTypeable() && !state.typeSubmitted) {
                    pile1Btn.style.display = 'none';
                    pile2Btn.style.display = 'none';
                    pile3Btn.style.display = 'none';
                    bottomBar.classList.remove('pile-active');
                }
                
                // Apply button style
                DOM.navigation.classList.add('pile-mode');
                DOM.navigation.classList.add(state.settings.pileButtonStyle);
                
                // Update button content based on style
                updatePileButtonContent();
                
                // Pile buttons never disabled - always can sort
                pile1Btn.disabled = false;
                pile2Btn.disabled = false;
                pile3Btn.disabled = false;
            } else {
                // Show arrow buttons, hide pile buttons
                prev.style.display = 'flex';
                next.style.display = 'flex';
                pile1Btn.style.display = 'none';
                pile2Btn.style.display = 'none';
                pile3Btn.style.display = 'none';
                bottomBar.classList.remove('pile-active');
                
                DOM.navigation.classList.remove('pile-mode', 'style1', 'style2', 'style3', 'style4');
                
                // Regular navigation - disable prev at start, enable next to show completion
                prev.disabled = state.currentIndex === 0;
                // Don't disable next button on last card - let it trigger completion
                next.disabled = false;
            }
        }

        function updatePileButtonContent() {
            const style = state.settings.pileButtonStyle;
            const pile1Btn = document.getElementById('pile1Btn');
            const pile2Btn = document.getElementById('pile2Btn');
            const pile3Btn = document.getElementById('pile3Btn');
            
            if (style === 'style1') {
                pile1Btn.innerHTML = '❌<br><small>Don\'t Know</small>';
                pile2Btn.innerHTML = '📚<br><small>Learning</small>';
                pile3Btn.innerHTML = '✅<br><small>Know</small>';
            } else if (style === 'style2') {
                pile1Btn.innerHTML = '✗';
                pile2Btn.innerHTML = '~';
                pile3Btn.innerHTML = '✓';
            } else if (style === 'style3') {
                pile1Btn.innerHTML = '1<br><small>Don\'t Know</small>';
                pile2Btn.innerHTML = '2<br><small>Learning</small>';
                pile3Btn.innerHTML = '3<br><small>Know</small>';
            } else if (style === 'style4') {
                pile1Btn.innerHTML = '❌';
                pile2Btn.innerHTML = '📚';
                pile3Btn.innerHTML = '✅';
            }
        }

        function sortCardToPile(pileNumber) {
            if (!state.flipped) {
                // Must flip card first
                flipCard();
                return;
            }
            
            const card = state.cards[state.currentIndex];
            const cardId = card.id;
            
            // Remove from all piles
            state.pileData.pile1 = state.pileData.pile1.filter(id => id !== cardId);
            state.pileData.pile2 = state.pileData.pile2.filter(id => id !== cardId);
            state.pileData.pile3 = state.pileData.pile3.filter(id => id !== cardId);
            
            // Add to selected pile
            if (pileNumber === 1) {
                if (!state.pileData.pile1.includes(cardId)) {
                    state.pileData.pile1.push(cardId);
                }
            } else if (pileNumber === 2) {
                if (!state.pileData.pile2.includes(cardId)) {
                    state.pileData.pile2.push(cardId);
                }
            } else if (pileNumber === 3) {
                if (!state.pileData.pile3.includes(cardId)) {
                    state.pileData.pile3.push(cardId);
                }
            }
            
            savePileData();
            updatePileCounters();
            
            // Move to next card or show completion
            if (state.currentIndex < state.cards.length - 1) {
                state.currentIndex++;
                displayCard('right');
            } else {
                // Finished current pile
                showPileCompletionScreen();
            }
        }

        function showPileCompletionScreen() {
            const pile1Count = state.pileData.pile1.length;
            const pile2Count = state.pileData.pile2.length;
            const pile3Count = state.pileData.pile3.length;
            
            if (pile1Count === 0 && pile2Count === 0 && pile3Count === 0) {
                // All cards sorted - show final completion
                showFinalCompletion();
            } else {
                // Show pile selection to continue
                showPileSelectionScreen();
            }
        }

        function showFinalCompletion() {
            const modal = document.getElementById('deckFinishedModal');
            const message = document.getElementById('deckFinishedMessage');
            
            message.innerHTML = `<strong>🎉 Deck Complete!</strong><br><br>All cards have been sorted!<br><br>📊 <strong>${state.pileData.pile3.length}</strong> cards you know`;
            
            // Hide "Study Still Learning" button, show only Reset and Back
            document.getElementById('studyStillLearningFinished').style.display = 'none';
            
            modal.classList.add('active');
        }

        function showPileSelectionScreen() {
            // Hide ALL other screens, show pile selection
            DOM.studyScreen.style.display = 'none';
            DOM.deckSelection.style.display = 'none';
            document.getElementById('pileSelectionScreen').classList.add('active');
            
            // Update deck name
            const deckName = formatDeckName();
            document.getElementById('pileSelectionDeckName').textContent = deckName;
            
            // Update counts
            updatePileCounters();
            
            // Set shuffle checkbox to current state
            document.getElementById('pileShuffleCheck').checked = state.settings.shuffle;
        }

        function studyPile(pileSelection) {
            state.currentPile = pileSelection;
            
            // Filter cards based on selection
            if (pileSelection === 'all') {
                // Study all unsorted + sorted cards, prioritize pile1
                const pile1Cards = state.allCards.filter(c => state.pileData.pile1.includes(c.id));
                const pile2Cards = state.allCards.filter(c => state.pileData.pile2.includes(c.id));
                const pile3Cards = state.allCards.filter(c => state.pileData.pile3.includes(c.id));
                
                // Combine with pile1 appearing more frequently
                state.cards = [];
                const maxLength = Math.max(pile1Cards.length, pile2Cards.length, pile3Cards.length);
                
                for (let i = 0; i < maxLength; i++) {
                    // Add 2 pile1 cards for every other pile card (prioritization)
                    if (i < pile1Cards.length) state.cards.push(pile1Cards[i]);
                    if (i < pile1Cards.length && pile1Cards[i + maxLength]) state.cards.push(pile1Cards[i + maxLength]);
                    if (i < pile2Cards.length) state.cards.push(pile2Cards[i]);
                    if (i < pile3Cards.length) state.cards.push(pile3Cards[i]);
                }
                
                // Add any unsorted cards (new cards)
                const sortedIds = [...state.pileData.pile1, ...state.pileData.pile2, ...state.pileData.pile3];
                const unsortedCards = state.allCards.filter(c => !sortedIds.includes(c.id));
                state.cards = [...unsortedCards, ...state.cards];
                
            } else if (pileSelection === 'pile1') {
                state.cards = state.allCards.filter(c => state.pileData.pile1.includes(c.id));
            } else if (pileSelection === 'pile2') {
                state.cards = state.allCards.filter(c => state.pileData.pile2.includes(c.id));
            } else if (pileSelection === 'pile3') {
                state.cards = state.allCards.filter(c => state.pileData.pile3.includes(c.id));
            }
            
            if (state.cards.length === 0) {
                alert('No cards in this pile!');
                return;
            }
            
            // Shuffle if enabled
            const shouldShuffle = document.getElementById('pileShuffleCheck').checked;
            if (shouldShuffle) {
                if (pileSelection === 'all') {
                    // Already prioritized, just shuffle within
                    shuffleArray(state.cards);
                } else {
                    shuffleArray(state.cards);
                }
            }
            
            state.totalCards = state.cards.length;
            state.currentIndex = 0;
            state.flipped = false;
            
            // Hide ALL other screens, show study screen
            DOM.deckSelection.style.display = 'none';
            document.getElementById('pileSelectionScreen').classList.remove('active');
            DOM.studyScreen.style.display = 'block';
            
            // Sync settings modals with current deck
            loadSettings();
            
            updateNavigationButtons();
            displayCard();
        }

        function savePileData() {
            const decks = state.currentDeck.includes(',') ? state.currentDeck.split(',') : [state.currentDeck];
            const pileKey = getPileDataKey(decks);
            localStorage.setItem(pileKey, JSON.stringify(state.pileData));
        }

        function loadPileData() {
            const decks = state.currentDeck.includes(',') ? state.currentDeck.split(',') : [state.currentDeck];
            const pileKey = getPileDataKey(decks);
            const saved = localStorage.getItem(pileKey);
            if (saved) {
                state.pileData = JSON.parse(saved);
            } else {
                state.pileData = { pile1: [], pile2: [], pile3: [] };
            }
        }

        function flipCard() {
            // In type mode, don't allow manual flip on typeable cards until answer is submitted
            if (state.typeMode && !state.flipped && isCurrentCardTypeable() && !state.typeSubmitted) {
                // Shake the input to hint they need to type
                DOM.typeAnswerInput.style.animation = 'none';
                DOM.typeAnswerInput.offsetHeight; // Trigger reflow
                DOM.typeAnswerInput.style.animation = '';
                DOM.typeAnswerInput.focus();
                return;
            }
            
            state.flipped = !state.flipped;
            DOM.flashcard.classList.toggle('flipped');
            
            if (state.settings.trackProgress && !state.settings.shortcutHintSeen && state.flipped) {
                DOM.shortcutHintBar.innerHTML = '⌨️ <b>Shortcut:</b> Press <b>←</b> to study again or <b>→</b> if you know the answer';
                DOM.shortcutHintBar.style.display = 'flex';
                DOM.shortcutHintBar.classList.add('active');
                
                // Auto-hide after 10 seconds
                setTimeout(() => {
                    DOM.shortcutHintBar.classList.remove('active');
                    DOM.shortcutHintBar.classList.add('hiding');
                    setTimeout(() => {
                        DOM.shortcutHintBar.classList.remove('hiding');
                        DOM.shortcutHintBar.style.display = 'none';
                    }, 300);
                }, 10000);
                
                state.settings.shortcutHintSeen = true;
                saveSettings();
            }
        }

        function navigateCard(dir) {
            if (state.settings.pileMode && state.flipped) {
                // In pile mode after flip, can't navigate - must sort to pile
                return;
            }
            
            // Regular navigation
            const newIdx = state.currentIndex + dir;
            if (newIdx >= 0 && newIdx < state.cards.length) {
                state.currentIndex = newIdx;
                displayCard(dir > 0 ? 'right' : 'left');
            } else if (newIdx >= state.cards.length && !state.settings.pileMode) {
                // Reached the end of deck in regular mode - show completion
                showRegularModeCompletion();
            }
        }

        function showRegularModeCompletion() {
            const modal = document.getElementById('deckFinishedModal');
            const message = document.getElementById('deckFinishedMessage');
            
            const deckName = formatDeckName();
            message.innerHTML = `<strong>🎉 Deck Complete!</strong><br><br>You've finished studying <strong>${deckName}</strong><br><br>📊 <strong>${state.cards.length}</strong> cards reviewed`;
            
            // Hide "Study Still Learning" button for regular mode
            document.getElementById('studyStillLearningFinished').style.display = 'none';
            
            modal.classList.add('active');
        }

        function saveTracking() {
            // Legacy - redirects to pile system
            if (state.settings.pileMode) {
                savePileData();
            }
        }

        function saveSettings() {
            localStorage.setItem('userSettings', JSON.stringify(state.settings));
            
            // Apply settings to body
            document.body.dataset.textSize = state.settings.textSize;
            
            // Save pile mode per deck
            if (state.currentDeck) {
                localStorage.setItem(`pileMode_${state.currentDeck}`, state.settings.pileMode);
            }
        }

        function loadSettings() {
            const saved = localStorage.getItem('userSettings');
            if (saved) state.settings = { ...state.settings, ...JSON.parse(saved) };
            
            // Note: Pile mode is now set at study time via study options modal, not loaded from deck settings
            
            // Apply settings
            document.body.dataset.textSize = state.settings.textSize;
            
            // Update UI - Global settings modal
            const textSizeSelect = document.getElementById('textSizeSelect');
            if (textSizeSelect) textSizeSelect.value = state.settings.textSize;
            
            const recentCountSelect = document.getElementById('recentCountSelect');
            if (recentCountSelect) recentCountSelect.value = state.settings.recentStudiedCount || 3;
            
            const pileButtonStyleSelect = document.getElementById('pileButtonStyleSelect');
            if (pileButtonStyleSelect) pileButtonStyleSelect.value = state.settings.pileButtonStyle;
            
            const pileKeyboardSelect = document.getElementById('pileKeyboardSelect');
            if (pileKeyboardSelect) pileKeyboardSelect.value = state.settings.pileKeyboardShortcuts;
            
            // Update UI - Study view modal
            const textSizeSelectStudy = document.getElementById('textSizeSelectStudy');
            if (textSizeSelectStudy) textSizeSelectStudy.value = state.settings.textSize;
        }

        function initTheme() {
            let theme = localStorage.getItem('theme') || 'light';
            // Map old themes to light/dark
            if (theme !== 'light' && theme !== 'dark') theme = 'light';
            document.body.dataset.theme = theme;
            localStorage.setItem('theme', theme);
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.theme === theme);
                btn.onclick = () => {
                    document.body.dataset.theme = btn.dataset.theme;
                    localStorage.setItem('theme', btn.dataset.theme);
                    document.querySelectorAll('.theme-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                };
            });
        }

        function openDeckSettings(deckFile) {
            // Store which deck we're configuring
            state.currentDeckForSettings = deckFile;
            
            // Update modal title
            document.getElementById('deckSettingsTitle').textContent = parseFileName(deckFile);
            
            // Show modal
            document.getElementById('deckSettingsModal').classList.add('active');
        }

        function initEvents() {
            // Search and sort event listeners
            const searchInput = document.getElementById('deckSearch');
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.trim();
                loadDecks();
            });
            
            const sortSelect = document.getElementById('sortDecks');
            sortSelect.addEventListener('change', (e) => {
                sortMode = e.target.value;
                loadDecks();
            });
            
            // Select All/None deck buttons
            document.getElementById('selectAllDecksBtn').onclick = () => {
                const filteredDecks = filterDecks();
                state.selectedDecks = [...filteredDecks];
                loadDecks();
            };
            
            document.getElementById('selectNoneDecksBtn').onclick = () => {
                state.selectedDecks = [];
                loadDecks();
            };
            
            // Study Options Modal handlers
            document.getElementById('studySelectedBtn').onclick = () => {
                if (state.selectedDecks.length) {
                    openStudyOptionsModal(state.selectedDecks);
                }
            };
            document.getElementById('clearSelectionBtn').onclick = () => { 
                state.selectedDecks = []; 
                loadDecks(); 
            };
            
            // Study option buttons
            document.getElementById('closeStudyOptionsBtn').onclick = closeStudyOptionsModal;
            document.getElementById('studyLinearBtn').onclick = () => {
                const decks = [...state.pendingStudyDecks];
                closeStudyOptionsModal();
                loadDeck(decks, 'linear');
            };
            document.getElementById('studyShuffledBtn').onclick = () => {
                const decks = [...state.pendingStudyDecks];
                closeStudyOptionsModal();
                loadDeck(decks, 'shuffled');
            };
            document.getElementById('studyWithPilesBtn').onclick = () => {
                const decks = [...state.pendingStudyDecks];
                closeStudyOptionsModal();
                loadDeck(decks, 'piles');
            };
            document.getElementById('studyWithTypingBtn').onclick = () => {
                const decks = [...state.pendingStudyDecks];
                closeStudyOptionsModal();
                loadDeck(decks, 'typing');
            };
            document.getElementById('resumeStudyBtn').onclick = () => {
                const decks = [...state.pendingStudyDecks];
                closeStudyOptionsModal();
                loadDeck(decks, 'piles'); // Resume always uses pile mode
            };
            document.getElementById('clearProgressBtn').onclick = () => {
                if (confirm('This will reset all pile data for this deck. Are you sure?')) {
                    const pileKey = getPileDataKey(state.pendingStudyDecks);
                    localStorage.removeItem(pileKey);
                    closeStudyOptionsModal();
                    alert('Progress cleared!');
                }
            };
            
            document.getElementById('prevBtn').onclick = () => navigateCard(-1);
            document.getElementById('nextBtn').onclick = () => navigateCard(1);
            document.getElementById('pile1Btn').onclick = (e) => { sortCardToPile(1); e.currentTarget.blur(); };
            document.getElementById('pile2Btn').onclick = (e) => { sortCardToPile(2); e.currentTarget.blur(); };
            document.getElementById('pile3Btn').onclick = (e) => { sortCardToPile(3); e.currentTarget.blur(); };
            DOM.flashcard.onclick = flipCard;
            
            // Type answer events
            DOM.typeAnswerInput.addEventListener('input', () => {
                const typed = DOM.typeAnswerInput.value;
                const card = state.cards[state.currentIndex];
                if (card && card.typeable && state.typeMode) {
                    renderTypingFeedback(typed, card.answer);
                }
            });
            DOM.typeAnswerSubmit.onclick = submitTypedAnswer;
            
            // Prevent type answer area clicks from flipping the card
            DOM.typeAnswerArea.addEventListener('click', e => e.stopPropagation());
            
            document.getElementById('backToDecksBtn').onclick = () => {
                state.selectedDecks = [];
                document.body.classList.remove('typing-active');
                DOM.deckSelection.style.display = 'block';
                DOM.studyScreen.style.display = 'none';
                document.getElementById('pileSelectionScreen').classList.remove('active');
                loadDecks();
            };
            
            document.getElementById('studySettingsBtn').onclick = () => document.getElementById('settingsModal').classList.add('active');
            document.getElementById('closeSettingsBtn').onclick = () => document.getElementById('settingsModal').classList.remove('active');
            
            // Global settings modal
            document.getElementById('globalSettingsBtn').onclick = () => document.getElementById('globalSettingsModal').classList.add('active');
            document.getElementById('closeGlobalSettingsBtn').onclick = () => document.getElementById('globalSettingsModal').classList.remove('active');
            
            // Deck settings modal
            document.getElementById('closeDeckSettingsBtn').onclick = () => {
                document.getElementById('deckSettingsModal').classList.remove('active');
                loadDecks(); // Refresh deck display to show updated pile counts
            };
            
            // Click outside modal to close
            const modals = ['settingsModal', 'globalSettingsModal', 'deckSettingsModal', 'studyOptionsModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                modal.onclick = (e) => {
                    // Only close if clicking the backdrop (not the content)
                    if (e.target === modal) {
                        modal.classList.remove('active');
                        if (modalId === 'deckSettingsModal') {
                            loadDecks(); // Refresh deck display
                        }
                        if (modalId === 'studyOptionsModal') {
                            closeStudyOptionsModal();
                        }
                    }
                };
            });
            
            // Text size settings (global)
            document.getElementById('textSizeSelect').onchange = e => {
                state.settings.textSize = e.target.value;
                saveSettings();
            };
            
            // Text size settings (study view)
            document.getElementById('textSizeSelectStudy').onchange = e => {
                state.settings.textSize = e.target.value;
                saveSettings();
            };
            
            // Pile mode settings - now in Global Settings
            document.getElementById('pileButtonStyleSelect').onchange = e => {
                state.settings.pileButtonStyle = e.target.value;
                saveSettings();
                if (state.settings.pileMode) {
                    DOM.navigation.classList.remove('style1', 'style2', 'style3', 'style4');
                    updateNavigationButtons();
                }
            };
            
            document.getElementById('pileKeyboardSelect').onchange = e => {
                state.settings.pileKeyboardShortcuts = e.target.value;
                saveSettings();
            };
            
            document.getElementById('recentCountSelect').onchange = (e) => {
                state.settings.recentStudiedCount = parseInt(e.target.value);
                saveSettings();
                loadRecentStudied(); // Refresh display
            };
            
            document.getElementById('clearRecentStudiedBtn').onclick = clearRecentStudied;
            document.getElementById('clearAllRecentBtn').onclick = clearRecentStudied;
            
            // Clear current deck (study view)
            document.getElementById('clearCurrentDeckBtnStudy').onclick = () => {
                if (confirm('Clear progress for this deck?')) {
                    localStorage.removeItem(`pileData_${state.currentDeck}`);
                    localStorage.removeItem(`progress_${state.currentDeck}`);
                    state.pileData = { pile1: [], pile2: [], pile3: [] };
                    
                    // Reinitialize all cards to pile1
                    state.allCards.forEach(card => {
                        state.pileData.pile1.push(card.id);
                    });
                    savePileData();
                    
                    state.cards = [...state.allCards];
                    state.totalCards = state.cards.length;
                    state.currentIndex = 0;
                    state.flipped = false;
                    
                    updatePileCounters();
                    updateNavigationButtons();
                    displayCard();
                    alert('Progress cleared!');
                }
            };
            
            // Clear current deck (deck settings modal)
            document.getElementById('clearCurrentDeckBtn').onclick = () => {
                const deckFile = state.currentDeckForSettings;
                if (!deckFile) return;
                
                if (confirm(`Clear progress for ${parseFileName(deckFile)}?`)) {
                    localStorage.removeItem(`pileData_${deckFile}`);
                    localStorage.removeItem(`progress_${deckFile}`);
                    alert('Progress cleared!');
                    document.getElementById('deckSettingsModal').classList.remove('active');
                    loadDecks();
                }
            };
            
            document.getElementById('clearAllDecksBtn').onclick = () => {
                if (confirm('Clear ALL deck progress?')) {
                    Object.keys(localStorage).forEach(k => {
                        if (k.startsWith('pileData_') || k.startsWith('progress_') || k.startsWith('pileMode_')) {
                            localStorage.removeItem(k);
                        }
                    });
                    
                    // If we're in study view, reinitialize current deck
                    if (state.allCards && state.allCards.length > 0) {
                        state.pileData = { pile1: [], pile2: [], pile3: [] };
                        state.allCards.forEach(card => {
                            state.pileData.pile1.push(card.id);
                        });
                        savePileData();
                    }
                    
                    updatePileCounters();
                    updateNavigationButtons();
                    alert('All progress cleared!');
                    
                    // Close modal and refresh
                    document.getElementById('globalSettingsModal').classList.remove('active');
                    if (DOM.deckSelection.style.display === 'block') {
                        loadDecks();
                    }
                }
            };
            document.getElementById('focusBtn').onclick = () => {
                state.focusModeActive = !state.focusModeActive;
                document.body.classList.toggle('focus-mode', state.focusModeActive);
                document.getElementById('focusBtn').classList.toggle('active', state.focusModeActive);
            };
            
            // Pile selection screen handlers
            document.getElementById('studyAllPiles').onclick = () => studyPile('all');
            document.getElementById('studyPile1').onclick = () => studyPile('pile1');
            document.getElementById('studyPile2').onclick = () => studyPile('pile2');
            document.getElementById('studyPile3').onclick = () => studyPile('pile3');
            document.getElementById('backToDecksFromPileSelect').onclick = () => {
                document.getElementById('pileSelectionScreen').classList.remove('active');
                document.body.classList.remove('typing-active');
                DOM.deckSelection.style.display = 'block';
                loadDecks();
            };
            
            // Deck completion modal handlers (simplified for pile mode)
            document.getElementById('studyStillLearningFinished').style.display = 'none'; // Not used in pile mode
            
            document.getElementById('restartDeckFinished').onclick = () => {
                document.getElementById('deckFinishedModal').classList.remove('active');
                
                if (state.settings.pileMode) {
                    // Reset all piles and return to pile selection
                    state.pileData = { pile1: [], pile2: [], pile3: [] };
                    state.allCards.forEach(card => {
                        state.pileData.pile1.push(card.id);
                    });
                    savePileData();
                    showPileSelectionScreen();
                } else {
                    // Regular mode - restart
                    state.currentIndex = 0;
                    state.flipped = false;
                    state.cards = [...state.allCards];
                    state.totalCards = state.cards.length;
                    
                    if (state.settings.shuffle) {
                        if (state.currentDeck.includes(',')) {
                            state.cards = smartShuffle(state.cards);
                        } else {
                            shuffleArray(state.cards);
                        }
                    }
                    
                    updateNavigationButtons();
                    displayCard();
                }
            };
            
            document.getElementById('backToDecksFinished').onclick = () => {
                document.getElementById('deckFinishedModal').classList.remove('active');
                document.getElementById('pileSelectionScreen').classList.remove('active');
                state.selectedDecks = [];
                document.body.classList.remove('typing-active');
                DOM.deckSelection.style.display = 'block';
                DOM.studyScreen.style.display = 'none';
                loadDecks();
            };
            
            document.onkeydown = e => {
                // Only handle shortcuts when study screen is visible
                if (DOM.studyScreen.style.display !== 'block') return;
                
                // Don't hijack keyboard when typing an answer
                if (document.activeElement === DOM.typeAnswerInput) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        submitTypedAnswer();
                    }
                    return; // Let all other keys go to the input
                }
                
                // Pile mode shortcuts
                if (state.settings.pileMode && state.flipped) {
                    const shortcuts = state.settings.pileKeyboardShortcuts;
                    
                    if (shortcuts === 'keys123') {
                        if (e.key === '1') { e.preventDefault(); sortCardToPile(1); return; }
                        if (e.key === '2') { e.preventDefault(); sortCardToPile(2); return; }
                        if (e.key === '3') { e.preventDefault(); sortCardToPile(3); return; }
                    } else if (shortcuts === 'arrows') {
                        if (e.key === 'ArrowLeft') { e.preventDefault(); sortCardToPile(1); return; }
                        if (e.key === 'ArrowDown') { e.preventDefault(); sortCardToPile(2); return; }
                        if (e.key === 'ArrowRight') { e.preventDefault(); sortCardToPile(3); return; }
                    } else if (shortcuts === 'qwe') {
                        if (e.key.toLowerCase() === 'q') { e.preventDefault(); sortCardToPile(1); return; }
                        if (e.key.toLowerCase() === 'w') { e.preventDefault(); sortCardToPile(2); return; }
                        if (e.key.toLowerCase() === 'e') { e.preventDefault(); sortCardToPile(3); return; }
                    } else if (shortcuts === 'asd') {
                        if (e.key.toLowerCase() === 'a') { e.preventDefault(); sortCardToPile(1); return; }
                        if (e.key.toLowerCase() === 's') { e.preventDefault(); sortCardToPile(2); return; }
                        if (e.key.toLowerCase() === 'd') { e.preventDefault(); sortCardToPile(3); return; }
                    }
                }
                
                // Regular navigation shortcuts (when not in pile mode or not flipped)
                if (!state.settings.pileMode || !state.flipped) {
                    if (e.key === 'ArrowLeft') { e.preventDefault(); navigateCard(-1); }
                    else if (e.key === 'ArrowRight') { e.preventDefault(); navigateCard(1); }
                }
                
                // Flip shortcuts (always available)
                if (['ArrowUp', 'ArrowDown', ' '].includes(e.key)) { e.preventDefault(); flipCard(); }
            };

            // Improved touch handling for better reliability - works in ALL modes
            let touchStartX, touchStartY, touchStartTime, isSwiping;
            const SWIPE_THRESHOLD = 50; // pixels
            const SWIPE_VELOCITY_THRESHOLD = 0.3; // pixels per ms
            
            DOM.flashcardWrapper.ontouchstart = e => {
                // Don't track swipes when interacting with type answer
                if (e.target.closest('.type-answer-area')) return;
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
                touchStartTime = Date.now();
                isSwiping = false;
            };
            
            DOM.flashcardWrapper.ontouchmove = e => {
                if (e.target.closest('.type-answer-area')) return;
                const touchX = e.changedTouches[0].screenX;
                const touchY = e.changedTouches[0].screenY;
                const diffX = touchX - touchStartX;
                const diffY = touchY - touchStartY;

                // Pile mode: support 3-direction swipes (left, down, right)
                if (state.settings.pileMode && state.flipped) {
                    // Check for horizontal OR vertical swipe
                    if (Math.abs(diffX) > 15 || Math.abs(diffY) > 15) {
                        e.preventDefault();
                        isSwiping = true;
                        
                        // Visual feedback for pile mode (could add CSS classes here)
                        DOM.flashcardWrapper.classList.remove('swiping-left', 'swiping-down', 'swiping-right');
                        
                        // Determine primary direction
                        if (Math.abs(diffX) > Math.abs(diffY)) {
                            // Horizontal swipe
                            if (diffX < -25) {
                                DOM.flashcardWrapper.classList.add('swiping-left');
                            } else if (diffX > 25) {
                                DOM.flashcardWrapper.classList.add('swiping-right');
                            }
                        } else {
                            // Vertical swipe (down for pile 2)
                            if (diffY > 25) {
                                DOM.flashcardWrapper.classList.add('swiping-down');
                            }
                        }
                    }
                } else {
                    // Regular mode: horizontal swipe only
                    if (Math.abs(diffX) > Math.abs(diffY) * 1.5 && Math.abs(diffX) > 15) {
                        e.preventDefault();
                        isSwiping = true;
                    }
                }
            };
            
            DOM.flashcardWrapper.ontouchend = e => {
                if (e.target.closest('.type-answer-area')) return;
                const touchEndX = e.changedTouches[0].screenX;
                const touchEndY = e.changedTouches[0].screenY;
                const touchEndTime = Date.now();
                
                const diffX = touchEndX - touchStartX;
                const diffY = touchEndY - touchStartY;
                const timeDiff = touchEndTime - touchStartTime;
                const velocityX = Math.abs(diffX) / timeDiff;
                const velocityY = Math.abs(diffY) / timeDiff;
                
                // Clear visual feedback
                DOM.flashcardWrapper.classList.remove('swiping-left', 'swiping-down', 'swiping-right');
                
                // Pile mode: 3-direction swipe detection
                if (state.settings.pileMode && state.flipped && isSwiping) {
                    // Determine primary swipe direction
                    if (Math.abs(diffX) > Math.abs(diffY)) {
                        // Horizontal swipe
                        if ((Math.abs(diffX) > SWIPE_THRESHOLD || velocityX > SWIPE_VELOCITY_THRESHOLD)) {
                            if (diffX < 0) {
                                sortCardToPile(1); // Swipe left = Pile 1 (Don't Know)
                            } else {
                                sortCardToPile(3); // Swipe right = Pile 3 (Know)
                            }
                        }
                    } else {
                        // Vertical swipe (down only)
                        if (diffY > SWIPE_THRESHOLD || velocityY > SWIPE_VELOCITY_THRESHOLD) {
                            sortCardToPile(2); // Swipe down = Pile 2 (Learning)
                        }
                    }
                } else if (!state.settings.pileMode && isSwiping) {
                    // Regular mode: horizontal navigation
                    if (Math.abs(diffX) > SWIPE_THRESHOLD || velocityX > SWIPE_VELOCITY_THRESHOLD) {
                        navigateCard(diffX > 0 ? -1 : 1);
                    }
                }
                
                isSwiping = false;
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            const ver = document.querySelector('meta[name="app-version"]').content;
            const saved = localStorage.getItem('appVersion');
            if (saved && saved !== ver) {
                if ('caches' in window) caches.keys().then(names => names.forEach(n => caches.delete(n)));
                localStorage.setItem('appVersion', ver);
                window.location.reload(true);
            } else if (!saved) localStorage.setItem('appVersion', ver);
            
            loadDecks();
            initTheme();
            initEvents();
            loadSettings();
            loadRecentStudied();
            
            // Start background pre-loading of recent decks for instant loading
            backgroundPreloadRecentDecks();
        });
    </script>
</body>
</html>
