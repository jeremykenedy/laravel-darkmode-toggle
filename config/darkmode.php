<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Dark Mode Strategy
    |--------------------------------------------------------------------------
    |
    | How dark mode is applied to the HTML element.
    | Supported: "class" (adds/removes the class below on <html>)
    |
    */

    'strategy' => env('DARKMODE_STRATEGY', 'class'),

    'class_name' => env('DARKMODE_CLASS', 'dark'),

    /*
    |--------------------------------------------------------------------------
    | Data Attribute
    |--------------------------------------------------------------------------
    |
    | An optional attribute mirrored onto <html> alongside the class, set to
    | "dark" or "light". Bootstrap 5.3+ drives its own dark mode from
    | data-bs-theme, so set this to "data-bs-theme" on Bootstrap 5.
    |
    | Leave null to only toggle the class.
    |
    */

    'data_attribute' => env('DARKMODE_DATA_ATTRIBUTE'),

    /*
    |--------------------------------------------------------------------------
    | Color Scheme
    |--------------------------------------------------------------------------
    |
    | Mirror the resolved theme onto the CSS color-scheme property so native
    | form controls, scrollbars and spellcheck underlines match the theme.
    | Off by default so existing styling is left alone.
    |
    */

    'color_scheme' => env('DARKMODE_COLOR_SCHEME', false),

    /*
    |--------------------------------------------------------------------------
    | Default Mode
    |--------------------------------------------------------------------------
    |
    | The default mode when no preference is stored.
    | Supported: "light", "dark", "system"
    |
    */

    'default' => env('DARKMODE_DEFAULT', 'system'),

    /*
    |--------------------------------------------------------------------------
    | Persistence
    |--------------------------------------------------------------------------
    |
    | How the user's preference is stored.
    | localStorage key used on the client side.
    |
    */

    'storage_key' => env('DARKMODE_STORAGE_KEY', 'theme'),

    /*
    |--------------------------------------------------------------------------
    | Cross-tab Sync
    |--------------------------------------------------------------------------
    |
    | Keep every open tab in step by listening for storage events, so changing
    | the theme in one tab updates the others immediately. Off by default.
    |
    */

    'sync_across_tabs' => env('DARKMODE_SYNC_TABS', false),

    /*
    |--------------------------------------------------------------------------
    | Server-side Persistence
    |--------------------------------------------------------------------------
    |
    | When enabled, the toggle will save the preference to the server via
    | the configured route. Requires authentication.
    |
    */

    'persist_to_server' => env('DARKMODE_PERSIST', true),

    'persist_route' => env('DARKMODE_PERSIST_ROUTE', '/profile/dark-mode'),

    'persist_method' => env('DARKMODE_PERSIST_METHOD', 'PUT'),

    'persist_field' => env('DARKMODE_PERSIST_FIELD', 'dark_mode'),

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

    'css_framework' => env('DARKMODE_CSS'),

    /*
    |--------------------------------------------------------------------------
    | Frontend
    |--------------------------------------------------------------------------
    |
    | Which frontend the application uses for the toggle.
    | Supported: "blade", "livewire", "vue", "react", "svelte"
    | Set to null to inherit from config('ui-kit.frontend').
    |
    */

    'frontend' => env('DARKMODE_FRONTEND'),

    /*
    |--------------------------------------------------------------------------
    | Component Prefix
    |--------------------------------------------------------------------------
    |
    | The view namespace and component prefix. With the default "darkmode",
    | views resolve as darkmode::toggle and the Blade component is rendered
    | as <x-darkmode-toggle />.
    |
    */

    'prefix' => env('DARKMODE_PREFIX', 'darkmode'),

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
        'enabled'    => env('DARKMODE_ROUTES_ENABLED', true),
        'prefix'     => env('DARKMODE_ROUTES_PREFIX', 'darkmode'),
        'middleware' => ['web', 'auth'],
    ],

];
