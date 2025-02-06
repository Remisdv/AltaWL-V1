<?php
/* Useful functions.
 * This file contains some useful functions for demo.
 * @author : MarkisDev
 * @copyright : https://markis.dev
 */
 
# A function to redirect user.
function redirect($url)
{
    if (!headers_sent())
    {    
        header('Location: '.$url);
        exit;
    }
    else
    {  
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
        exit;
    }
}

# A function which returns users IP
function client_ip()
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}

# Check user's avatar type
function is_animated($avatar)
{
    $ext = substr($avatar, 0, 2);
    if ($ext == "a_")
    {
        return ".gif";
    }
    else
    {
        return ".png";
    }
}

# Function to retry getting user info
function retry_get_user($max_attempts = 10) {
    $attempts = 0;
    $user = null;

    while ($attempts < $max_attempts && $user === null) {
        $user = get_user();
        if ($user === null) {
            sleep(1); // Attendre 1 seconde avant de réessayer
        }
        $attempts++;
    }
	
    return $user;
}

?>
