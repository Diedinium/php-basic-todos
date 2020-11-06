<?php
require __DIR__ . '/../php/classes/_connect.php';
require __DIR__ . '/../php/account/_auth.php';

if (!$account->getAuthenticated()) {
    $_SESSION['errorMessage'] = "You did not provide valid login details.";
    header("Location: ../index.php");
    $connection->close();
    exit;
}

$todoGroups = [];
$errorMessage;
$successMessage;
$result = $connection->query("SELECT id, header, dateCreated FROM t_todogroup WHERE iduser = {$account->getId()}");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($todoGroups, $row);
    }
}

if (!empty($_SESSION['errorMessage'])) {
    $errorMessage = $_SESSION['errorMessage'];
    unset($_SESSION['errorMessage']);
}

if (!empty($_SESSION['successMessage'])) {
    $successMessage = $_SESSION['successMessage'];
    unset($_SESSION['successMessage']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todr - Todos</title>
    <script src="../main.js"></script>
    <link rel="stylesheet" href="../main.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top todr-navbar-top-accent shadow-sm">
        <div class="container">
            <a class="navbar-brand todr-brand-colour-text" href="#"><i class="fas fa-check-double mr-1"></i>Todr</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="../index.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php"><i class="fas fa-question"></i> About</a>
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0" action="../php/account/_logout.php" method="POST" id="logoutForm">
                    <div class="mr-sm-3 mr-3 text-muted"><i class="fas fa-user-circle"></i> <?= $account->getEmail() ?></div>
                    <a href="settings.php"><i class="fas fa-user-edit fa-lg todr-todogroup-edit mr-3" data-toggle="tooltip" data-placement="bottom" title="Edit user settings"></i></a>
                    <i class="fas fa-sign-out-alt fa-lg todr-todogroup-delete" onclick="submitLogout()" data-toggle="tooltip" data-placement="bottom" title="Logout"></i>
                </form>
            </div>
        </div>
    </nav>

    <main class="todr-navbar-spacer">
        <div class="container">
            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger mt-2">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)) : ?>
                <div class="alert alert-success mt-2">
                    <?= $successMessage ?>
                </div>
            <?php endif; ?>

            <div>
                <div class="card mt-3 todr-subtle-shadow bg-white">
                    <div class="card-body p-3">
                        <form action="../php/todogroup/_addTodoGroup.php" method="POST" id="formAddTodoGroup">
                            <div class="input-group">
                                <input class="form-control" type="text" name="todoGroupHeader" required maxlength="255" placeholder="Todo group title" autofocus>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">Add Group</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (count($todoGroups) > 0) : ?>
                    <?php foreach ($todoGroups as $todoGroup) :
                        $todoGroupID = $todoGroup['id'];
                        $todos = [];
                        $query = "SELECT * FROM t_todos WHERE idtodogroup = $todoGroupID";

                        $result = $connection->query($query);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                array_push($todos, $row);
                            }
                        }
                    ?>
                        <div class="card mt-2 todr-subtle-shadow bg-white">
                            <div class="card-body p-3">
                                <h4 class="card-title d-flex">
                                    <span class="mr-auto"><?= $todoGroup['header'] ?></span>
                                    <span class="todr-todogroup-actions">
                                        <i onclick="alert('Not yet implemented')" data-toggle="tooltip" data-placement="top" title="Edit todo group" class="fas fa-edit fa-sm todr-todogroup-edit mr-2"></i>
                                        <i data-toggle="tooltip" data-placement="top" title="Delete todo group" data-todogroup-id="<?= $todoGroupID ?>" class="fas fa-trash fa-sm todr-todogroup-delete event-todogroup-delete"></i>
                                    </span>
                                </h4>
                                <div>
                                    <?php if (count($todos) > 0) : ?>
                                        <ul class="list-group mb-3">
                                            <?php foreach ($todos as $todo) :
                                                $createdDate = new DateTime($todo['createdDate']);
                                            ?>
                                                <li class="list-group-item p-2">
                                                    <div class="row no-gutters">
                                                        <div class="col-9 col-md-11 pb-1">
                                                            <?php if ($todo['complete'] == true) : ?>
                                                                <strong><?= $todo['header'] ?> <span class="badge badge-success">Complete</span></strong>
                                                            <?php else : ?>
                                                                <strong><?= $todo['header'] ?> <span class="badge badge-warning">In Progress</span></strong>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-3 col-md-1 text-right pb-1">
                                                            <?php if ($todo['complete'] == true) : ?>
                                                                <i data-todo-id="<?= $todo['id'] ?>" data-duedate="<?= $todo['dueDate'] ?>" data-todo-status="<?= $todo['complete'] ?>" data-toggle="tooltip" data-placement="top" title="Mark as incomplete" class="fas fa-times todr-todogroup-delete mr-2 event-todo-status-toggle"></i>
                                                            <?php else : ?>
                                                                <i data-todo-id="<?= $todo['id'] ?>" data-duedate="<?= $todo['dueDate'] ?>" data-todo-status="<?= $todo['complete'] ?>" data-toggle="tooltip" data-placement="top" title="Mark as complete" class="fas fa-check todr-todogroup-check mr-2 event-todo-status-toggle"></i>
                                                            <?php endif; ?>
                                                            <i onclick="alert('Not yet implemented')" data-toggle="tooltip" data-placement="top" title="Edit todo item" class="fas fa-edit todr-todogroup-edit mr-2"></i>
                                                            <i data-toggle="tooltip" data-placement="top" title="Delete todo item" data-todo-id="<?= $todo['id'] ?>" class="fas fa-trash todr-todogroup-delete event-todo-delete"></i>
                                                        </div>
                                                        <div class="col-12 pb-1">
                                                            <?php echo nl2br($todo['description']); ?>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="d-flex">
                                                                <?php if (!empty($todo['dueDate'])) :
                                                                    $dueDate = new DateTime($todo['dueDate']);
                                                                    $now = new DateTime();
                                                                    $isOverdue = $dueDate < $now;
                                                                ?>
                                                                    <span class="mr-auto">
                                                                        <i class="fas fa-clock"></i>
                                                                        <span class="text-muted">
                                                                            <?= $dueDate->format('Y/m/d H:i a') ?>
                                                                        </span>
                                                                        <?php if ($isOverdue && $todo['complete'] == false) : ?>
                                                                            <span class="badge badge-danger">Overdue</span>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                    <span><i class="fas fa-info-circle todr-todogroup-edit" data-toggle="tooltip" data-placement="top" title="Created: <?= $createdDate->format('Y/m/d H:i a') ?>"></i></span>
                                                                <?php else : ?>
                                                                    <span class="mr-auto">
                                                                        <i class="fas fa-clock"></i>
                                                                        <span class="text-muted"> No due date</span>
                                                                    </span>
                                                                    <span><i class="fas fa-info-circle todr-todogroup-edit" data-toggle="tooltip" data-placement="top" title="Created: <?= $createdDate->format('Y/m/d H:i a') ?>"></i></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <div class="alert alert-info">This todo group does not yet contain any todos.</div>
                                    <?php endif; ?>
                                    <a href="#" class="btn btn-secondary" onclick="openAddTodoModal(<?= $todoGroupID ?>)"><i class="fas fa-plus-circle"></i> Add Todo</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="card mt-2 todr-subtle-shadow bg-white">
                        <div class="card-body p-3">
                            <h4 class="card-title d-flex">
                                No todo groups found.
                            </h4>
                            <div>
                                Use the input above to add your first group!
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <div class="modal" tabindex="-1" id="ModalAddTodo">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Todo</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="../php/todos/_addTodo.php" method="POST" id="formAddTodo">
                    <div class="modal-body">
                        <input type="hidden" value="" id="todoGroupID" name="todoGroupID">
                        <div class="form-group">
                            <label for="addTodoHeader">Header</label>
                            <input type="text" id="addTodoHeader" name="addTodoHeader" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="addTodoDescription">Description (Optional)</label>
                            <textarea id="addTodoDescription" name="addTodoDescription" cols="30" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="addTodoDate">Due date (Optional)</label>
                                <input type="date" id="addTodoDate" name="addTodoDate" class="form-control" data-msg-required="When you set a due time, a date is required.">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="addTodoTime">Due time (Optional)</label>
                                <input type="time" id="addTodoTime" name="addTodoTime" class="form-control" data-msg-required="When you set a due date, a time is required.">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add todo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/partials/common.php' ?>

    <footer>
        <hr />
        <div class="container mb-3">
            <span class="text-muted">&copy; Jake Hall</span>
            <span class="float-right text-muted"><?= date("Y") ?></span>
        </div>
    </footer>

    <script>
        function openAddTodoModal(id) {
            $('#ModalAddTodo').modal('show');
            $('#todoGroupID').val(id);
        }

        function submitLogout() {
            $('#logoutForm').submit();
        }

        $(function() {
            $('#formAddTodoGroup').validate({
                onkeyup: false,
                onclick: false,
                onfocusout: false,
                rules: {
                    todoGroupHeader: {
                        required: true,
                        noWhiteSpace: true
                    }
                },
                showErrors: function(errorMap, errorList) {
                    this.defaultShowErrors();
                    displayErrorToast(errorMap, errorList);
                },
                errorPlacement: function(error, element) {}
            });

            $('#formAddTodo').validate({
                rules: {
                    addTodoHeader: {
                        maxlength: 250,
                        required: true,
                        noWhiteSpace: true
                    },
                    addTodoDescription: {
                        noWhiteSpace: true
                    },
                    addTodoDate: {
                        required: '#addTodoTime:filled'
                    },
                    addTodoTime: {
                        required: '#addTodoDate:filled'
                    }
                },
                errorElement: 'small'
            });

            $('[data-toggle="tooltip"]').tooltip();

            $('input, select').focusout(function() {
                $(this).removeClass('error');
            });

            $(document).on('click', '.event-todogroup-delete', function() {
                const $parentToRemove = $(this).closest('div.card');
                $.ajax({
                    type: 'POST',
                    url: '../php/todogroup/_deleteTodoGroup.php',
                    data: {
                        id: $(this).attr('data-todogroup-id')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            displaySuccessToast(response.message);
                            $parentToRemove.fadeOut(500, () => {
                                $parentToRemove.remove();
                                $('.tooltip').tooltip('hide');
                            });
                        } else {
                            displayErrorToastStandard(response.message);
                        }
                    }
                });
            });

            $(document).on('click', '.event-todo-delete', function() {
                const $parentToRemove = $(this).closest('li.list-group-item');
                const $parentListGroup = $(this).closest('ul.list-group');
                $.ajax({
                    type: 'POST',
                    url: '../php/todos/_deleteTodo.php',
                    data: { id: $(this).attr('data-todo-id') },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            displaySuccessToast(response.message);
                            $parentToRemove.fadeOut(500, () => {
                                $parentToRemove.remove();
                                const $remainingTodos = $parentListGroup.find('li');
                                if ($remainingTodos.length < 1) {
                                    $parentListGroup.replaceWith($('#templates #noTodosAlert').clone());
                                }
                                $('.tooltip').tooltip('hide');
                            });
                        }
                        else {
                            displayErrorToastStandard(response.message);
                        }
                    }
                });
            });

            $(document).on('click', '.event-todo-status-toggle', function() {
                const $parentToUpdate = $(this).closest('li.list-group-item');
                $.ajax({
                    type: 'POST',
                    url: '../php/todos/_toggleTodoStatus.php',
                    data: {
                        id: $(this).attr('data-todo-id'),
                        status: $(this).attr('data-todo-status')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            displaySuccessToast(response.message);
                            const $changeButton = $parentToUpdate.find('i.event-todo-status-toggle');
                            $changeButton.attr('data-todo-status', response.status);
                            if (response.status == true) {
                                $changeButton.removeClass('todr-todogroup-check fa-check').addClass('todr-todogroup-delete fa-times');
                                $parentToUpdate.find('.badge.badge-danger').fadeOut(500, () => $parentToUpdate.find('.badge.badge-danger').remove());
                            }
                            else {
                                $changeButton.removeClass('todr-todogroup-delete fa-times').addClass('todr-todogroup-check fa-check');
                                let dueDate = new Date($changeButton.attr('data-duedate'));
                                
                                if (dueDate < new Date()) {
                                    $parentToUpdate.find('div.col-12 div.d-flex span.mr-auto').append('<span class="badge badge-danger">Overdue</span>');
                                }
                            }
                        } else {
                            displayErrorToastStandard(response.message);
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>

<?php
$connection->close();
$result->close();
?>