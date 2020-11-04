<?php
require __DIR__ . '/../php/classes/_connect.php';
require __DIR__ . '/../php/account/_auth.php';

if ($account->getAuthenticated()) {
    $_SESSION['errorMessage'] = "You cannot create an account while you are still logged in. Please logout first.";
    header("Location: todos.php");
    $connection->close();
    die;
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
                <form class="form-inline my-2 my-lg-0" action="../php/account/_auth.php" method="POST" id="logonForm">
                    <input class="form-control mr-sm-2 mb-sm-0 mb-2" type="email" name="email" required placeholder="Email" aria-label="Email">
                    <input class="form-control mr-sm-2" type="password" required name="password" placeholder="Password" aria-label="Password">
                    <button class="btn btn-primary my-2 my-sm-0" type="submit">Login</button>
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

            <div class="d-flex justify-content-center mt-3 pt-5 pb-5">
                <div class="card todr-subtle-shadow" style="max-width: 450px;">
                    <div class="card-content p-3 text-center">
                        <h1 class="dipslay-4 todr-brand-colour-text"><i class="fas fa-check-double mr-1"></i>Todr</h1>
                        <h4>Create an account</h4>
                        <p>Already have an account? Login by using the login form in the navbar.</p>
                        <form action="../php/account/_createAccount.php" method="POST" class="text-left" id="formCreateAccount">
                            <div class="form-label-group">
                                <input type="email" id="createEmail" name="createEmail" class="form-control" placeholder="Email address" autofocus>
                                <label for="createEmail">Email address</label>
                            </div>
                            <div class="form-label-group">
                                <input type="password" id="createPassword" name="createPassword" class="form-control" placeholder="Email address">
                                <label for="createPassword">Password</label>
                            </div>
                            <div class="form-label-group">
                                <input type="password" id="createPasswordConfirm" name="createPasswordConfirm" class="form-control" data-msg-equalTo="Passwords do not match." placeholder="Email address">
                                <label for="createPasswordConfirm">Retype Password</label>
                            </div>
                            <div class="d-sm-flex">
                                <div class="form-label-group flex-fill mr-1">
                                    <input type="text" id="createFirstName" name="createFirstName" class="form-control" placeholder="First Name">
                                    <label for="createFirstName">First Name</label>
                                </div>
                                <div class="form-label-group flex-fill ml-1">
                                    <input type="text" id="createLastName" name="createLastName" class="form-control" placeholder="Last Name">
                                    <label for="createLastName">Last Name</label>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn todr-brand-colour-bg text-white btn-lg">Create Account</button>
                            </div>
                        </form>
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
        $(function() {
            $('#formCreateAccount').validate({
                rules: {
                    createEmail: {
                        required: true,
                        maxlength: 200,
                        noWhiteSpace: true
                    },
                    createPassword: {
                        required: true,
                        maxlength: 150,
                        minlength: 8,
                        noWhiteSpace: true
                    },
                    createPasswordConfirm: {
                        required: true,
                        maxlength: 150,
                        minlength: 8,
                        noWhiteSpace: true,
                        equalTo: '#createPassword'
                    },
                    createFirstName: {
                        required: true,
                        maxlength: 50,
                        noWhiteSpace: true
                    },
                    createLastName: {
                        required: true,
                        maxlength: 50,
                        noWhiteSpace: true
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