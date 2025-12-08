<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    session_start();

    $isAdmin = isset($_SESSION["admin_authenticated"]);
    $authenticated = isset($_SESSION["user_authenticated"]);

    echo "Admin: " . ($isAdmin ? "true" : "false");
    echo "<br>";
    echo "Authenticated: " . ($authenticated ? "true" : "false");
    echo "<br>";

    if ($authenticated) {
        echo "<a href='logout.php'>logout</a>";
    }else{
        echo "<a href='login.php'>login</a>";
    }

    ?>
</body>

</html>