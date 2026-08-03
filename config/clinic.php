<?php

return [

    /*
     * Used only by ProductionSeeder on first launch (empty database) to
     * create the one admin account. Read via config() rather than env()
     * directly in the seeder so this still works correctly after
     * `artisan config:cache` - which a packaged NativePHP build is
     * likely to run as part of its own production optimization, and
     * which makes raw env() calls outside config files return null.
     */
    'admin_name' => env('ADMIN_NAME', 'Clinic Admin'),
    'admin_username' => env('ADMIN_USERNAME', 'admin'),
    'admin_password' => env('ADMIN_PASSWORD'),

];
