<?php

namespace Ferdiunal\NovaSettings;

use Ferdiunal\NovaSettings\Traits\Utils;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Support\LazyCollection;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\ResolvesFields;

abstract class SettingResource
{
    use ConditionallyLoadsAttributes, ResolvesFields, Utils;

    /**
     * The settings class that this resource corresponds to.
     *
     * @return class-string<\Spatie\LaravelSettings\Settings>
     */
    abstract public static function settings(): string;

    /**
     * Get the fields displayed by the resource.
     */
    abstract public static function fields(): array;

    /**
     * The title name for the setting resource.
     */
    abstract public static function title(): string;

    /**
     * The description for the setting resource.
     */
    abstract public static function description(): string;

    /**
     * The order of the setting resource.
     */
    abstract public static function order(): int;

    protected static function group(): string
    {
        return strtolower(static::settings()::group());
    }

    /**
     * Serialize the resource for the client.
     */
    public function serialize(): array
    {
        $settings = app(
            static::settings()
        );

        $fields = LazyCollection::make(
            FieldCollection::make(static::fields())->authorized(request())->toArray()
        )->map(function ($field) {
            $field->panel = sprintf('%s_%s', static::group(), str()->random(5));

            return $field;
        });

        $addResolveCallback = function (&$field) use (&$settings) {
            if (! empty($field->attribute)) {
                if (property_exists($settings, $field->attribute)) {
                    $fakeResource = $this->makeFakeResource($field->attribute, $settings->{$field->attribute});
                    $field->resolve($fakeResource);
                }
            }
        };

        $fields->each(function (&$field) use ($addResolveCallback) {
            $addResolveCallback($field);
        });

        return [
            'group' => static::group(),
            'title' => static::title(),
            'description' => static::description(),
            'order' => static::order(),
            'settings' => static::settings(),
            'fields' => $fields,
        ];
    }
}
