<?php
/**
 * In here all basic database function, all other database functions depending on these two. Currently only PDO is implemented.
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * creates PDO for Database given in config-file
 * @return PDO
 */
function getPdo($options = false)
{
    // PDO::ATTR_EMULATE_PREPARES => false to get normal values ; must check code first
    $OPTIONS = array();
    if ($options) {
        $OPTIONS = array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        );
    }
    return new PDO('mysql:host=' . config::$SQL_SERVER . ';dbname=' . config::$SQL_SCHEMA . ';charset=utf8', config::$SQL_USER, config::$SQL_PASSWORD, $OPTIONS);
}

/**
 * Executes an SQL-Query without reading the result
 * @param string $prep_stmt SQL-Query with placeholders, prepared to bind params
 * @param array $params Array of params which should be bind, has to be the order which the '?' in the prepared statement are
 * @return bool|null On Success there will be true returned
 */
function ExecuteStatementWOR($prep_stmt, $params = array())
{
    dump($prep_stmt, 7);
    dump($params, 7);
    if (config::$SQL_Connector == "pdo") {
        $pdo = getPdo();
        $sql = $pdo->prepare($prep_stmt);
        if (count($params) > 0) {
            for ($i = 0; $i < count($params); $i++) {
                $sql->bindParam($i + 1, $params[$i]['val']);
            }
        }
        $result = $sql->execute();
        dump($result, 7);
        return $result;
    }
    return null;
}

/**
 * Executes an SQL-Query with or without reading the result
 * @param string $prep_stmt SQL-Query with placeholders, prepared to bind params
 * @param array $params if there are '?' then params has to be the order which the '?' in the prepared statement are otherwise the order is not important
 * @param bool $read Selects if result shout be read, if not set, the result will be read
 * @param bool $disableNull Selects if a given parameter can be casted to 'NULL' or not.
 * @return array|bool|null Depending on read: If true: the result is an array else on Success there will be true returned
 */
function ExecuteStatementWR($prep_stmt, $params, $read = true, $disableNull = false)
{
    dump($prep_stmt, 7);
    if (config::$SQL_Connector == "pdo") {
        $pdo = getPdo();
        $sql = $pdo->prepare($prep_stmt);
        if (count($params) > 0) {
            for ($i = 0; $i < count($params); $i++) {
                if ($params[$i]['val'] == "" && $disableNull == false) {
                    $sql->bindValue($params[$i]['nam'], null, PDO::PARAM_NULL);
                    dump("Set NUll.", 8);
                } else {
                    $sql->bindParam($params[$i]['nam'], $params[$i]['val']);
                }
            }
        }
        $result2 = $sql->execute();
        if ($read) {
            $result = $sql->fetchAll();
            dump($result, 7);
            return $result;
        }
        dump($sql->errorInfo(), 9);
        return $result2;
    }
    return null;
}
