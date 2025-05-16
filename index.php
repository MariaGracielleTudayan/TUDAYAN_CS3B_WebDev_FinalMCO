<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .task-card {
            transition: all 0.3s ease;
        }
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .completed {
            text-decoration: line-through;
            opacity: 0.7;
        }
        .action-btn {
            position: relative;
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            transform: scale(1.1);
        }
        .action-btn::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .action-btn:hover::after {
            opacity: 1;
            visibility: visible;
            bottom: 120%;
        }
        .btn-group {
            gap: 5px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">Task Management</h1>
        
        <!-- Add Task Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="taskForm" action="operations.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="title" placeholder="Task Title" required>
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control" name="due_date" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Add Task</button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <textarea class="form-control" name="description" placeholder="Task Description" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-6">
                <select class="form-select" id="statusFilter">
                    <option value="all">All Tasks</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="dateFilter">
                    <option value="all">All Dates</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
        </div>

        <!-- Tasks List -->
        <div id="tasksList">
            <?php
            $sql = "SELECT * FROM tasks ORDER BY due_date ASC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $statusClass = $row['status'] == 'completed' ? 'completed' : '';
                    $statusIcon = $row['status'] == 'completed' ? 'fa-check' : 'fa-check-circle';
                    $statusTooltip = $row['status'] == 'completed' ? 'Mark as Pending' : 'Mark as Done';
                    
                    echo '<div class="card task-card mb-3 ' . $statusClass . '" data-id="' . $row['id'] . '">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">' . htmlspecialchars($row['title']) . '</h5>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-success action-btn toggle-status" data-id="' . $row['id'] . '" data-tooltip="' . $statusTooltip . '">
                                            <i class="fas ' . $statusIcon . '"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary action-btn edit-task" data-id="' . $row['id'] . '" data-tooltip="Edit Task">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger action-btn delete-task" data-id="' . $row['id'] . '" data-tooltip="Delete Task">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="card-text">' . htmlspecialchars($row['description']) . '</p>
                                <small class="text-muted">Due: ' . $row['due_date'] . '</small>
                            </div>
                          </div>';
                }
            } else {
                echo '<div class="alert alert-info">No tasks found. Add some tasks to get started!</div>';
            }
            ?>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm" action="operations.php" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="editTaskId">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="editTitle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" name="due_date" id="editDueDate" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editTaskForm" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
</body>
</html> 