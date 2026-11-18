<?php
require_once __DIR__ . '/classes/Auth.php';

if (Auth::isStaff()) {
    header('Location: dashboard.php');
} else {
    header('Location: peminjaman.php');
}
exit;
