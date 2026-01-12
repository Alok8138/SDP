function getCurrentUser() {
  return JSON.parse(localStorage.getItem("currentUser"));
}

function clearCurrentUser() {
  localStorage.removeItem("currentUser");
}



function getMessages() {
  return JSON.parse(localStorage.getItem("messages")) || [];
}

function saveMessages(messages) {
  localStorage.setItem("messages", JSON.stringify(messages));
}
