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
    <meta name="mobile-web-app-capable" content="yes">
    <title>Memorize - PBE Team Bold</title>

    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">

    <?php
    $version = '2.0.0';
    echo "<meta name='app-version' content='$version'>";

    function getAllAudioFiles($baseDir = 'audio') {
        $allFiles = [];
        if (!is_dir($baseDir)) return $allFiles;
        $flatFiles = glob($baseDir . '/*.m4a');
        if (!empty($flatFiles)) {
            sort($flatFiles);
            $bookFiles = [];
            foreach ($flatFiles as $file) {
                $filename = basename($file);
                preg_match('/\d+/', $filename, $matches);
                $chapter = isset($matches[0]) ? (int)$matches[0] : 0;
                $bookFiles[] = ['filename' => $filename, 'path' => $file, 'chapter' => $chapter];
            }
            if (!empty($bookFiles)) {
                preg_match('/^([A-Za-z]+)/', $bookFiles[0]['filename'], $m);
                $allFiles[strtolower($m[1] ?? 'unknown')] = $bookFiles;
            }
        }
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
                $bookFiles[] = ['filename' => $filename, 'path' => $file, 'chapter' => $chapter];
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
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<style>
:root{
    --bg:#FFFBF4;--bg2:#FDF3E0;--card:#FFFFFF;
    --tx:#1A214A;--tx2:#3A3F63;--muted:#7A7F9A;
    --accent:#D20702;--accent-h:#B30602;--accent-glow:rgba(210,7,2,.25);
    --border:#E8D8B6;--shadow:rgba(26,33,74,.10);--shadow-h:rgba(26,33,74,.16);
    --loop-bg:rgba(210,7,2,.12);--success:#27ae60;
}
[data-theme="dark"]{
    --bg:#101016;--bg2:#1A1A24;--card:#212130;
    --tx:#EAEAE8;--tx2:#B8B7B2;--muted:#75746E;
    --accent:#D4943A;--accent-h:#E4A84E;--accent-glow:rgba(212,148,58,.25);
    --border:#2C2C3A;--shadow:rgba(0,0,0,.45);--shadow-h:rgba(0,0,0,.60);
    --loop-bg:rgba(212,148,58,.15);--success:#2ecc71;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Open Sans',-apple-system,sans-serif;background:var(--bg);color:var(--tx);
     min-height:100vh;display:flex;flex-direction:column;transition:background .3s,color .3s}

/* ── Topbar ──────────────────── */
.topbar{background:var(--bg);border-bottom:1px solid var(--border);
        box-shadow:0 2px 8px var(--shadow);padding:10px 20px;
        display:flex;align-items:center;justify-content:space-between;gap:10px;z-index:100}
.topbar-left{display:flex;align-items:center;gap:12px}
.topbar-left a{display:flex}
.topbar .logo{height:46px}
.topbar-title{font-size:18px;font-weight:700}
.topbar-title small{display:block;font-size:11px;font-weight:400;color:var(--muted)}
.topbar-right{display:flex;gap:8px;align-items:center}

/* ── Buttons ─────────────────── */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;
     border-radius:8px;padding:8px 16px;font-size:14px;font-weight:600;
     cursor:pointer;font-family:inherit;transition:all .15s;border:2px solid var(--border);
     background:var(--card);color:var(--tx)}
.btn:hover{border-color:var(--accent);color:var(--accent)}
.btn-sm{padding:5px 12px;font-size:13px;border-radius:6px}
.btn-icon{width:40px;height:40px;padding:0;border-radius:50%;font-size:18px}
.btn-accent{background:var(--accent);border-color:var(--accent);color:#fff}
.btn-accent:hover{background:var(--accent-h);border-color:var(--accent-h);color:#fff}
.btn-danger{border-color:#c0392b;color:#c0392b}
.btn-danger:hover{background:#c0392b;color:#fff}

/* ── Main ────────────────────── */
.main{flex:1;max-width:960px;width:100%;margin:0 auto;padding:16px 20px 300px}

/* ── Card ────────────────────── */
.card{background:var(--card);border:1px solid var(--border);border-radius:12px;
      padding:16px 20px;box-shadow:0 2px 8px var(--shadow);margin-bottom:14px}
.card-header{display:flex;justify-content:space-between;align-items:center;
             margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--border);
             flex-wrap:wrap;gap:8px}
.card-title{font-size:16px;font-weight:700}

/* ── Text ────────────────────── */
.text-body{font-size:19px;line-height:1.8;color:var(--tx2);
           -webkit-text-size-adjust:100%;text-rendering:optimizeLegibility;
           -webkit-font-smoothing:antialiased}
.text-body .vnum{font-size:13px;vertical-align:super;color:var(--accent);
                 font-weight:700;margin-right:2px;cursor:pointer;user-select:none}
.text-body .verse-group{cursor:pointer;display:inline;transition:background .15s;
                        border-radius:3px;padding:1px 2px}
.text-body .verse-group:hover{background:var(--bg2)}
.text-body.vpl .verse-group{display:block;margin-bottom:6px;padding:4px 6px;border-radius:6px}
.text-body .w{transition:color .15s;cursor:pointer;border-radius:0}
.text-body .w:hover{background:var(--bg2);border-radius:3px}
/* Bold highlight (default) */
.text-body .w.in-loop{background:var(--accent);color:#fff;padding:2px 0}
[data-theme="dark"] .text-body .w.in-loop{background:var(--accent);color:#fff}
/* Soft highlight mode */
.text-body.hl-soft .w.in-loop{background:#f0a19d;color:var(--tx2)}
[data-theme="dark"] .text-body.hl-soft .w.in-loop{background:#5c3d1b;color:var(--tx2)}
.text-body .w.sel-anchor{background:var(--accent);color:#fff;border-radius:3px;padding:2px 4px}

/* ── Bookmarks ───────────────── */
.bm-item{display:flex;align-items:center;gap:12px;padding:10px 14px;
         background:var(--bg2);border-radius:8px;cursor:pointer;transition:all .15s}
.bm-item:hover{background:var(--bg);box-shadow:0 2px 6px var(--shadow)}
.bm-item+.bm-item{margin-top:8px}
.bm-text{flex:1;min-width:0}
.bm-name{font-size:14px;font-weight:600;color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.bm-meta{font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.bm-loops{font-size:12px;font-weight:700;color:var(--accent);min-width:50px;text-align:right;white-space:nowrap}
.bm-del{background:none;border:none;font-size:16px;cursor:pointer;color:var(--muted);
        padding:4px;border-radius:4px;transition:color .15s}
.bm-del:hover{color:#c0392b}
.no-bm{text-align:center;padding:20px;color:var(--muted);font-size:14px}

/* ── Dock (fixed bottom) ─────── */
.dock{position:fixed;bottom:0;left:0;right:0;z-index:999}

/* Expandable panel */
.dock-expand{max-height:0;overflow:hidden;transition:max-height .3s ease;
             background:var(--card);border-top:1px solid var(--border);position:relative}
.dock-expand.open{max-height:260px}
.dock-expand-inner{max-width:960px;margin:0 auto;padding:12px 20px 8px}
.dock-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px}
.dock-row:last-child{margin-bottom:0}
.dock-spacer{flex:1}

.ab-group{display:flex;align-items:center;gap:5px}
.ab-tag{font-size:12px;font-weight:800;color:#fff;background:var(--accent);
        border-radius:5px;padding:2px 8px;letter-spacing:.04em}
.ab-tag.inactive{background:var(--muted);opacity:.5}
.ab-time{font-size:14px;font-weight:700;color:var(--tx);min-width:42px;
         font-variant-numeric:tabular-nums}
.mini-btn{width:28px;height:28px;border-radius:50%;border:1.5px solid var(--border);
          background:var(--bg2);color:var(--tx);font-size:15px;font-weight:700;
          cursor:pointer;display:flex;align-items:center;justify-content:center;
          transition:all .12s;font-family:inherit;padding:0}
.mini-btn:hover{background:var(--accent);color:#fff;border-color:var(--accent)}

/* Loop ring */
.loop-ring-wrap{position:relative;width:54px;height:54px;flex-shrink:0}
.loop-ring{transform:rotate(-90deg)}
.loop-ring-bg{fill:none;stroke:var(--bg2);stroke-width:5}
.loop-ring-fg{fill:none;stroke:var(--accent);stroke-width:5;stroke-linecap:round;
              transition:stroke-dashoffset .4s ease}
.loop-ring-text{position:absolute;inset:0;display:flex;flex-direction:column;
                align-items:center;justify-content:center;line-height:1}
.loop-ring-count{font-size:15px;font-weight:800;color:var(--accent)}
.loop-ring-label{font-size:8px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em}

/* Section duration chip */
.section-chip{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;
              background:var(--loop-bg);border-radius:12px;font-size:12px;font-weight:700;
              color:var(--accent)}

/* Slider rows */
.dock-label{font-size:12px;font-weight:600;color:var(--muted);min-width:42px;white-space:nowrap}
.dock-slider{flex:1;height:5px;border-radius:3px;background:var(--bg2);
             outline:none;-webkit-appearance:none;min-width:80px}
.dock-slider::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;
                                    border-radius:50%;background:var(--accent);cursor:pointer;
                                    box-shadow:0 2px 6px var(--accent-glow)}
.dock-slider::-moz-range-thumb{width:20px;height:20px;border-radius:50%;
                                background:var(--accent);cursor:pointer;border:none}
.dock-val{font-size:14px;font-weight:700;color:var(--accent);min-width:48px;
          text-align:right;font-variant-numeric:tabular-nums}

/* Config inputs */
.cfg-group{display:flex;align-items:center;gap:5px}
.cfg-group label{font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap}
.cfg-input{width:50px;text-align:center;border:1.5px solid var(--border);border-radius:6px;
           padding:5px;font-size:13px;font-weight:700;font-family:inherit;
           background:var(--bg2);color:var(--tx);transition:border-color .15s}
.cfg-input:focus{border-color:var(--accent);outline:none}

/* Toggle chevron */
.dock-toggle{position:absolute;top:-26px;left:50%;transform:translateX(-50%);
             width:52px;height:26px;border-radius:10px 10px 0 0;
             background:var(--card);border:1px solid var(--border);border-bottom:none;
             cursor:pointer;font-size:12px;color:var(--muted);display:none;
             align-items:center;justify-content:center;transition:all .15s}
.dock-toggle:hover{color:var(--accent)}
.dock-toggle.vis{display:flex}

/* Transport */
.dock-main{background:var(--card);border-top:1px solid var(--border);
           box-shadow:0 -2px 12px var(--shadow)}
[data-theme="dark"] .dock-main{background:#1A1A24;border-color:#2C2C3A}
[data-theme="dark"] .dock-expand{background:#1A1A24;border-color:#2C2C3A}

.dock-transport{display:flex;align-items:center;justify-content:center;
                gap:8px;padding:8px 20px 4px;max-width:960px;margin:0 auto}
.ctrl{background:var(--bg2);border:1px solid var(--border);border-radius:50%;
      width:38px;height:38px;display:flex;align-items:center;justify-content:center;
      cursor:pointer;font-size:14px;color:var(--tx);transition:all .15s;font-family:inherit;font-weight:700}
.ctrl:hover{transform:scale(1.08);box-shadow:0 2px 8px var(--shadow-h)}
.ctrl.play{width:48px;height:48px;background:var(--accent);border-color:var(--accent);
           color:#fff;font-size:18px;font-weight:400}
.ctrl.play:hover{background:var(--accent-h)}
.ctrl.ab{font-size:15px;font-weight:800;transition:all .2s}
.ctrl.ab.set{background:var(--accent);border-color:var(--accent);color:#fff;
             box-shadow:0 0 12px var(--accent-glow)}
.ctrl.ab.set:hover{background:var(--accent-h)}
@keyframes pulse-ring{0%{box-shadow:0 0 0 0 var(--accent-glow)}70%{box-shadow:0 0 0 10px transparent}100%{box-shadow:0 0 0 0 transparent}}
.ctrl.ab.looping{animation:pulse-ring 1.5s infinite}
.speed-wrap{position:relative}
.speed-pill{font-size:13px;font-weight:700;color:var(--accent);background:var(--bg2);
            border:1px solid var(--border);border-radius:16px;padding:4px 12px;
            font-variant-numeric:tabular-nums;cursor:pointer;white-space:nowrap;
            font-family:inherit;transition:all .15s}
.speed-pill:hover{border-color:var(--accent);background:var(--accent);color:#fff}
.speed-menu{display:none;position:absolute;bottom:calc(100% + 8px);left:50%;transform:translateX(-50%);
            background:var(--card);border:1px solid var(--border);border-radius:12px;
            padding:6px;box-shadow:0 4px 16px var(--shadow-h);z-index:1001;
            min-width:80px;max-height:240px;overflow-y:auto}
.speed-menu.open{display:flex;flex-direction:column;gap:2px}
.speed-opt{padding:7px 14px;border-radius:6px;font-size:13px;font-weight:600;
           text-align:center;cursor:pointer;transition:all .12s;color:var(--tx2);font-family:inherit;
           border:none;background:none}
.speed-opt:hover{background:var(--bg2)}
.speed-opt.active{background:var(--accent);color:#fff}
[data-theme="dark"] .ctrl{background:#1E1E2A;border-color:#363646}
[data-theme="dark"] .ctrl.play{background:var(--accent);border-color:var(--accent)}
[data-theme="dark"] .ctrl.ab.set{background:var(--accent);border-color:var(--accent)}

/* Progress */
.progress-wrap{position:relative;height:22px;background:rgba(0,0,0,.08);cursor:pointer;overflow:hidden}
[data-theme="dark"] .progress-wrap{background:rgba(0,0,0,.4)}
.progress-loop{position:absolute;top:0;height:100%;background:var(--loop-bg);pointer-events:none}
.progress-fill{position:absolute;top:0;left:0;height:100%;background:var(--accent);
               width:0%;pointer-events:none;transition:width .15s linear}
/* Dark time text (always visible, sits behind) */
.progress-time{position:absolute;inset:0;display:flex;justify-content:space-between;
               align-items:center;padding:0 14px;font-size:11px;font-weight:700;
               pointer-events:none;z-index:4;color:var(--tx)}
/* White time text (clipped to fill width, appears over red bar) */
.progress-time-light{position:absolute;inset:0;display:flex;justify-content:space-between;
               align-items:center;padding:0 14px;font-size:11px;font-weight:700;
               pointer-events:none;z-index:5;color:#fff;
               clip-path:inset(0 100% 0 0)}
/* A/B markers on progress bar */
.progress-marker{position:absolute;top:0;width:3px;height:100%;z-index:6;pointer-events:none;border-radius:1px}
.progress-marker.marker-a{background:var(--accent)}
.progress-marker.marker-b{background:var(--accent)}

/* ── Toast ───────────────────── */
.toast-container{position:fixed;top:80px;right:20px;z-index:3000;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.toast{padding:10px 18px;border-radius:10px;font-size:14px;font-weight:600;color:#fff;
       background:var(--accent);box-shadow:0 4px 16px var(--shadow-h);
       transform:translateX(120%);transition:transform .3s ease,opacity .3s ease;opacity:0;pointer-events:auto}
.toast.show{transform:translateX(0);opacity:1}
.toast.success{background:var(--success)}

/* ── Keyboard Help Overlay ───── */
.kb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);
            z-index:5000;align-items:center;justify-content:center}
.kb-overlay.active{display:flex}
.kb-card{background:var(--card);border-radius:16px;padding:28px;max-width:480px;width:90%;
         box-shadow:0 8px 32px var(--shadow-h);max-height:80vh;overflow-y:auto}
.kb-card h3{font-size:20px;font-weight:700;margin-bottom:16px;color:var(--tx);
            display:flex;justify-content:space-between;align-items:center}
.kb-card .close-x{cursor:pointer;font-size:18px;color:var(--muted)}
.kb-grid{display:grid;grid-template-columns:auto 1fr;gap:6px 16px;align-items:center}
.kb-key{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:28px;
        padding:0 8px;background:var(--bg2);border:2px solid var(--border);border-bottom-width:3px;
        border-radius:6px;font-size:12px;font-weight:700;color:var(--tx);font-family:inherit}
.kb-desc{font-size:13px;color:var(--tx2)}
.kb-section{grid-column:1/-1;font-size:11px;font-weight:700;color:var(--muted);
            text-transform:uppercase;letter-spacing:.05em;padding-top:10px}

/* ── Completion overlay ──────── */
.complete-flash{position:fixed;inset:0;background:rgba(39,174,96,.12);z-index:4000;
                pointer-events:none;opacity:0;transition:opacity .5s ease}
.complete-flash.show{opacity:1}
.complete-msg{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%) scale(.8);
              z-index:4001;background:var(--card);border-radius:16px;padding:30px 40px;
              text-align:center;box-shadow:0 8px 40px var(--shadow-h);
              opacity:0;transition:all .4s ease;pointer-events:none}
.complete-msg.show{opacity:1;transform:translate(-50%,-50%) scale(1)}
.complete-msg .check{font-size:48px;margin-bottom:8px}
.complete-msg .msg{font-size:18px;font-weight:700;color:var(--tx)}
.complete-msg .sub{font-size:14px;color:var(--muted);margin-top:4px}

/* ── Chapter Picker ──────────── */
.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:2000;
         align-items:center;justify-content:center}
.overlay.active{display:flex}
.modal{background:var(--card);border-radius:16px;padding:24px;max-width:600px;
       width:90%;max-height:80vh;overflow-y:auto;box-shadow:0 8px 32px var(--shadow-h)}
.modal h3{font-size:22px;font-weight:700;margin-bottom:16px;display:flex;
          align-items:center;justify-content:space-between}
.modal .close-x{cursor:pointer;font-size:18px;color:var(--muted)}
.year-pills{display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap}
.pill{padding:6px 16px;border-radius:20px;border:2px solid var(--border);background:var(--bg2);
      font-size:13px;font-weight:600;color:var(--tx2);cursor:pointer;transition:all .15s;font-family:inherit}
.pill:hover{border-color:var(--accent);color:var(--accent)}
.pill.active{background:var(--accent);border-color:var(--accent);color:#fff}
.book-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;margin-bottom:14px}
.book-card{padding:14px;border-radius:10px;border:2px solid var(--border);background:var(--bg2);
           text-align:center;cursor:pointer;transition:all .15s;font-family:inherit}
.book-card:hover{border-color:var(--accent);transform:translateY(-2px)}
.book-card .bk-name{font-size:16px;font-weight:700;color:var(--tx)}
.book-card .bk-info{font-size:12px;color:var(--muted);margin-top:3px}
.ch-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(56px,1fr));gap:8px}
.ch-btn{padding:12px 6px;border-radius:8px;border:2px solid var(--border);background:var(--bg2);
        text-align:center;font-size:15px;font-weight:600;color:var(--tx2);
        cursor:pointer;transition:all .15s;font-family:inherit}
.ch-btn:hover{border-color:var(--accent);transform:translateY(-1px)}
.ch-btn.active{background:var(--accent);border-color:var(--accent);color:#fff}
.ch-btn.na{opacity:.35;cursor:default}
.back-link{background:none;border:none;font-size:14px;color:var(--accent);font-weight:600;
           cursor:pointer;font-family:inherit;padding:0}
.back-link:hover{text-decoration:underline}

/* ── Responsive ──────────────── */
@media(max-width:768px){
    .topbar-title{font-size:15px}
    .topbar-title small{display:none}
    .topbar .logo{height:38px}
    .main{padding:10px 10px 300px}
    .card{padding:12px 14px;border-radius:10px}
    .text-body{font-size:17px;line-height:1.7}
    .dock-transport{gap:6px;padding:6px 12px 3px}
    .ctrl{width:34px;height:34px;font-size:13px}
    .ctrl.play{width:42px;height:42px;font-size:16px}
    .dock-expand-inner{padding:10px 12px 6px}
    .loop-ring-wrap{width:44px;height:44px}
    .loop-ring-count{font-size:12px}
    .toast-container{top:60px;right:10px;left:10px}
    .toast{font-size:13px;padding:8px 14px}
}
</style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="/"><img src="pbe_team_bold_logo.png" alt="Logo" class="logo"></a>
            <div class="topbar-title">Memorize<small id="headerSub">PBE Team Bold</small></div>
        </div>
        <div class="topbar-right">
            <button class="btn" id="chapterBtn">Select Chapter</button>
            <button class="btn btn-icon" id="themeBtn" title="Toggle Theme">🌙</button>
            <button class="btn btn-icon" id="kbBtn" title="Keyboard Shortcuts">?</button>
        </div>
    </div>

    <div class="main">
        <div class="card">
            <div class="card-header">
                <span class="card-title" id="chapterLabel">Select a chapter</span>
                <div style="display:flex;gap:6px">
                    <button class="btn btn-sm" id="vplToggle">¶ Verse/Line</button>
                    <button class="btn btn-sm" id="hlToggle">◐ Soft Highlight</button>
                </div>
            </div>
            <div class="text-body" id="textBody">
                <p style="text-align:center;padding:30px;color:var(--muted)">Choose a book and chapter above</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">⭐ Saved Sections</span></div>
            <div id="bmList"><div class="no-bm">No saved sections yet</div></div>
        </div>
    </div>

    <!-- ── Dock ────────────────── -->
    <div class="dock" id="dock">
        <div class="dock-expand" id="dockExpand">
            <div class="dock-expand-inner">
                <div class="dock-row">
                    <div class="ab-group">
                        <span class="ab-tag" id="aTag">A</span>
                        <span class="ab-time" id="aTime">—</span>
                        <button class="mini-btn" id="ftAm" title="−0.5s">−</button>
                        <button class="mini-btn" id="ftAp" title="+0.5s">+</button>
                    </div>
                    <div class="ab-group">
                        <span class="ab-tag" id="bTag">B</span>
                        <span class="ab-time" id="bTime">—</span>
                        <button class="mini-btn" id="ftBm" title="−0.5s">−</button>
                        <button class="mini-btn" id="ftBp" title="+0.5s">+</button>
                    </div>
                    <span class="section-chip" id="sectionDur" style="display:none">0s</span>
                    <div class="dock-spacer"></div>
                    <div class="loop-ring-wrap" id="loopRingWrap">
                        <svg class="loop-ring" viewBox="0 0 54 54" width="100%" height="100%">
                            <circle class="loop-ring-bg" cx="27" cy="27" r="22"/>
                            <circle class="loop-ring-fg" id="loopRingFg" cx="27" cy="27" r="22"
                                    stroke-dasharray="138.23" stroke-dashoffset="138.23"/>
                        </svg>
                        <div class="loop-ring-text">
                            <span class="loop-ring-count" id="loopCountDisp">0</span>
                            <span class="loop-ring-label">loops</span>
                        </div>
                    </div>
                </div>
                <div class="dock-row">
                    <label class="dock-label">Speed</label>
                    <input type="range" class="dock-slider" id="speedSlider" min="0.5" max="2.5" step="0.05" value="1">
                    <span class="dock-val" id="speedDisp">1.00×</span>
                </div>
                <div class="dock-row">
                    <div class="cfg-group">
                        <label>Loop</label>
                        <input type="number" class="cfg-input" id="loopTarget" min="1" max="999" value="10">
                        <button class="mini-btn" id="loopInfBtn" title="∞">∞</button>
                    </div>
                    <div class="dock-spacer"></div>
                    <button class="btn btn-sm" id="saveBmBtn">⭐ Save</button>
                    <button class="btn btn-sm btn-danger" id="clearBtn">✕ Clear</button>
                </div>
            </div>
            <button class="dock-toggle" id="dockToggle"><span id="dockToggleIcon">▼</span></button>
        </div>

        <div class="dock-main">
            <div class="dock-transport">
                <button class="ctrl" id="prevBtn" title="Prev Chapter ←">⏮</button>
                <button class="ctrl ab" id="setABtn" title="Set A">A</button>
                <button class="ctrl play" id="playBtn" title="Play/Pause">▶</button>
                <button class="ctrl ab" id="setBBtn" title="Set B">B</button>
                <button class="ctrl" id="nextBtn" title="Next Chapter →">⏭</button>
                <div class="speed-wrap">
                    <button class="speed-pill" id="speedPill">1.00×</button>
                    <div class="speed-menu" id="speedMenu"></div>
                </div>
            </div>
            <div class="progress-wrap" id="progressWrap">
                <div class="progress-loop" id="progressLoop" style="display:none"></div>
                <div class="progress-fill" id="progressFill"></div>
                <div class="progress-marker marker-a" id="markerA" style="display:none"></div>
                <div class="progress-marker marker-b" id="markerB" style="display:none"></div>
                <div class="progress-time">
                    <span id="curTime">0:00</span>
                    <span id="durTime">0:00</span>
                </div>
                <div class="progress-time-light" id="progressTimeLight">
                    <span id="curTimeL">0:00</span>
                    <span id="durTimeL">0:00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Toasts -->
    <div class="toast-container" id="toastBox"></div>

    <!-- Keyboard help -->
    <div class="kb-overlay" id="kbOverlay">
        <div class="kb-card">
            <h3>⌨️ Keyboard Shortcuts <span class="close-x" id="kbClose">✕</span></h3>
            <div class="kb-grid">
                <div class="kb-section">Playback</div>
                <span class="kb-key">Space</span><span class="kb-desc">Play / Pause</span>
                <span class="kb-key">J</span><span class="kb-desc">Seek back 5s</span>
                <span class="kb-key">L</span><span class="kb-desc">Seek forward 5s</span>
                <span class="kb-key">,</span><span class="kb-desc">Slow down</span>
                <span class="kb-key">.</span><span class="kb-desc">Speed up</span>
                <div class="kb-section">Loop</div>
                <span class="kb-key">A</span><span class="kb-desc">Set point A</span>
                <span class="kb-key">B</span><span class="kb-desc">Set point B</span>
                <span class="kb-key">Esc</span><span class="kb-desc">Clear loop</span>
                <div class="kb-section">Navigation</div>
                <span class="kb-key">←</span><span class="kb-desc">Previous chapter</span>
                <span class="kb-key">→</span><span class="kb-desc">Next chapter</span>
                <span class="kb-key">↑</span><span class="kb-desc">Previous verse</span>
                <span class="kb-key">↓</span><span class="kb-desc">Next verse</span>
                <span class="kb-key">C</span><span class="kb-desc">Chapter picker</span>
                <div class="kb-section">Toggles</div>
                <span class="kb-key">V</span><span class="kb-desc">Verse per line</span>
                <span class="kb-key">D</span><span class="kb-desc">Dark / Light mode</span>
                <span class="kb-key">?</span><span class="kb-desc">This help screen</span>
            </div>
        </div>
    </div>

    <!-- Completion overlay -->
    <div class="complete-flash" id="completeFlash"></div>
    <div class="complete-msg" id="completeMsg">
        <div class="check">✅</div>
        <div class="msg">Section Complete!</div>
        <div class="sub" id="completeSub">10 loops finished</div>
    </div>

    <!-- Chapter Picker -->
    <div class="overlay" id="pickerOverlay">
        <div class="modal">
            <h3><span id="pickerTitle">📖 Select Book</span><span class="close-x" id="pickerClose">✕</span></h3>
            <div id="pickerBookView">
                <div class="year-pills" id="yearPills"></div>
                <div class="book-grid" id="bookGrid"></div>
            </div>
            <div id="pickerChView" style="display:none">
                <button class="back-link" id="pickerBack">← Back to books</button>
                <div class="ch-grid" id="chGrid" style="margin-top:12px"></div>
            </div>
        </div>
    </div>

    <audio id="audio" preload="metadata"></audio>

<script>
// ═══ Config ═══
const PBE_YEARS=[
    {id:'2025-2026',label:'PBE 2025–2026',books:[{id:'isaiah',name:'Isaiah',chapters:33}]},
    {id:'2026-2027',label:'PBE 2026–2027',books:[
        {id:'mark',name:'Mark',chapters:16},{id:'1peter',name:'1 Peter',chapters:5},
        {id:'2peter',name:'2 Peter',chapters:3},{id:'1john',name:'1 John',chapters:5},
        {id:'2john',name:'2 John',chapters:1},{id:'3john',name:'3 John',chapters:1}]}
];
const ALL_BOOKS=PBE_YEARS.flatMap(y=>y.books);

// ═══ State ═══
const au=document.getElementById('audio');
const S={year:PBE_YEARS[0].id,book:PBE_YEARS[0].books[0].id,chapter:null,
         verses:[],words:[],
         aTime:null,bTime:null,looping:false,loopCount:0,loopTarget:10,
         speed:1.0,theme:'light',playing:false,vpl:false,hlSoft:false,lastActiveVerse:-1,
         // Word-tap selection: 'idle' | 'waitA' | 'waitB'
         tapMode:'idle',tapWordA:null,tapWordB:null,
         loopRafId:null}; // requestAnimationFrame for tight loop check

// ═══ DOM ═══
const $=id=>document.getElementById(id);
const D={chapterBtn:$('chapterBtn'),themeBtn:$('themeBtn'),headerSub:$('headerSub'),
    chapterLabel:$('chapterLabel'),vplToggle:$('vplToggle'),hlToggle:$('hlToggle'),
    setABtn:$('setABtn'),setBBtn:$('setBBtn'),clearBtn:$('clearBtn'),
    dockExpand:$('dockExpand'),dockToggle:$('dockToggle'),dockToggleIcon:$('dockToggleIcon'),
    aTag:$('aTag'),bTag:$('bTag'),aTime:$('aTime'),bTime:$('bTime'),
    ftAm:$('ftAm'),ftAp:$('ftAp'),ftBm:$('ftBm'),ftBp:$('ftBp'),
    sectionDur:$('sectionDur'),loopRingFg:$('loopRingFg'),
    loopCountDisp:$('loopCountDisp'),
    speedSlider:$('speedSlider'),speedDisp:$('speedDisp'),speedPill:$('speedPill'),speedMenu:$('speedMenu'),
    loopTarget:$('loopTarget'),loopInfBtn:$('loopInfBtn'),
    saveBmBtn:$('saveBmBtn'),
    textBody:$('textBody'),bmList:$('bmList'),
    playBtn:$('playBtn'),prevBtn:$('prevBtn'),nextBtn:$('nextBtn'),
    progressWrap:$('progressWrap'),progressFill:$('progressFill'),
    progressLoop:$('progressLoop'),progressTimeLight:$('progressTimeLight'),
    curTime:$('curTime'),durTime:$('durTime'),curTimeL:$('curTimeL'),durTimeL:$('durTimeL'),
    markerA:$('markerA'),markerB:$('markerB'),
    toastBox:$('toastBox'),
    kbOverlay:$('kbOverlay'),kbBtn:$('kbBtn'),kbClose:$('kbClose'),
    completeFlash:$('completeFlash'),completeMsg:$('completeMsg'),completeSub:$('completeSub'),
    pickerOverlay:$('pickerOverlay'),pickerTitle:$('pickerTitle'),
    pickerClose:$('pickerClose'),pickerBack:$('pickerBack'),
    pickerBookView:$('pickerBookView'),pickerChView:$('pickerChView'),
    yearPills:$('yearPills'),bookGrid:$('bookGrid'),chGrid:$('chGrid')};

// ═══ Helpers ═══
function fmt(s){if(s==null||isNaN(s))return'—';return Math.floor(s/60)+':'+String(Math.floor(s%60)).padStart(2,'0');}
function clamp(v,lo,hi){return Math.max(lo,Math.min(hi,v));}
const RING_CIRC=138.23;

// ═══ Toast ═══
function toast(msg,type=''){
    const el=document.createElement('div');el.className='toast'+(type?' '+type:'');el.textContent=msg;
    D.toastBox.appendChild(el);
    requestAnimationFrame(()=>el.classList.add('show'));
    setTimeout(()=>{el.classList.remove('show');setTimeout(()=>el.remove(),300);},2200);
}

// ═══ Theme ═══
function setTheme(t){S.theme=t;document.documentElement.setAttribute('data-theme',t);
    D.themeBtn.textContent=t==='dark'?'☀️':'🌙';localStorage.setItem('pbe-mem-theme',t);}

// ═══ Picker ═══
function openPicker(){D.pickerOverlay.classList.add('active');renderBooks();}
function closePicker(){D.pickerOverlay.classList.remove('active');}
function renderBooks(){
    D.pickerBookView.style.display='block';D.pickerChView.style.display='none';
    D.pickerTitle.textContent='📖 Select Book';
    D.yearPills.innerHTML='';
    PBE_YEARS.forEach(y=>{const p=document.createElement('button');p.className='pill'+(y.id===S.year?' active':'');
        p.textContent=y.label;p.onclick=()=>{S.year=y.id;renderBooks();};D.yearPills.appendChild(p);});
    const yr=PBE_YEARS.find(y=>y.id===S.year);D.bookGrid.innerHTML='';
    yr.books.forEach(b=>{const has=AUDIO_MAP[b.id]&&AUDIO_MAP[b.id].length>0;
        const el=document.createElement('div');el.className='book-card';if(!has)el.style.opacity='.45';
        el.innerHTML=`<div class="bk-name">${b.name}</div><div class="bk-info">${b.chapters} ch${b.chapters>1?'s':''}${has?'':' · soon'}</div>`;
        el.onclick=()=>renderChapters(b);D.bookGrid.appendChild(el);});
}
function renderChapters(book){
    S.book=book.id;D.pickerBookView.style.display='none';D.pickerChView.style.display='block';
    D.pickerTitle.textContent='📖 '+book.name;
    const avail=(AUDIO_MAP[book.id]||[]).map(f=>f.chapter);D.chGrid.innerHTML='';
    for(let i=1;i<=book.chapters;i++){const btn=document.createElement('button');const has=avail.includes(i);
        btn.className='ch-btn'+(has?'':' na')+(i===S.chapter&&book.id===S.book?' active':'');
        btn.textContent=i;btn.onclick=()=>{if(has){loadChapter(i);closePicker();}};D.chGrid.appendChild(btn);}
}

// ═══ Load Chapter ═══
async function loadChapter(num){
    const ba=AUDIO_MAP[S.book]||[];const f=ba.find(x=>x.chapter===num);if(!f)return;
    S.chapter=num;clearLoop();S.lastActiveVerse=-1;
    const bk=ALL_BOOKS.find(b=>b.id===S.book);const label=(bk?bk.name:S.book)+' '+num;
    D.chapterBtn.textContent=label;D.chapterLabel.textContent=label;
    const yr=PBE_YEARS.find(y=>y.id===S.year);D.headerSub.textContent=yr?yr.label:'';
    au.src=f.path;au.load();await loadText(num);
    localStorage.setItem('pbe-mem-last',JSON.stringify({year:S.year,book:S.book,chapter:num}));
}
async function loadText(num){
    const id=S.book;const sub=`text/${id}/${id}_${String(num).padStart(2,'0')}.csv`;
    const flat=`text/${id}_${String(num).padStart(2,'0')}.csv`;
    let resp=await fetch(sub);if(!resp.ok)resp=await fetch(flat);
    if(!resp.ok){D.textBody.innerHTML='<p style="text-align:center;padding:30px;color:var(--muted)">Text not found</p>';return;}
    const csv=await resp.text();const lines=csv.split(/\r?\n/);S.verses=[];
    for(let i=1;i<lines.length;i++){const l=lines[i].trim();if(!l)continue;
        let m=l.match(/^(\d+),([\d.]+),([\d.]+),"(.+)"$/)||l.match(/^(\d+),([\d.]+),([\d.]+),(.+)$/);
        if(m)S.verses.push({verse:+m[1],start:+m[2],end:+m[3],text:m[4].replace(/""/g,'"')});}
    renderWords();
}

// ═══ Render Words ═══
function renderWords(){
    D.textBody.innerHTML='';S.words=[];let gi=0;
    S.verses.forEach((v,vi)=>{
        const group=document.createElement('span');group.className='verse-group';group.dataset.vi=vi;
        // Verse group click: seek audio (only if no word selection in progress)
        group.addEventListener('click',e=>{
            // If a word span handled it, don't also seek
            if(e.target.classList.contains('w')||e.target.classList.contains('vnum'))return;
            au.currentTime=v.start;if(!S.playing)au.play();
        });
        const vn=document.createElement('sup');vn.className='vnum';vn.textContent=v.verse;
        vn.addEventListener('click',e=>{e.stopPropagation();au.currentTime=v.start;if(!S.playing)au.play();});
        group.appendChild(vn);
        v.text.split(/\s+/).forEach((word,wi,arr)=>{
            const span=document.createElement('span');span.className='w';
            // Include trailing space inside the span so background covers gaps
            span.textContent=word+' ';
            span.dataset.gi=gi;span.dataset.vi=vi;
            const est=v.start+(arr.length>1?wi/(arr.length-1):0)*(v.end-v.start);
            span.dataset.est=est;
            const idx=gi;
            span.addEventListener('click',e=>{e.stopPropagation();handleWordClick(idx);});
            group.appendChild(span);S.words.push({el:span,vi,text:word,est:est});
            gi++;
        });
        D.textBody.appendChild(group);
    });
    D.textBody.classList.toggle('vpl',S.vpl);
}

// ═══ Word-Tap Selection ═══
function handleWordClick(gi){
    const w=S.words[gi];

    if(S.tapMode==='idle'){
        if(S.looping){
            // Already looping — confirm new selection
            if(!confirm('Start a new selection?'))return;
            clearLoop();
        }
        // Reset speed to 1x for selection (easier to hear start/end points)
        setSpeed(1.0);
        // Begin new selection: seek to verse start, play, wait for A click
        const v=S.verses[w.vi];
        au.currentTime=v.start;
        if(!S.playing)au.play();
        S.tapMode='waitA';
        toast('Listening — tap a word to mark where to start');
    }
    else if(S.tapMode==='waitA'){
        // Set A at current audio position
        S.aTime=au.currentTime;
        S.tapWordA=gi;
        D.aTime.textContent=fmt(S.aTime);
        D.setABtn.classList.add('set');D.aTag.classList.remove('inactive');
        // Highlight just the anchor word
        w.el.classList.add('sel-anchor');
        S.tapMode='waitB';
        showDock();updateMarkers();
        toast('Start set at '+fmt(S.aTime)+' — now tap the end word');
    }
    else if(S.tapMode==='waitB'){
        if(gi===S.tapWordA){toast('Tap a different word');return;}
        // Set B at current audio position
        S.bTime=au.currentTime;
        S.tapWordB=gi;
        // Ensure A < B (swap times AND word indices if needed)
        if(S.bTime<S.aTime){
            [S.aTime,S.bTime]=[S.bTime,S.aTime];
            [S.tapWordA,S.tapWordB]=[S.tapWordB,S.tapWordA];
        }
        D.aTime.textContent=fmt(S.aTime);D.bTime.textContent=fmt(S.bTime);
        D.setBBtn.classList.add('set');D.bTag.classList.remove('inactive');

        // Highlight word range
        highlightRange();

        // Start loop
        S.looping=true;S.loopCount=0;D.loopCountDisp.textContent='0';updateLoopRing();
        updateMarkers();updateSectionDur();updateProgressLoop();
        D.setABtn.classList.add('looping');D.setBBtn.classList.add('looping');
        au.currentTime=S.aTime;if(!S.playing)au.play();
        startLoopTick();

        S.tapMode='idle';
        const count=Math.abs(S.tapWordB-S.tapWordA)+1;
        toast('Loop: '+count+' words · '+fmt(S.bTime-S.aTime)+' section');
    }
}

function highlightRange(){
    S.words.forEach(w=>w.el.classList.remove('in-loop','sel-anchor'));
    if(S.tapWordA===null||S.tapWordB===null)return;
    const lo=Math.min(S.tapWordA,S.tapWordB);
    const hi=Math.max(S.tapWordA,S.tapWordB);
    for(let i=lo;i<=hi;i++) S.words[i].el.classList.add('in-loop');
}

function clearWordTap(){
    S.tapMode='idle';S.tapWordA=null;S.tapWordB=null;
    S.words.forEach(w=>w.el.classList.remove('in-loop','sel-anchor'));
}


function setA(){
    if(!au.duration)return;clearWordTap();S.aTime=au.currentTime;D.aTime.textContent=fmt(S.aTime);
    D.setABtn.classList.add('set');D.aTag.classList.remove('inactive');
    if(S.bTime!==null&&S.aTime>=S.bTime){S.bTime=null;D.bTime.textContent='—';D.setBBtn.classList.remove('set');D.bTag.classList.add('inactive');}
    showDock();updateMarkers();updateSectionDur();
    if(S.bTime!==null)startLoop();else{highlightLoop();toast('Point A set at '+fmt(S.aTime));}
}
function setB(){
    if(!au.duration)return;clearWordTap();S.bTime=au.currentTime;
    if(S.aTime!==null&&S.bTime<=S.aTime){[S.aTime,S.bTime]=[S.bTime,S.aTime];D.aTime.textContent=fmt(S.aTime);}
    D.bTime.textContent=fmt(S.bTime);D.setBBtn.classList.add('set');D.bTag.classList.remove('inactive');
    showDock();updateMarkers();updateSectionDur();
    if(S.aTime!==null)startLoop();else{highlightLoop();toast('Point B set at '+fmt(S.bTime));}
}
function showDock(){D.dockExpand.classList.add('open');D.dockToggle.classList.add('vis');D.dockToggleIcon.textContent='▼';}
function startLoop(){
    S.looping=true;S.loopCount=0;D.loopCountDisp.textContent='0';updateLoopRing();
    highlightLoop();updateProgressLoop();
    D.setABtn.classList.add('looping');D.setBBtn.classList.add('looping');
    au.currentTime=S.aTime;if(!S.playing)au.play();
    startLoopTick();
    toast('Loop started — '+fmt(S.bTime-S.aTime)+' section');
}
function clearLoop(){
    S.aTime=null;S.bTime=null;S.looping=false;S.loopCount=0;
    stopLoopTick();
    D.aTime.textContent='—';D.bTime.textContent='—';
    D.dockExpand.classList.remove('open');D.dockToggle.classList.remove('vis');
    D.setABtn.classList.remove('set','looping');D.setBBtn.classList.remove('set','looping');
    D.aTag.classList.add('inactive');D.bTag.classList.add('inactive');
    D.loopCountDisp.textContent='0';updateLoopRing();
    D.progressLoop.style.display='none';D.markerA.style.display='none';D.markerB.style.display='none';
    D.sectionDur.style.display='none';
    clearWordTap();
    S.words.forEach(w=>w.el.classList.remove('in-loop'));
}
function fineTune(which,delta){
    if(which==='a'&&S.aTime!==null){S.aTime=clamp(S.aTime+delta,0,au.duration||9999);D.aTime.textContent=fmt(S.aTime);}
    else if(which==='b'&&S.bTime!==null){S.bTime=clamp(S.bTime+delta,0,au.duration||9999);D.bTime.textContent=fmt(S.bTime);}
    if(S.looping){highlightLoop();updateProgressLoop();updateMarkers();updateSectionDur();}
}
function highlightLoop(){
    S.words.forEach(w=>w.el.classList.remove('in-loop','sel-anchor'));
    if(S.tapWordA!==null&&S.tapWordB!==null){
        // Word-tap selection: highlight exact word range only
        const lo=Math.min(S.tapWordA,S.tapWordB);
        const hi=Math.max(S.tapWordA,S.tapWordB);
        for(let i=lo;i<=hi;i++) S.words[i].el.classList.add('in-loop');
    }
    // Transport-button loops: no text highlighting (only progress bar shows range)
}
function updateProgressLoop(){
    if(!au.duration||S.aTime===null||S.bTime===null){D.progressLoop.style.display='none';return;}
    D.progressLoop.style.display='block';
    D.progressLoop.style.left=(S.aTime/au.duration*100)+'%';
    D.progressLoop.style.width=((S.bTime-S.aTime)/au.duration*100)+'%';
}
function updateMarkers(){
    if(!au.duration){D.markerA.style.display='none';D.markerB.style.display='none';return;}
    if(S.aTime!==null){D.markerA.style.display='block';D.markerA.style.left=(S.aTime/au.duration*100)+'%';}
    else D.markerA.style.display='none';
    if(S.bTime!==null){D.markerB.style.display='block';D.markerB.style.left=(S.bTime/au.duration*100)+'%';}
    else D.markerB.style.display='none';
}
function updateSectionDur(){
    if(S.aTime!==null&&S.bTime!==null){const d=S.bTime-S.aTime;D.sectionDur.textContent=d.toFixed(1)+'s';D.sectionDur.style.display='inline-flex';}
    else D.sectionDur.style.display='none';
}
function updateLoopRing(){
    const target=S.loopTarget||0;const pct=target>0?Math.min(S.loopCount/target,1):0;
    D.loopRingFg.style.strokeDashoffset=RING_CIRC*(1-pct);
    D.loopCountDisp.textContent=target>0?S.loopCount+'/'+target:String(S.loopCount);
}

// ═══ Completion ═══
function showCompletion(){
    D.completeSub.textContent=S.loopCount+' loops finished';
    D.completeFlash.classList.add('show');D.completeMsg.classList.add('show');
    setTimeout(()=>{D.completeFlash.classList.remove('show');D.completeMsg.classList.remove('show');},2500);
    toast('Section complete! 🎉','success');
}

// ═══ Speed ═══
function setSpeed(spd){
    S.speed=clamp(Math.round(spd*100)/100,0.5,2.5);au.playbackRate=S.speed;
    D.speedSlider.value=S.speed;D.speedDisp.textContent=S.speed.toFixed(2)+'×';
    D.speedPill.textContent=S.speed.toFixed(2)+'×';
    D.speedMenu.querySelectorAll('.speed-opt').forEach(o=>
        o.classList.toggle('active',parseFloat(o.dataset.spd)===S.speed));
}

// ═══ Audio Events ═══
au.addEventListener('play',()=>{S.playing=true;D.playBtn.textContent='⏸';});
au.addEventListener('pause',()=>{S.playing=false;D.playBtn.textContent='▶';});
au.addEventListener('loadedmetadata',()=>{
    const dur=fmt(au.duration);D.durTime.textContent=dur;D.durTimeL.textContent=dur;
    updateProgressLoop();updateMarkers();
});

au.addEventListener('timeupdate',()=>{
    if(!au.duration)return;
    const pct=(au.currentTime/au.duration*100);
    D.progressFill.style.width=pct+'%';
    const ct=fmt(au.currentTime);
    D.curTime.textContent=ct;D.curTimeL.textContent=ct;
    // Clip light text to match fill width
    D.progressTimeLight.style.clipPath='inset(0 '+(100-pct)+'% 0 0)';

    // Active verse tracking for scroll
    let activeVi=-1;const t=au.currentTime;
    for(let i=S.verses.length-1;i>=0;i--){if(t>=S.verses[i].start){activeVi=i;break;}}
    if(activeVi!==S.lastActiveVerse){
        if(activeVi>=0){
            const g=D.textBody.querySelector(`.verse-group[data-vi="${activeVi}"]`);
            if(g) g.scrollIntoView({behavior:'smooth',block:'nearest'});
        }
        S.lastActiveVerse=activeVi;
    }
});

// ═══ Tight Loop Check (RAF) — catches B-point at any speed ═══
function loopTick(){
    if(!S.looping){S.loopRafId=null;return;}
    if(S.bTime!==null&&au.currentTime>=S.bTime){
        S.loopCount++;updateLoopRing();
        if(S.loopTarget>0&&S.loopCount>=S.loopTarget){
            S.looping=false;au.pause();
            D.setABtn.classList.remove('looping');D.setBBtn.classList.remove('looping');
            showCompletion();S.loopRafId=null;return;
        }
        au.currentTime=S.aTime;updateBmMastery();
    }
    S.loopRafId=requestAnimationFrame(loopTick);
}
function startLoopTick(){if(!S.loopRafId)S.loopRafId=requestAnimationFrame(loopTick);}
function stopLoopTick(){if(S.loopRafId){cancelAnimationFrame(S.loopRafId);S.loopRafId=null;}}

// ═══ Player ═══
function togglePlay(){S.playing?au.pause():au.play();}
function prevCh(){const ba=AUDIO_MAP[S.book]||[];const i=ba.findIndex(f=>f.chapter===S.chapter);if(i>0)loadChapter(ba[i-1].chapter);}
function nextCh(){const ba=AUDIO_MAP[S.book]||[];const i=ba.findIndex(f=>f.chapter===S.chapter);if(i<ba.length-1)loadChapter(ba[i+1].chapter);}
function jumpVerse(delta){
    if(!S.verses.length)return;const t=au.currentTime;
    let vi=0;for(let i=S.verses.length-1;i>=0;i--){if(t>=S.verses[i].start){vi=i;break;}}
    const next=clamp(vi+delta,0,S.verses.length-1);au.currentTime=S.verses[next].start;if(!S.playing)au.play();
}
D.progressWrap.onclick=e=>{if(!au.duration)return;au.currentTime=(e.clientX-D.progressWrap.getBoundingClientRect().left)/D.progressWrap.offsetWidth*au.duration;};

// ═══ Bookmarks ═══
function getBms(){try{return JSON.parse(localStorage.getItem('pbe-mem-bm')||'[]');}catch{return[];}}
function saveBms(a){localStorage.setItem('pbe-mem-bm',JSON.stringify(a));}
function saveBookmark(){
    if(S.aTime===null||S.bTime===null){toast('Set A and B first');return;}
    const bk=ALL_BOOKS.find(b=>b.id===S.book);let prev='';
    if(S.tapWordA!==null&&S.tapWordB!==null){
        const lo=Math.min(S.tapWordA,S.tapWordB),hi=Math.max(S.tapWordA,S.tapWordB);
        for(let i=lo;i<=hi;i++) prev+=S.words[i].text+' ';
    } else {
        // Fallback for transport-button loops: use overlapping verses
        S.verses.forEach(v=>{if(v.end>S.aTime&&v.start<S.bTime) prev+=v.text+' ';});
    }
    if(prev.length>80)prev=prev.slice(0,77)+'…';
    const bm={id:Date.now(),year:S.year,book:S.book,bookName:bk?bk.name:S.book,chapter:S.chapter,
              aTime:S.aTime,bTime:S.bTime,tapA:S.tapWordA,tapB:S.tapWordB,speed:S.speed,
              preview:prev.trim(),label:(bk?bk.name:S.book)+' '+S.chapter,
              loops:S.loopCount};
    const bms=getBms();bms.unshift(bm);saveBms(bms);renderBms();toast('Section saved ⭐','success');
}
function loadBm(bm){
    if(bm.book!==S.book||bm.chapter!==S.chapter){
        S.year=bm.year||S.year;S.book=bm.book;loadChapter(bm.chapter).then(()=>applyBm(bm));
    }else applyBm(bm);
}
function applyBm(bm){
    S.aTime=bm.aTime;S.bTime=bm.bTime;
    // Restore speed if saved
    if(bm.speed)setSpeed(bm.speed);
    // Restore word range if saved
    if(bm.tapA!=null&&bm.tapB!=null&&bm.tapA<S.words.length&&bm.tapB<S.words.length){
        S.tapWordA=bm.tapA;S.tapWordB=bm.tapB;S.tapMode='idle';
    } else {S.tapWordA=null;S.tapWordB=null;}
    D.aTime.textContent=fmt(S.aTime);D.bTime.textContent=fmt(S.bTime);
    D.setABtn.classList.add('set');D.setBBtn.classList.add('set');
    D.aTag.classList.remove('inactive');D.bTag.classList.remove('inactive');
    updateMarkers();updateSectionDur();
    S.looping=true;S.loopCount=0;D.loopCountDisp.textContent='0';updateLoopRing();
    highlightLoop();updateProgressLoop();showDock();
    D.setABtn.classList.add('looping');D.setBBtn.classList.add('looping');
    au.currentTime=S.aTime;if(!S.playing)au.play();startLoopTick();
}function updateBmMastery(){
    const bms=getBms();const m=bms.find(b=>b.book===S.book&&b.chapter===S.chapter&&Math.abs(b.aTime-S.aTime)<1&&Math.abs(b.bTime-S.bTime)<1);
    if(m){m.loops=(m.loops||0)+1;saveBms(bms);renderBms();}
}
function deleteBm(id){saveBms(getBms().filter(b=>b.id!==id));renderBms();toast('Bookmark removed');}
function renderBms(){
    const bms=getBms();if(!bms.length){D.bmList.innerHTML='<div class="no-bm">No saved sections yet</div>';return;}
    D.bmList.innerHTML='';
    bms.forEach(bm=>{const el=document.createElement('div');el.className='bm-item';
        el.innerHTML=`<div class="bm-text"><div class="bm-name">${bm.label} (${fmt(bm.aTime)}–${fmt(bm.bTime)})</div>
            <div class="bm-meta">${bm.preview}</div></div>
            <span class="bm-loops">${bm.loops||0} loops</span>
            <button class="bm-del" title="Delete">✕</button>`;
        el.querySelector('.bm-text').onclick=()=>loadBm(bm);
        el.querySelector('.bm-del').onclick=e=>{e.stopPropagation();deleteBm(bm.id);};
        D.bmList.appendChild(el);});
}

// ═══ Settings ═══
function saveCfg(){localStorage.setItem('pbe-mem-cfg',JSON.stringify({loopTarget:S.loopTarget,speed:S.speed,vpl:S.vpl,hlSoft:S.hlSoft}));}
function loadCfg(){
    try{const c=JSON.parse(localStorage.getItem('pbe-mem-cfg'));if(!c)return;
        S.loopTarget=c.loopTarget??10;S.speed=c.speed??1.0;S.vpl=c.vpl??false;S.hlSoft=c.hlSoft??false;
        if(S.loopTarget===0)D.loopTarget.value='∞';else D.loopTarget.value=S.loopTarget;
        setSpeed(S.speed);D.textBody.classList.toggle('vpl',S.vpl);
        D.vplToggle.classList.toggle('btn-accent',S.vpl);
        D.textBody.classList.toggle('hl-soft',S.hlSoft);
        D.hlToggle.textContent=S.hlSoft?'◐ Soft Highlight':'◉ Bold Highlight';
        D.hlToggle.classList.toggle('btn-accent',S.hlSoft);
    }catch{}
}

// ═══ Speed Dropdown ═══
(function buildSpeedMenu(){
    const speeds=[0.5,0.75,1.0,1.25,1.5,1.75,2.0,2.25,2.5];
    speeds.forEach(spd=>{
        const opt=document.createElement('button');opt.className='speed-opt'+(spd===S.speed?' active':'');
        opt.textContent=spd.toFixed(2)+'×';opt.dataset.spd=spd;
        opt.onclick=e=>{e.stopPropagation();setSpeed(spd);saveCfg();D.speedMenu.classList.remove('open');};
        D.speedMenu.appendChild(opt);
    });
})();
D.speedPill.onclick=e=>{e.stopPropagation();D.speedMenu.classList.toggle('open');};
document.addEventListener('click',e=>{
    if(!e.target.closest('.speed-wrap'))D.speedMenu.classList.remove('open');
});

// ═══ Events ═══
D.chapterBtn.onclick=openPicker;D.pickerClose.onclick=closePicker;D.pickerBack.onclick=renderBooks;
D.pickerOverlay.onclick=e=>{if(e.target===D.pickerOverlay)closePicker();};
D.themeBtn.onclick=()=>setTheme(S.theme==='dark'?'light':'dark');
D.playBtn.onclick=togglePlay;D.prevBtn.onclick=prevCh;D.nextBtn.onclick=nextCh;
D.setABtn.onclick=setA;D.setBBtn.onclick=setB;D.clearBtn.onclick=clearLoop;
D.ftAm.onclick=()=>fineTune('a',-0.5);D.ftAp.onclick=()=>fineTune('a',0.5);
D.ftBm.onclick=()=>fineTune('b',-0.5);D.ftBp.onclick=()=>fineTune('b',0.5);
D.speedSlider.oninput=function(){setSpeed(parseFloat(this.value));saveCfg();};
D.saveBmBtn.onclick=saveBookmark;
D.vplToggle.onclick=()=>{S.vpl=!S.vpl;D.textBody.classList.toggle('vpl',S.vpl);
    D.vplToggle.classList.toggle('btn-accent',S.vpl);saveCfg();};
D.hlToggle.onclick=()=>{S.hlSoft=!S.hlSoft;D.textBody.classList.toggle('hl-soft',S.hlSoft);
    D.hlToggle.textContent=S.hlSoft?'◐ Soft Highlight':'◉ Bold Highlight';
    D.hlToggle.classList.toggle('btn-accent',S.hlSoft);saveCfg();};
D.dockToggle.onclick=()=>{const o=D.dockExpand.classList.toggle('open');D.dockToggleIcon.textContent=o?'▼':'▲';};
D.loopTarget.onchange=function(){const v=this.value.trim();
    if(v==='∞'||v==='0'||v===''){S.loopTarget=0;this.value='∞';}else S.loopTarget=clamp(parseInt(v)||10,1,999);saveCfg();};
D.loopInfBtn.onclick=()=>{S.loopTarget=0;D.loopTarget.value='∞';saveCfg();toast('Infinite loop mode');};
D.kbBtn.onclick=()=>D.kbOverlay.classList.add('active');
D.kbClose.onclick=()=>D.kbOverlay.classList.remove('active');
D.kbOverlay.onclick=e=>{if(e.target===D.kbOverlay)D.kbOverlay.classList.remove('active');};

// ═══ Keyboard ═══
document.addEventListener('keydown',e=>{
    if(e.target.tagName==='INPUT'||e.target.tagName==='SELECT'||e.target.tagName==='TEXTAREA')return;
    if(e.ctrlKey||e.metaKey||e.altKey)return;
    const k=e.code;
    if(k==='Space'){e.preventDefault();togglePlay();}
    else if(k==='KeyA'){e.preventDefault();setA();}
    else if(k==='KeyB'){e.preventDefault();setB();}
    else if(k==='Escape'){clearLoop();closePicker();D.kbOverlay.classList.remove('active');}
    else if(k==='ArrowLeft'){e.preventDefault();prevCh();}
    else if(k==='ArrowRight'){e.preventDefault();nextCh();}
    else if(k==='ArrowUp'){e.preventDefault();jumpVerse(-1);}
    else if(k==='ArrowDown'){e.preventDefault();jumpVerse(1);}
    else if(k==='KeyC'){e.preventDefault();openPicker();}
    else if(k==='KeyD'){e.preventDefault();setTheme(S.theme==='dark'?'light':'dark');}
    else if(k==='KeyV'){e.preventDefault();D.vplToggle.click();}
    else if(k==='Period'){e.preventDefault();setSpeed(S.speed+0.05);saveCfg();}
    else if(k==='Comma'){e.preventDefault();setSpeed(S.speed-0.05);saveCfg();}
    else if(k==='KeyJ'){e.preventDefault();au.currentTime=Math.max(0,au.currentTime-5);}
    else if(k==='KeyL'){e.preventDefault();au.currentTime=Math.min(au.duration||0,au.currentTime+5);}
    else if(k==='Slash'){e.preventDefault();D.kbOverlay.classList.toggle('active');}
});

// ═══ Init ═══
(function init(){
    setTheme(localStorage.getItem('pbe-mem-theme')||'light');
    D.aTag.classList.add('inactive');D.bTag.classList.add('inactive');
    loadCfg();renderBms();
    const saved=localStorage.getItem('pbe-mem-last');
    if(saved){try{const l=JSON.parse(saved);if(l.year)S.year=l.year;if(l.book)S.book=l.book;
        if(l.chapter){loadChapter(l.chapter);return;}}catch{}}
    const ba=AUDIO_MAP[S.book];if(ba&&ba.length)loadChapter(1);
})();
</script>
</body>
</html>
