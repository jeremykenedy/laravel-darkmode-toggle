<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toggle extends Component
{
    public string $storageKey;

    public string $defaultMode;

    public string $persistRoute;

    public string $persistMethod;

    public string $persistField;

    public bool $persistToServer;

    public function __construct(
        ?string $default = null,
        ?string $persistRoute = null,
        ?bool $persistToServer = null,
    ) {
        $this->storageKey = config('darkmode.storage_key', 'theme');
        $this->defaultMode = $default ?? config('darkmode.default', 'system');
        $this->persistRoute = $persistRoute ?? config('darkmode.persist_route', '/profile/dark-mode');
        $this->persistMethod = config('darkmode.persist_method', 'PUT');
        $this->persistField = config('darkmode.persist_field', 'dark_mode');
        $this->persistToServer = $persistToServer ?? config('darkmode.persist_to_server', true);
    }

    public function userPreference(): string
    {
        $user = auth()->user();
        if ($user && $user->profile) {
            return $user->profile->dark_mode ?? $this->defaultMode;
        }

        return $this->defaultMode;
    }

    public function render(): View
    {
        $prefix = config('darkmode.prefix', 'darkmode');

        return view($prefix.'::toggle');
    }
}
