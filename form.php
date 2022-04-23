<?php

$name = "none";
$errors = [];
$defaultPicture = "public/uploads/default.png";
$uploadFile = $defaultPicture;

// Puis un script PHP
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $authorizedExtensions = ['jpg', 'gif', 'png', 'webp'];
    $maxFileSize = 1000000;

    // ------------- VALIDATION

    // Extension
    if ((!in_array($extension, $authorizedExtensions))) {
        $errors[] = 'Invalid format (jpg, gif, png, webp)';
    }

    // Size
    if (file_exists($_FILES['avatar']['tmp_name']) && filesize($_FILES['avatar']['tmp_name']) > $maxFileSize) {
        $errors[] = "File too large";
    }

    if (!isset($_POST["lastname"])) {
        $errors[] = "Please enter your name";
    }

    // ------------- SAVING FILE
    if (empty($errors)) {
        $name = trim($_POST["lastname"]);
        $nameForFile = str_replace(' ', '_', strtolower($name . "_"));
        $uploadDir = 'public/uploads/';
        $uploadFile = $uploadDir . uniqid($prefix = $nameForFile, $more_entropy = true) . "_" . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);
    }

    // ------------- DELETION
    if (isset($_POST["delete"]) && $uploadFile != $defaultPicture) {
        if (file_exists($uploadFile)) {
            unlink($uploadFile);
        }
        header("Location: /form.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Form</title>
</head>

<body>
    <?php
    foreach ($errors as $error) { ?>
        <div class="alert alert-danger errorsMessage"><?= $error; ?></div>
    <?php } ?>
    <section>
        <h2 class="mt-5 text-center">Your license:</h2>
        <div class="card mt-5 container-sm"">
        <img class=" card-img-top" src="<?= $uploadFile ?>" alt="Profile picture">
            <div class="card-body">
                <h3 class="card-title">Name: <?= $name ?></h3>
            </div>
            <?php if ($uploadFile != $defaultPicture) { ?>
                <div class="position-absolute top-100 start-50 translate-middle">
                    <form method="post">
                        <button class="btn btn-primary" name="delete">Delete picture</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </section>
    <section>
        <h2 class="mt-5 text-center">File upload:</h2>
        <form class="container-sm" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label" for="imageUpload">Picture:</label>
                <input class="form-control" type="file" name="avatar" id="imageUpload" required />
            </div>
            <div class="mb-3">
                <label class="form-label" for="lastname">Full name:</label>
                <input class="form-control" type="text" name="lastname" id="lastname" required />
            </div>
            <button class="btn btn-primary" name="send">Send</button>
        </form>
    </section>
</body>

</html>