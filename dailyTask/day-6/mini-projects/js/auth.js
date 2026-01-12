const loginBtn = document.getElementById("loginBtn");
const usernameInput = document.getElementById("username");
const errorText = document.getElementById("error");

loginBtn.addEventListener("click", () => {
  const username = usernameInput.value.trim();

  if (username === "") {
    errorText.textContent = "Username is required";
    return;
  }

  const user = {
    id: Date.now(),
    name: username
  };

  localStorage.setItem("currentUser", JSON.stringify(user));
  window.location.href = "chat.html";
});
