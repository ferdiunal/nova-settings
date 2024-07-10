<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\LazyCollection;
use Laravel\Nova\Http\Requests\NovaRequest;

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/

Route::get('/{group}', function (string $group, NovaRequest $request) {
    $group = str($group)->lower()->slug()->__toString();
    $resources = LazyCollection::make(Context::get('nova-settings-resources'))
        ->where(
            fn ($resource) => str($resource['group'])->lower()->slug()->__toString() === str($group)->lower()->slug()->__toString()
        );

    if ($resources->isEmpty()) {
        abort(404);
    }

    return inertia('NovaSettings', compact('group'));
});
