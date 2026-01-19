const taskInput = document.querySelector("#taskInput");
const employeeInput = document.querySelector("#employeeInput");
const addTaskBtn = document.querySelector("#addTaskBtn");
const taskCount = document.querySelector("#taskCount");

const columns = document.querySelectorAll(".column");

let totalTasks = 0;

// Add task
addTaskBtn.addEventListener("click", () => {
  const taskTitle = taskInput.value.trim();
  const employee = employeeInput.value.trim();

  if (!taskTitle || !employee) return;

  const task = document.createElement("div");
  task.className = "task pending";
  task.dataset.status = "pending";

  task.innerHTML = `
    <strong>${taskTitle}</strong>
    <p>🥷 ${employee}</p>
    <button data-action="next">Next</button>
    <button data-action="delete">Delete</button>
  `;

  columns[0].appendChild(task);

  totalTasks++;
  updateCount();

  taskInput.value = "";
  employeeInput.value = "";
});

// Event for task actions
document.addEventListener("click", (e) => {
    const action = e.target.dataset.action;
    console.log(action);
    
  if (!action) return;

    const task = e.target.closest(".task");

    console.log(task);
    

  if (action === "delete") {
    task.remove();
    totalTasks--;
  }

  if (action === "next") {
    moveTask(task);
  }

  updateCount();
});

// Move task to next column
function moveTask(task) {
  const currentStatus = task.dataset.status;

  if (currentStatus === "pending") {
    task.dataset.status = "progress";
    task.className = "task progress";
    columns[1].appendChild(task);
  } else if (currentStatus === "progress") {
    task.dataset.status = "done";
    task.className = "task done";
    columns[2].appendChild(task);
    task.querySelector('[data-action="next"]').remove();
  }
}

// Update task count
function updateCount() {
  taskCount.innerText = totalTasks;
}
