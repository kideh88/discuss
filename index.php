<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Discuss</title>
        <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/styles.css">
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/main.js"></script>
    </head>
    <body>
        <div class="row head">
            <div class="container">
                <h1 class="pull-left">Welcome to the chat</h1>
                <?php if (!isset($_GET["page"]) || $_GET["page"] == "frontpage"): ?>
                    <a href="index.php?page=lobby"><button type="button" id="continue" class="btn btn-primary pull-right">Continue without login</button></a>
                <?php else: ?>
                    <div class="profilemenu pull-right">
                        <?php if (isset($_GET["login"])): /* SESSION */ ?>
                            <a href="index.php?page=profile">Welcome Mikkel</a>
                            <a href="index.php?page=frontpage">Sign out</a>
                        <?php else: ?>
                            <a href="index.php?page=frontpage">Sign in / Sign up</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="container">
            <div class="row content">
                <?php
                if (isset($_GET["page"])) {
                    if (file_exists("views/" . $_GET["page"] . ".php")) {
                        include_once "views/" . $_GET["page"] . ".php";
                    } else {
                        include_once "views/404.php";
                    }
                } else {
                    include_once "views/frontpage.php";
                }
                ?>
            </div>
        </div>
    </body>
</html>
