<?php
require_once 'db.php';

function getProjects() {
    global $pdo;
    return $pdo->query("SELECT * FROM projects")->fetchAll();
}

function getTasksByProject($projectId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ?");
    $stmt->execute([$projectId]);
    return $stmt->fetchAll();
}

function getUsernameById($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

function addProject($name, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO projects (name, created_by, updated_by) VALUES (?, ?, ?)");
    $stmt->execute([$name, $userId, $userId]);
}

function deleteProject($projectId) {
    global $pdo;
    $pdo->prepare("DELETE FROM tasks WHERE project_id = ?")->execute([$projectId]);
    $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$projectId]);
}

function addTask($projectId, $taskName, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO tasks (project_id, name, created_by, updated_by) VALUES (?, ?, ?, ?)");
    $stmt->execute([$projectId, $taskName, $userId, $userId]);
}

function deleteTask($taskId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
}
