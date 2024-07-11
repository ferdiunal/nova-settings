<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;

use function Ferdiunal\NovaSettings\settingsResources;

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
    $resources = settingsResources()
        ->where(
            fn ($resource) => str($resource['group'])->lower()->slug()->__toString() === str($group)->lower()->slug()->__toString()
        );

    if ($resources->isEmpty()) {
        abort(404);
    }

    return inertia('NovaSettings', compact('group'));
});
