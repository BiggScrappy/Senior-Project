<?php
if(isset($_POST['submit'])) {
    $to = ''; // Put your email address here
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mailto_link = 'mailto:' . $to . '?subject=' . urlencode($subject) . '&body=' . urlencode($message);

    // Redirect to the mailto link
    header('Location: ' . $mailto_link);
    exit;
} else {
    echo "Form submission failed.";
}
?>
