<button class="chatbot-fab" id="chatbotFab" aria-label="Open chatbot">
  <i class="ri-customer-service-2-line"></i>
</button>

<div class="chatbot-panel" id="chatbotPanel" style="display:none;">
  <div class="chatbot-header">
    <div>
      <div class="title">ZMC Assistant</div>
      <div class="sub">Ask about accreditation, registration, payments, tracking</div>
    </div>
    <button class="btn btn-secondary" style="padding:6px 10px;border-radius:10px;" id="chatbotClose">Close</button>
  </div>

  <div class="chatbot-body" id="chatbotBody">
    <div class="bot-msg">
      Hi! I can help with common questions. Try one of these:
    </div>
    <div class="faq-chips" id="chatbotFaq">
      <button type="button" class="faq-chip" data-q="How do I get accredited as a media practitioner?">Media Practitioner Accreditation</button>
      <button type="button" class="faq-chip" data-q="How do I register a media house?">Media House Registration</button>
      <button type="button" class="faq-chip" data-q="What documents are required for accreditation?">Accreditation Documents</button>
      <button type="button" class="faq-chip" data-q="What documents are required for registration?">Registration Documents</button>
      <button type="button" class="faq-chip" data-q="How do I pay via Paynow?">Paynow Payments</button>
      <button type="button" class="faq-chip" data-q="How do I track my application status?">Track Application</button>
    </div>
  </div>

  <div class="chatbot-footer">
    <input id="chatbotInput" type="text" placeholder="Type your question..." />
    <button id="chatbotSend"><i class="ri-send-plane-2-line"></i></button>
  </div>
</div>

<style>
.chatbot-fab {
  position: fixed;
  right: 18px;
  bottom: 18px;
  width: 56px;
  height: 56px;
  border-radius: 999px;
  border: 0;
  background: #fbbf24;
  color: #111827;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 12px 30px rgba(0,0,0,.22);
  z-index: 2147483000;
}
.chatbot-fab i{ font-size: 24px; }

.chatbot-panel {
  position: fixed;
  right: 18px;
  bottom: 86px;
  width: 340px;
  max-width: calc(100vw - 36px);
  height: 480px;
  background: #ffffff;
  border: 1px solid rgba(15,23,42,.14);
  box-shadow: 0 18px 45px rgba(0,0,0,.25);
  border-radius: 14px;
  overflow: hidden;
  z-index: 2147483000;
}
.chatbot-header {
  background: #111827;
  color: #fff;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.chatbot-header .title { font-size: 15px; font-weight: 800; }
.chatbot-header .sub { font-size: 12px; opacity: .85; }
.chatbot-body {
  padding: 12px;
  height: calc(100% - 108px);
  overflow-y: auto;
  background: #f8fafc;
}
.chatbot-footer {
  height: 60px;
  display: flex;
  gap: 8px;
  align-items: center;
  padding: 10px;
  border-top: 1px solid rgba(15,23,42,.10);
  background: #fff;
}
.chatbot-footer input {
  flex: 1;
  border: 1px solid rgba(15,23,42,.18);
  border-radius: 12px;
  padding: 10px 12px;
  outline: none;
}
.chatbot-footer button {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  border: 0;
  background: #111827;
  color: #fff;
}

.bot-msg, .user-msg {
  font-size: 13px;
  line-height: 1.35;
  margin-bottom: 10px;
}
.user-msg { text-align: right; }

.faq-chips{ display:flex; flex-wrap:wrap; gap:8px; margin-top: 10px; }
.faq-chip{
  border: 1px solid rgba(15,23,42,.18);
  background:#fff;
  border-radius: 999px;
  padding: 6px 10px;
  font-size: 12px;
  font-weight: 800;
}
</style>

<script>
(function(){
  const fab = document.getElementById('chatbotFab');
  const panel = document.getElementById('chatbotPanel');
  const closeBtn = document.getElementById('chatbotClose');
  const input = document.getElementById('chatbotInput');
  const sendBtn = document.getElementById('chatbotSend');
  const body = document.getElementById('chatbotBody');

  function openChatbot(show){
    if(!panel) return;
    panel.style.display = show ? 'block' : 'none';
    if(show){ setTimeout(()=>input?.focus(), 50); }
  }

  function addMsg(text, who){
    const div = document.createElement('div');
    div.className = who === 'user' ? 'user-msg' : 'bot-msg';
    div.innerHTML = text.replace(/\n/g,'<br>');
    body.appendChild(div);
    body.scrollTop = body.scrollHeight;
  }

  async function send(msg){
    const m = (msg ?? input.value ?? '').trim();
    if(!m) return;
    addMsg(m, 'user');
    if(input) input.value = '';

    try{
      const res = await fetch('{{ route("chatbot.message") }}', {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':'{{ csrf_token() }}',
          'Accept':'application/json'
        },
        body: JSON.stringify({message: m})
      });
      const data = await res.json();

      if(data && data.response){
        addMsg(data.response, 'bot');
        if(data.matched === false){
          addMsg('If I missed it, you can email <b>zmcaccreditation@gmail.com</b>.', 'bot');
        }
      } else {
        addMsg('Sorry — I could not process that. Email <b>zmcaccreditation@gmail.com</b>.', 'bot');
      }

    }catch(e){
      addMsg('Network error. Please email <b>zmcaccreditation@gmail.com</b>.', 'bot');
    }
  }

  fab?.addEventListener('click', ()=> openChatbot(true));
  closeBtn?.addEventListener('click', ()=> openChatbot(false));
  sendBtn?.addEventListener('click', ()=> send());
  input?.addEventListener('keydown', (e)=>{ if(e.key==='Enter'){ e.preventDefault(); send(); } });

  document.getElementById('chatbotFaq')?.addEventListener('click', (e)=>{
    const btn = e.target.closest('.faq-chip');
    if(!btn) return;
    send(btn.getAttribute('data-q'));
  });
})();
</script>
