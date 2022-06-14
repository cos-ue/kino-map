<?php
/**
 * In this are all Functions for the authentication system.
 *
 * @package default
 */

if (!defined('NICE_PROJECT')) {
    die('Permission denied.');
}

/**
 * checks authdata provided by user on login
 * @param string $name username which has entered on login page
 * @param string $password password  which has entered on login page
 * @param bool $forceRemote selects if only remote auth is questioned
 * @return bool indicates if users auth-data is correct
 */
function getAuth($name, $password, $forceRemote = false)
{
    $localUsers = getAllUsernames(true);
    $UserData = "";
    $localUsed = true;
    dump($forceRemote, 3);
    if (in_array($name, $localUsers) && $forceRemote === false) {
        $UserData = getUserData($name);
        dump("local used", 3);
    } else {
        dump("remote used", 3);
        $resultRemote = getRemoteUserData($name);
        dump($resultRemote, 3);
        if ($resultRemote === false) {
            return false;
        }
        if ($resultRemote['name'] !== $name) {
            return false;
        }
        if (password_verify($password, $resultRemote['password'])) {
            if (in_array($name, $localUsers)) {
                dump("changed", 3);
                updateUser($resultRemote['name'], $resultRemote['password'], $resultRemote['email'], $resultRemote['firstname'], $resultRemote['lastname']);
            } else {
                dump("remote", 3);
                addUser($resultRemote['name'], $resultRemote['password'], $resultRemote['email'], $resultRemote['firstname'], $resultRemote['lastname']);
            }
            $resultRemote['from'] = 'COSE';
            setSessionData($resultRemote, false);
            logLogin("user");
            return true;
        }
        $localUsed = false;
    }
    dump($localUsed, 3);
    if (isset($UserData['password'])) {
        if (password_verify($password, $UserData['password'])) {
            $UserData['from'] = 'db';
            $UserData['result'] = true;
            setSessionData($UserData, false);
            dump("local used", 3);
            logLogin("user");
            return true;
        }
    }
    if ($localUsed) {
        getAuth($name, $password, true);
        dump("retry remote", 3);
    }
    if ($forceRemote) {
        return false;
    }
}

/**
 * Logs Login as Unique Visitor without logging username
 * @param string $type login type is guest or user
 */
function logLogin($type)
{
    insertLogUniqueVisitors(getUserIp(), $type);
}

/**
 * creates a static guest account with no rights, but all needed data that nothing fails
 */
function getGuestAuth()
{
    $Userdata = GuestAuthData();
    logLogin("guest");
    setSessionData($Userdata, true);
}

/**
 * privides Data for guest-login
 * @return array structured Data
 */
function GuestAuthData()
{
    $Userdata = array(
        'name' => 'gast',
        'firstname' => 'Forscher',
        'lastname' => '',
        'role' => array(
            'rolevalue' => config::$ROLE_GUEST,
            'rolename' => 'Gast'
        )
    );
    return $Userdata;
}

/**
 * sets sessiondata for user which is currently logging in
 * @param array $userData all data we know about that user
 * @param bool $external indicates if remote server has to be questioned
 */
function setSessionData($userData, $external = false)
{
    $_SESSION["username"] = $userData['name'];
    $_SESSION["firstName"] = $userData['firstname'];
    $_SESSION["lastName"] = $userData['lastname'];
    if (isset($userData['email'])){
        $_SESSION['email'] = $userData['email'];
    }
    if ($external) {
        $_SESSION['role'] = $userData['role']['rolevalue'];
        $_SESSION['rolename'] = $userData['role']['rolename'];
    } else {
        $role = remoteRole($userData['name']);
        $_SESSION['role'] = $role['role']['rolevalue'];
        $_SESSION['rolename'] = $role['role']['rolename'];
    }
}

/**
 * gets Userdata from remote server of cosp
 * @param string $name username of user which want to login
 * @return array|bool if user exists and is active user-data is returned as array otherwise result will be false
 */
function getRemoteUserData($name)
{
    $UserData = remoteLogin($name);
    dump($UserData, 3);
    if (key_exists("result", $UserData) == false) {
        return false;
    }
    if ($UserData['result'] == 0) {
        return false;
    }
    return $UserData;
}