<?php
require __DIR__ . '/../php/classes/_connect.php';
require __DIR__ . '/../php/account/_auth.php';

if (!$account->getAuthenticated()) {
    dieWithError("You did not provide valid login details.");
}

$errorMessage;
$successMessage;

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
                        <a class="nav-link" href="todos.php"><i class="fas fa-home"></i> Home</a>
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

            <div class="card mt-3 todr-subtle-shadow bg-white">
                <div class="card-body p-3">
                    <h2 class="card-title">Settings</h2>

                    <ul class="nav nav-tabs nav-fill" id="settingsTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details">Account Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="security-tab" data-toggle="tab" href="#security">Security</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="management-tab" data-toggle="tab" href="#management">Account Management</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="settingsTabContent">
                        <div class="tab-pane fade show active p-2" id="details" role="tabpanel">
                            <form>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><strong>Email</strong></label>
                                    <div class="col-sm-10">
                                        <span class="form-control text-muted border-0 pl-0"><?= $account->getEmail() ?></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><strong>Full Name</strong></label>
                                    <div class="col-sm-10">
                                        <span class="form-control text-muted border-0 pl-0"><?= $account->getFullName() ?></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><strong>Account created</strong></label>
                                    <div class="col-sm-10">
                                        <span class="form-control text-muted border-0 pl-0"><?php echo date('Y/m/d H:i a', strtotime($account->getDateCreated())) ?></span>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade p-2" id="security" role="tabpanel">
                            <form action="../php/account/_updatePassword.php" id="formChangePassword" method="POST">
                                <div class="form-group">
                                    <label for="firstName">Current Password</label>
                                    <input type="password" id="currentPassword" name="currentPassword" required class="form-control mw-50">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <input type="password" id="newPassword" name="newPassword" data-msg-minlength="Password must be at least 8 characters long." required class="form-control mw-50"></input>
                                </div>
                                <div class="form-group">
                                    <label for="newPasswordConfirm">Confirm new password</label>
                                    <input type="password" id="newPasswordConfirm" name="newPasswordConfirm" data-msg-minlength="Password must be at least 8 characters long." data-msg-equalTo="Passwords do not match." required class="form-control mw-50"></input>
                                </div>
                                <button class="btn todr-brand-colour-bg text-white">Change</button>
                            </form>
                        </div>

                        <div class="tab-pane fade p-2" id="management" role="tabpanel">
                            <form action="../php/account/_updateName.php" id="formUpdateName" method="POST">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" name="firstName" required class="form-control mw-50" value="<?= $account->getFirstName() ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" name="lastName" required class="form-control mw-50" value="<?= $account->getLastName() ?>"></input>
                                </div>
                                <button class="btn todr-brand-colour-bg text-white">Update</button>
                            </form>

                            <hr>

                            <h3>Account Actions</h3>
                            <div class="alert alert-warning">Please note that the settings below are permanent and cannot be undone!</div>

                            <ul class="list-group">
                                <li class="list-group-item">
                                    <div class="d-sm-flex align-items-center">
                                        <div class="pr-3">
                                            <strong>Delete all todos</strong>
                                            <div>This will delete all current todos along with the todo groups they are contained within. This action is permanent and cannot be undone.</div>
                                        </div>
                                        <form action="../php/todos/_deleteAllTodos.php" method="POST" class="flex-shrink-0 mt-2 mt-sm-0" id="formDeleteAllTodos">
                                            <button type="submit" class="btn btn-danger">Delete all todos</button>
                                        </form>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-sm-flex align-items-center">
                                        <div class="pr-3">
                                            <strong>Delete Account</strong>
                                            <div>This will permanently delete your account along with all associated data (todos etc). This action is permanent and cannot be undone.</div>
                                        </div>
                                        <form action="../php/account/_deleteAccount.php" method="POST" class="flex-shrink-0 mt-2 mt-sm-0" id="formDeleteAccount">
                                            <button type="submit" class="btn btn-danger">Delete Account</button>
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/partials/common.php' ?>

    <footer>
        <hr />
        <div class="container mb-3">
            <span class="text-muted">&copy; Jake Hall</span>
            <span class="float-right text-muted"><?= date("Y") ?></span>
        </div>
    </footer>

    <script>
        function submitLogout() {
            $('#logoutForm').submit();
        }

        $('#formDeleteAllTodos button').on('click', function(e) {
            e.preventDefault();
            
            confirmDialog('Are you sure you want to delete all todos? This cannot be undone', 'Confirm Deletion', function() {
                $('#formDeleteAllTodos').submit();
            });
        });

        $('#formDeleteAccount button').on('click', function(e) {
            e.preventDefault();
            $form = $(this).parent();

            confirmDialog('Are you sure you want to delete your account? This cannot be undone', 'Confirm Deletion', function() {
                $($form).submit();
            });
        });

        $(function() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);

            if (urlParams.has('tab')) {
                const tabName = urlParams.get('tab');

                $(`#${tabName}`).tab('show');
            }

            $('#formUpdateName').validate({
                rules: {
                    firstName: {
                        required: true,
                        maxlength: 50,
                        noWhiteSpace: true
                    },
                    lastName: {
                        required: true,
                        maxlength: 50,
                        noWhiteSpace: true
                    },
                },
                errorElement: 'small'
            });

            $('#formChangePassword').validate({
                rules: {
                    currentPassword: {
                        required: true,
                        maxlength: 100,
                        noWhiteSpace: true
                    },
                    newPassword: {
                        required: true,
                        maxlength: 100,
                        minlength: 8,
                        noWhiteSpace: true
                    },
                    newPasswordConfirm: {
                        required: true,
                        maxlength: 100,
                        minlength: 8,
                        noWhiteSpace: true,
                        equalTo: '#newPassword'
                    }
                },
                errorElement: 'small'
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
?>