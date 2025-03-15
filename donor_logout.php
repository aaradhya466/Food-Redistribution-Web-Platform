<?php
session_start();
session_destroy();
header("Location: donor_login.html");
exit();
?>
