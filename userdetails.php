<?php
include 'storage.php';

class UserStorage extends Storage
{
    public function __construct()
    {
        parent::__construct(new JsonIO('users.json'));
    }
}

session_start();
$user_storage = new UserStorage();

$cards = new Storage(new JsonIO('cards.json'));

$cardLimit = 5;

$remainingCards = $cardLimit - count($user_storage->findById($_SESSION["user"]["id"])["cards"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["sell"])) {
        $user_storage->removeCard($_SESSION["user"]["id"], $_POST["cardID"]);
        $user_storage->addCard("admin", $_POST["cardID"]);
        $user_storage->incrMoney($_SESSION["user"]["id"], $_POST["cardPrice"]);
        $remainingCards = $cardLimit - count($user_storage->findById($_SESSION["user"]["id"])["cards"]);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>User details</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">User details</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#"><?= $user_storage->findById($_SESSION["user"]["id"])["username"] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><?= $user_storage->findById($_SESSION["user"]["id"])["email"] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li>
                        <div class="btn btn-warning"><?= $user_storage->findById($_SESSION["user"]["id"])["money"] ?>💵</div>
                    </li>
                    <li>
                        <div class="btn btn-info">Remaining cards: <?= $_SESSION["user"]["id"] === "admin" ? "no limit :)" : $remainingCards ?></div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <h1> Owned cards by <span class="badge bg-secondary"> <?= $user_storage->findById($_SESSION["user"]["id"])["username"] ?></span></h1>

    <div class="d-flex gap-2 flex-wrap w-80">
        <?php foreach ($user_storage->findById($_SESSION["user"]["id"])["cards"] as $elem) { ?>
            <?php
            $backColor = "";
            match ($cards->findById($elem)["type"]) {
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

                <?php $link = "detailed.php?id=" . $cards->findById($elem)["id"]; ?>
                <a href=<?= $link ?>>
                    <img class="mw-100" src="<?= $cards->findById($elem)["image"] ?>">
                </a>

                <div class="text-center"><?= $cards->findById($elem)["name"] ?> </div>
                <div class="text-center"><?= $cards->findById($elem)["type"] ?> </div>
                <div class="d-flex gap-2 justify-content-center ">
                    <div class="text-center">❤️<?= $cards->findById($elem)["hp"] ?></div>
                    <div class="text-center">⚔️<?= $cards->findById($elem)["attack"] ?></div>
                    <div class="text-center">🛡️<?= $cards->findById($elem)["defense"] ?> </div>
                </div>
                <form action="" method="post">
                    <div class="d-flex justify-content-center ">
                        <?php if ($_SESSION["user"]["id"] !== "admin") : ?>
                            <button type="submit" class="btn btn-secondary " name="sell">
                                SELL 💸<s><?= $cards->findById($elem)["price"] ?></s> ->
                                <?= $cards->findById($elem)["price"] * 0.90 ?>
                            </button>
                            <input type="hidden" name="cardPrice" value="<?= $cards->findById($elem)["price"] * 0.90 ?>">
                            <input type="hidden" name="cardID" value="<?= $cards->findById($elem)["id"] ?>">


                        <?php endif ?>
                    </div>
                </form>

            </div>
        <?php } ?>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>

</html>