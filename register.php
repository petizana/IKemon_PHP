<?php
function isEmailValid($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


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

function user_exists($user_storage, $username)
{
    $users = $user_storage->findOne(['username' => $username]);
    return !is_null($users);
}
function add_user($user_storage)
{
    global $username;
    global $password1;
    global $email;
    $user = [
        'username'  => $username,
        'password'  => password_hash($password1, PASSWORD_DEFAULT),
        'email'  => $email,
        'cards' => [],
        'money' => 1500
    ];
    return $user_storage->add($user);
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


$errorMessages = [];
$username = isset($_POST) && isset($_POST["username"]) ? $_POST["username"] : "";
$email = isset($_POST) && isset($_POST["email"]) ? $_POST["email"] : "";
$password1 = isset($_POST) && isset($_POST["password1"]) ? $_POST["password1"] : "";
$password2 = isset($_POST) && isset($_POST["password2"]) ? $_POST["password2"] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["register"])) {

        if (!isEmailValid($email)) $errorMessages["email"] = "Email is not valid!";
        if (strlen($username < 1)) $errorMessages["username"] = "Username must be more than one character!";
        if (strlen($password1) < 4) $errorMessages["password1"] = "Password must be more than 4 character!";
        if ($password1 !== $password2) $errorMessages["password2"] = "The two password must match!";
    }

    // main
    $user_storage = new UserStorage();
    if (count($errorMessages) === 0) {
        session_start();

        if (user_exists($user_storage, $username)) {
            $errorMessages["username"] = "Username is used by a registered user!";
        } else {
            add_user($user_storage);
            $logged_in_user = check_user($user_storage, $username, $password1);
            var_dump($logged_in_user);
            login($logged_in_user);
            redirect('index.php');
        }
    }
}





?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
<a class="nav-link active" aria-current="page" href="index.php"> >>Home</a>
    <form action="register.php" method="post" novalidate>
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
            <label for="email" class="form-label">Email address</label>
            <?php if (!isset($errorMessages["email"])) : ?>
                <input type="email" class="form-control is-valid" id="email" name="email" value="<?= $email ?>" aria-describedby="emailErrorMsg">
            <?php else : ?>
                <input type="email" class="form-control is-invalid" id="email" name="email" value="<?= $email ?>" aria-describedby="emailErrorMsg">
                <div id="emailErrorMsg" class="invalid-feedback"><?= $errorMessages["email"] ?></div>
            <?php endif ?>
        </div>
        <div class="mb-3">
            <label for="password1" class="form-label">Password</label>
            <?php if (!isset($errorMessages["password1"])) : ?>
                <input type="password" class="form-control is-valid" id="password1" name="password1" value="<?= $password1 ?>" aria-describedby="password1ErrorMsg">
            <?php else : ?>
                <input type="password" class="form-control is-invalid" id="password1" name="password1" value="<?= $password1 ?>" aria-describedby="password1ErrorMsg">
                <div id="password1ErrorMsg" class="invalid-feedback"><?= $errorMessages["password1"] ?></div>
            <?php endif ?>
        </div>
        <div class="mb-3">
            <label for="password2" class="form-label">Password again</label>
            <?php if (!isset($errorMessages["password2"])) : ?>
                <input type="password" class="form-control is-valid" id="password2" name="password2" value="<?= $password2 ?>" aria-describedby="password2ErrorMsg">
            <?php else : ?>
                <input type="password" class="form-control is-invalid" id="password2" name="password2" value="<?= $password2 ?>" aria-describedby="password2ErrorMsg">
                <div id="password2ErrorMsg" class="invalid-feedback"><?= $errorMessages["password2"] ?></div>
            <?php endif ?>
        </div>
        <button type="submit" class="btn btn-primary" name="register">Register</button>
    </form>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>