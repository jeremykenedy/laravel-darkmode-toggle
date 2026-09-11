<?php

declare(strict_types=1);

namespace Jeremykenedy\LaravelDarkmodeToggle\Console\Concerns;

use Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode;

trait HandlesFrameworkSetup
{
    protected function getCssOption(): string
    {
        $css = $this->option('css');

        return is_string($css) && $css !== '' ? $css : DarkMode::cssFramework();
    }

    protected function getFrontendOption(): string
    {
        $frontend = $this->option('frontend');

        return is_string($frontend) && $frontend !== '' ? $frontend : DarkMode::frontend();
    }

    protected function validateCssFramework(string $css): bool
    {
        if (DarkMode::isValidCssFramework($css)) {
            return true;
        }

        $this->error("Invalid CSS framework: {$css}. Valid: ".implode(', ', DarkMode::CSS_FRAMEWORKS));

        return false;
    }

    protected function validateFrontend(string $frontend): bool
    {
        if (DarkMode::isValidFrontend($frontend)) {
            return true;
        }

        $this->error("Invalid frontend: {$frontend}. Valid: ".implode(', ', DarkMode::FRONTENDS));

        return false;
    }

    protected function updateEnvValue(string $key, string $value): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $path = base_path('.env');

        if (!file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);

        if ($content === false) {
            return;
        }

        if (preg_match("/^{$key}=/m", $content) === 1) {
            $content = (string) preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content = rtrim($content, "\n")."\n{$key}={$value}\n";
        }

        file_put_contents($path, $content);
    }

    protected function setCssFramework(string $css): void
    {
        $this->updateEnvValue('UI_KIT_CSS', $css);
        $this->updateEnvValueIfPresent('DARKMODE_CSS', $css);
        $this->clearCaches();
    }

    protected function setFrontendFramework(string $frontend): void
    {
        $this->updateEnvValue('UI_KIT_FRONTEND', $frontend);
        $this->updateEnvValueIfPresent('DARKMODE_FRONTEND', $frontend);
        $this->clearCaches();
    }

    /**
     * Update a key only when the application already sets it.
     *
     * DARKMODE_CSS and DARKMODE_FRONTEND take precedence over the UI kit keys,
     * so leaving a stale value behind would make the command report a switch
     * that never took effect. Applications that do not set them keep a .env
     * with nothing added to it.
     */
    protected function updateEnvValueIfPresent(string $key, string $value): void
    {
        if ($this->envKeyExists($key)) {
            $this->updateEnvValue($key, $value);
        }
    }

    protected function envKeyExists(string $key): bool
    {
        if (app()->runningUnitTests()) {
            return false;
        }

        $path = base_path('.env');

        if (!file_exists($path)) {
            return false;
        }

        $content = file_get_contents($path);

        return $content !== false && preg_match("/^{$key}=/m", $content) === 1;
    }

    protected function clearCaches(): void
    {
        $this->callSilently('config:clear');
        $this->callSilently('view:clear');
    }
}
