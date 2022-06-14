<?php
/**
 * This File includes all needed functions for user-login-table
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * Adds an user to the Database and sets initial field values.
 * @param string $name Username
 * @param string $pwd_hash Calculated hash of Password, is calculated somewhere else
 * @param string $email E-Mail, which belongs to User
 * @param string $firstname Firstname of User, facultative
 * @param string $lastname Lastname of User, facultative
 * @return bool|null On Success there will be true returned
 */
function addUser($name, $pwd_hash, $email, $firstname = "", $lastname = "")
{
    $prep_stmt = 'INSERT INTO `' . config::$SQL_PREFIX . 'user-login` ( `name` , `password` , `firstname` , `lastname` , `email` ) VALUES ( ? , ? , ? , ? , ? );';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $name;
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $pwd_hash;
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $firstname;
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $lastname;
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = $email;
    $result = ExecuteStatementWOR($prep_stmt, $params);
    return $result;
}

/**
 * Updates data for given User
 * @param string $name Username
 * @param string $pwd_hash Calculated hash of Password, is calculated somewhere else
 * @param string $email E-Mail, which belongs to User
 * @param string $firstname Firstname of User, facultative
 * @param string $lastname Lastname of User, facultative
 * @return bool|null On Success there will be true returned
 */
function updateUser($name, $pwd_hash, $email, $firstname = "", $lastname = "")
{
    $prep_stmt = 'Update `' . config::$SQL_PREFIX . 'user-login` SET password = :password , firstname = :firstname , lastname = :lastname , email = :email where name = :name ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $name;
    $params[0]['nam'] = ":name";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $pwd_hash;
    $params[1]['nam'] = ":password";
    $params[2] = array();
    $params[2]['typ'] = 's';
    $params[2]['val'] = $firstname;
    $params[2]['nam'] = ":firstname";
    $params[3] = array();
    $params[3]['typ'] = 's';
    $params[3]['val'] = $lastname;
    $params[3]['nam'] = ":lastname";
    $params[4] = array();
    $params[4]['typ'] = 's';
    $params[4]['val'] = $email;
    $params[4]['nam'] = ":email";
    $result = ExecuteStatementWR($prep_stmt, $params, false);
    return $result;
}

/**
 * selects all database fields for given username
 * @param string $name Username of User, which should be selected
 * @return array|bool|null If User exists there will be an array of userdata returned otherwise there will only on success true without array returned
 */
function getUserData($name)
{
    $prep_stmt = 'select * from `' . config::$SQL_PREFIX . 'user-login` where name = :name ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $name;
    $params[0]['nam'] = ":name";
    dump($params, 8);
    $result = ExecuteStatementWR($prep_stmt, $params);
    if (count($result) > 0) {
        $result = $result[0];
    }
    dump($result, 7);
    return $result;
}

/**
 * selects all database fields for given username
 * @param string $uid Username of User, which should be selected
 * @return array|bool|null If User exists there will be an array of userdata returned otherwise there will only on success true without array returned
 */
function getUserDataById($uid)
{
    $prep_stmt = 'select * from `' . config::$SQL_PREFIX . 'user-login` where id = :id ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $uid;
    $params[0]['nam'] = ":id";
    dump($params, 8);
    $result = ExecuteStatementWR($prep_stmt, $params);
    if (count($result) > 0) {
        $result = $result[0];
    }
    dump($result, 7);
    return $result;
}

/**
 * selects all usernames which are in local database and cosp-auth-database
 * @param bool $onlyLocal selects only usernames which are in local database
 * @return array Result will be array of all existent usernames
 */
function getAllUsernames($onlyLocal = false)
{
    $prep_stmt = 'select name from `' . config::$SQL_PREFIX . 'user-login` ;';
    $params = array();
    $result = ExecuteStatementWR($prep_stmt, $params);
    $tmp = array();
    foreach ($result as $res) {
        $tmp[] = $res['name'];
    }
    dump($tmp, 7);
    if ($onlyLocal === false) {
        $tmp2 = getRemoteAllUsernames();
        $tmp = array_merge($tmp, $tmp2);
    }
    return $tmp;
}

/**
 * aktivates or deaktivates an useraccount
 * @param string $username username of account which should be disabled or enabled
 * @param bool $state sets deaktivation state, if true account will be disabled
 * @return bool|null On success there will be true returned
 */
function updateDeaktivate($username, $state)
{
    $prep_stmt = 'update `' . config::$SQL_PREFIX . 'user-login` SET `deaktivate` = :deaktivate WHERE `' . config::$SQL_PREFIX . 'user-login`.`name` = :name ;';
    $params = array();
    $params[0] = array();
    $params[0]['typ'] = 's';
    $params[0]['val'] = $username;
    $params[0]['nam'] = ":name";
    $params[1] = array();
    $params[1]['typ'] = 's';
    $params[1]['val'] = $state;
    $params[1]['nam'] = ":deaktivate";
    return ExecuteStatementWR($prep_stmt, $params, false, true);
}