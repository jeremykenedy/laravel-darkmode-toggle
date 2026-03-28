# Laravel Dark Mode Toggle

A standalone, framework-agnostic dark mode toggle component for Laravel. Supports Tailwind CSS, Bootstrap 5, and Bootstrap 4, with Blade, Livewire, Vue, React, and Svelte frontend options.

[![Total Downloads](https://poser.pugx.org/jeremykenedy/laravel-darkmode-toggle/d/total.svg)](https://packagist.org/packages/jeremykenedy/laravel-darkmode-toggle)
[![Latest Stable Version](https://poser.pugx.org/jeremykenedy/laravel-darkmode-toggle/v/stable.svg)](https://packagist.org/packages/jeremykenedy/laravel-darkmode-toggle)
[![StyleCI](https://github.styleci.io/repos/1194798386/shield?branch=main)](https://github.styleci.io/repos/1194798386?branch=main)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

#### Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Usage](#usage)
- [Configuration](#configuration)
- [Server-Side Persistence](#server-side-persistence)
- [CSS Framework Switching](#css-framework-switching)
- [How It Works](#how-it-works)
- [Requirements](#requirements)
- [License](#license)

## Features

- Three modes: Light, Dark, System (follows OS preference)
- Persists to `localStorage` (instant, no flash)
- Optional server-side persistence (saves to user profile)
- Class-based dark mode (`<html class="dark">`)
- FOUC prevention via init script
- Multi-framework CSS: Tailwind, Bootstrap 5, Bootstrap 4
- Multi-framework frontend: Blade/Alpine.js, Livewire 3, Vue 3, React 18, Svelte 4
- Configurable via `config/darkmode.php`
- Artisan install command with framework selection

## Installation

```bash
composer require jeremykenedy/laravel-darkmode-toggle
```

## Quick Start

```bash
php artisan darkmode:install --css=tailwind --frontend=blade
```

### Options

| Flag | Values | Default |
|------|--------|---------|
| `--css` | `tailwind`, `bootstrap5`, `bootstrap4` | `tailwind` |
| `--frontend` | `blade`, `livewire`, `vue`, `react`, `svelte` | `blade` |
| `--no-config` | Skip publishing config | - |

## Usage

### 1. Add the init script to `<head>`

This prevents a flash of the wrong theme on page load:

```html
<head>
    @include('darkmode::init-script')
</head>
```

### 2. Add the toggle component

**Blade (with Alpine.js):**
```html
<x-darkmode-toggle />
```

**Livewire:**
```html
<livewire:darkmode-toggle />
```

**Vue:**
```vue
<script setup>
import DarkmodeToggle from './vendor/darkmode-toggle/DarkmodeToggle.vue'
</script>

<template>
    <DarkmodeToggle persist-url="/darkmode/preference" />
</template>
```

**React:**
```jsx
import DarkmodeToggle from './vendor/darkmode-toggle/DarkmodeToggle'

export default function Nav() {
    return <DarkmodeToggle persistUrl="/darkmode/preference" />
}
```

**Svelte:**
```svelte
<script>
import DarkmodeToggle from './vendor/darkmode-toggle/DarkmodeToggle.svelte'
</script>

<DarkmodeToggle persistUrl="/darkmode/preference" />
```

### 3. Publish frontend components (Vue/React/Svelte only)

```bash
php artisan darkmode:install --frontend=vue
php artisan darkmode:install --frontend=react
php artisan darkmode:install --frontend=svelte
```

## Configuration

```bash
php artisan vendor:publish --tag=darkmode-config
```

Key options in `config/darkmode.php`:

```php
return [
    'strategy' => 'class',          // Dark mode strategy
    'class_name' => 'dark',         // Class added to <html>
    'default' => 'system',          // Default mode
    'storage_key' => 'theme',       // localStorage key
    'persist_to_server' => true,    // Save to DB when authenticated
    'persist_route' => '/profile/dark-mode',
    'persist_method' => 'PUT',
    'persist_field' => 'dark_mode',
    'css_framework' => null,        // null = inherit from ui-kit config
    'routes' => [
        'enabled' => true,
        'prefix' => 'darkmode',
        'middleware' => ['web', 'auth'],
    ],
];
```

## Server-Side Persistence

The package includes a route `PUT /darkmode/preference` that saves the preference. To use your own route (e.g., from `laravel-profiles`), set in config:

```php
'persist_route' => '/profile/dark-mode',
'routes' => ['enabled' => false],   // disable package route
```

The toggle sends a JSON request:

```json
{ "dark_mode": "dark" }
```

## CSS Framework Switching

Switch CSS framework at any time:

```bash
# In .env
UI_KIT_CSS=bootstrap5
```

Or set directly in `config/darkmode.php`:

```php
'css_framework' => 'bootstrap5',
```

The component automatically renders with the correct framework's classes.

## How It Works

1. **Init script** runs synchronously in `<head>`, reads `localStorage`, adds `dark` class before paint
2. **Toggle component** renders sun/moon/monitor icons, dropdown with Light/Dark/System options
3. **On selection**: updates `localStorage`, toggles HTML class, optionally PUTs to server
4. **System mode**: listens for `prefers-color-scheme` media query changes in real time

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- Alpine.js 3 (for Blade views)

## License

MIT
