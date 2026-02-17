<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'ion_popescu';

header("Location: index.php");
exit;
