<?php
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script>
    // iOS standalone detection - add class to HTML element immediately
    if (window.navigator.standalone === true) {
    document.documentElement.classList.add('ios-standalone');
    }
    </script>
    <title>Study - PBE Team Bold</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    
    <?php 
    $version = '1.0.4';
    echo "<meta name='app-version' content='$version'>";
    
    // Scan audio files for all book subdirectories inside audio/
    function getAllAudioFiles($baseDir = 'audio') {
        $allFiles = [];
        if (!is_dir($baseDir)) return $allFiles;

        // Also check flat files in audio/ for backward compatibility
        $flatFiles = glob($baseDir . '/*.m4a');
        if (!empty($flatFiles)) {
            sort($flatFiles);
            $bookFiles = [];
            foreach ($flatFiles as $file) {
                $filename = basename($file);
                preg_match('/\d+/', $filename, $matches);
                $chapter = isset($matches[0]) ? (int)$matches[0] : 0;
                $bookFiles[] = [
                    'filename' => $filename,
                    'path' => $file,
                    'chapter' => $chapter
                ];
            }
            // Guess book id from first filename (e.g., "Isaiah_01.m4a" → "isaiah")
            if (!empty($bookFiles)) {
                $first = $bookFiles[0]['filename'];
                preg_match('/^([A-Za-z]+)/', $first, $m);
                $bookId = strtolower($m[1] ?? 'unknown');
                $allFiles[$bookId] = $bookFiles;
            }
        }

        // Scan subdirectories: audio/isaiah/, audio/mark/, etc.
        $bookDirs = glob($baseDir . '/*', GLOB_ONLYDIR);
        foreach ($bookDirs as $bookDir) {
            $bookId = basename($bookDir);
            $files = glob($bookDir . '/*.m4a');
            if (empty($files)) continue;
            sort($files);
            $bookFiles = [];
            foreach ($files as $file) {
                $filename = basename($file);
                preg_match('/\d+/', $filename, $matches);
                $chapter = isset($matches[0]) ? (int)$matches[0] : 0;
                $bookFiles[] = [
                    'filename' => $filename,
                    'path' => $file,
                    'chapter' => $chapter
                ];
            }
            $allFiles[$bookId] = $bookFiles;
        }

        return $allFiles;
    }

    $audioMap = getAllAudioFiles();
    echo "<script>const AUDIO_MAP = " . json_encode($audioMap) . ";</script>";
    ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Open+Sans:wght@400;600;700&family=Lato:wght@400;700&family=Source+Serif+4:wght@400;600;700&display=swap" rel="stylesheet">
    
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
            --highlight-bg: rgba(210, 7, 2, 0.15);
            --highlight-text: #1A214A;
        }

        [data-theme="dark"] {
            --bg-primary: #101016;
            --bg-secondary: #1A1A24;
            --bg-card: #212130;
            --text-primary: #EAEAE8;
            --text-secondary: #B8B7B2;
            --text-muted: #75746E;
            --accent: #D4943A;
            --accent-hover: #E4A84E;
            --border: #2C2C3A;
            --shadow: rgba(0, 0, 0, 0.45);
            --shadow-hover: rgba(0, 0, 0, 0.60);
            --highlight-bg: rgba(212, 148, 58, 0.22);
            --highlight-text: #EAEAE8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        /* Fixed Header */
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--bg-primary);
            border-bottom: 2px solid var(--border);
            z-index: 100;
            box-shadow: 0 2px 8px var(--shadow);
        }

        /* Content with top padding to account for fixed header */
        .main-content {
            margin-top: 0;
            flex: 1;
            overflow-y: auto;
        }

        /* Mobile: Less top padding */
        @media (max-width: 768px) {
            .main-content {
                padding-top: 60px !important; /* Override inline style */
            }
        }

        /* Fixed Header with Scroll Behavior */
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 8px var(--shadow);
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .fixed-header.hidden {
            transform: translateY(-100%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 120px;
        }

        /* Mobile: Remove padding for full-width text and minimal spacing */
        @media (max-width: 768px) {
            .container {
                padding: 0; /* Remove all padding */
                padding-bottom: 50px; /* Just enough space for controls + progress */
            }
        }

        /* Header container */
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 20px 5px 20px; /* Reduced bottom padding */
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0;
            margin-bottom: 5px; /* Reduced from 15px */
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
            font-family: 'Open Sans', -apple-system, sans-serif;
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

        /* Compact Chapter Select Button */
        .chapter-select-btn {
            background: var(--accent);
            border: 2px solid var(--accent);
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            color: white;
            font-family: 'Open Sans', -apple-system, sans-serif;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px var(--shadow);
        }

        .chapter-select-btn:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px var(--shadow-hover);
        }

        .settings-btn {
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

        .settings-btn:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
            transform: rotate(90deg);
            box-shadow: 0 4px 12px var(--shadow-hover);
        }

        /* Fixed Bottom Player */
        .player-wrapper {
            position: fixed;
            bottom: 28px; /* Leave space for progress bar (28px) */
            left: 0;
            right: 0;
            background: rgba(255, 251, 244, 0.25);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px); /* needed for Safari/iOS */
            border-top: 1px solid var(--border);
            box-shadow: 0 -2px 12px var(--shadow);
            z-index: 999;
            transition: transform 0.3s ease;
        }


        [data-theme="dark"] .player-wrapper {
            background: rgba(16, 16, 22, 0.25);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        [data-theme="dark"] .control-btn {
            background: #1E1E2A;
            border-color: #363646;
        }

        [data-theme="dark"] .control-btn:hover {
            background: #2A2A3A;
            border-color: #4A4A5A;
        }

        [data-theme="dark"] .control-btn.play-btn {
            background: var(--accent);
            border-color: var(--accent);
            box-shadow: 0 2px 12px rgba(212, 148, 58, 0.30);
        }

        [data-theme="dark"] .control-btn.play-btn:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 16px rgba(212, 148, 58, 0.40);
        }

        [data-theme="dark"] .speed-btn {
            background: #1E1E2A;
            border-color: #363646;
        }

        [data-theme="dark"] .speed-btn:hover {
            background: var(--accent);
            border-color: var(--accent);
        }

        [data-theme="dark"] .progress-bar {
            background: rgba(0, 0, 0, 0.95);
        }

        [data-theme="dark"] .collapse-btn {
            background: #1E1E2A;
            border-color: #363646;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .chapter-select-btn {
            box-shadow: 0 2px 8px rgba(212, 148, 58, 0.20);
        }

        [data-theme="dark"] .modal-content,
        [data-theme="dark"] .chapter-picker-content {
            border: 1px solid var(--border);
        }

        [data-theme="dark"] .chapter-grid-item:hover,
        [data-theme="dark"] .book-grid-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        [data-theme="dark"] .settings-btn {
            border-color: #363646;
        }

        [data-theme="dark"] .settings-btn:hover {
            box-shadow: 0 4px 12px rgba(212, 148, 58, 0.25);
        }



        /* Mobile Collapsed State - Hide controls, keep progress bar visible */
        @media (max-width: 768px) {
            .player-wrapper.collapsed {
                transform: translateY(100% + 16px);
            }
        }

        .player-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 8px 20px 6px 20px;
            position: relative;
        }

        /* Collapse/Expand Button (Mobile Only) - Fixed, independent of player */
        .collapse-btn {
            position: fixed;
            bottom: 45px; /* Above player controls when expanded */
            right: 15px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            z-index: 1001; /* Above player and progress */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            color: var(--text-primary);
        }

        .collapse-btn.player-collapsed {
            bottom: 45px; /* Just above progress bar when controls are hidden */
        }

        @media (max-width: 768px) {
            .collapse-btn {
                display: flex;
            }
        }

        .collapse-btn:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        /* Controls Wrapper - Above Progress, Hidden when Collapsed */
        .controls-wrapper {
            margin-bottom: 4px;
        }

        /* Hide controls when collapsed on mobile */
        @media (max-width: 768px) {
            .player-wrapper.collapsed .controls-wrapper {
                display: none;
            }
        }

        /* Progress Section - Fixed at Absolute Bottom */
        .progress-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 28px; /* Just larger than font size */
            z-index: 998;
            margin: 0;
            padding: 0;
        }

        .progress-bar {
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.90); /* Dark background */
            cursor: pointer;
            position: relative;
            overflow: visible;
            margin: 0;
            padding: 0;
        }

        .progress-fill {
            height: 100%;
            background: var(--accent);
            width: 0%;
            transition: width 0.1s linear;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Time Display - Inside Progress Bar */
        .time-display {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            font-size: 13px;
            font-weight: 600;
            pointer-events: none;
            z-index: 2;
        }

        .time-display span {
            position: relative;
            color: rgba(255, 255, 255, 0.9); /* Always light - readable */
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }


            /* PWA standalone mode: extra padding to clear iPhone rounded corners */
            .ios-standalone .time-display {
            padding: 0 30px;
        }

        /* Controls Container - Centered */
        .controls-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Control Buttons */
        .control-btn {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 16px;
            color: var(--text-primary);
        }

        .control-btn.play-btn {
            width: 48px;
            height: 48px;
            background: var(--accent);
            border-color: var(--accent);
            color: white;
            font-size: 20px;
        }

        .control-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px var(--shadow-hover);
        }

        .control-btn.play-btn:hover {
            background: var(--accent-hover);
        }

        /* Skip Buttons (10s forward/back) */
        .control-btn.skip-btn {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }

        /* Speed Control */
        .speed-control {
            position: relative;
        }

        .speed-btn {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            min-width: 50px;
            text-align: center;
        }

        .speed-btn:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .speed-dropdown {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 4px 12px var(--shadow-hover);
            display: none;
            flex-direction: column;
            gap: 4px;
            min-width: 80px;
            z-index: 1001;
        }

        .speed-dropdown.active {
            display: flex;
        }

        .speed-option {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            color: var(--text-primary);
        }

        .speed-option:hover {
            background: var(--bg-secondary);
        }

        .speed-option.active {
            background: var(--accent);
            color: white;
        }

        /* Mobile Responsive Adjustments */
        @media (max-width: 768px) {
            .controls-left,
            .controls-right {
                gap: 8px;
            }

            .control-btn {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .control-btn.play-btn {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }

            .volume-slider {
                display: none; /* Hide volume slider on mobile, show only mute button */
            }

            .speed-btn {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 45px;
            }
        }

        /* Text Section */
        .text-wrapper {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 16px var(--shadow);
        }

        /* Mobile: Full-width text, no borders, minimal padding */
        @media (max-width: 768px) {
            .text-wrapper {
                border: none; /* Remove all borders */
                border-radius: 0;
                background: transparent; /* No background box */
                padding: 15px; /* Minimal padding */
                box-shadow: none; /* No shadow */
                margin-bottom: 30px; /* No bottom margin */
            }
        }

        .text-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .text-controls h3 {
            font-family: 'Open Sans', -apple-system, sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .toggle-highlight {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .toggle-switch {
            position: relative;
            width: 48px;
            height: 26px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .toggle-switch.active {
            background: var(--accent);
            border-color: var(--accent);
        }

        .toggle-knob {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .toggle-switch.active .toggle-knob {
            left: 24px;
        }

        /* Professional reading experience - comfortable line-height */
        .text-content {
            font-family: 'Open Sans', -apple-system, sans-serif; /* Changed default to Open Sans */
            font-size: 20px;
            line-height: 1.6; /* Reading-optimized spacing */
            color: var(--text-secondary);
            max-height: none;
            overflow-y: visible;
            padding-right: 10px;
            /* iOS rendering optimizations */
            -webkit-text-size-adjust: 100%;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }

        /* Mobile default to small size */
        @media (max-width: 768px) {
            .text-content {
                font-size: 16px; /* Default to small on mobile */
            }
        }

        .text-content::-webkit-scrollbar {
            width: 8px;
        }

        .text-content::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 4px;
        }

        .text-content::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 4px;
        }

        /* Font family variations */
        .text-content.font-opensans { font-family: 'Open Sans', -apple-system, sans-serif; }
        .text-content.font-sourceserif { font-family: 'Source Serif 4', Georgia, serif; }
        .text-content.font-merriweather { font-family: 'Merriweather', Georgia, serif; }
        .text-content.font-lato { font-family: 'Lato', -apple-system, sans-serif; }

        /* Font sizes - all use comfortable 1.6 line-height */
        .text-content.size-xs { font-size: 14px; }
        .text-content.size-s { font-size: 16px; }
        .text-content.size-m { font-size: 18px; }
        .text-content.size-l { font-size: 20px; }
        .text-content.size-xl { font-size: 22px; }
        .text-content.size-xxl { font-size: 26px; }
        .text-content.size-xxxl { font-size: 30px; }

        .text-content::-webkit-scrollbar {
            width: 8px;
        }

        .text-content::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 4px;
        }

        .text-content::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 4px;
        }

        .verse {
            display: inline;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        /* Verse-per-line mode - use newline character instead of display:block */
        .text-content.verse-per-line .verse::after {
            content: "\a";
            white-space: pre;
        }

        /* Clean professional highlight - simple and effective */
        .verse.highlight {
            background-color: var(--highlight-bg);
            color: var(--highlight-text);
            border-radius: 2px;
            /* No padding - just background color! */
        }

        /* ── Highlight Color Options ──────────────────────────────────────────
           Each named color defines --highlight-bg for both light and dark.
           Selectors are scoped to body so specificity beats :root / [data-theme="dark"].
        ──────────────────────────────────────────────────────────────────── */
        body[data-highlight="crimson"]                          { --highlight-bg: rgba(210,   7,   2, 0.18); }
        body[data-highlight="crimson"][data-theme="dark"]       { --highlight-bg: rgba(255,  90,  80, 0.42); }

        body[data-highlight="sapphire"]                         { --highlight-bg: rgba( 30, 100, 220, 0.18); }
        body[data-highlight="sapphire"][data-theme="dark"]      { --highlight-bg: rgba(100, 170, 255, 0.40); }

        body[data-highlight="gold"]                             { --highlight-bg: rgba(190, 140,   0, 0.22); }
        body[data-highlight="gold"][data-theme="dark"]          { --highlight-bg: rgba(255, 210,  60, 0.38); }

        body[data-highlight="amethyst"]                         { --highlight-bg: rgba(130,  40, 190, 0.16); }
        body[data-highlight="amethyst"][data-theme="dark"]      { --highlight-bg: rgba(195, 130, 255, 0.38); }

        /* ── Highlight Color Swatch Picker ─────────────────────────────────── */
        .highlight-color-grid {
            display: flex;
            gap: 16px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .highlight-color-swatch {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 4px;
            border-radius: 8px;
            transition: transform 0.15s ease;
        }

        .highlight-color-swatch:hover {
            transform: translateY(-2px);
        }

        .swatch-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 3px solid transparent;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .swatch-check {
            display: none;
            font-size: 16px;
            font-weight: 700;
            color: white;
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            line-height: 1;
        }

        .highlight-color-swatch.active .swatch-circle {
            border-color: var(--text-primary);
            box-shadow: 0 0 0 2px var(--bg-card), 0 0 0 4px var(--text-primary);
        }

        .highlight-color-swatch.active .swatch-check {
            display: block;
        }

        .swatch-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .highlight-color-swatch.active .swatch-label {
            color: var(--text-primary);
        }

        .verse-number {
            font-size: 14px;
            vertical-align: super;
            color: var(--accent);
            font-weight: 600;
            margin-right: 2px;
        }

        /* Theme Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px var(--shadow-hover);
        }

        .modal-content::-webkit-scrollbar {
            width: 8px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 4px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 4px;
        }

        .modal-header {
            font-family: 'Open Sans', -apple-system, sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Settings Tabs */
        .settings-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 25px;
            border-bottom: 2px solid var(--border);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .settings-tab {
            padding: 12px 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
            white-space: nowrap;
            background: transparent;
            border-top: none;
            border-left: none;
            border-right: none;
            margin-bottom: -2px;
        }

        .settings-tab:hover {
            color: var(--text-secondary);
        }

        .settings-tab.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        .settings-tab-content {
            display: none;
        }

        .settings-tab-content.active {
            display: block;
        }

        /* Settings Sections */
        .settings-section {
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border);
        }

        .settings-section:last-of-type {
            border-bottom: none;
        }

        .settings-section h3 {
            font-family: 'Open Sans', -apple-system, sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px 15px;
            background: var(--bg-secondary);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .setting-item:hover {
            background: var(--bg-primary);
        }

        .setting-label {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .setting-label-main {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .setting-label-sub {
            font-size: 12px;
            color: var(--text-muted);
        }

        .theme-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 10px;
        }

        .theme-option {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            font-size: 14px;
            color: white;
        }

        .theme-option:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .theme-option.active {
            border-color: var(--accent);
            background: var(--accent);
            color: white;
        }

        /* Font Grid */
        .font-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .font-option {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            font-size: 14px;
        }

        .font-option:hover {
            border-color: var(--accent);
        }

        .font-option.active {
            border-color: var(--accent);
            background: var(--accent);
            color: white;
        }

        /* Font Size Buttons */
        .font-size-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-top: 10px;
        }

        .font-size-option {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 10px 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
        }

        .font-size-option:hover {
            border-color: var(--accent);
        }

        .font-size-option.active {
            border-color: var(--accent);
            background: var(--accent);
            color: white;
        }

        /* Playback Speed Slider */
        .speed-control {
            margin-top: 0px;
        }

        .speed-slider {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: var(--bg-secondary);
            outline: none;
            -webkit-appearance: none;
        }

        .speed-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--accent);
            cursor: pointer;
        }

        .speed-slider::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--accent);
            cursor: pointer;
            border: none;
        }

        .speed-value {
            text-align: center;
            margin-top: 8px;
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .close-modal {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .close-modal:hover {
            background: var(--accent-hover);
        }

        .reset-settings {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            margin-top: 10px;
            transition: all 0.2s ease;
        }

        .reset-settings:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        /* Chapter Picker Modal */
        .chapter-picker-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .chapter-picker-modal.active {
            display: flex;
        }

        .chapter-picker-content {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 30px;
            max-width: 700px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px var(--shadow-hover);
            position: relative;
        }

        .chapter-picker-header {
            font-family: 'Open Sans', -apple-system, sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-button {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 16px;
            cursor: pointer;
            font-size: 14px;
            color: var(--text-secondary);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .back-button:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        /* Book Grid */
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .book-grid-item {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 20px 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .book-grid-item:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .book-grid-item .book-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-family: 'Open Sans', -apple-system, sans-serif;
        }

        .book-grid-item .book-info {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Year Tabs in Chapter Picker */
        .year-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .year-tab {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 20px;
            padding: 8px 18px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            transition: all 0.2s ease;
            white-space: nowrap;
            font-family: 'Open Sans', -apple-system, sans-serif;
        }

        .year-tab:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .year-tab.active {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        /* Keyboard Shortcuts */
        .shortcut-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .shortcut-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: var(--bg-secondary);
            border-radius: 8px;
            transition: background 0.15s ease;
        }

        .shortcut-row:hover {
            background: var(--bg-primary);
        }

        .shortcut-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .shortcut-key-area {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .keycap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 32px;
            padding: 0 10px;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-bottom-width: 3px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            font-family: 'Open Sans', monospace;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
            user-select: none;
        }

        .keycap:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .keycap.listening {
            border-color: var(--accent);
            background: var(--accent);
            color: white;
            animation: keycap-pulse 1s infinite;
        }

        @keyframes keycap-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .shortcut-reset-btn {
            background: none;
            border: none;
            font-size: 14px;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
            border-radius: 4px;
            transition: color 0.15s ease;
            line-height: 1;
        }

        .shortcut-reset-btn:hover {
            color: var(--accent);
        }

        .shortcut-section-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 0 6px;
        }

        .shortcut-section-label:first-child {
            padding-top: 0;
        }

        .shortcuts-note {
            font-size: 12px;
            color: var(--text-muted);
            text-align: center;
            padding: 12px 0 0;
            line-height: 1.5;
        }

        .reset-shortcuts-btn {
            width: 100%;
            padding: 10px;
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            margin-top: 12px;
            transition: all 0.2s ease;
        }

        .reset-shortcuts-btn:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        /* Chapter Grid */
        .chapter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .chapter-grid-item {
            background: var(--bg-secondary);
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 15px 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .chapter-grid-item:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px var(--shadow);
        }

        .chapter-grid-item.active {
            border-color: var(--accent);
            background: var(--accent);
            color: white;
        }

        .close-chapter-picker {
            margin-top: 20px;
            width: 100%;
            padding: 14px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .close-chapter-picker:hover {
            background: var(--accent-hover);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            /* Tablet */
            .logo {
                height: 50px;
            }

            .title-section h1 {
                font-size: 20px;
            }

            .chapter-grid {
                grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            }

            .player-wrapper {
                padding: 12px 18px;
            }

            .text-wrapper {
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            /* Mobile */
            .header-container {
                padding: 12px 15px;
            }

            .logo {
                height: 40px;
            }

            .title-section h1 {
                font-size: 18px;
            }

            .title-section p {
                font-size: 11px;
            }

            .chapter-select-btn {
                font-size: 14px;
                padding: 9px 16px;
            }

            .player-wrapper {
                padding: 0 15px;
                margin-bottom: 12px;
                bottom: 16px; /* Tighter gap to progress bar on mobile */
            }

            .text-wrapper {
                padding: 20px;
            }

            .chapter-title {
                font-size: 18px;
                margin-bottom: 10px;
            }

            .control-btn {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .control-btn.play-btn {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }

            .controls-section {
                gap: 10px;
            }

            .text-content {
                font-size: 18px;
            }

            .chapter-grid {
                grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
                gap: 8px;
            }

            .chapter-grid-item {
                padding: 12px 8px;
                font-size: 14px;
            }

            .book-grid {
                grid-template-columns: 1fr;
            }

            .chapter-picker-content {
                padding: 20px;
                width: 95%;
            }

            .chapter-picker-header {
                font-size: 22px;
            }

            .settings-btn {
                width: 42px;
                height: 42px;
                font-size: 20px;
            }

            .settings-tabs {
                gap: 4px;
            }

            .settings-tab {
                padding: 10px 14px;
                font-size: 13px;
            }
        }

        /* Loading State */
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Fixed Header -->
    <div class="fixed-header" id="fixedHeader">
        <div class="header-container">
            <!-- Header -->
            <div class="header">
                <div class="logo-section">
                    <a href="/"><img src="pbe_team_bold_logo.png" alt="PBE Team Bold Logo" class="logo"></a>
                    <div class="title-section">
                        <h1>PBE Study</h1>
                        <p id="headerSubtitle">Pathfinder Bible Experience</p>
                    </div>
                </div>
                
                <div class="header-controls">
                    <button class="chapter-select-btn" id="chapterSelectBtn">
                        <span id="currentChapterDisplay">Select Chapter</span>
                    </button>
                    <button class="settings-btn" id="settingsBtn" title="Settings">⚙️</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scrollable Main Content -->
    <div class="main-content" id="mainContent" style="padding-top: 500px;"> <!-- Reduced padding -->
        <div class="container">
            <!-- Text Section -->
            <div class="text-wrapper">
                <div class="text-content" id="textContent">
                    <p class="loading">Select a chapter to begin</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fixed Bottom Player -->
    <div class="player-wrapper" id="playerWrapper">
        <audio id="audioPlayer" preload="metadata"></audio>
        
        <div class="player-content">
            <!-- Controls (Hidden when collapsed) -->
            <div class="controls-wrapper">
                <div class="controls-container">
                    <button class="control-btn" id="prevChapterBtn" title="Previous Chapter">⏮</button>
                    <button class="control-btn play-btn" id="playBtn" title="Play/Pause">▶</button>
                    <button class="control-btn" id="nextChapterBtn" title="Next Chapter">⏭</button>
                    
                    <!-- Speed Control -->
                    <div class="speed-control">
                        <button class="speed-btn" id="speedBtn" title="Playback Speed">1×</button>
                        <div class="speed-dropdown" id="speedDropdown">
                            <div class="speed-option" data-speed="0.5">0.5×</div>
                            <div class="speed-option" data-speed="0.75">0.75×</div>
                            <div class="speed-option active" data-speed="1">1×</div>
                            <div class="speed-option" data-speed="1.25">1.25×</div>
                            <div class="speed-option" data-speed="1.5">1.5×</div>
                            <div class="speed-option" data-speed="1.75">1.75×</div>
                            <div class="speed-option" data-speed="2">2×</div>
                            <div class="speed-option" data-speed="2.25">2.25×</div>
                            <div class="speed-option" data-speed="2.5">2.5×</div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collapse/Expand Button (Mobile Only) - Independent of player-wrapper -->
    <button class="collapse-btn" id="collapseBtn" title="Collapse Player">
        <span id="collapseIcon">▼</span>
    </button>

    <!-- Progress Section - Independent, always at bottom -->
    <div class="progress-section">
        <div class="progress-bar" id="progressBar">
            <div class="progress-fill" id="progressFill"></div>
            <div class="time-display">
                <span id="currentTime">0:00</span>
                <span id="duration">0:00</span>
            </div>
        </div>
    </div>

    <!-- Chapter Picker Modal -->
    <div class="chapter-picker-modal" id="chapterPickerModal">
        <div class="chapter-picker-content">
            <div class="chapter-picker-header">
                <button class="back-button" id="backToBooks" style="display: none;">
                    <span>←</span>
                    <span>Back</span>
                </button>
                <span id="modalTitle">📖 Select Book</span>
                <span style="font-size: 20px; cursor: pointer;" id="closeChapterPickerX">✕</span>
            </div>
            
            <!-- Book Selection View -->
            <div id="bookSelectionView">
                <div class="year-tabs" id="yearTabs">
                    <!-- Year tabs populated by JS -->
                </div>
                <div class="book-grid" id="bookGrid">
                    <!-- Books will be populated here -->
                </div>
            </div>
            
            <!-- Chapter Selection View (hidden by default) -->
            <div id="chapterSelectionView" style="display: none;">
                <div class="chapter-grid" id="chapterGrid">
                    <!-- Chapters will be populated here -->
                </div>
            </div>
            
            <button class="close-chapter-picker" id="closeChapterPicker">Done</button>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal" id="settingsModal">
        <div class="modal-content">
            <h2 class="modal-header">⚙️ Settings</h2>
            
            <!-- Settings Tabs -->
            <div class="settings-tabs">
                <button class="settings-tab active" data-tab="playback">🎵 Playback</button>
                <button class="settings-tab" data-tab="reading">📖 Reading</button>
                <button class="settings-tab" data-tab="visual">🎨 Visual</button>
                <button class="settings-tab" data-tab="shortcuts">⌨️ Keys</button>
                <button class="settings-tab" data-tab="about">ℹ️ About</button>
            </div>

            <!-- Playback Tab -->
            <div class="settings-tab-content active" id="playbackTab">
                <div class="settings-section">
                    <h3>🎵 Audio Playback</h3>
                    
                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-label-main">Playback Speed</span>
                            <span class="setting-label-sub">Adjust audio playback speed</span>
                        </div>
                    </div>
                    <div class="speed-control">
                        <input type="range" min="0.5" max="2.5" step="0.25" value="1.0" class="speed-slider" id="speedSlider">
                        <div class="speed-value" id="speedValue">1.0x Normal</div>
                    </div>

                    <div class="setting-item" style="margin-top: 15px;">
                        <div class="setting-label">
                            <span class="setting-label-main">Auto-Advance</span>
                            <span class="setting-label-sub">Play next chapter automatically</span>
                        </div>
                        <div class="toggle-switch active" id="autoAdvanceToggle">
                            <div class="toggle-knob"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reading Tab -->
            <div class="settings-tab-content" id="readingTab">
                <div class="settings-section">
                    <h3>📖 Reading Options</h3>
                    
                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-label-main">Verse Per Line</span>
                            <span class="setting-label-sub">Each verse on a new line</span>
                        </div>
                        <div class="toggle-switch active" id="versePerLineToggle">
                            <div class="toggle-knob"></div>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-label-main">Highlight Active Verse</span>
                            <span class="setting-label-sub">Highlight currently playing verse</span>
                        </div>
                        <div class="toggle-switch active" id="highlightToggle">
                            <div class="toggle-knob"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual Tab -->
            <div class="settings-tab-content" id="visualTab">
                <div class="settings-section">
                    <h3>🎨 Visual Settings</h3>
                    
                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-label-main">Theme</span>
                            <span class="setting-label-sub">Choose your color scheme</span>
                        </div>
                    </div>
                    <div class="theme-grid">
                        <div class="theme-option active" data-theme="light">☀️ Light</div>
                        <div class="theme-option" data-theme="dark">🌙 Dark</div>
                    </div>

                    <div class="setting-item" style="margin-top: 20px;">
                        <div class="setting-label">
                            <span class="setting-label-main">Highlight Color</span>
                            <span class="setting-label-sub">Color used to highlight the active verse</span>
                        </div>
                    </div>
                    <div class="highlight-color-grid">
                        <button class="highlight-color-swatch active" data-color="crimson" title="Crimson">
                            <div class="swatch-circle" style="background: rgba(210,7,2,0.75);">
                                <span class="swatch-check">✓</span>
                            </div>
                            <span class="swatch-label">Crimson</span>
                        </button>
                        <button class="highlight-color-swatch" data-color="sapphire" title="Sapphire">
                            <div class="swatch-circle" style="background: rgba(30,100,220,0.80);">
                                <span class="swatch-check">✓</span>
                            </div>
                            <span class="swatch-label">Sapphire</span>
                        </button>
                        <button class="highlight-color-swatch" data-color="gold" title="Gold">
                            <div class="swatch-circle" style="background: rgba(190,140,0,0.85);">
                                <span class="swatch-check">✓</span>
                            </div>
                            <span class="swatch-label">Gold</span>
                        </button>
                        <button class="highlight-color-swatch" data-color="amethyst" title="Amethyst">
                            <div class="swatch-circle" style="background: rgba(130,40,190,0.80);">
                                <span class="swatch-check">✓</span>
                            </div>
                            <span class="swatch-label">Amethyst</span>
                        </button>
                    </div>

                    <div class="setting-item" style="margin-top: 20px;">
                        <div class="setting-label">
                            <span class="setting-label-main">Font Family</span>
                            <span class="setting-label-sub">Choose your reading font</span>
                        </div>
                    </div>
                    <div class="font-grid">
                        <div class="font-option active" data-font="opensans">Open Sans</div>
                        <div class="font-option" data-font="sourceserif">Source Serif</div>
                        <div class="font-option" data-font="merriweather">Merriweather</div>
                        <div class="font-option" data-font="lato">Lato</div>
                    </div>

                    <div class="setting-item" style="margin-top: 20px;">
                        <div class="setting-label">
                            <span class="setting-label-main">Font Size</span>
                            <span class="setting-label-sub">Adjust text size for comfort</span>
                        </div>
                    </div>
                    <div class="font-size-grid">
                        <div class="font-size-option" data-size="xs">XS</div>
                        <div class="font-size-option" data-size="s">S</div>
                        <div class="font-size-option" data-size="m">M</div>
                        <div class="font-size-option active" data-size="l">L</div>
                        <div class="font-size-option" data-size="xl">XL</div>
                        <div class="font-size-option" data-size="xxl">2XL</div>
                        <div class="font-size-option" data-size="xxxl">3XL</div>
                    </div>
                </div>
            </div>

            <!-- Shortcuts Tab -->
            <div class="settings-tab-content" id="shortcutsTab">
                <div class="settings-section">
                    <h3>⌨️ Keyboard Shortcuts</h3>
                    <div class="shortcut-list" id="shortcutList">
                        <!-- Populated by JS -->
                    </div>
                    <p class="shortcuts-note">Click any key to rebind. Press <strong>Escape</strong> to cancel.</p>
                    <button class="reset-shortcuts-btn" id="resetShortcutsBtn">↺ Reset All Shortcuts</button>
                </div>
            </div>

            <!-- About Tab -->
            <div class="settings-tab-content" id="aboutTab">
                <div class="settings-section">
                    <h3>ℹ️ About</h3>
                    
                    <div class="setting-item">
                        <div class="setting-label">
                            <span class="setting-label-main">PBE Study</span>
                            <span class="setting-label-sub">Version 2.0.0</span>
                        </div>
                    </div>
                    
                    <!-- 
                    ========================================
                    ACKNOWLEDGMENTS & CREDITS
                    ========================================
                    
                    Add your acknowledgments, credits, copyright info, and thanks here.
                    This section is commented out for you to manually edit later.
                    
                    Example structure:
                    
                    <div style="margin-top: 20px; padding: 20px; background: var(--bg-secondary); border-radius: 8px;">
                        <h4 style="margin-bottom: 15px; color: var(--text-primary);">Credits & Acknowledgments</h4>
                        
                        <p style="margin-bottom: 10px; color: var(--text-secondary); line-height: 1.6;">
                            <strong>Audio Recording:</strong> [Your source/credit]
                        </p>
                        
                        <p style="margin-bottom: 10px; color: var(--text-secondary); line-height: 1.6;">
                            <strong>Scripture Text:</strong> [Version/translation]
                        </p>
                        
                        <p style="margin-bottom: 10px; color: var(--text-secondary); line-height: 1.6;">
                            <strong>Special Thanks:</strong> [Names/organizations]
                        </p>
                        
                        <p style="margin-bottom: 10px; color: var(--text-secondary); line-height: 1.6;">
                            <strong>Copyright:</strong> © 2025 [Your organization]. All rights reserved.
                        </p>
                        
                        <p style="color: var(--text-muted); font-size: 13px; margin-top: 15px;">
                            [Any additional legal notices or disclaimers]
                        </p>
                    </div>
                    
                    ========================================
                    END OF ACKNOWLEDGMENTS SECTION
                    ========================================
                    -->
                    
                    <div style="margin-top: 20px; padding: 20px; background: var(--bg-secondary); border-radius: 8px; text-align: center;">
                        <p style="color: var(--text-muted); font-size: 14px;">
                            Built with dedication for PBE.
                        </p>
                        <p style="color: var(--text-muted); font-size: 14px;">
                            For personal study.
                        </p>
                        <p style="color: var(--text-muted); font-size: 14px;">
                            Hide God's Word in your heart.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <button class="reset-settings" id="resetSettings">↺ Reset All Settings</button>
            <button class="close-modal" id="closeSettingsModal">Done</button>
        </div>
    </div>

    <script>
        // PBE Year/Book configuration
        const PBE_YEARS = [
            {
                id: '2025-2026',
                label: 'PBE 2025–2026',
                books: [
                    { id: 'isaiah', name: 'Isaiah', chapters: 33 }
                ]
            },
            {
                id: '2026-2027',
                label: 'PBE 2026–2027',
                books: [
                    { id: 'mark', name: 'Mark', chapters: 16 },
                    { id: '1peter', name: '1 Peter', chapters: 5 },
                    { id: '2peter', name: '2 Peter', chapters: 3 },
                    { id: '1john', name: '1 John', chapters: 5 },
                    { id: '2john', name: '2 John', chapters: 1 },
                    { id: '3john', name: '3 John', chapters: 1 }
                ]
            }
            // Future years: just add another object here
        ];

        // Flatten all books for lookup
        const ALL_BOOKS = PBE_YEARS.flatMap(y => y.books);

        // Keyboard Shortcut Defaults
        const DEFAULT_SHORTCUTS = [
            // Playback
            { id: 'playPause',       key: 'Space',        label: 'Play / Pause',          group: 'Playback' },
            { id: 'seekBack',        key: 'KeyJ',         label: 'Seek Back 10s',         group: 'Playback' },
            { id: 'seekForward',     key: 'KeyL',         label: 'Seek Forward 10s',      group: 'Playback' },
            { id: 'speedUp',         key: 'BracketRight', label: 'Speed Up (+0.25×)',      group: 'Playback' },
            { id: 'speedDown',       key: 'BracketLeft',  label: 'Speed Down (−0.25×)',    group: 'Playback' },
            // Navigation
            { id: 'prevChapter',     key: 'ArrowLeft',    label: 'Previous Chapter',       group: 'Navigation' },
            { id: 'nextChapter',     key: 'ArrowRight',   label: 'Next Chapter',           group: 'Navigation' },
            { id: 'prevVerse',       key: 'ArrowUp',      label: 'Previous Verse',         group: 'Navigation' },
            { id: 'nextVerse',       key: 'ArrowDown',    label: 'Next Verse',             group: 'Navigation' },
            { id: 'openChapters',    key: 'KeyC',         label: 'Open Chapter Picker',    group: 'Navigation' },
            // Toggles
            { id: 'openSettings',    key: 'Comma',        label: 'Open Settings',          group: 'Toggles' },
            { id: 'toggleTheme',     key: 'KeyD',         label: 'Toggle Dark / Light',    group: 'Toggles' },
            { id: 'toggleVerseLine', key: 'KeyV',         label: 'Toggle Verse Per Line',  group: 'Toggles' },
            { id: 'toggleHighlight', key: 'KeyH',         label: 'Toggle Highlight',       group: 'Toggles' },
        ];

        // Active shortcuts (mutable — overridden by user rebinds)
        let shortcuts = DEFAULT_SHORTCUTS.map(s => ({ ...s }));

        function loadShortcuts() {
            const saved = localStorage.getItem('pbe-study-shortcuts');
            if (!saved) return;
            try {
                const overrides = JSON.parse(saved); // { id: key, ... }
                shortcuts.forEach(s => {
                    if (overrides[s.id]) s.key = overrides[s.id];
                });
            } catch (e) { /* ignore */ }
        }

        function saveShortcuts() {
            const overrides = {};
            shortcuts.forEach((s, i) => {
                if (s.key !== DEFAULT_SHORTCUTS[i].key) overrides[s.id] = s.key;
            });
            if (Object.keys(overrides).length) {
                localStorage.setItem('pbe-study-shortcuts', JSON.stringify(overrides));
            } else {
                localStorage.removeItem('pbe-study-shortcuts');
            }
        }

        // Pretty-print a keycode for display
        function prettyKey(code) {
            const map = {
                Space: 'Space', ArrowLeft: '←', ArrowRight: '→', ArrowUp: '↑', ArrowDown: '↓',
                BracketLeft: '[', BracketRight: ']', Backquote: '`', Minus: '−', Equal: '=',
                Comma: ',', Period: '.', Slash: '/', Semicolon: ';', Quote: "'", Backslash: '\\',
                Escape: 'Esc', Enter: 'Enter', Backspace: '⌫', Tab: 'Tab', Delete: 'Del',
                Home: 'Home', End: 'End', PageUp: 'PgUp', PageDown: 'PgDn',
            };
            if (map[code]) return map[code];
            if (code.startsWith('Key')) return code.slice(3);
            if (code.startsWith('Digit')) return code.slice(5);
            return code;
        }

        // Render shortcuts list in settings
        let listeningForId = null;

        function renderShortcutList() {
            const container = document.getElementById('shortcutList');
            if (!container) return;
            container.innerHTML = '';

            let currentGroup = '';
            shortcuts.forEach(s => {
                // Group header
                if (s.group !== currentGroup) {
                    currentGroup = s.group;
                    const groupEl = document.createElement('div');
                    groupEl.className = 'shortcut-section-label';
                    groupEl.textContent = currentGroup;
                    container.appendChild(groupEl);
                }

                const row = document.createElement('div');
                row.className = 'shortcut-row';

                const isDefault = s.key === DEFAULT_SHORTCUTS.find(d => d.id === s.id).key;
                const isListening = listeningForId === s.id;

                row.innerHTML = `
                    <span class="shortcut-label">${s.label}</span>
                    <span class="shortcut-key-area">
                        <span class="keycap${isListening ? ' listening' : ''}" data-shortcut-id="${s.id}">
                            ${isListening ? 'Press a key…' : prettyKey(s.key)}
                        </span>
                        ${!isDefault ? `<button class="shortcut-reset-btn" data-reset-id="${s.id}" title="Reset to default">↺</button>` : ''}
                    </span>
                `;
                container.appendChild(row);
            });

            // Keycap click → enter listening mode
            container.querySelectorAll('.keycap').forEach(el => {
                el.onclick = () => {
                    listeningForId = el.dataset.shortcutId;
                    renderShortcutList();
                };
            });

            // Per-shortcut reset
            container.querySelectorAll('.shortcut-reset-btn').forEach(el => {
                el.onclick = (e) => {
                    e.stopPropagation();
                    const id = el.dataset.resetId;
                    const def = DEFAULT_SHORTCUTS.find(d => d.id === id);
                    const sc = shortcuts.find(s => s.id === id);
                    if (def && sc) {
                        sc.key = def.key;
                        saveShortcuts();
                        renderShortcutList();
                    }
                };
            });
        }

        // Rebind listener (captured at document level)
        function handleRebindKey(e) {
            if (!listeningForId) return;

            // Escape cancels rebinding
            if (e.code === 'Escape') {
                e.preventDefault();
                e.stopPropagation();
                listeningForId = null;
                renderShortcutList();
                return;
            }

            // Ignore modifier-only presses
            if (['ShiftLeft', 'ShiftRight', 'ControlLeft', 'ControlRight', 'AltLeft', 'AltRight', 'MetaLeft', 'MetaRight'].includes(e.code)) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const newKey = e.code;

            // Check for conflicts
            const conflict = shortcuts.find(s => s.key === newKey && s.id !== listeningForId);
            if (conflict) {
                // Swap: give the conflicting shortcut the old key
                const current = shortcuts.find(s => s.id === listeningForId);
                conflict.key = current.key;
            }

            // Assign new key
            const sc = shortcuts.find(s => s.id === listeningForId);
            if (sc) sc.key = newKey;

            listeningForId = null;
            saveShortcuts();
            renderShortcutList();
        }

        // Verse navigation
        function jumpToVerse(index) {
            if (!state.verses.length) return;
            const clamped = Math.max(0, Math.min(state.verses.length - 1, index));
            DOM.audio.currentTime = state.verses[clamped].startTime;
            if (!state.isPlaying) DOM.audio.play();
        }

        function currentVerseIndex() {
            if (!state.verses.length) return 0;
            const t = DOM.audio.currentTime;
            for (let i = state.verses.length - 1; i >= 0; i--) {
                if (t >= state.verses[i].startTime) return i;
            }
            return 0;
        }

        // Seek helpers
        function seekBy(seconds) {
            if (!DOM.audio.duration) return;
            DOM.audio.currentTime = Math.max(0, Math.min(DOM.audio.duration, DOM.audio.currentTime + seconds));
        }

        function cycleSpeed(delta) {
            const current = state.settings.playbackSpeed;
            const next = Math.round((current + delta) * 100) / 100;
            const clamped = Math.max(0.5, Math.min(2.5, next));
            applyPlaybackSpeed(clamped);
            DOM.speedBtn.textContent = clamped + '×';
            // Sync dropdown active state
            DOM.speedOptions.forEach(opt => {
                opt.classList.toggle('active', parseFloat(opt.dataset.speed) === clamped);
            });
        }

        // Main shortcut handler
        function handleShortcuts(e) {
            // Don't trigger shortcuts when rebinding
            if (listeningForId) return;

            // Don't trigger in input fields
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) return;

            // Escape always closes modals (not rebindable)
            if (e.code === 'Escape') {
                if (DOM.chapterPickerModal.classList.contains('active')) {
                    DOM.chapterPickerModal.classList.remove('active');
                    resetModalView();
                    return;
                }
                if (DOM.settingsModal.classList.contains('active')) {
                    DOM.settingsModal.classList.remove('active');
                    return;
                }
                return;
            }

            // Don't trigger if any modifier held (let browser defaults work)
            if (e.ctrlKey || e.metaKey || e.altKey) return;

            const sc = shortcuts.find(s => s.key === e.code);
            if (!sc) return;

            e.preventDefault();

            const actions = {
                playPause:       () => togglePlay(),
                seekBack:        () => seekBy(-10),
                seekForward:     () => seekBy(10),
                speedUp:         () => cycleSpeed(0.25),
                speedDown:       () => cycleSpeed(-0.25),
                prevChapter:     () => previousChapter(),
                nextChapter:     () => nextChapter(),
                prevVerse:       () => jumpToVerse(currentVerseIndex() - 1),
                nextVerse:       () => jumpToVerse(currentVerseIndex() + 1),
                openChapters:    () => {
                    DOM.chapterPickerModal.classList.toggle('active');
                    if (DOM.chapterPickerModal.classList.contains('active')) resetModalView();
                },
                openSettings:    () => DOM.settingsModal.classList.toggle('active'),
                toggleTheme:     () => applyTheme(state.settings.theme === 'dark' ? 'light' : 'dark'),
                toggleVerseLine: () => toggleVersePerLine(),
                toggleHighlight: () => toggleHighlight(),
            };

            if (actions[sc.id]) actions[sc.id]();
        }

        // State
        const state = {
            currentYear: PBE_YEARS[0].id,
            currentBook: PBE_YEARS[0].books[0].id,
            currentChapter: null,
            currentVerseIndex: 0,
            lastHighlightedVerse: -1,
            isPlaying: false,
            verses: [],
            audio: document.getElementById('audioPlayer'),
            animationFrameId: null,
            settings: {
                theme: 'light',
                fontFamily: 'opensans',
                fontSize: window.innerWidth <= 768 ? 's' : 'l',
                versePerLine: true,
                highlightEnabled: true,
                highlightColor: 'crimson',
                playbackSpeed: 1.0,
                autoAdvance: true
            }
        };

        // DOM Elements
        const DOM = {
            audio: document.getElementById('audioPlayer'),
            playBtn: document.getElementById('playBtn'),
            prevChapterBtn: document.getElementById('prevChapterBtn'),
            nextChapterBtn: document.getElementById('nextChapterBtn'),
            progressBar: document.getElementById('progressBar'),
            progressFill: document.getElementById('progressFill'),
            currentTime: document.getElementById('currentTime'),
            duration: document.getElementById('duration'),
            textContent: document.getElementById('textContent'),
            mainContent: document.getElementById('mainContent'),
            // Header elements
            fixedHeader: document.getElementById('fixedHeader'),
            chapterSelectBtn: document.getElementById('chapterSelectBtn'),
            currentChapterDisplay: document.getElementById('currentChapterDisplay'),
            // Player elements
            playerWrapper: document.getElementById('playerWrapper'),
            collapseBtn: document.getElementById('collapseBtn'),
            collapseIcon: document.getElementById('collapseIcon'),
            // Speed control
            speedBtn: document.getElementById('speedBtn'),
            speedDropdown: document.getElementById('speedDropdown'),
            speedOptions: document.querySelectorAll('.speed-option'),
            // Chapter picker modal
            chapterPickerModal: document.getElementById('chapterPickerModal'),
            bookSelectionView: document.getElementById('bookSelectionView'),
            chapterSelectionView: document.getElementById('chapterSelectionView'),
            bookGrid: document.getElementById('bookGrid'),
            chapterGrid: document.getElementById('chapterGrid'),
            yearTabs: document.getElementById('yearTabs'),
            modalTitle: document.getElementById('modalTitle'),
            backToBooks: document.getElementById('backToBooks'),
            closeChapterPicker: document.getElementById('closeChapterPicker'),
            closeChapterPickerX: document.getElementById('closeChapterPickerX'),
            headerSubtitle: document.getElementById('headerSubtitle'),
            // Settings modal
            settingsBtn: document.getElementById('settingsBtn'),
            settingsModal: document.getElementById('settingsModal'),
            closeSettingsModal: document.getElementById('closeSettingsModal'),
            settingsTabs: document.querySelectorAll('.settings-tab'),
            highlightToggle: document.getElementById('highlightToggle'),
            versePerLineToggle: document.getElementById('versePerLineToggle'),
            autoAdvanceToggle: document.getElementById('autoAdvanceToggle'),
            speedSlider: document.getElementById('speedSlider'),
            speedValue: document.getElementById('speedValue'),
            resetSettings: document.getElementById('resetSettings')
        };

        // Initialize
        function init() {
            loadShortcuts();
            renderYearTabs();
            renderBookGrid();
            calculateHeaderHeight();
            loadSettings();
            initTheme();
            setupEventListeners();
            setupSettingsTabs();
            renderShortcutList();
            
            // Restore last book/chapter or auto-load first available
            const saved = localStorage.getItem('pbe-study-last');
            if (saved) {
                try {
                    const last = JSON.parse(saved);
                    if (last.year) state.currentYear = last.year;
                    if (last.book) state.currentBook = last.book;
                    renderYearTabs();
                    renderBookGrid();
                    if (last.chapter) {
                        loadChapter(last.chapter);
                        return;
                    }
                } catch (e) { /* ignore */ }
            }
            
            // Default: load chapter 1 of first book if audio exists
            const bookAudio = AUDIO_MAP[state.currentBook];
            if (bookAudio && bookAudio.length > 0) {
                loadChapter(1);
            }
        }

        // Calculate and set header height for main content padding
        function calculateHeaderHeight() {
            const header = document.querySelector('.fixed-header');
            if (header) {
                const height = header.offsetHeight;
                DOM.mainContent.style.paddingTop = (height + 20) + 'px';
            }
        }

        // Render Year Tabs
        function renderYearTabs() {
            DOM.yearTabs.innerHTML = '';
            PBE_YEARS.forEach(year => {
                const tab = document.createElement('button');
                tab.className = 'year-tab' + (year.id === state.currentYear ? ' active' : '');
                tab.textContent = year.label;
                tab.onclick = () => {
                    state.currentYear = year.id;
                    renderYearTabs();
                    renderBookGrid();
                };
                DOM.yearTabs.appendChild(tab);
            });
        }

        // Render Book Grid (filtered by selected year)
        function renderBookGrid() {
            DOM.bookGrid.innerHTML = '';
            const year = PBE_YEARS.find(y => y.id === state.currentYear);
            if (!year) return;

            year.books.forEach(book => {
                const hasAudio = AUDIO_MAP[book.id] && AUDIO_MAP[book.id].length > 0;
                const item = document.createElement('div');
                item.className = 'book-grid-item';
                if (!hasAudio) item.style.opacity = '0.5';
                item.onclick = () => showChapterSelection(book);

                item.innerHTML = `
                    <div class="book-title">${book.name}</div>
                    <div class="book-info">${book.chapters} chapter${book.chapters > 1 ? 's' : ''}${hasAudio ? '' : ' · coming soon'}</div>
                `;

                DOM.bookGrid.appendChild(item);
            });
        }

        // Show chapter selection for a specific book
        function showChapterSelection(book) {
            state.currentBook = book.id;

            DOM.modalTitle.textContent = `📖 ${book.name}`;
            DOM.backToBooks.style.display = 'flex';

            DOM.bookSelectionView.style.display = 'none';
            DOM.chapterSelectionView.style.display = 'block';

            const bookAudio = AUDIO_MAP[book.id] || [];
            const availableChapters = bookAudio.map(f => f.chapter);

            DOM.chapterGrid.innerHTML = '';
            for (let i = 1; i <= book.chapters; i++) {
                const item = document.createElement('div');
                item.className = 'chapter-grid-item';
                item.textContent = i;
                item.dataset.chapter = i;

                const hasAudio = availableChapters.includes(i);
                if (!hasAudio) item.style.opacity = '0.4';

                if (i === state.currentChapter && book.id === state.currentBook) {
                    item.classList.add('active');
                }

                item.onclick = () => {
                    if (!hasAudio) return; // Skip chapters without audio
                    loadChapter(i);
                    DOM.chapterPickerModal.classList.remove('active');
                    resetModalView();
                };

                DOM.chapterGrid.appendChild(item);
            }
        }

        // Reset modal to book selection view
        function resetModalView() {
            DOM.bookSelectionView.style.display = 'block';
            DOM.chapterSelectionView.style.display = 'none';
            DOM.modalTitle.textContent = '📖 Select Book';
            DOM.backToBooks.style.display = 'none';
            renderYearTabs();
            renderBookGrid();
        }

        // Setup Settings Tabs
        function setupSettingsTabs() {
            DOM.settingsTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabName = tab.dataset.tab;
                    
                    // Update tab active states
                    DOM.settingsTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    
                    // Update content active states
                    document.querySelectorAll('.settings-tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    document.getElementById(`${tabName}Tab`).classList.add('active');
                });
            });
        }

        // Load Chapter
        async function loadChapter(chapterNum) {
            const bookAudio = AUDIO_MAP[state.currentBook] || [];
            const file = bookAudio.find(f => f.chapter === chapterNum);

            if (!file) return;

            state.currentChapter = chapterNum;
            state.lastHighlightedVerse = -1;

            // Update chapter display button
            const book = ALL_BOOKS.find(b => b.id === state.currentBook);
            const displayText = `${book ? book.name : state.currentBook} ${chapterNum}`;
            DOM.currentChapterDisplay.textContent = displayText;

            // Update header subtitle
            const year = PBE_YEARS.find(y => y.id === state.currentYear);
            if (DOM.headerSubtitle && year) {
                DOM.headerSubtitle.textContent = year.label;
            }

            // Load audio
            DOM.audio.src = file.path;
            DOM.audio.load();

            // Load text with timing
            await loadChapterText(chapterNum);

            // Persist last position
            localStorage.setItem('pbe-study-last', JSON.stringify({
                year: state.currentYear,
                book: state.currentBook,
                chapter: chapterNum
            }));

            // Auto-play if was playing
            if (state.isPlaying) {
                DOM.audio.play();
            }
        }

        // Load Chapter Text from CSV
        async function loadChapterText(chapterNum) {
            const bookId = state.currentBook;
            // Try subfolder first: text/isaiah/isaiah_01.csv, fall back to flat: text/isaiah_01.csv
            const subfolderPath = `text/${bookId}/${bookId}_${String(chapterNum).padStart(2, '0')}.csv`;
            const flatPath = `text/${bookId}_${String(chapterNum).padStart(2, '0')}.csv`;
            
            let filename = subfolderPath;
            
            try {
                let response = await fetch(subfolderPath);
                
                // Fall back to flat path if subfolder not found
                if (!response.ok) {
                    response = await fetch(flatPath);
                    filename = flatPath;
                }
                
                // Check if file exists
                if (!response.ok) {
                    console.error(`File not found: ${filename} (Status: ${response.status})`);
                    DOM.textContent.innerHTML = `<p class="loading">CSV file not found: ${filename}<br>Please create this file in the 'text' folder.</p>`;
                    return;
                }
                
                const csvText = await response.text();
                console.log(`Loaded ${filename}, ${csvText.length} characters`);
                
                // Parse CSV - more robust parsing
                const lines = csvText.split(/\r?\n/); // Handle both \n and \r\n
                state.verses = [];
                
                for (let i = 1; i < lines.length; i++) { // Skip header
                    const line = lines[i].trim();
                    if (!line) continue;
                    
                    // Try to parse CSV line
                    // More flexible regex that handles quoted and unquoted text
                    let match = line.match(/^(\d+),([\d.]+),([\d.]+),"(.+)"$/);
                    
                    if (!match) {
                        // Try without quotes
                        match = line.match(/^(\d+),([\d.]+),([\d.]+),(.+)$/);
                    }
                    
                    if (match) {
                        state.verses.push({
                            verse: parseInt(match[1]),
                            startTime: parseFloat(match[2]),
                            endTime: parseFloat(match[3]),
                            text: match[4].replace(/""/g, '"') // Handle escaped quotes
                        });
                    } else {
                        console.warn(`Could not parse line ${i}: ${line.substring(0, 50)}...`);
                    }
                }
                
                console.log(`Parsed ${state.verses.length} verses from ${filename}`);
                
                if (state.verses.length === 0) {
                    DOM.textContent.innerHTML = `<p class="loading">No verses found in ${filename}<br>Check the CSV format. See browser console for details.</p>`;
                    return;
                }
                
                renderText();
            } catch (error) {
                console.error('Error loading chapter text:', error);
                DOM.textContent.innerHTML = `<p class="loading">Error loading text: ${error.message}<br>Check browser console for details.</p>`;
            }
        }

        // Render Text
        function renderText() {
            if (state.verses.length === 0) {
                DOM.textContent.innerHTML = '<p class="loading">No text available</p>';
                return;
            }

            DOM.textContent.innerHTML = state.verses.map((v, i) => 
                `<span class="verse" data-index="${i}" data-start-time="${v.startTime}"><sup class="verse-number">${v.verse}</sup>${v.text}</span> `
            ).join('');
            
            // Add click-to-seek functionality
            attachVerseClickListeners();
        }

        // Attach click listeners to verses for seeking
        function attachVerseClickListeners() {
            document.querySelectorAll('.verse').forEach(verseEl => {
                verseEl.onclick = (e) => {
                    e.preventDefault();
                    const startTime = parseFloat(verseEl.dataset.startTime);
                    if (!isNaN(startTime)) {
                        DOM.audio.currentTime = startTime;
                        // Auto-play if not already playing
                        if (!state.isPlaying) {
                            DOM.audio.play();
                        }
                    }
                };
            });
        }

        // Play/Pause
        function togglePlay() {
            if (state.isPlaying) {
                DOM.audio.pause();
            } else {
                DOM.audio.play();
            }
        }

        // Previous Chapter
        function previousChapter() {
            if (!state.currentChapter) return;
            const bookAudio = AUDIO_MAP[state.currentBook] || [];
            const currentIndex = bookAudio.findIndex(f => f.chapter === state.currentChapter);
            if (currentIndex > 0) {
                loadChapter(bookAudio[currentIndex - 1].chapter);
            }
        }

        // Next Chapter
        function nextChapter() {
            if (!state.currentChapter) return;
            const bookAudio = AUDIO_MAP[state.currentBook] || [];
            const currentIndex = bookAudio.findIndex(f => f.chapter === state.currentChapter);
            if (currentIndex < bookAudio.length - 1) {
                loadChapter(bookAudio[currentIndex + 1].chapter);
            }
        }

        // Handle Scroll for Header Show/Hide
        let lastScrollY = 0;
        
        function handleScroll(currentScrollY) {
            // CONFIGURATION: Adjust these values to change scroll sensitivity
            const MOBILE_HIDE_THRESHOLD = 10;   // Mobile: Hide after scrolling down 10px (very sensitive)
            const DESKTOP_HIDE_THRESHOLD = 20;  // Desktop: Hide after scrolling down 20px (slightly more delay)
            
            const isMobile = window.innerWidth <= 768;
            const hideThreshold = isMobile ? MOBILE_HIDE_THRESHOLD : DESKTOP_HIDE_THRESHOLD;
            
            // Calculate scroll direction
            const scrollingDown = currentScrollY > lastScrollY;
            const scrollingUp = currentScrollY < lastScrollY;
            
            // Hide header when scrolling down (after threshold)
            if (scrollingDown && currentScrollY > hideThreshold) {
                DOM.fixedHeader.classList.add('hidden');
            }
            
            // Show header when scrolling up
            if (scrollingUp) {
                DOM.fixedHeader.classList.remove('hidden');
            }
            
            // Always show header when at top of page
            if (currentScrollY <= 5) {
                DOM.fixedHeader.classList.remove('hidden');
            }
            
            lastScrollY = currentScrollY;
        }

        // Update Progress Bar
        // Smooth Progress Bar Update using requestAnimationFrame
        function updateProgress() {
            // Cancel any existing animation frame
            if (state.animationFrameId) {
                cancelAnimationFrame(state.animationFrameId);
            }
            
            // Smooth update function
            function animateProgress() {
                if (DOM.audio.duration) {
                    const percent = (DOM.audio.currentTime / DOM.audio.duration) * 100;
                    DOM.progressFill.style.width = percent + '%';
                    
                    DOM.currentTime.textContent = formatTime(DOM.audio.currentTime);
                    DOM.duration.textContent = formatTime(DOM.audio.duration);
                    
                    // Update highlighted verse
                    if (state.settings.highlightEnabled) {
                        updateHighlight(DOM.audio.currentTime);
                    }
                }
                
                // Continue animation if playing
                if (state.isPlaying) {
                    state.animationFrameId = requestAnimationFrame(animateProgress);
                }
            }
            
            // Start animation
            animateProgress();
        }

        // Update Highlighted Verse - Only Auto-Scroll on Verse CHANGE
        function updateHighlight(currentTime) {
            const verseElements = document.querySelectorAll('.verse');
            verseElements.forEach((el, i) => {
                el.classList.remove('highlight');
            });
            
            // Find current verse
            for (let i = 0; i < state.verses.length; i++) {
                const verse = state.verses[i];
                if (currentTime >= verse.startTime && currentTime <= verse.endTime) {
                    const el = document.querySelector(`.verse[data-index="${i}"]`);
                    if (el) {
                        el.classList.add('highlight');
                        
                        // CRITICAL: Only scroll if verse INDEX has CHANGED
                        // This allows user to scroll freely - only auto-scrolls when moving to next verse
                        if (i !== state.lastHighlightedVerse) {
                            state.lastHighlightedVerse = i; // Update tracked verse
                            
                            // Smart auto-scroll: only scroll if verse is not visible
                            const rect = el.getBoundingClientRect();
                            const header = document.querySelector('.fixed-header');
                            const headerHeight = header ? header.offsetHeight : 0;
                            
                            // Check if verse is visible in viewport (accounting for fixed header)
                            const isVisible = (
                                rect.top >= headerHeight &&
                                rect.bottom <= (window.innerHeight - 90)
                            );
                            
                            // Only scroll if not visible AND verse changed
                            if (!isVisible) {
                                // Scroll to top of verse, accounting for fixed header
                                const elementPosition = el.getBoundingClientRect().top;
                                const offsetPosition = elementPosition + window.pageYOffset - 5;
                                
                                window.scrollTo({
                                    top: offsetPosition,
                                    behavior: 'smooth'
                                });
                            }
                        }
                    }
                    break;
                }
            }
        }

        // Seek
        function seek(e) {
            const rect = DOM.progressBar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            DOM.audio.currentTime = percent * DOM.audio.duration;
        }

        // Format Time
        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }

        // Theme Functions
        function initTheme() {
            // Shared 'theme' key takes priority; fall back to loaded settings, then default
            const shared = localStorage.getItem('theme');
            if (shared && ['light', 'dark'].includes(shared)) {
                applyTheme(shared);
            } else if (state.settings.theme) {
                applyTheme(state.settings.theme);
            } else {
                applyTheme('light');
            }
        }

        function applyTheme(theme) {
            state.settings.theme = theme;
            document.body.setAttribute('data-theme', theme === 'light' ? '' : theme);
            document.querySelectorAll('.theme-option').forEach(opt => {
                opt.classList.toggle('active', opt.dataset.theme === theme);
            });
            localStorage.setItem('theme', theme); // shared key
            saveSettings();
        }

        // Font Family
        function applyFontFamily(font) {
            state.settings.fontFamily = font;
            
            // Remove all font classes
            DOM.textContent.classList.remove('font-opensans', 'font-sourceserif', 'font-merriweather', 'font-lato');
            
            // Add selected font class
            DOM.textContent.classList.add(`font-${font}`);
            
            // Update UI
            document.querySelectorAll('.font-option').forEach(opt => {
                opt.classList.toggle('active', opt.dataset.font === font);
            });
            
            saveSettings();
        }

        // Font Size
        function applyFontSize(size) {
            state.settings.fontSize = size;
            
            // Remove all size classes
            DOM.textContent.classList.remove('size-xs', 'size-s', 'size-m', 'size-l', 
                                            'size-xl', 'size-xxl', 'size-xxxl');
            
            // Add selected size class
            DOM.textContent.classList.add(`size-${size}`);
            
            // Update UI
            document.querySelectorAll('.font-size-option').forEach(opt => {
                opt.classList.toggle('active', opt.dataset.size === size);
            });
            
            saveSettings();
        }

        // Verse Per Line
        function applyVersePerLine(enabled) {
            state.settings.versePerLine = enabled;
            DOM.textContent.classList.toggle('verse-per-line', enabled);
            DOM.versePerLineToggle.classList.toggle('active', enabled);
            saveSettings();
        }

        function toggleVersePerLine() {
            applyVersePerLine(!state.settings.versePerLine);
        }

        // Highlight Toggle
        function applyHighlight(enabled) {
            state.settings.highlightEnabled = enabled;
            DOM.highlightToggle.classList.toggle('active', enabled);
            
            if (!enabled) {
                document.querySelectorAll('.verse').forEach(el => {
                    el.classList.remove('highlight');
                });
            }
            
            saveSettings();
        }

        function toggleHighlight() {
            applyHighlight(!state.settings.highlightEnabled);
        }

        // Highlight Color
        function applyHighlightColor(color) {
            state.settings.highlightColor = color;
            document.body.setAttribute('data-highlight', color);
            document.querySelectorAll('.highlight-color-swatch').forEach(swatch => {
                swatch.classList.toggle('active', swatch.dataset.color === color);
            });
            saveSettings();
        }

        // Playback Speed
        function applyPlaybackSpeed(speed) {
            state.settings.playbackSpeed = speed;
            DOM.audio.playbackRate = speed;
            
            // Update slider and display
            DOM.speedSlider.value = speed;
            const speedText = speed === 1.0 ? '1.0x Normal' : `${speed.toFixed(2)}x`;
            DOM.speedValue.textContent = speedText;
            
            saveSettings();
        }

        // Auto-Advance
        function applyAutoAdvance(enabled) {
            state.settings.autoAdvance = enabled;
            DOM.autoAdvanceToggle.classList.toggle('active', enabled);
            saveSettings();
        }

        function toggleAutoAdvance() {
            applyAutoAdvance(!state.settings.autoAdvance);
        }

        // Apply All Settings
        function applyAllSettings() {
            applyTheme(state.settings.theme);
            applyFontFamily(state.settings.fontFamily);
            applyFontSize(state.settings.fontSize);
            applyVersePerLine(state.settings.versePerLine);
            applyHighlight(state.settings.highlightEnabled);
            applyHighlightColor(state.settings.highlightColor || 'crimson');
            applyPlaybackSpeed(state.settings.playbackSpeed);
            applyAutoAdvance(state.settings.autoAdvance);
        }

        // Reset Settings
        function resetSettings() {
            if (confirm('Reset all settings to default?')) {
                state.settings = {
                    theme: 'light',
                    fontFamily: 'opensans', // Changed default to Open Sans
                    fontSize: window.innerWidth <= 768 ? 's' : 'l', // Small on mobile, Large on desktop
                    versePerLine: true,
                    highlightEnabled: true,
                    highlightColor: 'crimson',
                    playbackSpeed: 1.0,
                    autoAdvance: true
                };
                applyAllSettings();
            }
        }

        // Settings Persistence
        function saveSettings() {
            localStorage.setItem('pbe-study-settings', JSON.stringify(state.settings));
        }

        function loadSettings() {
            let saved = localStorage.getItem('pbe-study-settings');
            // Migrate from old key
            if (!saved) {
                saved = localStorage.getItem('isaiah-settings');
                if (saved) {
                    localStorage.setItem('pbe-study-settings', saved);
                    localStorage.removeItem('isaiah-settings');
                }
            }
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    state.settings = { ...state.settings, ...parsed };
                    // Migrate old themes to light/dark
                    if (!['light', 'dark'].includes(state.settings.theme)) {
                        state.settings.theme = 'light';
                    }
                    // Migrate old fonts to available fonts
                    if (!['opensans', 'sourceserif', 'merriweather', 'lato'].includes(state.settings.fontFamily)) {
                        state.settings.fontFamily = 'opensans';
                    }
                } catch (e) {
                    console.error('Error loading settings:', e);
                }
            }
            applyAllSettings();
        }

        // Event Listeners
        function setupEventListeners() {
            // Audio events
            DOM.audio.addEventListener('play', () => {
                state.isPlaying = true;
                DOM.playBtn.textContent = '⏸';
                updateProgress(); // Start smooth animation
            });

            DOM.audio.addEventListener('pause', () => {
                state.isPlaying = false;
                DOM.playBtn.textContent = '▶';
                // Cancel animation frame on pause
                if (state.animationFrameId) {
                    cancelAnimationFrame(state.animationFrameId);
                    state.animationFrameId = null;
                }
            });

            DOM.audio.addEventListener('timeupdate', updateProgress);
            
            DOM.audio.addEventListener('loadedmetadata', () => {
                DOM.duration.textContent = formatTime(DOM.audio.duration);
            });

            DOM.audio.addEventListener('ended', () => {
                // Stop smooth animation
                if (state.animationFrameId) {
                    cancelAnimationFrame(state.animationFrameId);
                    state.animationFrameId = null;
                }
                
                if (state.settings.autoAdvance) {
                    nextChapter();
                }
            });

            // Control buttons
            DOM.playBtn.onclick = togglePlay;
            DOM.prevChapterBtn.onclick = previousChapter;
            DOM.nextChapterBtn.onclick = nextChapter;
            DOM.progressBar.onclick = seek;

            // Speed control
            DOM.speedBtn.onclick = () => {
                DOM.speedDropdown.classList.toggle('active');
            };

            DOM.speedOptions.forEach(option => {
                option.onclick = () => {
                    const speed = parseFloat(option.dataset.speed);
                    DOM.audio.playbackRate = speed;
                    state.settings.playbackSpeed = speed;
                    DOM.speedBtn.textContent = speed + '×';
                    
                    // Update active state
                    DOM.speedOptions.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    
                    DOM.speedDropdown.classList.remove('active');
                    saveSettings();
                };
            });

            // Close speed dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.speed-control')) {
                    DOM.speedDropdown.classList.remove('active');
                }
            });

            // Collapse/Expand player (mobile)
            DOM.collapseBtn.onclick = () => {
                DOM.playerWrapper.classList.toggle('collapsed');
                const isCollapsed = DOM.playerWrapper.classList.contains('collapsed');
                DOM.collapseIcon.textContent = isCollapsed ? '▲' : '▼';
                DOM.collapseBtn.classList.toggle('player-collapsed', isCollapsed);
            };

            // Header scroll behavior
            let lastScrollY = window.scrollY;
            let ticking = false;

            window.addEventListener('scroll', () => {
                lastScrollY = window.scrollY;

                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        handleScroll(lastScrollY);
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            // Chapter select button - opens modal
            DOM.chapterSelectBtn.onclick = () => {
                DOM.chapterPickerModal.classList.add('active');
                resetModalView(); // Always start at book selection
            };

            // Modal navigation
            DOM.backToBooks.onclick = () => {
                resetModalView();
            };

            DOM.closeChapterPicker.onclick = () => {
                DOM.chapterPickerModal.classList.remove('active');
                resetModalView();
            };

            DOM.closeChapterPickerX.onclick = () => {
                DOM.chapterPickerModal.classList.remove('active');
                resetModalView();
            };

            // Close modal on outside click
            DOM.chapterPickerModal.onclick = (e) => {
                if (e.target === DOM.chapterPickerModal) {
                    DOM.chapterPickerModal.classList.remove('active');
                    resetModalView();
                }
            };

            // Settings button
            DOM.settingsBtn.onclick = () => {
                DOM.settingsModal.classList.add('active');
            };
            DOM.closeSettingsModal.onclick = () => {
                DOM.settingsModal.classList.remove('active');
            };
            
            // Close modal on outside click
            DOM.settingsModal.onclick = (e) => {
                if (e.target === DOM.settingsModal) {
                    DOM.settingsModal.classList.remove('active');
                }
            };
            
            // Theme options
            document.querySelectorAll('.theme-option').forEach(opt => {
                opt.onclick = () => applyTheme(opt.dataset.theme);
            });

            // Highlight color swatches
            document.querySelectorAll('.highlight-color-swatch').forEach(swatch => {
                swatch.onclick = () => applyHighlightColor(swatch.dataset.color);
            });

            // Font family options
            document.querySelectorAll('.font-option').forEach(opt => {
                opt.onclick = () => applyFontFamily(opt.dataset.font);
            });

            // Font size options
            document.querySelectorAll('.font-size-option').forEach(opt => {
                opt.onclick = () => applyFontSize(opt.dataset.size);
            });

            // Toggles
            DOM.versePerLineToggle.onclick = toggleVersePerLine;
            DOM.highlightToggle.onclick = toggleHighlight;
            DOM.autoAdvanceToggle.onclick = toggleAutoAdvance;

            // Playback speed slider
            DOM.speedSlider.oninput = (e) => {
                applyPlaybackSpeed(parseFloat(e.target.value));
            };

            // Reset settings
            DOM.resetSettings.onclick = resetSettings;

            // Window resize - recalculate header height
            window.addEventListener('resize', calculateHeaderHeight);

            // Keyboard shortcuts
            document.addEventListener('keydown', handleRebindKey, true); // Capture phase for rebinding
            document.addEventListener('keydown', handleShortcuts);

            // Reset all shortcuts button
            const resetShortcutsBtn = document.getElementById('resetShortcutsBtn');
            if (resetShortcutsBtn) {
                resetShortcutsBtn.onclick = () => {
                    shortcuts = DEFAULT_SHORTCUTS.map(s => ({ ...s }));
                    localStorage.removeItem('pbe-study-shortcuts');
                    listeningForId = null;
                    renderShortcutList();
                };
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
