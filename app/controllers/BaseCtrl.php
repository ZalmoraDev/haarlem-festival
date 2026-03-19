<?php

namespace app\controllers;

use app\services\exceptions\BaseServExc;
use Exception;

abstract readonly class BaseCtrl
{
    protected function handleException(Exception $e): void
    {
        $_SESSION['flash_errors'] ??= [];

        if ($e instanceof BaseServExc) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            return;
        }

        $_SESSION['flash_errors'][] = "An unexpected error occurred.";
    }

    protected function redirect(string $path): void
    {
        header("Location: {$path}", true, 302);
        exit;
    }
}
