<?php

if (!function_exists('mhn_active')) {
    function mhn_active(string $route) {
        return ($route == request()->path()) ? 'active' : '';
    }
}