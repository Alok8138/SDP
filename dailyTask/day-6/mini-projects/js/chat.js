const channel = new BroadcastChannel("chat_app_channel");
const userNameSpan = document.getElementById("currentUser");
const logoutBtn = document.getElementById("logoutBtn");
const messagesDiv = document.getElementById("messages");
const messageInput = document.getElementById("messageInput");
const sendBtn = document.getElementById("sendBtn");

// 🔒 Route Protection
const user = getCurrentUser();
if (!user) {
  window.location.href = "index.html";
}

state.user = user;
state.messages = getMessages();

userNameSpan.textContent = `Logged in as: ${state.user.name}`;

// Render messages
function renderMessages() {
  messagesDiv.innerHTML = "";
  console.log(state);
  
  state.messages.forEach(msg => {
    const msgDiv = document.createElement("div");
    msgDiv.className = msg.sender === state.user.name ? "message own" : "message";
    msgDiv.innerHTML = `
      <strong>${msg.sender}</strong>
      <p>${msg.text}</p>
      <span class="time">${msg.time}</span>
    `;
    messagesDiv.appendChild(msgDiv);
  });

  messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

// Send message
sendBtn.addEventListener("click", sendMessage);


messageInput.addEventListener("keypress", e => {
  if (e.key === "Enter") sendMessage();
});

function sendMessage() {
  const text = messageInput.value.trim();
  if (text === "") return;

  const message = {
    id: Date.now(),
    sender: state.user.name,
    text,
    time: new Date().toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit"
    })
  };

  state.messages.push(message);
  saveMessages(state.messages);

  channel.postMessage(message);


  renderMessages();
  messageInput.value = "";
}

// Initial load
renderMessages();

channel.onmessage = (event) => {
  console.log("Received message:", event);
  const incomingMsg = event.data;

  const exists = state.messages.some(msg => msg.id === incomingMsg.id);
  if (exists) return;

  state.messages.push(incomingMsg);
  saveMessages(state.messages);
  renderMessages();
};


// Logout
logoutBtn.addEventListener("click", () => {
  clearCurrentUser();
  window.location.href = "index.html";
});
