<?php
// Suppress errors during session handling
@session_start();

// Clear all session variables
$_SESSION = array();

// If session cookie exists, destroy it
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destroy the session
@session_destroy();

// Redirect to index page (with semicolon added)
header("Location: index.php");
exit; // Add exit after redirect to prevent further execution
?>