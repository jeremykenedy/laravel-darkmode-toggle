# Changelog

All notable changes to this package are documented here.

## Unreleased

### Added

- Bootstrap 5 and Bootstrap 4 Livewire views. The Livewire toggle previously rendered Tailwind
  markup no matter which CSS framework was selected.
- `data_attribute` config option. Set it to `data-bs-theme` so Bootstrap 5.3 dark mode follows
  the toggle. Off by default.
- `color_scheme` config option, mirrors the theme onto the CSS `color-scheme` property so native
  form controls and scrollbars match. Off by default.
- `sync_across_tabs` config option, keeps other open tabs in step through the `storage` event.
  Off by default.
- `frontend` config option, mirrors `css_framework` and inherits from `ui-kit.frontend`.
- Environment variable support for every config option. The README documented this before it
  existed.
- `darkmode-js` publish tag. The Vue, React, and Svelte components now publish to
  `resources/js/vendor/darkmode-toggle`, which is the import path the README documents.
- `dataAttribute`, `colorScheme`, `syncAcrossTabs`, `toggleLabel` and `labels` props on the Vue,
  React and Svelte components, bringing them to parity with the Blade views.
- Keyboard support on the toggle: `Escape` closes and restores focus, `ArrowDown` and `ArrowUp`
  move between options.
- ARIA menu button semantics: `aria-haspopup`, `aria-expanded`, `role="menu"`,
  `role="menuitemradio"` with `aria-checked`, and visible focus styles.
- PHPStan at level 6 and a static analysis job in CI.
- A composer job in CI running `composer validate --strict` and `composer audit`.
- A frontend job in CI that compiles the Vue, React, and Svelte components rather than grepping
  them.
- PHP 8.5 to the test matrix.

### Changed

- The toggle labels and the trigger label now come from the package language files. They were
  hardcoded English while the language file sat unused.
- `localStorage` access is wrapped in `try`/`catch`. A browser that blocks storage no longer
  throws on page load.
- The toggle reads the preference from the configured `persist_field` instead of a hardcoded
  `dark_mode` column.
- The Livewire component falls back to `config('darkmode.default')` instead of a hardcoded
  `system`.
- The test suite grew from 105 tests to 301, including a backwards compatibility contract that
  pins the public API.

### Fixed

- Keyboard navigation now works on every frontend. Vue, React, Svelte and Livewire handled
  Escape only, so the arrow key and focus return behaviour was Blade specific.
- The Vue component used a `v-click-outside` directive that was never defined, so it threw on
  mount unless the host application happened to register one. It now uses a plain document
  listener that is removed on unmount.
- The Vue, React, and Svelte components now follow `prefers-color-scheme` changes while in
  System mode, which only the Blade views did.
- `.env` key matching in the install and switch commands is anchored to the start of a line. A
  key such as `APP_UI_KIT_CSS` could previously make the write silently do nothing.
- The controller checks that the profile is an object before updating it.
- `darkmode:install --css=material --no-interaction` wrote an unsupported framework instead of
  failing. Passing a single flag skipped validation.
- A `default` that is not light, dark or system, set in config or passed to the component,
  rendered a toggle where no option was ever marked as the current one. It now falls back to
  system.
- `darkmode:switch` and `darkmode:update` wrote only `UI_KIT_CSS` and `UI_KIT_FRONTEND`. An
  application that sets `DARKMODE_CSS` or `DARKMODE_FRONTEND`, which take precedence, saw the
  command report success while the framework never changed. Those keys are now updated too when
  the application already sets them.
- The Livewire toggle ignored `persist_to_server` and always wrote to the profile.
- `sync_across_tabs` did nothing on the Livewire toggle. It only ever worked on the Blade views.
- The React component checked storage rather than the selected mode when the operating system
  preference changed, so System stopped following the OS when a browser blocked storage.
- The init script and the Livewire markup read raw config while the Blade component normalised
  it. An empty `class_name` made both emit `classList.toggle('')`, which throws, and an invalid
  `default` stopped the init script following the operating system. Both now resolve their
  settings the same way the component does.

### Notes

- Nothing in this release requires a change in a consuming application. Every existing config
  key, class name, public property, Blade component alias, view name, publish tag, and command
  signature is unchanged, and all three new config options default to off.
- `composer.json` still allows Laravel 10 and 11, but Composer no longer installs either branch
  because every release is affected by CVE-2026-48019 with no patched version on those
  branches. CI covers Laravel 12 and 13.

## v1.2.0

- Installer updates.

## v1.1.0

- README and badge updates.

## v1.0.1

- StyleCI fixes.

## v1.0.0

- First release.
