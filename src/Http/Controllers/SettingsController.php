<?php

namespace Ferdiunal\NovaSettings\Http\Controllers;

use Ferdiunal\NovaSettings\Traits\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Http\Requests\NovaRequest;

use function Ferdiunal\NovaSettings\settingsResources;

class SettingsController extends Controller
{
    use Utils;

    public function get(string $group, NovaRequest $request)
    {
        $resources = settingsResources()
            ->where(
                fn ($resource) => str($resource['group'])->lower()->slug()->__toString() === str($group)->lower()->slug()->__toString()
            )->sort(
                fn ($a, $b) => $a['order'] <=> $b['order']
            )->map(function ($resource) {
                unset($resource['settings']);

                return $resource;
            })->values();

        abort_unless($resources->isNotEmpty(), 404);

        return new JsonResponse(compact('resources'), 200);
    }

    public function save(string $group, NovaRequest $request)
    {
        $resources = settingsResources()
            ->where(
                fn ($resource) => (
                    str($resource['group'])->lower()->slug()->__toString() === str($group)->lower()->slug()->__toString()
                )
            )->sort(
                fn ($a, $b) => $a['order'] <=> $b['order']
            );

        abort_unless($resources->isNotEmpty(), 404);

        $fields = $resources->pluck('fields', 'settings');
        $rules = [];
        $fields->each(
            function ($_fields, $settings) use (&$request, &$rules) {
                $settingsClass = app($settings);

                $_fields->each(
                    function ($field) use (&$request, &$rules, $settingsClass) {
                        if (property_exists($settingsClass, $field->attribute)) {
                            $fakeResource = $this->makeFakeResource($field->attribute, $settingsClass->{$field->attribute} ?? '');
                            $field->resolve($fakeResource, $field->attribute);

                            if ($field->isRequired($request)) {
                                $field->rules('required');
                            }

                            $rules = array_merge($rules, $field->getRules($request));
                        }
                    }
                );
            }
        );

        Validator::make($request->all(), $rules)->validate();

        $fields->each(function ($_fields, $settings) use (&$request) {
            $settingsClass = app($settings);

            $_fields->whereInstanceOf(Resolvable::class)->each(function ($field) use (&$request, &$settingsClass) {
                if (empty($field->attribute)) {
                    return;
                }

                if ($field->isReadonly(app(NovaRequest::class))) {
                    return;
                }

                $tempResource = new \Laravel\Nova\Support\Fluent;
                $field->fill($request, $tempResource);

                $settingsClass->{$field->attribute} = $tempResource->{$field->attribute};
            });

            $settingsClass->save();
        });

        return back();
    }
}
