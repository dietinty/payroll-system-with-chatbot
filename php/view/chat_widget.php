<!-- 
  Chat widget: include this file at the bottom of any employee dashboard page,
  right before </body>. Example:

      <?php include '../view/chat_widget.php'; ?>
      </body>
      </html>

  Adjust the fetch() URL below if chatbot.php lives somewhere other than
  the same folder as this widget.
-->

<style>
#chat-toggle-btn {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #2563eb;
    color: #fff;
    border: none;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    z-index: 9999;
}

#chat-window {
    position: fixed;
    bottom: 92px;
    right: 24px;
    width: 320px;
    max-height: 440px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 9999;
    font-family: Arial, sans-serif;
}

#chat-window.open {
    display: flex;
}

#chat-header {
    background: #2563eb;
    color: #fff;
    padding: 12px 16px;
    font-weight: bold;
}

#chat-messages {
    flex: 1;
    padding: 12px;
    overflow-y: auto;
    font-size: 14px;
    background: #f7f8fa;
}

.chat-msg {
    margin-bottom: 10px;
    padding: 8px 12px;
    border-radius: 10px;
    max-width: 85%;
    line-height: 1.4;
}

.chat-msg.user {
    background: #2563eb;
    color: #fff;
    margin-left: auto;
}

.chat-msg.bot {
    background: #e5e7eb;
    color: #111;
    margin-right: auto;
}

#chat-input-row {
    display: flex;
    border-top: 1px solid #e5e7eb;
}

#chat-input {
    flex: 1;
    border: none;
    padding: 10px 12px;
    font-size: 14px;
    outline: none;
}

#chat-send-btn {
    border: none;
    background: #2563eb;
    color: #fff;
    padding: 0 16px;
    cursor: pointer;
}
</style>

<button id="chat-toggle-btn">💬</button>

<div id="chat-window">
    <div id="chat-header">Payroll Assistant</div>
    <div id="chat-messages">
        <div class="chat-msg bot">Hi! Ask me about your salary, pay date, or incentives.</div>
    </div>
    <div id="chat-input-row">
        <input type="text" id="chat-input" placeholder="Type a message...">
        <button id="chat-send-btn">Send</button>
    </div>
</div>

<script>
(function() {
    const toggleBtn = document.getElementById('chat-toggle-btn');
    const chatWindow = document.getElementById('chat-window');
    const messagesEl = document.getElementById('chat-messages');
    const inputEl = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send-btn');

    toggleBtn.addEventListener('click', () => {
        chatWindow.classList.toggle('open');
    });

    function addMessage(text, sender) {
        const div = document.createElement('div');
        div.className = 'chat-msg ' + sender;
        div.textContent = text;
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    async function sendMessage() {
        const text = inputEl.value.trim();
        if (!text) return;

        addMessage(text, 'user');
        inputEl.value = '';

        try {
            // Adjust this path if chatbot.php is located elsewhere relative to this page
            const res = await fetch('chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: text })
            });
            const data = await res.json();
            addMessage(data.reply, 'bot');
        } catch (err) {
            addMessage("Sorry, I couldn't reach the server. Please try again.", 'bot');
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    inputEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
})();
</script>
