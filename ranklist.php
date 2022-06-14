<?php
/**
 * Display of rank list for users
 * 
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "bin/inc.php";
if (isset($_SESSION["username"]) === false) {
    Redirect('index.php', false);
}
checkPermission(config::$ROLE_AUTH_USER);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Kino Karte</title>
    <?php
    generateHeaderTags();
    ?>
</head>

<body style="height: auto">
<?php
generateHeader(true, $lang);
$ApiKeyReq = getRanklist()['points'];
$ranktypesReq = getRanktypes()['ranktypes'];
ksort($ApiKeyReq);
$ranktypes = array();
ksort($ranktypesReq);
foreach ($ranktypesReq as $r) {
    $ranktypes[] = $r;
}
$ApiKey = array();
foreach ($ApiKeyReq as $a) {
    $ApiKey[] = $a;
}
dump($ranktypes, 3);
dump($ApiKey, 3);
?>
<div class="container mx-auto mt-4 text-light pt-5">
    <div class="justify-content-center mx-auto">
        <h1>Bestenliste</h1>
        <table class="table table-dark do-reflow reflow-ratio reflow-40">
            <tr>
                <th scope="col">Nutzername</th>
                <th scope="col">Rang</th>
                <th scope="col">Punkte Total</th>
                <th scope="col">Punkte im letzten Jahr</th>
                <?php
                if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
                    ?><th scope="col">Validierungswert</th><?php
                }
                ?>
            </tr><?php
            for ($k = sizeof($ApiKey) - 1; $k > -1; $k = $k - 1) {
                $API = $ApiKey[$k];
                ?>
                <tr><td><?= $API["name"]; ?></td>
                    <td><?php
                        $set = false;
                        dump($ranktypes, 8);
                        for ($i = sizeof($ranktypes) - 1; $i > -1; $i = $i - 1) {
                            if ($API["SUMTOTAL"] >= $ranktypes[$i]['value'] * 10) {
                                if ($set == false) {
                                    echo $ranktypes[$i]['name'];
                                    $set = true;
                                    break;
                                }
                            }
                        }
                        if ($set == false) {
                            $rname = "";
                            $rval = PHP_INT_MAX;
                            foreach ($ranktypes as $ranktype) {
                                if ($ranktype['value'] < $rval) {
                                    $rval = $ranktype['value'];
                                    $rname = $ranktype['name'];
                                }
                            }
                            echo $rname;
                        }
                        ?></td>
                    <td><?= $API["SUMTOTAL"]; ?></td>
                    <td><?= $API["SUMYEAR"]; ?>
                    </td><?php
                    if ($_SESSION['role'] >= config::$ROLE_EMPLOYEE) {
                        ?><td><?= getValidationValue($API["name"]); ?></td><?php
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>