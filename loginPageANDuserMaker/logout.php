 <!--logout function -->
 <!--Ember Adkins 901893134-->
<?php

session_start();
session_destroy();

header("Location: index.php");
exit;
