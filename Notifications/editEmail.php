<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form data
    $to = implode(",", $_POST["email"]); // Convert array of email addresses to comma-separated string
    $subject = $_POST["subject"];
    $message = $_POST["message"];
    
    // Construct the mailto URL
    $mailto = "mailto:" . $to . "?subject=" . rawurlencode($subject) . "&body=" . rawurlencode($message);
    
    // Redirect to the mailto URL
    header("Location: " . $mailto);
    exit;
}
?>
