<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Jeremykenedy\LaravelDarkmodeToggle\Enums\Mode;
use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;

class Toggle extends Component
{
    public string $storageKey;

    public string $defaultMode;

    public string $persistRoute;

    public string $persistMethod;

    public string $persistField;

    public bool $persistToServer;

    public string $className;

    public ?string $dataAttribute;

    public bool $syncAcrossTabs;

    public bool $colorScheme;

    public function __construct(
        ?string $default = null,
        ?string $persistRoute = null,
        ?bool $persistToServer = null,
    ) {
        $this->storageKey = DarkMode::storageKey();
        $this->defaultMode = Mode::isValid($default) ? $default : DarkMode::defaultMode();
        $this->persistRoute = $persistRoute ?? config('darkmode.persist_route', '/profile/dark-mode');
        $this->persistMethod = config('darkmode.persist_method', 'PUT');
        $this->persistField = DarkMode::persistField();
        $this->persistToServer = $persistToServer ?? config('darkmode.persist_to_server', true);
        $this->className = DarkMode::className();
        $this->dataAttribute = DarkMode::dataAttribute();
        $this->syncAcrossTabs = (bool) config('darkmode.sync_across_tabs', false);
        $this->colorScheme = (bool) config('darkmode.color_scheme', false);
    }

    public function userPreference(): string
    {
        return DarkMode::preferenceFor(Auth::user(), $this->persistField) ?? $this->defaultMode;
    }

    public function render(): View
    {
        return view(DarkMode::prefix().'::toggle');
    }
}
