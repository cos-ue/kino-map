<?php
/**
 * This file represents the logout function. It doesn't need any specific query-string or post information
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "bin/inc.php";
$guest = false;
if ($_SESSION['username'] == "gast") {
    $guest = true;
}
session_destroy();
if ($guest) {
    Redirect('index.php');
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Sie wurden ausgeloggt</title>
    <?php
    generateHeaderTags();
    ?>
</head>
<body>
<?php
generateHeader(false, $lang);
?>
<div class="container d-flex justify-content-center align-items-center">
    <div class="card card-login col-5 d-flex align-items-center">
        <h3 class="mb-3 mt-3" style="color: white; text-align: center">Ihre Abmeldung war erfolgreich.</h3>
        <a class="btn btn-warning mb-3 col-5" href="index.php<?php if (config::$MAINTENANCE) {
            echo "?m=0";
        } else if (config::$BETA) {
            echo "?b=0";
        } ?>">Zurück zum Login</a>
    </div>
</div>
</body>
</html>