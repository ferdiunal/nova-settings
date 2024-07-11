<?php

namespace Ferdiunal\NovaSettings;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaSettings extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('nova-settings', __DIR__.'/../dist/js/tool.js');
        Nova::style('nova-settings', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @return mixed
     */
    public function menu(Request $request)
    {
        $resources = settingsResources()->pluck('group')->unique()->map(
            fn ($group) => MenuItem::make(
                str($group)->title()
                    ->when(
                        str($group)->lower()->endsWith('settings'),
                        fn ($title) => str($title)->replace(' settings', '')->ucfirst()->__toString()
                    )
                    ->append(' Settings')
                    ->__toString(),
                str($group)->lower()->slug()->prepend('/nova-settings/')->__toString()
            )
        );

        return MenuSection::make('Nova Settings', $resources->toArray())
            ->collapsable()
            ->icon('cog');
    }
}
