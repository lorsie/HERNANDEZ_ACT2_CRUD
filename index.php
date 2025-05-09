<?php
session_start();
require 'dbConfig.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];

// Add project
if (isset($_POST['project_name'])) {
    $stmt = $pdo->prepare("INSERT INTO projects (name, created_by, updated_by) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['project_name'], $userId, $userId]);
}

// Delete project and its tasks
if (isset($_GET['delete_project'])) {
    $projectId = $_GET['delete_project'];
    $pdo->prepare("DELETE FROM tasks WHERE project_id = ?")->execute([$projectId]);
    $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$projectId]);
}

// Add task
if (isset($_POST['task_name'], $_POST['project_id'])) {
    $stmt = $pdo->prepare("INSERT INTO tasks (project_id, name, created_by, updated_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['project_id'], $_POST['task_name'], $userId, $userId]);
}

// Delete task
if (isset($_GET['delete_task'])) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$_GET['delete_task']]);
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$projects = $pdo->query("SELECT * FROM projects")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Manager</title>
    <style>
        body { background-color: beige; font-family: sans-serif; padding: 20px; }
        .card { background: white; padding: 15px; margin-bottom: 20px; border-radius: 10px; }
        .meta { color: gray; font-size: 0.9em; margin-top: 10px; }

        input[type="text"] { padding: 5px; }
        button, .btn { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
        button { background-color: #3498db; color: white; }
        .btn-update { background-color: #f39c12; color: white; text-decoration: none; }
        .btn-delete { background-color: #e74c3c; color: white; text-decoration: none; }
        .btn-update:hover, .btn-delete:hover, button:hover { opacity: 0.9; }

        .btn-form { display: inline-block; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?> | <a href="?logout">Logout</a></h1>

    <h2>Add Project</h2>
    <form method="POST">
        <input type="text" name="project_name" placeholder="Project Name" required>
        <button type="submit">Add Project</button>
    </form>

    <?php foreach ($projects as $project): ?>
        <div class="card">
            <h3><?= htmlspecialchars($project['name']) ?></h3>

            <div class="btn-form">
                <a href="?edit_project=<?= $project['id'] ?>" class="btn btn-update">Update</a>
                <a href="?delete_project=<?= $project['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this project and all tasks?');">Delete</a>
            </div>

            <form method="POST">
                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                <input type="text" name="task_name" placeholder="New Task" required>
                <button type="submit">Add Task</button>
            </form>

            <?php
                $stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ?");
                $stmt->execute([$project['id']]);
                $tasks = $stmt->fetchAll();
            ?>
            <ul>
                <?php foreach ($tasks as $task): ?>
                    <li>
                        <?= htmlspecialchars($task['name']) ?>
                        <a href="?delete_task=<?= $task['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this task?');">Delete</a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php
                $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->execute([$project['created_by']]);
                $creator = $stmt->fetchColumn();

                $stmt->execute([$project['updated_by']]);
                $updater = $stmt->fetchColumn();
            ?>
            <div class="meta">
                Created by: <?= htmlspecialchars($creator) ?> | Last updated by: <?= htmlspecialchars($updater) ?>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>
