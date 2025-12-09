<div class="container mt-4">
  <h4>Chatbot Bantuan Sistem Kegiatan</h4>
  <div id="chat-box" class="border p-3 rounded" style="height:300px; overflow-y:auto;"></div>

  <div class="input-group mt-2">
    <input type="text" id="inputMessage" class="form-control" placeholder="Ketik pertanyaan...">
    <button class="btn btn-primary" id="sendBtn">Kirim</button>
  </div>
</div>

<script>
document.getElementById("sendBtn").addEventListener("click", async () => {
  const message = document.getElementById("inputMessage").value;
  appendMessage("user", message);

  const res = await fetch("/chatbot/ask", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      message
    })
  });

  const data = await res.json();
  appendMessage("bot", data.answer);
});

function appendMessage(sender, text) {
  const box = document.getElementById("chat-box");
  box.innerHTML += `<p><strong>${sender === "user" ? "Anda:" : "Bot:"}</strong> ${text}</p>`;
  box.scrollTop = box.scrollHeight;
}
</script>