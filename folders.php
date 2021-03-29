<?php

require "./functionsForFolders.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["foldersPath"])) {
    $folderPath = getAllFoldersWrapper($_GET["foldersPath"]);
    if ($folderPath) {
        $formattedPath = rearrangeArray($folderPath);
    } else {
        $formattedPath = "Error reading folder path";
    }    
} 
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <title>Show folders</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="page_body">
    <div class="form_page">
        <form class="form" action="./folders.php" method="get" autocomplete="off">
            <label>
                <input type="text" name="foldersPath" placeholder="Введите директорию">
                <button type="submit">Отправить</button>
            </label>
        </form> 
    </div>
    <?php if (isset($formattedPath)) :?>
    <h1> Содержимое папки <?=htmlspecialchars($_GET["foldersPath"] ?? "")?></h1>

    <div class="folder_content">
        <?php 
        if (isset($formattedPath)) {
            print_r($formattedPath);
        }
        ?>
    </div>
    <?php endif;?>
</body>
</html>
