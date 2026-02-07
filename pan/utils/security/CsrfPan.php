<?php

namespace Pan\Utils\Security;


class CsrfPan {

    public static function generateToken($key = 'default')
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        
        return $token;
    }

    public static function getToken($key = 'default')
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return $_SESSION['csrf_tokens'][$key] ?? null;
    }

    public static function validateToken($token, $key = 'default')
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $valid = hash_equals(\Pan\Utils\SessionPan::getSession($key) ?? '', $token);
        \Pan\Utils\SessionPan::sessionKill($key); // invalida el token tras usarlo

        return $valid;
    }
}