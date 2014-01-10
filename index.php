<?php
$strRootFolder = $_SERVER['DOCUMENT_ROOT'] . '/discuss';
require_once($strRootFolder . '/helpers/Session.helper.php');
SessionHelper::startSession();

require_once($strRootFolder . '/controllers/Base.controller.php');
$objBaseController = new BaseController();

$objBaseController->_requireController('User');
$objUserController = new UserController();
if($_SESSION['blnLoggedIn']) {
    $objUserController->_objUserModel->setUserActive($_SESSION['intUserId']);
}
require_once($strRootFolder . '/helpers/Cookie.helper.php');
$strCookieData = CookieHelper::get('discuss');
if(null !== $strCookieData && '' !== $strCookieData) {
    $objUserController->doUserCookieLogin($strCookieData);
}

if((!isset($_GET["page"]) || $_GET["page"] === "frontpage")  && $_SESSION['blnLoggedIn']) {
    header("Location: /discuss/index.php?page=lobby");
    exit;
}

$objBaseController->_requireController('Chat');
$objChatController = new ChatController();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Discuss</title>
        <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/sceditor/buttons.css" type="text/css" media="all" />
        <link rel="stylesheet" href="css/styles.css?ver=1.2">

        <script src="js/general.js?ver=1.0"></script>
        <script src="js/ajax-connector.js?ver=1.0"></script>
        <script src="js/form-submit.js?ver=1.0"></script>
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/main.js?ver=1.0"></script>

        <script type="text/javascript" src="js/jquery.sceditor.xhtml.js"></script>
        <script src="js/main.js"></script>
        <script src="js/pass.js"></script>
    </head>
    <body>
        <div class="row head">
            <div class="container">
                <a href="index.php?page=frontpage"><h1 class="pull-left" id="page-title">discuss</h1></a>
                <? if (!isset($_GET["page"]) || $_GET["page"] == "frontpage"): ?>
                    <a href="index.php?page=lobby"><button type="button" id="continue" class="btn btn-primary pull-right">Continue without login</button></a>
                <? else: ?>
                    <div class="profilemenu pull-right">
                        <? if ($_SESSION["blnLoggedIn"]): ?>
                            Welcome <a href="index.php?page=profile&amp;user=<? echo $_SESSION['strUsername']?>"><?php echo $_SESSION["strUsername"]; ?></a>
                            <a href="#" id="link-log-out" >Sign out</a>
                        <? else: ?>
                            <a href="index.php?page=frontpage">Sign in / Sign up</a>
                        <? endif; ?>
                    </div>
                <? endif; ?>
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
