document.addEventListener("DOMContentLoaded", function () {
    var statusSelects = document.querySelectorAll(".task-status-select");

    statusSelects.forEach(function (select) {
        select.addEventListener("change", function () {
            var taskId = this.getAttribute("data-task-id");
            var newStatus = this.value;
            var messageBox = document.getElementById("task-status-message-" + taskId);

            messageBox.innerText = "Updating...";

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Controller/update_task_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);

                        if (response.success) {
                            messageBox.style.color = "green";
                            messageBox.innerText = response.message;
                        } else {
                            messageBox.style.color = "red";
                            messageBox.innerText = response.message;
                        }
                    } catch (e) {
                        messageBox.style.color = "red";
                        messageBox.innerText = "Invalid server response.";
                    }
                } else {
                    messageBox.style.color = "red";
                    messageBox.innerText = "Request failed.";
                }
            };

            xhr.send("task_id=" + encodeURIComponent(taskId) + "&status=" + encodeURIComponent(newStatus));
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var adminUserSearch = document.getElementById("adminUserSearch");
    var adminUsersTableBody = document.getElementById("adminUsersTableBody");
    var adminUserSearchMessage = document.getElementById("adminUserSearchMessage");

    if (adminUserSearch && adminUsersTableBody) {
        adminUserSearch.addEventListener("keyup", function () {
            var searchValue = adminUserSearch.value.trim();

            adminUserSearchMessage.innerText = "Searching...";

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../Controller/search_users.php?search=" + encodeURIComponent(searchValue), true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);

                        if (response.success) {
                            adminUsersTableBody.innerHTML = "";

                            if (response.users.length > 0) {
                                response.users.forEach(function (user) {
                                    var statusText = user.is_active == 1 ? "Active" : "Inactive";

                                    var activeSelected = user.is_active == 1 ? "selected" : "";
                                    var inactiveSelected = user.is_active == 0 ? "selected" : "";

                                    var memberSelected = user.role === "member" ? "selected" : "";
                                    var leadSelected = user.role === "team_lead" ? "selected" : "";
                                    var clientSelected = user.role === "client" ? "selected" : "";
                                    var adminSelected = user.role === "admin" ? "selected" : "";

                                    var companyName = user.company_name ? user.company_name : "";
                                    var phone = user.phone ? user.phone : "";

                                    var profilePicCell = "No Image";

                                    if (user.profile_pic && user.profile_pic !== "") {
                                        profilePicCell = `<img src="../../${escapeHtml(user.profile_pic)}" alt="Profile Picture" width="50" height="50">`;
                                    }

                                    var row = `
                                        <tr>
                                            <td>${escapeHtml(user.id)}</td>
                                            <td>${profilePicCell}</td>
                                            <td>${escapeHtml(user.name)}</td>
                                            <td>${escapeHtml(user.email)}</td>
                                            <td>${escapeHtml(phone)}</td>
                                            <td>${escapeHtml(companyName)}</td>
                                            <td>${escapeHtml(user.role)}</td>
                                            <td>${statusText}</td>
                                            <td>${escapeHtml(user.created_at)}</td>

                                            <td>
                                                <form action="../../controllers/admin_user_controller.php" method="POST">
                                                    <input type="hidden" name="action" value="change_status">
                                                    <input type="hidden" name="user_id" value="${escapeHtml(user.id)}">

                                                    <select name="is_active">
                                                        <option value="1" ${activeSelected}>Active</option>
                                                        <option value="0" ${inactiveSelected}>Inactive</option>
                                                    </select>

                                                    <button type="submit">Update</button>
                                                </form>
                                            </td>

                                            <td>
                                                <form action="../../controllers/admin_user_controller.php" method="POST">
                                                    <input type="hidden" name="action" value="change_role">
                                                    <input type="hidden" name="user_id" value="${escapeHtml(user.id)}">

                                                    <select name="role">
                                                        <option value="member" ${memberSelected}>Member</option>
                                                        <option value="team_lead" ${leadSelected}>Team Lead</option>
                                                        <option value="client" ${clientSelected}>Client</option>
                                                        <option value="admin" ${adminSelected}>Admin</option>
                                                    </select>

                                                    <button type="submit">Update</button>
                                                </form>
                                            </td>
                                        </tr>
                                    `;

                                    adminUsersTableBody.innerHTML += row;
                                });

                                adminUserSearchMessage.style.color = "green";
                                adminUserSearchMessage.innerText = response.users.length + " user(s) found.";
                            } 
                            else {
                                adminUsersTableBody.innerHTML = `
                                    <tr>
                                        <td colspan="11">No users found.</td>
                                    </tr>
                                `;

                                adminUserSearchMessage.style.color = "red";
                                adminUserSearchMessage.innerText = "No users found.";
                            }
                        } 
                        else {
                            adminUserSearchMessage.style.color = "red";
                            adminUserSearchMessage.innerText = response.message;
                        }
                    } 
                    catch (e) {
                        adminUserSearchMessage.style.color = "red";
                        adminUserSearchMessage.innerText = "Invalid server response.";
                    }
                } 
                else {
                    adminUserSearchMessage.style.color = "red";
                    adminUserSearchMessage.innerText = "Request failed.";
                }
            };

            xhr.send();
        });
    }
});

function escapeHtml(text) {
    if (text === null || text === undefined) {
        return "";
    }

    return String(text)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}
document.addEventListener("DOMContentLoaded", function () {
    var memberStatusSelects = document.querySelectorAll(".member-task-status-select");

    memberStatusSelects.forEach(function (select) {
        select.addEventListener("change", function () {
            var taskId = this.getAttribute("data-task-id");
            var newStatus = this.value;

            var messageBox = this.parentElement.querySelector(".member-task-status-message");

            if (!messageBox) {
                messageBox = document.getElementById("member-task-status-message-" + taskId);
            }

            if (!messageBox) {
                return;
            }

            if (taskId === "" || isNaN(taskId)) {
                messageBox.style.color = "red";
                messageBox.innerText = "Invalid task selected.";
                return;
            }

            var allowedStatuses = ["todo", "in_progress", "review", "done"];

            if (!allowedStatuses.includes(newStatus)) {
                messageBox.style.color = "red";
                messageBox.innerText = "Invalid status selected.";
                return;
            }

            messageBox.style.color = "black";
            messageBox.innerText = "Updating...";

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../Controller/member_update_task_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);

                        if (response.success) {
                            messageBox.style.color = "green";
                            messageBox.innerText = response.message;
                        } else {
                            messageBox.style.color = "red";
                            messageBox.innerText = response.message;
                        }
                    } catch (e) {
                        messageBox.style.color = "red";
                        messageBox.innerText = "Invalid server response.";
                    }
                } else {
                    messageBox.style.color = "red";
                    messageBox.innerText = "Request failed.";
                }
            };

            xhr.send("task_id=" + encodeURIComponent(taskId) + "&status=" + encodeURIComponent(newStatus));
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    var projectFilter = document.getElementById("client_task_project");
    var statusFilter = document.getElementById("client_task_status");
    var tableBody = document.getElementById("clientTaskTableBody");
    var messageBox = document.getElementById("clientTaskFilterMessage");

    if (projectFilter && statusFilter && tableBody) {
        function loadClientTasks() {
            var projectId = projectFilter.value;
            var status = statusFilter.value;

            if (messageBox) {
                messageBox.style.color = "black";
                messageBox.innerText = "Loading tasks...";
            }

            var xhr = new XMLHttpRequest();
            xhr.open(
                "GET",
                "../Controller/client_filter_tasks.php?project_id=" + encodeURIComponent(projectId) + "&status=" + encodeURIComponent(status),
                true
            );

            xhr.onload = function () {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);

                        if (response.success) {
                            tableBody.innerHTML = "";

                            if (response.tasks.length > 0) {
                                response.tasks.forEach(function (task) {
                                    var milestoneTitle = task.milestone_title ? task.milestone_title : "No Milestone";
                                    var assignedMember = task.assigned_member ? task.assigned_member : "Not Assigned";

                                    var row = `
                                        <tr>
                                            <td>${escapeHtml(task.id)}</td>
                                            <td>${escapeHtml(task.project_name)}</td>
                                            <td>${escapeHtml(milestoneTitle)}</td>
                                            <td>${escapeHtml(task.title)}</td>
                                            <td>${escapeHtml(task.description)}</td>
                                            <td>${escapeHtml(assignedMember)}</td>
                                            <td>${escapeHtml(task.priority)}</td>
                                            <td>${escapeHtml(task.status)}</td>
                                            <td>${escapeHtml(task.due_date)}</td>
                                            <td>${escapeHtml(task.estimated_hours)}</td>
                                        </tr>
                                    `;

                                    tableBody.innerHTML += row;
                                });

                                if (messageBox) {
                                    messageBox.style.color = "green";
                                    messageBox.innerText = response.tasks.length + " task(s) found.";
                                }
                            } else {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="10">No task found.</td>
                                    </tr>
                                `;

                                if (messageBox) {
                                    messageBox.style.color = "red";
                                    messageBox.innerText = "No task found.";
                                }
                            }
                        } else {
                            if (messageBox) {
                                messageBox.style.color = "red";
                                messageBox.innerText = response.message;
                            }
                        }
                    } catch (e) {
                        if (messageBox) {
                            messageBox.style.color = "red";
                            messageBox.innerText = "Invalid server response.";
                        }
                    }
                } else {
                    if (messageBox) {
                        messageBox.style.color = "red";
                        messageBox.innerText = "Request failed.";
                    }
                }
            };

            xhr.send();
        }

        projectFilter.addEventListener("change", loadClientTasks);
        statusFilter.addEventListener("change", loadClientTasks);
    }
});