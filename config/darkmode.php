<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dark Mode Strategy
    |--------------------------------------------------------------------------
    |
    | How dark mode is applied to the HTML element.
    | Supported: "class" (adds/removes 'dark' class on <html>)
    |
    */

    'strategy' => 'class',

    'class_name' => 'dark',

    /*
    |--------------------------------------------------------------------------
    | Default Mode
    |--------------------------------------------------------------------------
    |
    | The default mode when no preference is stored.
    | Supported: "light", "dark", "system"
    |
    */

    'default' => 'system',

    /*
    |--------------------------------------------------------------------------
    | Persistence
    |--------------------------------------------------------------------------
    |
    | How the user's preference is stored.
    | localStorage key used on the client side.
    |
    */

    'storage_key' => 'theme',

    /*
    |--------------------------------------------------------------------------
    | Server-side Persistence
    |--------------------------------------------------------------------------
    |
    | When enabled, the toggle will save the preference to the server via
    | the configured route. Requires authentication.
    |
    */

    'persist_to_server' => true,

    'persist_route' => '/profile/dark-mode',

    'persist_method' => 'PUT',

    'persist_field' => 'dark_mode',

    /*
    |--------------------------------------------------------------------------
    | CSS Framework
    |--------------------------------------------------------------------------
    |
    | Which CSS framework views to use for the toggle component.
    | Supported: "tailwind", "bootstrap5", "bootstrap4"
    | Set to null to inherit from config('ui-kit.css_framework').
    |
    */

    'css_framework' => null,

    /*
    |--------------------------------------------------------------------------
    | Component Prefix
    |--------------------------------------------------------------------------
    |
    | The Blade component prefix. With default "darkmode", the component
    | is rendered as <x-darkmode::toggle />.
    |
    */

    'prefix' => 'darkmode',

    /*
    |--------------------------------------------------------------------------
    | Route Settings
    |--------------------------------------------------------------------------
    |
    | The package can register its own route for saving preferences.
    | Disable if you handle persistence through your own routes.
    |
    */

    'routes' => [
        'enabled'    => true,
        'prefix'     => 'darkmode',
        'middleware' => ['web', 'auth'],
    ],

];
