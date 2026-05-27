<button class="chatbot-fab" type="button" id="chatbotFab" aria-label="Open chatbot">
  <i class="ri-chat-3-line" style="font-size:22px;"></i>
</button>

<div class="chatbot-panel" id="chatbotPanel" style="display:none;">
  <div class="chatbot-header">
    <div>
      <div class="title fw-bold">ZMC Assistant</div>
      <div class="sub text-muted" style="font-size:12px;">Media Practitioner Accreditation Help</div>
    </div>
    <button type="button" class="btn btn-sm btn-light" id="chatbotClose">
      <i class="ri-close-line"></i>
    </button>
  </div>
  <div class="chatbot-body" id="chatbotBody">
    <div class="chat-message bot">
      <div class="message-content">
        Hello! I'm the ZMC Assistant. I can help you with:
        <ul class="mt-2 mb-0 ps-3">
          <li>Media practitioner accreditation process</li>
          <li>Required documents</li>
          <li>Fees and payments</li>
          <li>Office locations</li>
          <li>Application tracking</li>
        </ul>
      </div>
    </div>
  </div>
  <div class="chatbot-footer">
    <input type="text" id="chatbotInput" placeholder="Type your question..." />
    <button type="button" id="chatbotSend"><i class="ri-send-plane-2-line"></i></button>
  </div>
</div>

<style>
.chatbot-fab {
  position: fixed;
  bottom: 24px;
  right: 24px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #1a5f2a 0%, #2d8a3e 100%);
  color: white;
  border: none;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  cursor: pointer;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s, box-shadow 0.2s;
}
.chatbot-fab:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 16px rgba(0,0,0,0.25);
}
.chatbot-panel {
  position: fixed;
  bottom: 90px;
  right: 24px;
  width: 380px;
  max-width: calc(100vw - 48px);
  height: 500px;
  max-height: calc(100vh - 120px);
  background: white;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
  z-index: 1001;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.chatbot-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: linear-gradient(135deg, #1a5f2a 0%, #2d8a3e 100%);
  color: white;
}
.chatbot-header .title { font-size: 16px; }
.chatbot-header .btn { color: white; }
.chatbot-body {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background: #f8f9fa;
}
.chat-message {
  margin-bottom: 12px;
  max-width: 90%;
}
.chat-message.bot { margin-right: auto; }
.chat-message.user { margin-left: auto; }
.chat-message .message-content {
  padding: 12px 16px;
  border-radius: 16px;
  font-size: 14px;
  line-height: 1.5;
}
.chat-message.bot .message-content {
  background: white;
  border: 1px solid #e5e7eb;
  border-bottom-left-radius: 4px;
}
.chat-message.user .message-content {
  background: #1a5f2a;
  color: white;
  border-bottom-right-radius: 4px;
}
.chatbot-footer {
  display: flex;
  gap: 8px;
  padding: 12px 16px;
  background: white;
  border-top: 1px solid #e5e7eb;
}
.chatbot-footer input {
  flex: 1;
  padding: 10px 14px;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  font-size: 14px;
  outline: none;
}
.chatbot-footer input:focus { border-color: #1a5f2a; }
.chatbot-footer button {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: #1a5f2a;
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.chatbot-footer button:hover { background: #2d8a3e; }
.typing-indicator {
  display: flex;
  gap: 4px;
  padding: 12px 16px;
}
.typing-indicator span {
  width: 8px;
  height: 8px;
  background: #9ca3af;
  border-radius: 50%;
  animation: typing 1.4s infinite;
}
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing {
  0%, 60%, 100% { transform: translateY(0); }
  30% { transform: translateY(-4px); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const fab = document.getElementById('chatbotFab');
  const panel = document.getElementById('chatbotPanel');
  const closeBtn = document.getElementById('chatbotClose');
  const input = document.getElementById('chatbotInput');
  const sendBtn = document.getElementById('chatbotSend');
  const body = document.getElementById('chatbotBody');
  const csrfToken = '{{ csrf_token() }}';

  fab.addEventListener('click', () => {
    panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
    if (panel.style.display === 'flex') input.focus();
  });

  closeBtn.addEventListener('click', () => {
    panel.style.display = 'none';
  });

  function formatResponse(text) {
    return text
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\n/g, '<br>')
      .replace(/- /g, '&bull; ');
  }

  function addMessage(content, isUser = false) {
    const msg = document.createElement('div');
    msg.className = `chat-message ${isUser ? 'user' : 'bot'}`;
    msg.innerHTML = `<div class="message-content">${isUser ? content : formatResponse(content)}</div>`;
    body.appendChild(msg);
    body.scrollTop = body.scrollHeight;
  }

  function showTyping() {
    const typing = document.createElement('div');
    typing.className = 'chat-message bot';
    typing.id = 'typingIndicator';
    typing.innerHTML = `<div class="message-content typing-indicator"><span></span><span></span><span></span></div>`;
    body.appendChild(typing);
    body.scrollTop = body.scrollHeight;
  }

  function hideTyping() {
    const typing = document.getElementById('typingIndicator');
    if (typing) typing.remove();
  }

  async function sendMessage() {
    const message = input.value.trim();
    if (!message) return;

    addMessage(message, true);
    input.value = '';
    showTyping();

    try {
      const response = await fetch('{{ route("chatbot.message") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ message }),
      });

      const data = await response.json();
      hideTyping();
      addMessage(data.response);
    } catch (error) {
      hideTyping();
      addMessage("I'm having trouble connecting right now. Please try again or email zmcaccreditation@gmail.com for assistance.");
    }
  }

  sendBtn.addEventListener('click', sendMessage);
  input.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
  });
});
</script>
