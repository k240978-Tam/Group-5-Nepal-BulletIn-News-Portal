<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="font-weight-bold" style="font-family: var(--font-serif, Georgia); font-size: 2.5rem;">
                <i class="fas fa-bolt text-danger"></i> Nepal Bulletin
            </h1>
            <p class="text-muted lead">Your AI-powered news digest — read out loud or read at a glance.</p>
            <hr>
        </div>
    </div>

    <!-- Period Toggle Buttons -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger period-btn active" data-period="today">
                    <i class="fas fa-sun"></i> Today
                </button>
                <button type="button" class="btn btn-outline-danger period-btn" data-period="weekly">
                    <i class="fas fa-calendar-week"></i> This Week
                </button>
                <button type="button" class="btn btn-outline-danger period-btn" data-period="monthly">
                    <i class="fas fa-calendar-alt"></i> This Month
                </button>
            </div>
        </div>
    </div>

    <!-- Bulletin Output Area -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0" style="border-radius: 12px; overflow: hidden;">

                <!-- Card Header with actions -->
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
                    <span id="bulletin-title" class="font-weight-bold" style="font-size: 1.1rem;">
                        <i class="fas fa-newspaper mr-2"></i> Today's Bulletin
                    </span>
                    <div>
                        <button id="refresh-btn" class="btn btn-sm btn-light text-danger mr-2" title="Regenerate">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button id="listen-btn" class="btn btn-sm btn-light text-danger" title="Listen" style="display:none;">
                            <i class="fas fa-volume-up"></i> Listen
                        </button>
                    </div>
                </div>

                <!-- Skeleton Loader -->
                <div id="bulletin-loading" class="card-body p-4">
                    <div class="skeleton-line" style="height: 14px; width: 90%; background: #e2e8f0; border-radius: 6px; margin-bottom: 12px; animation: pulse 1.5s infinite;"></div>
                    <div class="skeleton-line" style="height: 14px; width: 80%; background: #e2e8f0; border-radius: 6px; margin-bottom: 12px; animation: pulse 1.5s infinite .1s;"></div>
                    <div class="skeleton-line" style="height: 14px; width: 95%; background: #e2e8f0; border-radius: 6px; margin-bottom: 12px; animation: pulse 1.5s infinite .2s;"></div>
                    <div class="skeleton-line" style="height: 14px; width: 70%; background: #e2e8f0; border-radius: 6px; margin-bottom: 12px; animation: pulse 1.5s infinite .3s;"></div>
                    <div class="skeleton-line" style="height: 14px; width: 85%; background: #e2e8f0; border-radius: 6px; margin-bottom: 12px; animation: pulse 1.5s infinite .4s;"></div>
                    <div class="skeleton-line" style="height: 14px; width: 60%; background: #e2e8f0; border-radius: 6px; animation: pulse 1.5s infinite .5s;"></div>
                    <p class="text-muted text-center mt-4" style="font-size: 13px;"><i class="fas fa-robot mr-1"></i> AI is preparing your bulletin...</p>
                </div>

                <!-- Bulletin Content -->
                <div id="bulletin-content" class="card-body p-4" style="display: none; font-size: 1.1rem; line-height: 1.9; color: #2d3748;"></div>

                <!-- Error Area -->
                <div id="bulletin-error" class="card-body p-4 text-center" style="display: none;">
                    <i class="fas fa-exclamation-circle text-danger fa-3x mb-3"></i>
                    <p class="text-danger font-weight-bold" id="bulletin-error-msg">Something went wrong.</p>
                    <button class="btn btn-sm btn-outline-danger" onclick="generateBulletin()">Try Again</button>
                </div>

            </div>

            <!-- Timestamp -->
            <p class="text-muted text-center mt-3" style="font-size: 12px;">
                <i class="fas fa-clock"></i> Generated at <span id="gen-time"></span>
            </p>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
.period-btn.active {
    background-color: #c0392b !important;
    border-color: #c0392b !important;
    color: white !important;
}
</style>

<script>
let currentPeriod = 'today';
let currentBulletinText = '';
let isSpeaking = false;

const titles = {
    today: "<i class='fas fa-sun mr-2'></i> Today's Bulletin",
    weekly: "<i class='fas fa-calendar-week mr-2'></i> Weekly Bulletin",
    monthly: "<i class='fas fa-calendar-alt mr-2'></i> Monthly Bulletin"
};

function showLoading() {
    document.getElementById('bulletin-loading').style.display = 'block';
    document.getElementById('bulletin-content').style.display = 'none';
    document.getElementById('bulletin-error').style.display = 'none';
    document.getElementById('listen-btn').style.display = 'none';
    window.speechSynthesis.cancel();
    isSpeaking = false;
    document.getElementById('listen-btn').innerHTML = '<i class="fas fa-volume-up"></i> Listen';
}

function showContent(text) {
    currentBulletinText = text;
    // Format paragraphs
    let formatted = text.split('\n').filter(p => p.trim() !== '').map(p => `<p>${p}</p>`).join('');
    document.getElementById('bulletin-content').innerHTML = formatted;
    document.getElementById('bulletin-loading').style.display = 'none';
    document.getElementById('bulletin-content').style.display = 'block';
    document.getElementById('listen-btn').style.display = 'inline-block';
    document.getElementById('gen-time').textContent = new Date().toLocaleTimeString();
}

function showError(msg) {
    document.getElementById('bulletin-error-msg').textContent = msg;
    document.getElementById('bulletin-loading').style.display = 'none';
    document.getElementById('bulletin-error').style.display = 'block';
}

async function generateBulletin() {
    showLoading();
    document.getElementById('bulletin-title').innerHTML = titles[currentPeriod];

    try {
        const response = await fetch('/newsportal/api/bulletin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ period: currentPeriod })
        });
        const data = await response.json();

        if (data.bulletin) {
            showContent(data.bulletin);
        } else if (data.error) {
            showError(data.error);
        }
    } catch (err) {
        showError('Network error. Please check your connection and try again.');
    }
}

// Period toggle buttons
document.querySelectorAll('.period-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.period-btn').forEach(b => {
            b.classList.remove('active', 'btn-danger');
            b.classList.add('btn-outline-danger');
        });
        this.classList.add('active', 'btn-danger');
        this.classList.remove('btn-outline-danger');
        currentPeriod = this.getAttribute('data-period');
        generateBulletin();
    });
});

// Refresh button
document.getElementById('refresh-btn').addEventListener('click', generateBulletin);

// Listen / TTS button
document.getElementById('listen-btn').addEventListener('click', function() {
    if (!('speechSynthesis' in window)) {
        alert("Sorry, your browser doesn't support text-to-speech.");
        return;
    }

    if (isSpeaking) {
        window.speechSynthesis.cancel();
        isSpeaking = false;
        this.innerHTML = '<i class="fas fa-volume-up"></i> Listen';
        return;
    }

    const utterance = new SpeechSynthesisUtterance(currentBulletinText);
    utterance.lang = 'en-US';
    utterance.rate = 0.95; // Slightly slower for broadcast feel

    this.innerHTML = '<i class="fas fa-stop-circle"></i> Stop';
    isSpeaking = true;

    utterance.onend = () => {
        isSpeaking = false;
        this.innerHTML = '<i class="fas fa-volume-up"></i> Listen';
    };
    utterance.onerror = () => {
        isSpeaking = false;
        this.innerHTML = '<i class="fas fa-volume-up"></i> Listen';
    };

    window.speechSynthesis.speak(utterance);
});

// Auto-generate on page load
document.addEventListener('DOMContentLoaded', generateBulletin);
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
