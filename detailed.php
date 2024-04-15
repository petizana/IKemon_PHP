<?php
include 'storage.php';
$cards = new Storage(new JsonIO('cards.json'));
$id = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    }
}
$detailedElement = $cards->findById($id);


$backColor = "white";
match ($detailedElement["type"]) {
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
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $detailedElement["name"] ?> részletező</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="w-1-3">
        <img class="mw-100" src="<?= $detailedElement["image"] ?>">
        <div class="text-center"><?= $detailedElement["name"] ?> </div>
        <div class="text-center"><?= $detailedElement["type"] ?> </div>
        <div class="d-flex gap-2 justify-content-center ">
            <div class="text-center">❤️<?= $detailedElement["hp"] ?></div>
            <div class="text-center">⚔️<?= $detailedElement["attack"] ?></div>
            <div class="text-center">🛡️<?= $detailedElement["defense"] ?> </div>
        </div>
        <div class="d-flex justify-content-center ">
            <button type="submit" class="btn btn-secondary disabled ">💰<?= $detailedElement["price"] ?></button>
        </div>
        <div class="text-center"><?= $detailedElement["description"] ?></div>
        <form action="index.php" method="post">
            <button type="submit" class="btn btn-primary">Vissza a főoldalra</a></button>
        </form>

    </div>
    <script>
        document.body.style.backgroundColor = "<?= $backColor ?>";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>