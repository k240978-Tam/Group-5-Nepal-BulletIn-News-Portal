    </main>
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Nepal Bulletin</h4>
                    <p class="text-gray mb-2">Delivering the latest news, breaking stories, and insightful analysis from Nepal and around the world.</p>
                </div>
                <div class="footer-col">
                    <h4>Categories</h4>
                    <ul class="footer-links">
                        <li><a href="/newsportal/category?name=Politics">Politics</a></li>
                        <li><a href="/newsportal/category?name=Business">Business</a></li>
                        <li><a href="/newsportal/category?name=Sports">Sports</a></li>
                        <li><a href="/newsportal/category?name=Technology">Technology</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Subscribe</h4>
                    <p class="text-gray mb-2">Get daily news updates right to your inbox.</p>
                    <form style="display:flex; gap:0.5rem;">
                        <input type="email" class="form-control" placeholder="Email address" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; <?= date('Y') ?> Nepal Bulletin Board. Group 5 Project.
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 4.0 JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <!-- AI Chatbot Widget -->
    <button id="chatbot-toggle" class="btn btn-danger rounded-circle shadow" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; z-index: 1000; font-size: 24px;">
        <i class="fas fa-robot"></i>
    </button>

    <div id="chatbot-window" class="card shadow-lg" style="display: none; position: fixed; bottom: 90px; right: 20px; width: 350px; height: 450px; z-index: 1000; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 font-weight-bold" style="font-size:16px;"><i class="fas fa-robot mr-2"></i> AI Assistant</h5>
            <button id="chatbot-close" class="btn btn-sm text-white p-0" style="background: none; border: none; font-size: 20px;"><i class="fas fa-times"></i></button>
        </div>
        <div id="chatbot-messages" class="card-body bg-light" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
            <div class="p-2 rounded bg-white border" style="max-width: 85%; align-self: flex-start; font-size:14px;">
                Hello! I'm your AI news assistant. How can I help you today?
            </div>
        </div>
        <div class="card-footer bg-white p-2">
            <div class="input-group">
                <input type="text" id="chatbot-input" class="form-control" placeholder="Ask a question..." style="border-radius: 20px 0 0 20px;">
                <div class="input-group-append">
                    <button id="chatbot-send" class="btn btn-danger" style="border-radius: 0 20px 20px 0;"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('chatbot-toggle');
        const closeBtn = document.getElementById('chatbot-close');
        const chatWindow = document.getElementById('chatbot-window');
        const sendBtn = document.getElementById('chatbot-send');
        const chatInput = document.getElementById('chatbot-input');
        const messagesContainer = document.getElementById('chatbot-messages');

        toggleBtn.addEventListener('click', () => {
            chatWindow.style.display = chatWindow.style.display === 'none' ? 'flex' : 'none';
            chatWindow.style.flexDirection = 'column';
            if (chatWindow.style.display === 'flex') chatInput.focus();
        });

        closeBtn.addEventListener('click', () => chatWindow.style.display = 'none');

        function appendMessage(text, isUser = false) {
            const div = document.createElement('div');
            div.className = 'p-2 rounded border ' + (isUser ? 'bg-danger text-white' : 'bg-white');
            div.style.maxWidth = '85%';
            div.style.alignSelf = isUser ? 'flex-end' : 'flex-start';
            div.style.fontSize = '14px';
            div.textContent = text;
            messagesContainer.appendChild(div);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            return div;
        }

        async function sendMessage() {
            const text = chatInput.value.trim();
            if (!text) return;

            appendMessage(text, true);
            chatInput.value = '';

            const loading = appendMessage('Typing...', false);

            try {
                const response = await fetch('/newsportal/api/chat', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ message: text })
                });
                const data = await response.json();
                
                loading.remove();
                if (data.reply) {
                    appendMessage(data.reply, false);
                } else if (data.error) {
                    appendMessage(data.error, false);
                }
            } catch (err) {
                loading.remove();
                appendMessage('Connection error.', false);
            }
        }

        sendBtn.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });
        
        // Dark Mode Toggle Handler
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeKey = 'nepal-bulletin-dark-mode';
        
        function updateDarkModeIcon() {
            const isDark = document.documentElement.classList.contains('dark-mode');
            darkModeToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        }
        
        if (darkModeToggle) {
            updateDarkModeIcon();
            darkModeToggle.addEventListener('click', function() {
                const isDarkMode = document.documentElement.classList.toggle('dark-mode');
                document.body.classList.toggle('dark-mode');
                localStorage.setItem(darkModeKey, isDarkMode);
                updateDarkModeIcon();
            });
        }
    });
    </script>
</body>
</html>
