<?php
session_start();

$message;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_SESSION['loginMessage'])) {
        $message = $_SESSION['loginMessage'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todr - Home</title>
    <script src="./main.js"></script>
    <link rel="stylesheet" href="./main.css">
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
                    <a class="nav-link" href="#"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/about.php"><i class="fas fa-question"></i> About</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" action="pages/login.php" method="POST">
                <input class="form-control mr-sm-2 mb-sm-0 mb-2" type="email" name="email" required placeholder="Email" aria-label="Email">
                <input class="form-control mr-sm-2" type="password" required name="password" placeholder="Password" aria-label="Password">
                <button class="btn btn-primary my-2 my-sm-0" type="submit">Login</button>
            </form>
        </div>
    </nav>

    <main class="todr-navbar-spacer">
        <div class="container">
            <?php if (!empty($message)) : ?>
                <div class="alert alert-danger mt-3">
                    <?= $message ?>
                </div>
            <?php
                unset($_SESSION['loginMessage']);
            endif;
            ?>
            <div class="jumbotron mt-3 text-center">
                <h1 class="display-4">Welcome to Todr!</h1>
                <p>A basic Todo lister demonstration site written using PHP.</p>
            </div>
            <p>Login to view your todos.</p>
        </div>
    </main>

    <footer>
        <hr />
        <div class="container mb-3">
            <span class="text-muted">&copy; Jake Hall</span>
            <span class="float-right text-muted"><?= date("Y") ?></span>
        </div>
    </footer>

    <script>
        $(function() {
            console.log("jQuery is working!");
        });
    </script>
</body>

</html>