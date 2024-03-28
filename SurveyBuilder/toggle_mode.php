<?php
session_start();

// Toggle mode and store preference in session
if (isset($_SESSION['mode']) && $_SESSION['mode'] === 'dark') {
    $_SESSION['mode'] = 'light';
} else {
    $_SESSION['mode'] = 'dark';
}

// Redirect back to the referring page
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: index.php'); // Redirect to your home page
}
?>
