<?php

namespace Ferdiunal\NovaSettings;

if (! function_exists('settings')) {
    function settings(?string $group = null)
    {
        return once(
            function () use (&$group) {
                $helper = new SettingsHelper(
                    group: $group
                );

                if ($group) {
                    if (str($group)->contains('.')) {
                        [$_group, $_key] = str($group)->explode('.')->take(2);

                        return $helper->{$_group}->{$_key};
                    }

                    return $helper->{$group};
                }

                return $helper;
            }
        );
    }
}
