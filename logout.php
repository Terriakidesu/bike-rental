<?php
session_start();


if (isset($_SESSION["admin_authenticated"]) || isset($_SESSION["user_authenticated"])) {
    $_SESSION["admin_authenticated"] = false;
    $_SESSION["user_authenticated"] = false;
    $_SESSION["last_activity"] = 0;
}

session_destroy();

header("Location: ");

?>