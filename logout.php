<?php
session_start();
session_destroy();
header("Location: index.html"); // redirect to homepage or login
exit();
?>
