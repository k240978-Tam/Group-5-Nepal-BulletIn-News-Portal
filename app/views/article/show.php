<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mt-4">
    <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1">
        <?php if (!empty($article['category_name'])): ?>
            <a href="/newsportal/category?name=<?= urlencode($article['category_name']) ?>" class="badge badge-danger mb-3 p-2"><?= htmlspecialchars($article['category_name']) ?></a>
        <?php endif; ?>
        
        <h1 class="mb-3 font-weight-bold" style="font-size: 2.5rem; line-height: 1.2;"><?= htmlspecialchars($article['title']) ?></h1>
        
        <div class="text-muted mb-4 d-flex align-items-center">
            <i class="fas fa-user-circle fa-2x mr-2"></i>
            <div>
                <strong><?= htmlspecialchars($article['author_name'] ?? 'Unknown') ?></strong><br>
                <small><i class="far fa-calendar-alt"></i> <?= date('F j, Y, g:i a', strtotime($article['created_at'])) ?> &nbsp;&bull;&nbsp; <i class="fas fa-eye"></i> <?= $article['views'] + 1 ?> views</small>
            </div>
        </div>
        
        <?php if (!empty($article['image_url'])): ?>
            <img src="<?= htmlspecialchars($article['image_url']) ?>" class="img-fluid rounded mb-5 shadow-sm w-100" alt="<?= htmlspecialchars($article['title']) ?>">
        <?php endif; ?>
        
        <!-- AI Summarizer -->
        <div class="mb-4 bg-light p-3 rounded border">
            <div class="d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-danger"><i class="fas fa-magic"></i> AI Summary</span>
                <button id="summarize-btn" class="btn btn-sm btn-outline-danger" data-id="<?= $article_id ?>">Generate Summary</button>
            </div>
            <div id="summary-result" class="mt-3" style="display: none; font-size: 15px;"></div>
        </div>

        <div class="article-content mb-5" style="font-size: 1.15rem; line-height: 1.8;">
            <?= nl2br(htmlspecialchars($article['content'])) ?>
        </div>

        <hr class="my-5">
        
        <!-- Comments Section -->
        <div class="comments-section">
            <h4 class="mb-4"><i class="far fa-comments"></i> Comments (<?= count($comments) ?>)</h4>
            
            <div class="card mb-5 border-0 bg-light">
                <div class="card-body">
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="/newsportal/article/comment" method="POST">
                        <input type="hidden" name="article_id" value="<?= htmlspecialchars($article_id) ?>">
                        <div class="form-group">
                            <label for="comment" class="font-weight-bold">Leave a Comment</label>
                            <textarea class="form-control" name="comment" id="comment" rows="3" required placeholder="What are your thoughts?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger px-4">Post Comment</button>
                    </form>
                    <?php else: ?>
                    <p class="mb-0">Please <a href="/newsportal/login" class="font-weight-bold text-danger">log in</a> to join the discussion.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Existing Comments -->
            <div class="comment-list">
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="media mb-4 pb-4 border-bottom">
                            <div class="mr-3 text-secondary">
                                <i class="fas fa-user-circle fa-3x"></i>
                            </div>
                            <div class="media-body">
                                <h5 class="mt-0 font-weight-bold mb-1"><?= htmlspecialchars($comment['user_name']) ?></h5>
                                <small class="text-muted mb-2 d-block"><?= time_elapsed_string($comment['created_at']) ?></small>
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('summarize-btn');
    const resultDiv = document.getElementById('summary-result');
    
    if (btn) {
        btn.addEventListener('click', async function() {
            const articleId = this.getAttribute('data-id');
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<span class="text-muted">Analyzing article content...</span>';

            try {
                const response = await fetch('/newsportal/api/summarize', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ article_id: articleId })
                });
                const data = await response.json();
                if (data.summary) {
                    // Convert markdown bullet points to HTML
                    let formatted = data.summary.replace(/\n\*/g, '<br>•').replace(/\n-/g, '<br>•');
                    
                    let html = '<div class="d-flex justify-content-between align-items-center mb-2">';
                    html += '<strong>Key Takeaways:</strong>';
                    html += '<button id="play-summary-btn" class="btn btn-sm btn-light text-danger border"><i class="fas fa-volume-up"></i> Listen</button>';
                    html += '</div>';
                    html += '<div>' + formatted + '</div>';
                    
                    resultDiv.innerHTML = html;
                    btn.innerHTML = 'Summary Complete';

                    // Attach Speech API listener
                    const playBtn = document.getElementById('play-summary-btn');
                    if (playBtn) {
                        playBtn.addEventListener('click', function() {
                            if ('speechSynthesis' in window) {
                                // Stop any currently playing audio
                                window.speechSynthesis.cancel();
                                
                                // Clean up the text for reading (remove markdown asterisks)
                                let cleanText = data.summary.replace(/\*/g, '').replace(/-/g, '');
                                
                                let utterance = new SpeechSynthesisUtterance(cleanText);
                                utterance.lang = 'en-US';
                                
                                // Handle button state
                                playBtn.innerHTML = '<i class="fas fa-volume-up fa-beat"></i> Playing...';
                                utterance.onend = function() {
                                    playBtn.innerHTML = '<i class="fas fa-volume-up"></i> Listen';
                                };
                                
                                window.speechSynthesis.speak(utterance);
                            } else {
                                alert("Sorry, your browser doesn't support text to speech!");
                            }
                        });
                    }
                } else if (data.error) {
                    resultDiv.innerHTML = '<span class="text-danger">' + data.error + '</span>';
                    btn.disabled = false;
                    btn.innerHTML = 'Try Again';
                }
            } catch (err) {
                resultDiv.innerHTML = '<span class="text-danger">Network error occurred.</span>';
                btn.disabled = false;
                btn.innerHTML = 'Try Again';
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
