<?php
// ARUNHost - Premium Web Hosting Platform
// Developed by ARUN HACKER
// YouTube: @akbrother2906
// Telegram: @arunwave25
// FIXED - URL + COPY BUTTON IMMEDIATELY AFTER PUBLISH!

error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Create directories
$sites_dir = 'sites';
$uploads_dir = 'uploads';

if (!is_dir($sites_dir)) {
    mkdir($sites_dir, 0777, true);
    chmod($sites_dir, 0777);
}

if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
    chmod($uploads_dir, 0777);
}

@chmod($sites_dir, 0777);
@chmod($uploads_dir, 0777);

$upload_message = '';
$message_type = '';
$published_url = '';
$published_name = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'paste_publish') {
        $site_name = isset($_POST['site_name']) ? trim($_POST['site_name']) : '';
        $html_code = isset($_POST['html_code']) ? $_POST['html_code'] : '';
        
        if (empty($site_name)) {
            $site_name = 'site-' . time();
        }
        
        $site_name = preg_replace('/[^a-zA-Z0-9\-_]/', '', $site_name);
        
        if (empty($site_name)) {
            $site_name = 'site-' . time();
        }
        
        if (!empty($html_code)) {
            $filename = $site_name . '.html';
            $file_path = $sites_dir . '/' . $filename;
            
            if (file_put_contents($file_path, $html_code) !== false) {
                @chmod($file_path, 0777);
                
                // Get base URL
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $script_dir = dirname($_SERVER['PHP_SELF']);
                if ($script_dir === '/') {
                    $script_dir = '';
                }
                $base_url = $protocol . '://' . $host . $script_dir;
                if (substr($base_url, -1) !== '/') {
                    $base_url .= '/';
                }
                
                $published_url = $base_url . '?view=' . urlencode($filename);
                $published_name = $site_name;
                
                $upload_message = "✅ SUCCESS! Site Published!";
                $message_type = "success";
            } else {
                $upload_message = "❌ Error saving file!";
                $message_type = "error";
            }
        } else {
            $upload_message = "❌ Please paste HTML code!";
            $message_type = "error";
        }
    }
}

// Handle view request
if (isset($_GET['view'])) {
    $view_file = basename($_GET['view']);
    $file_path = $sites_dir . '/' . $view_file;
    
    if (file_exists($file_path) && in_array(strtolower(pathinfo($file_path, PATHINFO_EXTENSION)), ['html', 'htm'])) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($file_path);
        exit;
    } else {
        die("<h1>❌ 404 - Site Not Found</h1>");
    }
}

// Get all hosted sites
$sites = [];
if (is_dir($sites_dir)) {
    $files = array_diff(scandir($sites_dir), ['.', '..']);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['html', 'htm'])) {
            $full_path = $sites_dir . '/' . $file;
            if (is_file($full_path)) {
                $sites[] = [
                    'name' => pathinfo($file, PATHINFO_FILENAME),
                    'file' => $file,
                    'size' => filesize($full_path),
                    'modified' => filemtime($full_path)
                ];
            }
        }
    }
}

usort($sites, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

// Get base URL
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_dir = dirname($_SERVER['PHP_SELF']);
if ($script_dir === '/') {
    $script_dir = '';
}
$base_url = $protocol . '://' . $host . $script_dir;
if (substr($base_url, -1) !== '/') {
    $base_url .= '/';
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['confirm'])) {
    $delete_file = basename($_GET['delete']);
    $file_path = $sites_dir . '/' . $delete_file;
    
    if (file_exists($file_path) && in_array(strtolower(pathinfo($file_path, PATHINFO_EXTENSION)), ['html', 'htm'])) {
        if (unlink($file_path)) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARUNHost - Premium Web Hosting | Developed by ARUNbhai</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            min-height: 100vh;
            color: #e0e7ff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(99, 102, 241, 0.3);
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .branding {
            font-size: 12px;
            color: #a78bfa;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .social-links {
            display: flex;
            gap: 10px;
        }

        .social-btn {
            padding: 10px 16px;
            background: rgba(99, 102, 241, 0.2);
            border: 2px solid #6366f1;
            color: #a78bfa;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }

        .social-btn:hover {
            background: #6366f1;
            color: white;
            transform: translateY(-2px);
        }

        .tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            background: rgba(15, 23, 42, 0.5);
            padding: 15px;
            border-radius: 15px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            overflow-x: auto;
        }

        .tab-btn {
            padding: 12px 30px;
            border: none;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
        }

        .tab-btn:hover:not(.active) {
            color: #e0e7ff;
            background: rgba(99, 102, 241, 0.1);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            background: rgba(15, 23, 42, 0.7);
            border: 2px solid rgba(99, 102, 241, 0.2);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
            transition: border-color 0.3s;
        }

        .card:hover {
            border-color: rgba(99, 102, 241, 0.5);
        }

        .card-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .message {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message.success {
            background: rgba(34, 197, 94, 0.2);
            border: 2px solid #22c55e;
            color: #86efac;
        }

        .message.error {
            background: rgba(239, 68, 68, 0.2);
            border: 2px solid #ef4444;
            color: #fca5a5;
        }

        .input-group {
            margin-bottom: 25px;
        }

        .input-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .text-input, .code-input {
            width: 100%;
            padding: 15px 20px;
            background: #1e293b;
            border: 2px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            color: #e0e7ff;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .code-input {
            font-family: 'Monaco', monospace;
            font-size: 14px;
            min-height: 300px;
            resize: vertical;
        }

        .text-input:focus, .code-input:focus {
            outline: none;
            border-color: #6366f1;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex: 1;
            min-width: 150px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(99, 102, 441, 0.4);
        }

        .preview-frame {
            width: 100%;
            min-height: 400px;
            border: 2px solid rgba(99, 102, 241, 0.3);
            border-radius: 15px;
            background: white;
            margin-top: 20px;
        }

        /* SUCCESS URL BOX */
        .url-box {
            background: rgba(34, 197, 94, 0.15);
            border: 2px solid #22c55e;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .url-box-title {
            color: #86efac;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .url-display {
            background: rgba(0, 0, 0, 0.3);
            border: 2px solid #22c55e;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            word-break: break-all;
            color: #86efac;
            font-family: monospace;
            font-size: 14px;
            line-height: 1.6;
        }

        .url-button-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .url-btn {
            flex: 1;
            min-width: 150px;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .copy-btn {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(34, 197, 94, 0.3);
        }

        .copy-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(34, 197, 94, 0.4);
        }

        .view-btn {
            background: transparent;
            border: 2px solid #22c55e;
            color: #86efac;
        }

        .view-btn:hover {
            background: rgba(34, 197, 94, 0.2);
        }

        .site-item {
            background: rgba(30, 41, 59, 0.8);
            border: 2px solid rgba(99, 102, 241, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .site-item:hover {
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateX(5px);
        }

        .site-name {
            font-size: 16px;
            font-weight: 600;
            color: #6366f1;
            margin-bottom: 12px;
        }

        .site-url {
            font-size: 13px;
            color: #94a3b8;
            font-family: monospace;
            word-break: break-all;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .site-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .icon-btn {
            padding: 10px 16px;
            border: none;
            background: rgba(99, 102, 241, 0.2);
            border-radius: 8px;
            color: #a78bfa;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
            flex: 1;
            min-width: 100px;
            justify-content: center;
        }

        .icon-btn:hover {
            background: #6366f1;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            padding: 30px;
            color: #64748b;
            font-size: 13px;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
            margin-top: 50px;
        }

        .footer-brand {
            font-weight: 600;
            color: #a78bfa;
        }

        .info-box {
            background: rgba(99, 102, 241, 0.1);
            border-left: 4px solid #6366f1;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .card {
                padding: 25px;
            }

            .button-group {
                flex-direction: column;
            }

            .icon-btn {
                min-width: 80px;
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <div class="logo-icon">⚡</div>
                <div>
                    <div>ARUNHost</div>
                    <div class="branding">Developed by ARUNbhai</div>
                </div>
            </div>
            <div class="social-links">
                <a href="https://youtube.com/@geniushacker29" target="_blank" class="social-btn">📺 YouTube</a>
                <a href="https://t.me/geniushackerfreetools" target="_blank" class="social-btn">✈️ Telegram</a>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('paste')">📝 Paste Code</button>
            <button class="tab-btn" onclick="switchTab('preview')">👁️ HTML Preview</button>
            <button class="tab-btn" onclick="switchTab('sites')">🌐 My Sites</button>
        </div>

        <!-- Paste Code Tab -->
        <div id="paste" class="tab-content active">
            <div class="card">
                <div class="card-title">✨ Publish Your HTML Code</div>
                
                <?php if (!empty($upload_message)): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <span><?php echo $upload_message; ?></span>
                    </div>
                <?php endif; ?>

                <div class="info-box">
                    💡 <strong>Tip:</strong> Paste your HTML code, give it a name, and click Publish. Your link will appear below instantly!
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="paste_publish">
                    
                    <div class="input-group">
                        <label class="input-label">📝 Site Name (ID)</label>
                        <input type="text" name="site_name" class="text-input" 
                               placeholder="geniushacker-main" pattern="[a-zA-Z0-9\-_]*">
                        <small style="color:#94a3b8; margin-top:5px; display:block;">Only letters, numbers, hyphens and underscores</small>
                    </div>

                    <div class="input-group">
                        <label class="input-label">✏️ Paste Your HTML Code</label>
                        <textarea name="html_code" class="code-input" 
                                  placeholder="&lt;!DOCTYPE html&gt;&#10;&lt;html&gt;&#10;&lt;head&gt;&#10;    &lt;title&gt;My Site&lt;/title&gt;&#10;&lt;/head&gt;&#10;&lt;body&gt;&#10;    &lt;h1&gt;Hello World!&lt;/h1&gt;&#10;&lt;/body&gt;&#10;&lt;/html&gt;"></textarea>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">🚀 Publish Site</button>
                    </div>
                </form>

                <!-- SUCCESS URL BOX - APPEARS AFTER PUBLISH -->
                <?php if (!empty($published_url)): ?>
                    <div class="url-box">
                        <div class="url-box-title">🎉 Your Site Published!</div>
                        <div class="url-display"><?php echo htmlspecialchars($published_url); ?></div>
                        <div class="url-button-group">
                            <button class="url-btn copy-btn" onclick="copyPublishedLink('<?php echo str_replace("'", "\\'", htmlspecialchars($published_url)); ?>')">
                                📋 COPY LINK
                            </button>
                            <a href="<?php echo htmlspecialchars($published_url); ?>" target="_blank" class="url-btn view-btn">
                                👁️ VIEW SITE
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- HTML Preview Tab -->
        <div id="preview" class="tab-content">
            <div class="card">
                <div class="card-title">👁️ Live HTML Preview</div>
                
                <div class="info-box">
                    💡 <strong>Tip:</strong> Paste HTML code below and instantly see the preview without publishing!
                </div>

                <div class="input-group">
                    <label class="input-label">✏️ Paste HTML Code To Preview</label>
                    <textarea id="previewCode" class="code-input" 
                              placeholder="&lt;!DOCTYPE html&gt;&#10;&lt;html&gt;&#10;&lt;head&gt;&#10;    &lt;title&gt;My Site&lt;/title&gt;&#10;&lt;/head&gt;&#10;&lt;body&gt;&#10;    &lt;h1&gt;Hello World!&lt;/h1&gt;&#10;&lt;/body&gt;&#10;&lt;/html&gt;" 
                              onchange="updatePreview()" oninput="updatePreview()"></textarea>
                </div>

                <div class="button-group">
                    <button class="btn btn-primary" onclick="updatePreview()">🔄 Refresh Preview</button>
                </div>

                <div style="margin-top: 30px;">
                    <label class="input-label">📺 Preview:</label>
                    <iframe id="previewFrame" class="preview-frame"></iframe>
                </div>
            </div>
        </div>

        <!-- Sites Tab -->
        <div id="sites" class="tab-content">
            <div class="card">
                <div class="card-title">🌐 My Sites</div>
                
                <div class="info-box">
                    ✅ <strong>SSL Certificate:</strong> FREE HTTPS enabled on all sites! Share these links anywhere!
                </div>

                <div id="sitesList">
                    <?php if (count($sites) > 0): ?>
                        <?php foreach ($sites as $site): 
                            $full_url = $base_url . '?view=' . urlencode($site['file']);
                        ?>
                            <div class="site-item">
                                <div class="site-name">📄 <?php echo htmlspecialchars($site['name']); ?></div>
                                <div class="site-url"><?php echo htmlspecialchars($full_url); ?></div>
                                <div class="site-actions">
                                    <a href="<?php echo htmlspecialchars($full_url); ?>" 
                                       class="icon-btn" target="_blank">👁️ View</a>
                                    <button class="icon-btn" onclick="copyPublishedLink('<?php echo str_replace("'", "\\'", htmlspecialchars($full_url)); ?>')">📋 Copy</button>
                                    <a href="?delete=<?php echo urlencode($site['file']); ?>&confirm=1" 
                                       class="icon-btn" onclick="return confirm('❌ Delete this site?')">🗑️ Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <div style="font-size:20px; font-weight:600; color:#cbd5e1; margin-bottom:10px;">No Sites Yet</div>
                            <div style="color:#94a3b8;">Go to "Paste Code" tab and publish your first site!</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-brand">⚡ ARUNHost Premium - FREE Web Hosting</div>
            <div style="margin-top: 15px;">Developed by ARUNbhai | Premium Web Hosting Platform</div>
            <div style="margin-top: 10px; font-size:11px;">
                📺 <a href="https://youtube.com/@geniushacker29" target="_blank" style="color:#6366f1; text-decoration:none;">YouTube: @geniushacker29</a> | 
                ✈️ <a href="https://t.me/geniushackerfreetools" target="_blank" style="color:#6366f1; text-decoration:none;">Telegram: @geniushackerfreetools</a>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function updatePreview() {
            const code = document.getElementById('previewCode').value;
            const frame = document.getElementById('previewFrame');
            frame.srcdoc = code;
        }

        function copyPublishedLink(url) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    alert('✅ Link Copied to Clipboard!');
                }).catch(() => {
                    fallbackCopy(url);
                });
            } else {
                fallbackCopy(url);
            }
        }

        function fallbackCopy(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                alert('✅ Link Copied to Clipboard!');
            } catch (err) {
                alert('❌ Failed to copy. Copy manually:\n\n' + text);
            }
            document.body.removeChild(textArea);
        }
    </script>
</body>
</html>
