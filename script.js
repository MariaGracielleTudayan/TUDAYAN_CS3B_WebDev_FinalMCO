$(document).ready(function() {
    // Handle task deletion
    $('.delete-task').click(function() {
        if (confirm('Are you sure you want to delete this task?')) {
            const taskId = $(this).data('id');
            const form = $('<form>', {
                'method': 'POST',
                'action': 'operations.php'
            });
            form.append($('<input>', {
                'name': 'action',
                'value': 'delete',
                'type': 'hidden'
            }));
            form.append($('<input>', {
                'name': 'id',
                'value': taskId,
                'type': 'hidden'
            }));
            $('body').append(form);
            form.submit();
        }
    });

    // Handle task status toggle
    $('.toggle-status').click(function() {
        const taskId = $(this).data('id');
        const form = $('<form>', {
            'method': 'POST',
            'action': 'operations.php'
        });
        form.append($('<input>', {
            'name': 'action',
            'value': 'toggle_status',
            'type': 'hidden'
        }));
        form.append($('<input>', {
            'name': 'id',
            'value': taskId,
            'type': 'hidden'
        }));
        $('body').append(form);
        form.submit();
    });

    // Handle task editing
    $('.edit-task').click(function() {
        const taskId = $(this).data('id');
        const taskCard = $(this).closest('.task-card');
        const title = taskCard.find('.card-title').text();
        const description = taskCard.find('.card-text').text();
        const dueDate = taskCard.find('.text-muted').text().replace('Due: ', '');

        $('#editTaskId').val(taskId);
        $('#editTitle').val(title);
        $('#editDescription').val(description);
        $('#editDueDate').val(dueDate);

        const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
        editModal.show();
    });

    // Handle status filter
    $('#statusFilter').change(function() {
        const status = $(this).val();
        if (status === 'all') {
            $('.task-card').show();
        } else {
            $('.task-card').each(function() {
                const isCompleted = $(this).hasClass('completed');
                if ((status === 'completed' && isCompleted) || (status === 'pending' && !isCompleted)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Handle date filter
    $('#dateFilter').change(function() {
        const filter = $(this).val();
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        $('.task-card').each(function() {
            const dueDate = new Date($(this).find('.text-muted').text().replace('Due: ', ''));
            dueDate.setHours(0, 0, 0, 0);

            let show = true;
            if (filter === 'today') {
                show = dueDate.getTime() === today.getTime();
            } else if (filter === 'week') {
                const weekStart = new Date(today);
                weekStart.setDate(today.getDate() - today.getDay());
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekStart.getDate() + 6);
                show = dueDate >= weekStart && dueDate <= weekEnd;
            } else if (filter === 'month') {
                show = dueDate.getMonth() === today.getMonth() && 
                       dueDate.getFullYear() === today.getFullYear();
            }

            $(this).toggle(show);
        });
    });

    // Show success/error messages
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    if (success) {
        alert(success);
    }
    if (error) {
        alert('Error: ' + error);
    }
}); 