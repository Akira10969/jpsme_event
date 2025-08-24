<?php
// quizbee_registration.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'security.php';
include 'db.php';
include 'registration_form.php';
render_registration_form('quizbee', 'Quizbee Competition');
?>
