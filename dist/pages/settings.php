<?php
require __DIR__ . '/../php/_connect.php';
require __DIR__ . '/../php/_auth.php';

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
                <form class="form-inline my-2 my-lg-0" action="../php/_logout.php" method="POST" id="logoutForm">
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
                            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details">Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="name-tab" data-toggle="tab" href="#name">Update Names</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="password-tab" data-toggle="tab" href="#password">Update Password</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="settingsTabContent">
                        <div class="tab-pane fade show active border border-top-0 p-2" id="details" role="tabpanel">
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

                        <div class="tab-pane fade border border-top-0 p-2" id="name" role="tabpanel">
                            <form action="../php/_updateName.php" id="formUpdateName" method="POST">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" name="firstName" required class="form-control mw-50" value="<?= $account->getFirstName() ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" name="lastName" required class="form-control mw-50" value="<?= $account->getLastName() ?>"></input>
                                </div>
                                <button class="btn btn-primary">Update</button>
                            </form>
                        </div>

                        <div class="tab-pane fade border border-top-0 p-2" id="password" role="tabpanel">
                            <form action="../php/_updatePassword.php" id="formChangePassword" method="POST">
                                <div class="form-group">
                                    <label for="firstName">Current Password</label>
                                    <input type="password" id="currentPassword" name="currentPassword" required class="form-control mw-50">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <input type="password" id="newPassword" name="newPassword" required class="form-control mw-50"></input>
                                </div>
                                <div class="form-group">
                                    <label for="newPasswordConfirm">Confirm new password</label>
                                    <input type="password" id="newPasswordConfirm" name="newPasswordConfirm" data-msg-equalTo="Passwords do not match." required class="form-control mw-50"></input>
                                </div>
                                <button class="btn btn-primary">Change</button>
                            </form>
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

        $(function() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);

            if (urlParams.has('tab')) {
                const tabName = urlParams.get('tab');

                $(`#${tabName}`).tab('show');
            }

            $('#formUpdateName').validate({
                onkeyup: false,
                onclick: false,
                onfocusout: false,
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
                    }
                },
                showErrors: function(errorMap, errorList) {
                    this.defaultShowErrors();
                    displayErrorToast(errorMap, errorList);
                },
                errorPlacement: function(error, element) {}
            });

            $('#formChangePassword').validate({
                onkeyup: false,
                onclick: false,
                onfocusout: false,
                rules: {
                    currentPassword: {
                        required: true,
                        maxlength: 100,
                        noWhiteSpace: true
                    },
                    newPassword: {
                        required: true,
                        maxlength: 100,
                        noWhiteSpace: true
                    },
                    newPasswordConfirm: {
                        required: true,
                        maxlength: 100,
                        noWhiteSpace: true,
                        equalTo: '#newPassword'
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
?>