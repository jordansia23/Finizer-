<?php
// Start or resume the session so we can properly destroy it
session_start();
// 1. Unset all session variables (clears $_SESSION array)
session_unset();
// 2. Destroy the session data on the server
session_destroy();
// 3. Delete the session cookie on the client
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(                 
        session_name(),      // the name of the session cookie (e.g., PHPSESSID)
        '',                  // empty value
        time() - 30,       // expiration time in the past
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}
// 4. Optionally redirect to a login or home page
header("Location: landing.php");
exit;
//setcookie()-Explicitly tells the browser to remove the session cookie (e.g., PHPSESSID) by setting it to an empty value with an expiration time in the past.
//Using session_get_cookie_params() ensures you match the original cookie’s path, domain, and flags.
//ini_get("session.use_cookies") is a PHP function call that reads a configuration directive from php.ini—specifically the session.use_cookies setting.
?>