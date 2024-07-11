<?php

namespace Ferdiunal\NovaSettings;

use Error;
use Exception;
use Illuminate\Support\Collection;
use RuntimeException;

class SettingsHelper
{
    public function __construct(
        protected array $settings = [],
        protected ?string $group = null
    ) {
        if (empty($this->settings)) {
            $this->settings = config('settings.settings');
        }
    }

    private function getSettings($name): Collection
    {
        try {
            return collect($this->settings)->filter(
                fn ($setting) => $setting::group() === $name
            )->flatMap(
                fn ($setting) => app($setting)->toArray()
            );
        } catch (RuntimeException | Exception | Error $e) {
            return new Collection([]);
        }
    }

    public function __call($name, $arguments)
    {
        $value = data_get($this->settings, $name, false);

        if ($value) {
            return $value;
        }

        $settings = $this->getSettings($name);

        if (empty($arguments)) {
            return new SettingsHelper(
                group: $name,
                settings: $settings->toArray()
            );
        }

        return $settings->get($arguments[0]);
    }

    public function __get($name)
    {
        $settings = $this->getSettings($name);

        if ($settings->isNotEmpty()) {
            return new SettingsHelper(
                group: $name,
                settings: $settings->toArray()
            );
        }

        return data_get($this->settings, $name, null);
    }

    protected function all(bool $dot = true): Collection
    {
        return collect($this->settings)->when(
            !$this->group,
            fn ($collection) => $collection->mapWithKeys(
                fn ($setting) => [
                    $setting::group() => app($setting)->toArray(),
                ]
            )->when(
                $dot,
                fn ($collection) => $collection->dot()
            )
        );
    }

    public function toArray(): array
    {
        return $this->all(
            dot: false
        )->toArray();
    }

    public function toCollection(): Collection
    {
        return $this->all(
            dot: false
        );
    }

    public function toJson(): string
    {
        return $this->all(
            dot: false
        )->toJson();
    }
}
