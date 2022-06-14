<?php
/**
 * In this file are all functions to get data for statistics page
 *
 * @package default
 */


if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * formats correct for login/user statistical data
 * @param array $Input structured request data
 * @return array structured result data for chart tool
 */
function loginStatistics($Input)
{
    $dataI = $Input['data'];
    $result = array();
    foreach ($dataI as $dateI) {
        $amount = $dateI['Amount'];
        $type = $dateI['type'];
        $data = array();
        switch ($type) {
            case 'D':
                $data = getStatisticalDataLastDays($amount);
                break;
            case 'W':
                $data = getStatisticalDataLastWeeks($amount);
                break;
            case 'M':
                $data = getStatisticalDataLastMonth($amount);
                break;
            case 'Y':
                $data = getStatisticalDataLastYear($amount);
                break;
        }
        $guestData = array();
        $userData = array();
        foreach ($data as $date) {
            if ($date['type'] == 'user') {
                $userData[] = array(
                    'x' => $date['time'],
                    'y' => $date['counter']
                );
            } else if ($date['type'] == 'guest') {
                $guestData[] = array(
                    'x' => $date['time'],
                    'y' => $date['counter']
                );
            }
        }
        $userData = fillUnknownData($userData, $amount, $type);
        $guestData = fillUnknownData($guestData, $amount, $type);
        $UserDataset = createGraph($userData, "rgba(39,135,28,0.4)", "rgba(39,135,28,1)", "Nutzer", true);
        $GuestDataset = createGraph($guestData, "rgba(211,47,57,0.4)", "rgba(211,47,57,1)", "Gäste");
        $result[] = array_merge($dateI, array('data' => array($UserDataset, $GuestDataset)));
    }
    return $result;
}

/**
 * inserts missing dates
 * @param array $statisticalData given statistical data
 * @param int $periodeAmount number of amount of timespan of type
 * @param string $type timespan is given in month, year, days, weeks
 * @return array with completed data
 */
function fillUnknownData($statisticalData, $periodeAmount, $type)
{
    $dates = array();
    $identifier = "";
    switch ($type) {
        case 'D':
            $identifier = "D";
            break;
        case 'W':
            $identifier = "W";
            break;
        case 'M':
            $identifier = "M";
            break;
        case 'Y':
            $identifier = "Y";
            break;
    }
    $periode = new DateInterval("P" . $periodeAmount . $identifier);
    $enddate = new DateTime(date("Y-m-d"));
    $enddate->sub($periode);
    $dates_periode_gen = new DatePeriod(
        new DateTime($enddate->format('Y-m-d')),
        new DateInterval('P1D'),
        new DateTime(date("Y-m-d"))
    );
    foreach ($statisticalData as $date) {
        $dates[] = $date['x'];
    }
    foreach ($dates_periode_gen as $value) {
        $date = $value->format('Y-m-d');
        if (in_array($date, $dates) == false) {
            $statisticalData[] = array('x' => $date, 'y' => 0);
        }
    }
    $test = true;
    while ($test) {
        $test = false;
        for ($i = 0; $i < count($statisticalData) - 1; $i++) {
            if ($statisticalData[$i]['x'] > $statisticalData[$i + 1]['x']) {
                $bucket = $statisticalData[$i];
                $statisticalData[$i] = $statisticalData[$i + 1];
                $statisticalData[$i + 1] = $bucket;
                $test = true;
            }
        }
    }
    if ($statisticalData[count($statisticalData) - 1]['x'] !== date('Y-m-d')) {
        $statisticalData[] = array('x' => date('Y-m-d'), 'y' => 0);
    }
    return $statisticalData;
}

/**
 * creates structures for graphs
 * @param array $data data to show
 * @param string $colorBg rgba background color
 * @param string $colorFont rgba background font
 * @param string $label the name which should be stand for the line of things
 * @param boolean $fill defines if graph is filled; standard is false
 * @return array return structured information
 */
function createGraph($data, $colorBg, $colorFont, $label, $fill = false)
{
    $Bg = array();
    $font = array();
    if (count($data) > 0) {
        for ($i = 0; $i < count($data); $i++) {
            $Bg[] = $colorBg;
            $font[] = $colorFont;
        }
    } else {
        $Bg[] = $colorBg;
        $font[] = $colorFont;
    }
    $Dataset = array(
        'label' => $label,
        'data' => $data,
        'backgroundColor' => $Bg,
        'borderColor' => $font,
        'borderWidth' => 3,
        "lineTension" => 0,
        "fill" => $fill
    );
    return $Dataset;
}

/**
 * formats correct for poi statistical data
 * @param array $Input structured request data
 * @param string $source selects the source for the input
 * @return array structured result data for chart tool
 */
function CreateStatistics($Input, $source)
{
    $dataI = $Input['data'];
    $result = array();
    $label = "";
    $poi = false;
    $comment = false;
    switch ($source) {
        case "poi":
            $poi = true;
            $label = "Neue oder Geänderte Interessenpunkte";
            break;
        case "com":
            $comment = true;
            $label = "Angelegte oder Geänderte Kommentare";
            break;
    }
    foreach ($dataI as $dateI) {
        $amount = $dateI['Amount'];
        $type = $dateI['type'];
        $data = array();
        switch ($type) {
            case 'D':
                if ($poi) {
                    $data = getPoiCreateStatisticalDataLastDays($amount);
                } else if ($comment) {
                    $data = getCommentsCreateStatisticalDataLastDays($amount);
                }
                break;
            case 'W':
                if ($poi) {
                    $data = getPoiCreateStatisticalDataLastWeeks($amount);
                } else if ($comment) {
                    $data = getCommentsCreateStatisticalDataLastWeeks($amount);
                }
                break;
            case 'M':
                if ($poi) {
                    $data = getPoiCreateStatisticalDataLastMonth($amount);
                } else if ($comment) {
                    $data = getCommentsCreateStatisticalDataLastMonth($amount);
                }
                break;
            case 'Y':
                if ($poi) {
                    $data = getPoiCreateStatisticalDataLastYear($amount);
                } else if ($comment) {
                    $data = getCommentsCreateStatisticalDataLastYear($amount);
                }
                break;
        }
        $finalData = array();
        foreach ($data as $date) {
            $finalData[] = array(
                'x' => $date['time'],
                'y' => $date['counter']
            );
        }
        $finalData = correctData($finalData);
        $finalData = fillUnknownData($finalData, $amount, $type);
        $UserDataset = createGraph($finalData, "rgba(39,135,28,0.4)", "rgba(39,135,28,1)", $label, true);
        $result[] = array_merge($dateI, array('data' => array($UserDataset)));
    }
    return $result;
}

/**
 * corrects given data
 * @param array $inputArray structured input array
 * @return array structured results
 */
function correctData($inputArray)
{
    $orderdByDate = array();
    $dates = array();
    foreach ($inputArray as $data) {
        if (in_array($data['x'], $dates) == false) {
            $dates[] = $data['x'];
        }
    }
    foreach ($dates as $date) {
        $orderdByDate[$date] = 0;
    }
    foreach ($inputArray as $data) {
        if (is_int($data['y']) == false) {
            $orderdByDate[$data['x']] += intval($data['y']);
        }
    }
    $result = array();
    foreach (array_keys($orderdByDate) as $key) {
        $result[] = array('x' => $key, 'y' => $orderdByDate[$key]);
    }
    return $result;
}

/**
 * sends statistical data on poi validated values
 * @return array structured result
 */
function CreatePoiValidationStats()
{
    $data = getStatisticsValidatedPoiData();
    $result = array(
        array(
            "ID" => 8,
            "data" => array(
                array(
                    "data" => array(
                        $data['Validated'],
                        $data['PartValidated'],
                        $data['unvalidated']
                    ),
                    'backgroundColor' => ["rgba(0,255,0,0.4)", "rgba(0,0,255,0.4)", "rgba(255,0,0,0.4)"],
                    'borderColor' => ["rgba(0,255,0,1)", "rgba(0,0,255,1)", "rgba(255,0,0,1)"]
                )
            ),
            "labels" => array(
                "Validiert",
                "Teilvalidiert",
                "Unvalidiert"
            ),
            "total" => $data['Total']
        )
    );
    return $result;
}