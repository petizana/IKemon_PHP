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

$errorMessages = [];
$name = isset($_POST) && isset($_POST["name"]) ? $_POST["name"] : "";
$type = isset($_POST) && isset($_POST["type"]) ? $_POST["type"] : "";
$hp = isset($_POST) && isset($_POST["hp"]) ? $_POST["hp"] : 0;
$attack = isset($_POST) && isset($_POST["attack"]) ? $_POST["attack"] : 0;
$defense = isset($_POST) && isset($_POST["defense"]) ? $_POST["defense"] : 0;
$price = isset($_POST) && isset($_POST["price"]) ? $_POST["price"] : 0;
$description = isset($_POST) && isset($_POST["description"]) ? $_POST["description"] : "";
$image = isset($_POST) && isset($_POST["image"]) ? $_POST["image"] : "";
$id = isset($_POST) && isset($_POST["id"]) ? $_POST["id"] : "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["register"])) {

        if (strlen($name < 1)) $errorMessages["name"] = "Name must be more than one character!";
        if (strlen($type < 1)) $errorMessages["type"] = "Type must be more than one character!";
        if ($hp <= 0) $errorMessages["hp"] = "Health Point must be more than 0!";
        if ($attack <= 0) $errorMessages["attack"] = "Attack must be more than 0!";
        if ($defense <= 0) $errorMessages["defense"] = "Defense must be more than 0!";
        if ($price <= 0) $errorMessages["price"] = "Price must be more than 0!";
        if (strlen($description) < 1) $errorMessages["description"] = "Description must be more than one character!";
        if (strlen($image)< 1) $errorMessages["image"] = "Image must be more than one character!";
        if (strlen($id)< 1) $errorMessages["id"] = "Id must be more than one character!";


        if (count($errorMessages) === 0) {
            global $name;
            global $type;
            global $hp;
            global $attack;
            global $defense;
            global $price;
            global $description;
            global $image;
            global $id;
            $oldId = $cards->add(
                [
                    'name' => $name,
                    'type' => $type,
                    'hp' => $hp,
                    'attack' => $attack,
                    'defense' => $defense,
                    'price' => $price,
                    'description' => $description,
                    'image' => $image,
                    'id' => $id

                ]

            );
            $cards->changeId($oldId, $id);
            echo "<script type='text/javascript'>alert('Successfully added!');</script>";
        }
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
    <title>NEW CARD</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Adding a new Card</a>
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
                </ul>
            </div>
        </div>
    </nav>

    <form action="new_card.php" method="post" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Pokémon name</label>
            <?php if (!isset($errorMessages["name"])) : ?>
                <input type="text" class="form-control is-valid" id="name" name="name" value="<?= $name ?>" aria-describedby="nameErrorMsg">
            <?php else : ?>
                <input type="text" class="form-control is-invalid" id="name" name="name" value="<?= $name ?>" aria-describedby="nameErrorMsg">
                <div id="nameErrorMsg" class="invalid-feedback"><?= $errorMessages["name"] ?></div>
            <?php endif ?>

        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Pokémon type</label>
            <?php if (!isset($errorMessages["type"])) : ?>
                <input type="text" class="form-control is-valid" id="type" name="type" value="<?= $type ?>" aria-describedby="typeErrorMsg">
            <?php else : ?>
                <input type="text" class="form-control is-invalid" id="type" name="type" value="<?= $type ?>" aria-describedby="typeErrorMsg">
                <div id="typeErrorMsg" class="invalid-feedback"><?= $errorMessages["type"] ?></div>
            <?php endif ?>
        </div>
        <div class="mb-3">
            <label for="hp" class="form-label">Health Point</label>
            <?php if (!isset($errorMessages["hp"])) : ?>
                <input type="number" class="form-control is-valid" id="hp" name="hp" value="<?= $hp ?>" aria-describedby="hpErrorMsg">
            <?php else : ?>
                <input type="number" class="form-control is-invalid" id="hp" name="hp" value="<?= $hp ?>" aria-describedby="hpErrorMsg">
                <div id="hpErrorMsg" class="invalid-feedback"><?= $errorMessages["hp"] ?></div>
            <?php endif ?>
        </div>
        <div class="mb-3">
            <label for="attack" class="form-label">Attack points</label>
            <?php if (!isset($errorMessages["attack"])) : ?>
                <input type="number" class="form-control is-valid" id="attack" name="attack" value="<?= $attack ?>" aria-describedby="attackErrorMsg">
            <?php else : ?>
                <input type="number" class="form-control is-invalid" id="attack" name="attack" value="<?= $attack ?>" aria-describedby="attackErrorMsg">
                <div id="attackErrorMsg" class="invalid-feedback"><?= $errorMessages["attack"] ?></div>
            <?php endif ?>
        </div>

        <div class="mb-3">
            <label for="defense" class="form-label">Defense points</label>
            <?php if (!isset($errorMessages["defense"])) : ?>
                <input type="number" class="form-control is-valid" id="defense" name="defense" value="<?= $defense ?>" aria-describedby="defenseErrorMsg">
            <?php else : ?>
                <input type="number" class="form-control is-invalid" id="defense" name="defense" value="<?= $defense ?>" aria-describedby="defenseErrorMsg">
                <div id="defenseErrorMsg" class="invalid-feedback"><?= $errorMessages["defense"] ?></div>
            <?php endif ?>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price points</label>
            <?php if (!isset($errorMessages["price"])) : ?>
                <input type="number" class="form-control is-valid" id="price" name="price" value="<?= $price ?>" aria-describedby="priceErrorMsg">
            <?php else : ?>
                <input type="number" class="form-control is-invalid" id="price" name="price" value="<?= $price ?>" aria-describedby="priceErrorMsg">
                <div id="priceErrorMsg" class="invalid-feedback"><?= $errorMessages["price"] ?></div>
            <?php endif ?>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <?php if (!isset($errorMessages["description"])) : ?>
                <input type="text" class="form-control is-valid" id="description" name="description" value="<?= $description ?>" aria-describedby="descriptionErrorMsg">
            <?php else : ?>
                <input type="text" class="form-control is-invalid" id="description" name="description" value="<?= $description ?>" aria-describedby="descriptionErrorMsg">
                <div id="descriptionErrorMsg" class="invalid-feedback"><?= $errorMessages["description"] ?></div>
            <?php endif ?>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image (with URL)</label>
            <?php if (!isset($errorMessages["image"])) : ?>
                <input type="text" class="form-control is-valid" id="image" name="image" value="<?= $image ?>" aria-describedby="imageErrorMsg">
            <?php else : ?>
                <input type="text" class="form-control is-invalid" id="image" name="image" value="<?= $image ?>" aria-describedby="imageErrorMsg">
                <div id="imageErrorMsg" class="invalid-feedback"><?= $errorMessages["image"] ?></div>
            <?php endif ?>
        </div>

        <div class="mb-3">
            <label for="id" class="form-label">ID</label>
            <?php if (!isset($errorMessages["id"])) : ?>
                <input type="text" class="form-control is-valid" id="id" name="id" value="<?= $id ?>" aria-describedby="idErrorMsg">
            <?php else : ?>
                <input type="text" class="form-control is-invalid" id="id" name="id" value="<?= $id ?>" aria-describedby="idErrorMsg">
                <div id="idErrorMsg" class="invalid-feedback"><?= $errorMessages["id"] ?></div>
            <?php endif ?>
        </div>

        <button type="submit" class="btn btn-primary" name="register">Register</button>
    </form>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>

</html>