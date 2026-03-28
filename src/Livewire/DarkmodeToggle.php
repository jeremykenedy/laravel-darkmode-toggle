<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Livewire;

use Livewire\Component;

class DarkmodeToggle extends Component
{
    public string $current = 'system';

    public function mount(): void
    {
        $user = auth()->user();
        $field = config('darkmode.persist_field', 'dark_mode');

        if ($user && $user->profile) {
            $this->current = $user->profile->{$field} ?? config('darkmode.default', 'system');
        }
    }

    public function setTheme(string $mode): void
    {
        if (!in_array($mode, ['light', 'dark', 'system'])) {
            return;
        }

        $this->current = $mode;
        $field = config('darkmode.persist_field', 'dark_mode');

        $user = auth()->user();
        if ($user && method_exists($user, 'ensureProfile')) {
            $user->ensureProfile();
            $user->profile->update([$field => $mode]);
        }

        $this->dispatch('theme-changed', mode: $mode);
    }

    public function render()
    {
        $prefix = config('darkmode.prefix', 'darkmode');

        return view($prefix.'::livewire.toggle');
    }
}
