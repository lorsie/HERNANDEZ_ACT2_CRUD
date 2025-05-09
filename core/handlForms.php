<?php
session_start();
require_once 'models.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user']['id'];

// Add project
if (isset($_POST['project_name'])) {
    addProject($_POST['project_name'], $userId);
}

// Delete project
if (isset($_GET['delete_project'])) {
    deleteProject($_GET['delete_project']);
}

// Add task
if (isset($_POST['task_name'], $_POST['project_id'])) {
    addTask($_POST['project_id'], $_POST['task_name'], $userId);
}

// Delete task
if (isset($_GET['delete_task'])) {
    deleteTask($_GET['delete_task']);
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
