<div class="row">
    <div class="col-lg-8">
        <!-- Article Content -->
        <article class="bg-white p-4 rounded shadow-sm mb-4">
            <?php if ($isPreview): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> This is a preview. This article is currently <strong><?= htmlspecialchars($article['status']) ?></strong>.
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <span class="badge badge-danger"><?= htmlspecialchars($article['category_name']) ?></span>
                <span class="text-muted ml-2"><i class="far fa-clock"></i> <?= date('F j, Y, g:i a', strtotime($article['created_at'])) ?></span>
                <span class="text-muted ml-2"><i class="far fa-eye"></i> <?= number_format($article['views']) ?> views</span>
            </div>

            <h1 class="h2 font-weight-bold mb-3"><?= htmlspecialchars($article['title']) ?></h1>
            
            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                <div class="mr-3">
                    <?php if (!empty($article['author_avatar'])): ?>
                        <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($article['author_avatar'], '/') ?>" class="rounded-circle" width="50" height="50" alt="Author">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <h6 class="mb-0 font-weight-bold">By <?= htmlspecialchars($article['author_name']) ?></h6>
                    <small class="text-muted">Journalist</small>
                </div>
            </div>

            <?php if ($article['image_url']): ?>
                <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($article['image_url'], '/') ?>" class="img-fluid rounded mb-4 w-100" alt="<?= htmlspecialchars($article['title']) ?>" style="max-height: 500px; object-fit: cover;">
            <?php endif; ?>

            <!-- AI Smart Summary Component -->
            <style>
            .ai-summary-container {
                perspective: 1000px;
                margin-bottom: 2rem;
            }
            .ai-summary-card {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(226, 232, 240, 0.8);
                border-radius: 16px;
                padding: 1.25rem 1.5rem;
                box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.12), 0 2px 8px -1px rgba(148, 163, 184, 0.08);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }
            .ai-summary-card:hover {
                box-shadow: 0 10px 25px -3px rgba(148, 163, 184, 0.18), 0 4px 12px -2px rgba(148, 163, 184, 0.12);
                border-color: color-mix(in srgb, var(--accent, #c0392b) 25%, rgba(226, 232, 240, 0.8));
            }
            .ai-summary-header {
                margin-bottom: 0.75rem;
                border-bottom: 1px solid rgba(226, 232, 240, 0.5);
                padding-bottom: 0.75rem;
            }
            .ai-summary-badge {
                position: relative;
                overflow: hidden;
                display: inline-flex;
                align-items: center;
                background: linear-gradient(135deg, color-mix(in srgb, var(--accent, #c0392b) 10%, white) 0%, color-mix(in srgb, var(--accent, #c0392b) 20%, white) 100%);
                color: var(--accent, #c0392b);
                font-size: 0.8rem;
                font-weight: 700;
                padding: 0.35rem 0.8rem;
                border-radius: 30px;
                letter-spacing: 0.03em;
                text-transform: uppercase;
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.4);
                border: 1px solid color-mix(in srgb, var(--accent, #c0392b) 30%, rgba(255,255,255,0));
            }
            .ai-summary-badge .shimmer-effect {
                position: absolute;
                top: 0;
                left: -100%;
                width: 50%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
                transform: skewX(-25deg);
                animation: badge-shimmer 3s infinite;
            }
            @keyframes badge-shimmer {
                0% { left: -150%; }
                30% { left: 150%; }
                100% { left: 150%; }
            }
            .ai-summary-body {
                transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                max-height: 500px;
                overflow: hidden;
            }
            .ai-summary-body.collapsed {
                max-height: 0 !important;
                padding-top: 0;
                padding-bottom: 0;
                margin-top: 0;
            }
            .summary-bullets-content {
                display: flex;
                flex-direction: column;
                gap: 0.85rem;
                padding: 0.25rem 0;
                animation: fadeInSummary 0.5s ease-out forwards;
            }
            @keyframes fadeInSummary {
                from { opacity: 0; transform: translateY(8px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .summary-bullet-item {
                gap: 0.75rem;
            }
            .summary-bullet-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--accent, #c0392b);
                font-size: 0.45rem;
                margin-top: 0.55rem;
                opacity: 0.85;
            }
            .summary-bullet-text {
                font-size: 0.96rem;
                line-height: 1.6;
                color: #334155;
                font-weight: 450;
            }
            .generate-summary-btn {
                background: linear-gradient(135deg, var(--accent, #c0392b) 0%, color-mix(in srgb, var(--accent, #c0392b) 85%, black) 100%);
                color: white !important;
                border: none;
                border-radius: 30px;
                font-size: 0.88rem;
                font-weight: 600;
                padding: 0.55rem 1.35rem;
                box-shadow: 0 4px 12px rgba(192, 57, 43, 0.25);
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .generate-summary-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(192, 57, 43, 0.35);
            }
            .generate-summary-btn:active {
                transform: translateY(0);
            }

            /* Skeleton Loader */
            .summary-skeleton {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                padding: 0.5rem 0;
            }
            .skeleton-line {
                height: 12px;
                background: #e2e8f0;
                border-radius: 6px;
                width: 100%;
            }
            .skeleton-line.shimmer {
                background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
                background-size: 200% 100%;
                animation: skeleton-pulse 1.5s infinite;
            }
            @keyframes skeleton-pulse {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
            </style>

            <div class="ai-summary-container">
                <div class="ai-summary-card">
                    <div class="ai-summary-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center" style="gap: 0.75rem;">
                            <span class="ai-summary-badge">
                                <span class="shimmer-effect"></span>
                                <i class="fas fa-magic mr-1"></i> Smart Summary
                            </span>
                            <small class="text-muted summary-status-info" style="font-size: 0.78rem; font-weight: 500;">
                                <?php if (!empty($article['ai_summary'])): ?>
                                    ✓ Cached
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <button class="btn btn-sm btn-outline-secondary read-summary-btn" id="read-summary-btn" style="border-radius: 20px; font-size: 0.78rem; padding: 0.2rem 0.65rem; display: <?= !empty($article['ai_summary']) ? 'inline-flex' : 'none' ?>;" title="Listen to Summary">
                                <i class="fas fa-volume-up mr-1" id="read-summary-icon"></i> <span id="read-summary-text">Listen</span>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary toggle-summary-btn" id="toggle-summary-btn" style="border-radius: 20px; font-size: 0.78rem; padding: 0.2rem 0.65rem;">
                                <i class="fas fa-chevron-up mr-1" id="toggle-summary-icon"></i> <span id="toggle-summary-text">Hide</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="ai-summary-body" id="ai-summary-body">
                        <!-- If summary already exists in DB, render it! -->
                        <?php if (!empty($article['ai_summary'])): ?>
                            <div class="summary-bullets-content">
                                <?php 
                                $bullets = explode("\n", $article['ai_summary']);
                                foreach ($bullets as $bullet):
                                    $clean = ltrim(trim($bullet), '•* ');
                                    if (empty($clean)) continue;
                                ?>
                                    <div class="summary-bullet-item d-flex align-items-start">
                                        <span class="summary-bullet-icon"><i class="fas fa-circle"></i></span>
                                        <span class="summary-bullet-text"><?= htmlspecialchars($clean) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <!-- Placeholder / Trigger -->
                            <div id="summary-placeholder-state" class="py-2">
                                <p class="text-muted mb-3" style="font-size: 0.95rem;">Get a quick, informative 3-point summary of this article instantly.</p>
                                <button class="btn generate-summary-btn" id="generate-summary-btn">
                                    <i class="fas fa-wand-magic-sparkles mr-2"></i> Generate Smart Summary
                                </button>
                            </div>
                            
                            <!-- Skeleton Loader -->
                            <div id="summary-skeleton-loader" class="summary-skeleton" style="display: none;">
                                <div class="skeleton-line shimmer"></div>
                                <div class="skeleton-line shimmer" style="width: 85%;"></div>
                                <div class="skeleton-line shimmer" style="width: 90%;"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const generateBtn = document.getElementById('generate-summary-btn');
                const toggleBtn = document.getElementById('toggle-summary-btn');
                const toggleIcon = document.getElementById('toggle-summary-icon');
                const toggleText = document.getElementById('toggle-summary-text');
                const summaryBody = document.getElementById('ai-summary-body');
                const placeholderState = document.getElementById('summary-placeholder-state');
                const skeletonLoader = document.getElementById('summary-skeleton-loader');
                const statusInfo = document.querySelector('.summary-status-info');
                
                // Voice Reader variables
                const readBtn = document.getElementById('read-summary-btn');
                const readIcon = document.getElementById('read-summary-icon');
                const readText = document.getElementById('read-summary-text');
                let isSpeaking = false;
                let currentUtterance = null;

                // Warm up speech synthesis voices cache immediately on load
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.getVoices();
                    if (window.speechSynthesis.onvoiceschanged !== undefined) {
                        window.speechSynthesis.onvoiceschanged = () => {
                            window.speechSynthesis.getVoices();
                        };
                    }
                }

                // Helper to find the most premium, natural, enhanced neural voice on client browser
                function getAdvancedVoice(lang) {
                    const voices = window.speechSynthesis.getVoices();
                    if (!voices || voices.length === 0) return null;

                    const langPattern = lang.split('-')[0].toLowerCase();
                    const langVoices = voices.filter(v => v.lang.toLowerCase().startsWith(langPattern));

                    if (langVoices.length === 0) return null;

                    // Keywords in voice names indicating state-of-the-art neural/high-quality profiles
                    const premiumKeywords = ['natural', 'premium', 'google', 'siri', 'enhanced', 'samantha', 'daniel', 'karen', 'moira'];
                    
                    langVoices.sort((a, b) => {
                        const aName = a.name.toLowerCase();
                        const bName = b.name.toLowerCase();
                        
                        let aIdx = premiumKeywords.findIndex(kw => aName.includes(kw));
                        let bIdx = premiumKeywords.findIndex(kw => bName.includes(kw));
                        
                        if (aIdx === -1) aIdx = 999;
                        if (bIdx === -1) bIdx = 999;
                        
                        return aIdx - bIdx;
                    });

                    return langVoices[0];
                }

                // Toggle Expand/Collapse
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function() {
                        const isCollapsed = summaryBody.classList.contains('collapsed');
                        if (isCollapsed) {
                            summaryBody.classList.remove('collapsed');
                            toggleIcon.className = 'fas fa-chevron-up mr-1';
                            toggleText.textContent = 'Hide';
                        } else {
                            summaryBody.classList.add('collapsed');
                            toggleIcon.className = 'fas fa-chevron-down mr-1';
                            toggleText.textContent = 'Show';
                        }
                    });
                }

                // Voice Reader speech trigger
                if (readBtn) {
                    readBtn.addEventListener('click', function() {
                        if (isSpeaking) {
                            stopSpeaking();
                        } else {
                            startSpeaking();
                        }
                    });
                }

                function startSpeaking() {
                    const bulletElements = summaryBody.querySelectorAll('.summary-bullet-text');
                    if (bulletElements.length === 0) return;

                    let speechText = "";
                    bulletElements.forEach((el, i) => {
                        speechText += "Point " + (i + 1) + ": " + el.textContent + ". ";
                    });

                    if (!speechText) return;

                    window.speechSynthesis.cancel(); // Terminate current plays

                    currentUtterance = new SpeechSynthesisUtterance(speechText);
                    
                    // Dynamic Lang Detector: Check for Devanagari (Nepali) Unicode block
                    const isNepali = /[\u0900-\u097F]/.test(speechText);
                    const langCode = isNepali ? 'ne-NP' : 'en-US';
                    
                    currentUtterance.lang = langCode;
                    currentUtterance.rate = 0.95; // Slower, clear professional reading speed

                    // Bind advanced natural voice if available
                    const bestVoice = getAdvancedVoice(langCode);
                    if (bestVoice) {
                        currentUtterance.voice = bestVoice;
                    }

                    currentUtterance.onstart = function() {
                        isSpeaking = true;
                        readBtn.classList.remove('btn-outline-secondary');
                        readBtn.classList.add('btn-danger');
                        readIcon.className = 'fas fa-stop mr-1';
                        readText.textContent = 'Stop';
                    };

                    currentUtterance.onend = function() {
                        resetReadButton();
                    };

                    currentUtterance.onerror = function() {
                        resetReadButton();
                    };

                    window.speechSynthesis.speak(currentUtterance);
                }

                function stopSpeaking() {
                    window.speechSynthesis.cancel();
                    resetReadButton();
                }

                function resetReadButton() {
                    isSpeaking = false;
                    if (readBtn) {
                        readBtn.classList.remove('btn-danger');
                        readBtn.classList.add('btn-outline-secondary');
                        readIcon.className = 'fas fa-volume-up mr-1';
                        readText.textContent = 'Listen';
                    }
                }

                // Cancel speech on page unload so it doesn't leak
                window.addEventListener('beforeunload', () => {
                    window.speechSynthesis.cancel();
                });

                // AJAX Generator
                if (generateBtn) {
                    generateBtn.addEventListener('click', async function() {
                        placeholderState.style.display = 'none';
                        skeletonLoader.style.display = 'flex';
                        if (statusInfo) statusInfo.textContent = 'Generating...';

                        try {
                            const response = await fetch('<?= \App\Core\App::get('config')['url'] ?>/article/summarize?article_id=<?= $article['id'] ?>');
                            const data = await response.json();

                            skeletonLoader.style.display = 'none';

                            if (data.error) {
                                if (statusInfo) statusInfo.textContent = 'Error';
                                summaryBody.innerHTML = `<div class="alert alert-danger mb-0">${data.error}</div>`;
                            } else if (data.summary) {
                                if (statusInfo) {
                                    statusInfo.textContent = data.method === 'api' ? '✓ Generated by AI' : '✓ Extracted by Smart NLP';
                                }
                                
                                // Render bullets
                                const bullets = data.summary.split('\n');
                                let html = '<div class="summary-bullets-content">';
                                bullets.forEach(bullet => {
                                    let clean = bullet.trim();
                                    if (clean.startsWith('•') || clean.startsWith('*')) {
                                        clean = clean.substring(1).trim();
                                    }
                                    if (clean.length > 0) {
                                        html += `
                                            <div class="summary-bullet-item d-flex align-items-start">
                                                <span class="summary-bullet-icon"><i class="fas fa-circle"></i></span>
                                                <span class="summary-bullet-text">${escapeHtml(clean)}</span>
                                            </div>
                                        `;
                                    }
                                });
                                html += '</div>';
                                summaryBody.innerHTML = html;

                                // Show the speech reader button now that text content is rendered!
                                if (readBtn) {
                                    readBtn.style.display = 'inline-flex';
                                }
                            } else {
                                if (statusInfo) statusInfo.textContent = 'Failed';
                                summaryBody.innerHTML = `<div class="alert alert-danger mb-0">Failed to generate summary.</div>`;
                            }
                        } catch (error) {
                            skeletonLoader.style.display = 'none';
                            if (statusInfo) statusInfo.textContent = 'Error';
                            summaryBody.innerHTML = `<div class="alert alert-danger mb-0">Connection failed. Please try again.</div>`;
                        }
                    });
                }

                function escapeHtml(text) {
                    return text
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }
            });
            </script>

            <div class="article-body" style="font-size: 1.1rem; line-height: 1.8;">
                <?= nl2br(htmlspecialchars($article['content'])) ?>
            </div>

            <?php if (!empty($tags)): ?>
                <div class="mt-4 pt-3 border-top">
                    <h5 class="mb-3">Tags:</h5>
                    <?php foreach ($tags as $tag): ?>
                        <span class="badge badge-light border mr-1 p-2"><?= htmlspecialchars($tag['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <!-- Comments Section -->
        <div class="bg-white p-4 rounded shadow-sm mb-4" id="comments">
            <h4 class="border-bottom pb-2 mb-4">Comments (<?= count($comments) ?>)</h4>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="<?= \App\Core\App::get('config')['url'] ?>/comment" method="POST" class="mb-4">
                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                    <div class="form-group">
                        <textarea class="form-control" name="content" rows="3" placeholder="Leave a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Post Comment</button>
                </form>
            <?php else: ?>
                <div class="alert alert-light border mb-4">
                    Please <a href="<?= \App\Core\App::get('config')['url'] ?>/login" class="text-danger font-weight-bold">login</a> to leave a comment.
                </div>
            <?php endif; ?>

            <div class="comments-list">
                <?php if (empty($comments)): ?>
                    <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="media mb-4 pb-3 border-bottom">
                            <?php if (!empty($comment['user_avatar'])): ?>
                                <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($comment['user_avatar'], '/') ?>" class="mr-3 rounded-circle" width="45" height="45" alt="User">
                            <?php else: ?>
                                <div class="mr-3 bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 45px; height: 45px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                            <div class="media-body">
                                <h6 class="mt-0 font-weight-bold"><?= htmlspecialchars($comment['user_name']) ?> <small class="text-muted ml-2"><?= date('M j, Y, g:i a', strtotime($comment['created_at'])) ?></small></h6>
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Related Articles -->
        <?php if (!empty($related)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="font-weight-bold border-bottom pb-2">Related News</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($related as $rel): ?>
                        <div class="media mb-3 pb-3 border-bottom">
                            <?php if ($rel['image_url']): ?>
                                <img src="<?= \App\Core\App::get('config')['url'] . '/' . ltrim($rel['image_url'], '/') ?>" class="mr-3 rounded" width="80" height="60" style="object-fit: cover;" alt="...">
                            <?php endif; ?>
                            <div class="media-body">
                                <h6 class="mt-0 mb-1" style="font-size: 0.95rem; line-height: 1.3;">
                                    <a href="<?= \App\Core\App::get('config')['url'] ?>/article?id=<?= $rel['id'] ?>" class="text-dark font-weight-bold text-decoration-none">
                                        <?= htmlspecialchars($rel['title']) ?>
                                    </a>
                                </h6>
                                <small class="text-muted"><?= date('M j, Y', strtotime($rel['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Recent News -->
        <?php if (!empty($recent_news)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="font-weight-bold border-bottom pb-2">Recent Updates</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recent_news as $news): ?>
                            <li class="mb-3 pb-2 border-bottom">
                                <span class="text-danger small font-weight-bold text-uppercase d-block mb-1"><?= htmlspecialchars($news['category_name']) ?></span>
                                <a href="<?= \App\Core\App::get('config')['url'] ?>/article?id=<?= $news['id'] ?>" class="text-dark font-weight-bold text-decoration-none d-block mb-1" style="line-height: 1.3;">
                                    <?= htmlspecialchars($news['title']) ?>
                                </a>
                                <small class="text-muted"><i class="far fa-clock"></i> <?= date('g:i a, M j', strtotime($news['created_at'])) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
