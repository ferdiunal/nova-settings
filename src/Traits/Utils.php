<?php

namespace Ferdiunal\NovaSettings\Traits;

use function Ferdiunal\NovaSettings\getSettingReourceNamespace;

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
            realpath(__DIR__ . "/../../stubs/{$name}.stub")
        );
    }

    protected function getNamespace(): string
    {
        return getSettingReourceNamespace();
    }
}
