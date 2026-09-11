<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Jeremykenedy\LaravelDarkmodeToggle\Enums\Mode;
use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;
use Livewire\Component;

class DarkmodeToggle extends Component
{
    public string $current = 'system';

    public function mount(): void
    {
        $this->current = DarkMode::preferenceFor(Auth::user()) ?? DarkMode::defaultMode();
    }

    public function setTheme(string $mode): void
    {
        if (!Mode::isValid($mode)) {
            return;
        }

        $this->current = $mode;

        DarkMode::persistFor(Auth::user(), $mode);

        $this->dispatch('theme-changed', mode: $mode);
    }

    /**
     * @return View
     */
    public function render()
    {
        return view(DarkMode::prefix().'::livewire.toggle');
    }
}
