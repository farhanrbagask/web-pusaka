<?php
/**
 * Admin Logout — Pusaka Himatif
 */

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
