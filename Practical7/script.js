function updateMessage() {
    var taskList = document.getElementById("taskList");
    var message = document.getElementById("message");

    if (taskList.children.length === 0) {
        message.textContent = "No tasks added yet.";
    } else {
        message.textContent = "Manage your tasks below.";
    }
}

function addTask() {
    var taskInput = document.getElementById("taskInput");
    var taskTextValue = taskInput.value.trim();
    var taskList = document.getElementById("taskList");

    if (taskTextValue === "") {
        document.getElementById("message").textContent = "Please enter a task before adding.";
        return;
    }

    var listItem = document.createElement("li");
    listItem.className = "task-item";

    var taskLeft = document.createElement("div");
    taskLeft.className = "task-left";

    var checkbox = document.createElement("input");
    checkbox.type = "checkbox";

    var taskText = document.createElement("span");
    taskText.className = "task-text";
    taskText.textContent = taskTextValue;

    checkbox.onclick = function () {
        if (checkbox.checked) {
            taskText.classList.add("completed");
        } else {
            taskText.classList.remove("completed");
        }
    };

    var removeButton = document.createElement("button");
    removeButton.type = "button";
    removeButton.className = "remove-button";
    removeButton.textContent = "Remove";
    removeButton.onclick = function () {
        taskList.removeChild(listItem);
        updateMessage();
    };

    taskLeft.appendChild(checkbox);
    taskLeft.appendChild(taskText);
    listItem.appendChild(taskLeft);
    listItem.appendChild(removeButton);
    taskList.appendChild(listItem);

    taskInput.value = "";
    taskInput.focus();
    updateMessage();
}

document.getElementById("taskInput").addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
        addTask();
    }
});