<?php
if (!function_exists('setFlashMessage')) {
    function setFlashMessage($type, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$type] = $message;
    }
} 