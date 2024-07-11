<?php

namespace Ferdiunal\NovaSettings;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;

if (! function_exists('settings')) {
    function settings(?string $group = null, string|null|bool|array|object|int $default = null)
    {
        $helper = new SettingsHelper(
            group: $group
        );

        if ($group) {
            if (str($group)->contains('.')) {
                [$_group, $_key] = str($group)->explode('.')->take(2);

                return $helper->{$_group}->{$_key} ?? $default;
            }

            return $helper->{$group} ?? $default;
        }

        return $helper ?? $default;
    }
}

if (! function_exists('getSettingReourceNamespace')) {
    function getSettingReourceNamespace(): string
    {
        $path = preg_replace(
            [
                '/^('.preg_quote(base_path(), '/').')/',
                '/\//',
            ],
            [
                '',
                '\\',
            ],
            config('nova-settings.setting_resource_class_path')
        );

        $namespace = implode('\\', array_map(fn ($directory) => ucfirst($directory), explode('\\', $path)));

        // Remove leading backslash if present
        if (substr($namespace, 0, 1) === '\\') {
            $namespace = substr($namespace, 1);
        }

        return $namespace;
    }
}

if (! function_exists('settingsResources')) {
    function settingsResources(): LazyCollection|Collection
    {
        $resourcePath = Config::get('nova-settings.setting_resource_class_path', 'app/NovaSettings');
        $namespace = getSettingReourceNamespace();
        if (! File::exists($resourcePath)) {
            return new Collection([]);
        }

        $files = File::files($resourcePath);

        return LazyCollection::make(array_map(
            function ($file) use (&$namespace) {
                $class = sprintf('%s\\%s', $namespace, pathinfo($file, PATHINFO_FILENAME));

                return (new $class)->serialize();
            },
            $files
        ));
    }
}
