<?php
/**
 * Display of statistics with chart.js, mostly frame chart.js and supply data to get statistical data from api
 *
 * @package default
 */

/**
 * @const enables loading of other files without dying to improve security
 */
define('NICE_PROJECT', true);
require_once "bin/inc.php";
if (isset($_SESSION["username"]) == false) {
    Redirect("index.php");
    permissionDenied();
}
checkPermission(config::$ROLE_EMPLOYEE);
$type = 'login';

/**
 * all types whoch are currently availible to display on statistics page
 */
$allowedTypes = array(
    'login' => "Nutzungsdaten",
    'poisc' => "Angelegte/Geänderte Interessenpunkte",
    "comm" => "Angelegte/Geänderte Kommentare",
    'poival' => "validierte Interessenpunkte"
);

/*
 * array with all timespanes for which diagrams are generated
 */
$diagramms = array(
    array(
        "type" => 'D',
        "Amount" => 3,
        "ID" => 0
    ),
    array(
        "type" => 'D',
        "Amount" => 5,
        "ID" => 1
    ),
    array(
        "type" => 'W',
        "Amount" => 2,
        "ID" => 2
    ),
    array(
        "type" => 'W',
        "Amount" => 4,
        "ID" => 3
    ),
    array(
        "type" => 'M',
        "Amount" => 3,
        "ID" => 4
    ),
    array(
        "type" => 'M',
        "Amount" => 6,
        "ID" => 5
    ),
    array(
        "type" => 'M',
        "Amount" => 9,
        "ID" => 6
    ),
    array(
        "type" => 'Y',
        "Amount" => 1,
        "ID" => 7
    )
);

if (isset($_GET['type'])) {
    $newType = filter_input(INPUT_GET, 'type');
    if (key_exists($newType, $allowedTypes)) {
        $type = $newType;
    }
}

if ($type == 'poival') {
    $diagramms = array(
        array(
            "type" => 'B',
            "Amount" => 0,
            "ID" => 8
        )
    );
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Kino Karte - Statistik</title>
    <?php
    generateHeaderTags(
        array(
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "jse/moment.min.js"
            ),
            array(
                "type" => "link",
                "rel" => "stylesheet",
                "href" => "css/statistics.css",
                "hrefmin" => "css/statistics.min.css"
            ),
            array(
                "type" => "link",
                "rel" => "stylesheet",
                "href" => "csse/Chart.min.css"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "jse/Chart.min.js"
            ),
            array(
                "type" => "script",
                "typeval" => "text/javascript",
                "href" => "js/statistics.js",
                "hrefmin" => "js/statistics.min.js"
            )
        )
    );
    ?>
</head>

<body style="height: auto">
<?php
generateHeader(true, $lang);
?>
<div class="container text-light pt-3">
    <div style="margin-top: 50px; width: 100%;">
        <div class="container p-0" style="display: flow-root">
            <h1 class="float-left">Statistik</h1>
            <div class="float-left full-400">
                <div class="dropdown">
                    <button class="btn btn-secondary ml-5 dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $allowedTypes[$type]; ?>
                    </button>
                    <div class="dropdown-menu poi-dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php
                        foreach (array_keys($allowedTypes) as $key) {
                            ?>
                            <a class="dropdown-item poi-dropdown-item"
                               href="statistics.php?<?php echo http_build_query(array('type' => $key), '', '&amp;'); ?>"><?php echo $allowedTypes[$key]; ?></a>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var loading = <?php generateJson(array('src' => $type, 'data' => $diagramms)) ?>;
            loadStatisticalData();
        </script>
        <?php
        foreach ($diagramms as $diagramm) {
            if ($type != 'poival') {
                $title = "Letzte " . $diagramm['Amount'] . ' ';
                switch ($diagramm['type']) {
                    case 'D':
                        $title .= "Tage";
                        break;
                    case 'W':
                        $title .= "Wochen";
                        break;
                    case 'M':
                        $title .= "Monate";
                        break;
                    case 'Y':
                        $title .= "Jahre";
                        break;
                }
            } else if ($type == 'poival') {
                $title = "Validierungstatistik der Interessenpunkte";
            }
            ?>
            <div class="mb-3">
                <h2><?php echo $title ?></h2>
                <div class="card card-statistics align-content-center">
                    <canvas id="myChart_<?php echo $diagramm['ID'] ?>"></canvas>
                    <?php
                    if ($type != 'poival') {
                        ?>
                        <script type="text/javascript">
                            var ctx_<?php echo $diagramm['ID'] ?> = document.getElementById('myChart_<?php echo $diagramm['ID'] ?>').getContext('2d');
                            var myChart = new Chart(ctx_<?php echo $diagramm['ID'] ?>, generateConfig(<?php echo $diagramm['ID'] ?>, '<?php echo $diagramm['type']?>'));
                        </script>
                    <?php
                    } else if ($type == 'poival') {
                    ?>
                        <script type="text/javascript">
                            var ctx_<?php echo $diagramm['ID'] ?> = document.getElementById('myChart_<?php echo $diagramm['ID'] ?>').getContext('2d');
                            var myChart = new Chart(ctx_<?php echo $diagramm['ID'] ?>, generateConfig(<?php echo $diagramm['ID'] ?>, '<?php echo $diagramm['type']?>'));
                            placeValueMiddleDoughnot(<?php echo $diagramm['ID'] ?>);
                        </script>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
</body>
</html>