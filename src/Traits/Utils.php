<?php

namespace Ferdiunal\NovaSettings\Traits;

trait Utils
{
    protected function makeFakeResource(string $fieldName, $fieldValue)
    {
        $fakeResource = new \Laravel\Nova\Support\Fluent;
        $fakeResource->{$fieldName} = $fieldValue;

        return $fakeResource;
    }

    protected function getStub($name)
    {
        return file_get_contents(
            realpath(__DIR__."/../../stubs/{$name}.stub")
        );
    }

    protected function getNamespace(): string
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
