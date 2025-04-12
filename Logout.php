<?php
@include '/Buildify/config.php';

session_start();
session_unset();
session_destroy();

header('location: /landingpage.php');
exit();
