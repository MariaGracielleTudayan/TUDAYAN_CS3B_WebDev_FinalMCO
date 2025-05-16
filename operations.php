<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);

            $sql = "INSERT INTO tasks (title, description, due_date) VALUES ('$title', '$description', '$due_date')";
            if (mysqli_query($conn, $sql)) {
                header('Location: index.php?success=Task added successfully');
            } else {
                header('Location: index.php?error=' . urlencode(mysqli_error($conn)));
            }
            break;

        case 'update':
            $id = (int)$_POST['id'];
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);

            $sql = "UPDATE tasks SET title='$title', description='$description', due_date='$due_date' WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                header('Location: index.php?success=Task updated successfully');
            } else {
                header('Location: index.php?error=' . urlencode(mysqli_error($conn)));
            }
            break;

        case 'delete':
            $id = (int)$_POST['id'];
            $sql = "DELETE FROM tasks WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                header('Location: index.php?success=Task deleted successfully');
            } else {
                header('Location: index.php?error=' . urlencode(mysqli_error($conn)));
            }
            break;

        case 'toggle_status':
            $id = (int)$_POST['id'];
            $current_status = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM tasks WHERE id=$id"))['status'];
            $new_status = $current_status === 'completed' ? 'pending' : 'completed';
            
            $sql = "UPDATE tasks SET status='$new_status' WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                header('Location: index.php?success=Task status updated');
            } else {
                header('Location: index.php?error=' . urlencode(mysqli_error($conn)));
            }
            break;

        default:
            header('Location: index.php?error=Invalid action');
            break;
    }
} else {
    header('Location: index.php');
}
?> 