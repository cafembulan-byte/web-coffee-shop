<?php

declare(strict_types=1);

if (!defined('SUMMIT_PRIVATE_ROOT')) {
    define('SUMMIT_PRIVATE_ROOT', getenv('SUMMIT_PRIVATE_ROOT') ?: dirname(__DIR__));
}