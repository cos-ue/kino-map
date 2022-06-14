<?php
/**
 * Page with backgroundinformation of projekt
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "bin/inc.php";
if (config::$DEBUG) {
    if (isset($_SESSION['username']) == false) {
        Redirect('index.php');
    } else if (isset($_SESSION['username']) && $_SESSION['username'] == 'gast') {
        Redirect('index.php');
    }
}
if (config::$BETA || config::$MAINTENANCE) {
    if (isset($_SESSION['username']) == false) {
        Redirect('index.php');
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Impressum</title>
    <?php
    generateHeaderTags();
    ?>
</head>
<body style="height: auto">
<?php
generateHeader(isset($_SESSION['username']), $lang);
?>
<div class="container">
    <div class="mx-auto mt-4 text-light pt-5 pb-5 mb-3 col-lg-8">
        <h1>Projekthintergrund</h1>
        <p>
            Hier bitte den Projekthintergrund einfügen!
        </p>
    </div>
</div>
</body>
</html>