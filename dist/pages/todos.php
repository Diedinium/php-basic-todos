<?php
require __DIR__ . '/../php/_auth.php';
require __DIR__ . '/../php/_connect.php';

$userQuery;
$todoGroups = [];
$result;

if (!$validLogon) {
    session_start();
    $_SESSION['loginMessage'] = "You did not provide valid login details.";
    header("Location: ../index.php");
    $connection->close();
    exit;
}

$query = "SELECT id, header, dateCreated FROM t_todogroup WHERE iduser = $userID";

$result = $connection->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($todoGroups, $row);
    }
}

session_start();
$errorMessage;

if (!empty($_SESSION['todoAddError'])) {
    $errorMessage = $_SESSION['todoAddError'];
    unset($_SESSION['todoAddError']);
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
            <form class="form-inline my-2 my-lg-0" action="../php/_logout.php" method="POST">
                <div class="mr-sm-3 mr-2 text-muted"><i class="fas fa-user"></i> <?= $verifiedEmail ?></div>
                <button class="btn btn-danger my-2 my-sm-0" type="submit">Logout</button>
            </form>
        </div>
    </nav>

    <main class="todr-navbar-spacer">
        <div class="container">
            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger mt-2">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>

            <div>
                <div class="todr-subtle-shadow p-3 mt-3">
                    <form action="../php/_addTodoGroup.php" method="POST" id="formAddTodoGroup">
                        <div class="input-group">
                            <input class="form-control" type="text" name="todoGroupHeader" required maxlength="255" placeholder="Todo group title">
                            <input type="hidden" name="userID" value="<?= $userID ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Add Group</button>
                            </div>
                        </div>
                    </form>
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
                                        <i onclick="alert('Not yet implemented')" data-toggle="tooltip" data-placement="top" title="Delete todo group" class="fas fa-trash fa-sm todr-todogroup-delete"></i>
                                    </span>
                                </h4>
                                <div>
                                    <?php if (count($todos) > 0) : ?>
                                        <ul class="list-group mb-3">
                                            <?php foreach ($todos as $todo) : ?>
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
                                                                <i onclick="alert('Not yet implemented')" data-toggle="tooltip" data-placement="top" title="Mark as incomplete" class="fas fa-times todr-todogroup-delete mr-2"></i>
                                                            <?php else : ?>
                                                                <i onclick="alert('Not yet implemented')" data-toggle="tooltip" data-placement="top" title="Mark as complete" class="fas fa-check todr-todogroup-check mr-2"></i>
                                                            <?php endif; ?>
                                                            <i onclick="alert('Not yet implemented')" data-toggle="tooltip" data-placement="top" title="Edit todo item" class="fas fa-edit todr-todogroup-edit mr-2"></i>
                                                            <i onclick="alert('Not yet implemented')" data-toggle="tooltip" data-placement="top" title="Delete todo item" class="fas fa-trash todr-todogroup-delete"></i>
                                                        </div>
                                                        <div class="col-12 pb-1">
                                                            <?= $todo['description'] ?>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="d-flex">
                                                                <?php if (!empty($todo['dueDate'])) :
                                                                    $dueDate = new DateTime($todo['dueDate']);
                                                                    $createdDate = new DateTime($todo['createdDate']);
                                                                    $now = new DateTime();
                                                                    $isOverdue = $dueDate < $now;
                                                                ?>
                                                                    <span class="mr-auto">
                                                                        <i class="fas fa-clock"></i>
                                                                        <span class="text-muted">
                                                                            <?= $dueDate->format('Y/m/d H:i a') ?>
                                                                        </span>
                                                                        <?php if ($isOverdue) : ?>
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
                <form action="../php/_addTodo.php" method="POST" id="formAddTodo">
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
                                <input type="date" id="addTodoDate" name="addTodoDate" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="addTodoTime">Due time (Optional)</label>
                                <input type="time" id="addTodoTime" name="addTodoTime" class="form-control">
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
                onkeyup: false,
                onclick: false,
                onfocusout: false,
                rules: {
                    addTodoHeader: {
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
                showErrors: function(errorMap, errorList) {
                    this.defaultShowErrors();
                    displayErrorToast(errorMap, errorList);
                },
                errorPlacement: function(error, element) {}
            });

            $('[data-toggle="tooltip"]').tooltip();

            $('input, select').focusout(function() {
                $(this).removeClass('error');
            });
        });
    </script>
</body>

</html>

<?php
$connection->close();
$result->close();
?>