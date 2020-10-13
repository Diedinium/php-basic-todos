<?php
require __DIR__.'/../php/_connect.php';
require __DIR__.'/../php/_auth.php';

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todr - Login</title>
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
            <form class="form-inline my-2 my-lg-0" action="../php/logout.php" method="POST">
                <div class="mr-sm-3 mb-sm-0 mb-2 text-muted"><i class="fas fa-user"></i> <?= $verifiedEmail ?></div>
                <button class="btn btn-danger my-2 my-sm-0" type="submit">Logout</button>
            </form>
        </div>
    </nav>

    <main class="todr-navbar-spacer">
        <div class="container">
            <div>
                <div class="todr-subtle-shadow p-3 mt-3">
                    <form action="../php/_addTodo.php" method="POST">
                        <div class="input-group">
                            <input class="form-control" type="text" name="todoGroupHeader" required placeholder="Todo group title" aria-label="Email">
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
                            <div class="card-body">
                                <h4 class="card-title">
                                    <span class="mr-auto"><?= $todoGroup['header'] ?></span>
                                    <span onclick="alert('Not yet implemented')" class="float-right">
                                        <i class="fas fa-edit todr-todogroup-edit"></i>
                                    </span>
                                </h4>
                                <div>
                                    <?php if (count($todos) > 0) : ?>
                                        <ul class="list-group mb-3">
                                            <?php foreach ($todos as $todo) : ?>
                                                <li class="list-group-item">
                                                    <div>
                                                        <p class="mb-1"><strong><?= $todo['header'] ?></strong></p>
                                                        <p class="mb-0"><?= $todo['description'] ?></p>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <div class="alert alert-info">This todo group does not yet contain any todos.</div>
                                    <?php endif; ?>
                                    <a href="#" class="btn btn-secondary"><i class="fas fa-plus-circle"></i> Add Todo</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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

<?php
$connection->close();
$result->close();
?>