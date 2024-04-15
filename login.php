<?php
include 'storage.php';

class UserStorage extends Storage
{
    public function __construct()
    {
        parent::__construct(new JsonIO('users.json'));
    }
}

function redirect($page)
{
    header("Location: $page");
    exit();
}

function check_user($user_storage, $username, $password)
{
    $users = $user_storage->findMany(function ($user) use ($username, $password) {
        return $user["username"] === $username &&
            password_verify($password, $user["password"]);
    });
    return count($users) === 1 ? array_shift($users) : NULL;
}
function login($user)
{
    $_SESSION["user"] = $user;
}

$username = isset($_POST) && isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST) && isset($_POST["password"]) ? $_POST["password"] : "";
$errorMessages = [];

// main
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["login"])) {
        session_start();
        $user_storage = new UserStorage();
        if (isset($_POST["username"]) && isset($_POST["password"])) {
            if (strlen($username < 1)) $errorMessages["username"] = "Username must be more than one character!";
            if (strlen($password) < 4) $errorMessages["password"] = "Password must be more than 4 character!";
            if (count($errorMessages) === 0) {
                $logged_in_user = check_user($user_storage, $username, $password);
                if (!$logged_in_user) {
                    echo "<script type='text/javascript'>alert('Login failed!');</script>";
                } else {
                    login($logged_in_user);
                    redirect('index.php');
                }
            }
        }
    }

    if (isset($_POST["register"])) {
        redirect('register.php');
    }
}


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
<a class="nav-link active" aria-current="page" href="index.php"> >>Home</a>
    <form action="" method="post" novalidate>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <?php if (!isset($errorMessages["username"])) : ?>
                <input type="text" class="form-control is-valid" id="username" name="username" value="<?= $username ?>" aria-describedby="usernameErrorMsg">
            <?php else : ?>
                <input type="text" class="form-control is-invalid" id="username" name="username" value="<?= $username ?>" aria-describedby="usernameErrorMsg">
                <div id="usernameErrorMsg" class="invalid-feedback"><?= $errorMessages["username"] ?></div>
            <?php endif ?>

        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <?php if (!isset($errorMessages["password"])) : ?>
                <input type="password" class="form-control is-valid" id="password" name="password" value="<?= $password ?>" aria-describedby="passwordErrorMsg">
            <?php else : ?>
                <input type="password" class="form-control is-invalid" id="password" name="password" value="<?= $password ?>" aria-describedby="passwordErrorMsg">
                <div id="passwordErrorMsg" class="invalid-feedback"><?= $errorMessages["password"] ?></div>
            <?php endif ?>
        </div>
        <button type="submit" class="btn btn-primary" name="login">Login</button>
        <a href="/register.php">
            <button type="button" class="btn btn-secondary" name="register">Register</button>
        </a>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>