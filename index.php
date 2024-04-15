<?php

use function PHPSTORM_META\type;


include 'storage.php';
$cards = new Storage(new JsonIO('cards.json'));

class UserStorage extends Storage
{
    public function __construct()
    {
        parent::__construct(new JsonIO('users.json'));
    }
}

session_start();
$user_storage = new UserStorage();

$cardLimit = 5;

if (isset($_SESSION["user"])) $remainingCards = $cardLimit - count($user_storage->findById($_SESSION["user"]["id"])["cards"]);


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["logout"])) {
        unset($_SESSION["user"]);
    }
    if (isset($_POST["buy"])) {
        if ($remainingCards !== 0 && $user_storage->findById($_SESSION["user"]["id"])["money"] - $_POST["cardPrice"] >= 0) {
            $user_storage->addCard($_SESSION["user"]["id"], strval($_POST["cardId"]));
            $user_storage->removeCard("admin", strval($_POST["cardId"]));
            $user_storage->decrMoney($_SESSION["user"]["id"], $_POST["cardPrice"]);
        } else if ($user_storage->findById($_SESSION["user"]["id"])["money"] - $_POST["cardPrice"] < 0) echo "<script type='text/javascript'>alert('You do not have enough money for that!');</script>";
        else if ($remainingCards === 0) echo "<script type='text/javascript'>alert('You cannot have more cards, you have reached your limit!');</script>";
    }

}



?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IKémon - HOME</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">IKémon</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <?php if (!isset($_SESSION["user"])) : ?>
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        <a class="nav-link" href="login.php">Login</a>
                        <a class="nav-link" href="register.php">Register</a>
                    <?php else : ?>

                        <a class="nav-link" href="userdetails.php">
                            <h1> Logged in as <span class="badge bg-secondary"><?= $user_storage->findById($_SESSION["user"]["id"])["username"] ?></span></h1>
                        </a>

                        <form action="index.php" method="post">
                            <div class="btn btn-secondary"><?= $user_storage->findById($_SESSION["user"]["id"])["money"] ?>💵</div>
                            <button type="submit" class="btn btn-danger" name="logout">Logout</button>
                            <?php if ($_SESSION["user"]["id"] === "admin") : ?>
                                <a href="new_card.php" class="btn btn-success">New card</a>
                            <?php endif ?>
                        </form>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </nav>
    IKémon where you can trade your cards.

    <?php if (!isset($_SESSION["user"])) : ?>
        <div class="alert alert-warning" role="alert" id="alert">
            You need to log in to buy or trade cards
        </div>
    <?php else : ?>
        <div class="alert alert-success" role="alert" id="alert">
            Successfully logged in!
        </div>
    <?php endif ?>

    <form action="index.php" method="post">
        <label for="filter">Filter Poké Cards:</label>
        <select name="chosenType" id="chosenType">
            <option value="default">--- Choose a type ---</option>
            <option value="grass">Grass</option>
            <option value="fire">Fire</option>
            <option value="water">Water</option>
            <option value="lightning">Lightning</option>
            <option value="psychtic">Psychtic</option>
            <option value="fighting">Fighting</option>
            <option value="darkness">Darkness</option>
            <option value="metal">Metal</option>
            <option value="colorless">Colorless</option>
            <option value="fairy">Fairy</option>
            <option value="dragon">Dragon</option>
            <option value="poison">Poison</option>
        </select>
        <button type="submit" class="btn btn-secondary" name="filter">Select</button>

    </form>


    <br>CARDS:
    <div class="d-flex gap-2 flex-wrap w-80">

        <?php
        $filteredCards = [];
        if (!isset($_POST["filter"]) || !isset($_POST["chosenType"]) || $_POST["chosenType"] === "default") $filteredCards = $cards->findAll();
        else {
            foreach ($cards->findAll() as $elem) {
                if ($elem["type"] === $_POST["chosenType"]) $filteredCards[] = $elem;
            }
        }
        ?>
        <?php foreach ($filteredCards as $elem) { ?>
            <?php
            $backColor = "";
            match ($elem["type"]) {
                'water' => $backColor = "#00FFFF",
                'fire' => $backColor = "#FF4500",
                'grass' => $backColor = "#32CD32",
                'bug' => $backColor = "#9ACD32",
                'normal' => $backColor = "#C0C0C0",
                'fairy' => $backColor = "#EE82EE",
                'lightning' => $backColor = "#FFD700",
                'psychtic' => $backColor = "#FF1493",
                'fighting' => $backColor = "#B22222",
                'darkness' => $backColor = "#2F4F4F",
                'metal' => $backColor = "#8B4513",
                'colorless' => $backColor = "#D3D3D3",
                'dragon' => $backColor = "#8A2BE2",
                'poison' => $backColor = "#800080",
            }
            ?>


            <div class="w-1-3" style="background-color: <?= $backColor ?>;">
                <?php $link = "detailed.php?id=" . $elem["id"]; ?>
                <a href=<?= $link ?>>
                    <img class="mw-100" src="<?= $elem["image"] ?>">
                </a>

                <div class="text-center"><?= $elem["name"] ?> </div>
                <div class="text-center"><?= $elem["type"] ?> </div>
                <div class="d-flex gap-2 justify-content-center ">
                    <div class="text-center">❤️<?= $elem["hp"] ?></div>
                    <div class="text-center">⚔️<?= $elem["attack"] ?></div>
                    <div class="text-center">🛡️<?= $elem["defense"] ?> </div>
                </div>
                <form action="" method="post">
                    <?php if (isset($_SESSION["user"]) && in_array($elem["id"], $user_storage->findById("admin")["cards"]) && $_SESSION["user"]["id"] !== "admin") : ?>
                        <div class="d-flex justify-content-center ">
                            <button type="submit" class="btn btn-secondary" name="buy">💰<?= $elem["price"] ?></button>
                            <input type="hidden" name="cardId" value="<?= $elem["id"] ?>">
                            <input type="hidden" name="cardPrice" value="<?= $elem["price"] ?>">
                        </div>
                    <?php else : ?>
                        <div class="d-flex justify-content-center ">
                            <button type="submit" class="btn btn-secondary disabled ">💰<?= $elem["price"] ?></button>
                        </div>
                    <?php endif ?>
                </form>

            </div>


        <?php } ?>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>