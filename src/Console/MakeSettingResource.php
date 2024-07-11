<?php

namespace Ferdiunal\NovaSettings\Console;

use Ferdiunal\NovaSettings\Traits\Utils;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use function Ferdiunal\NovaSettings\settingsResources;

class MakeSettingResource extends Command
{
    use Utils;

    protected $signature = 'make:settings-resource {name} {--G|group=Default : The group name} {--F|force : Overwrite the existing resource}';

    protected $description = 'Make a new setting resource';

    protected $help = 'This command allows you to create a new setting resource.';

    public function handle()
    {
        $name = trim($this->argument('name'));

        $this->makeResource($name);
    }

    protected function novaSettingsPath($path = '')
    {
        if (str($path)->startsWith('/')) {
            $path = str($path)->replaceFirst('/', '');
        }

        return sprintf('%s/%s', config('nova-settings.setting_resource_class_path'), $path);
    }

    protected function getFilename(string $name, string $type): string
    {
        $lowerType = strtolower($type);

        if (str($name)->endsWith($lowerType)) {
            $name = str($name)->replace($lowerType, '');
        }

        if (! str($name)->endsWith($type)) {
            $name = $name.$type;
        }

        return $name;
    }

    protected function makeResource($name)
    {
        $setting_name = $this->getFilename($name, 'Settings');
        $resource_name = $this->getFilename($name, 'Resource');
        $countResource = settingsResources()->count();

        $path = $this->novaSettingsPath(
            sprintf('%s.php', $resource_name)
        );

        if (file_exists($path)) {
            if (! $this->option('force')) {
                $this->error('Resource already exists!');

                return;
            }

            $this->warn('Resource already exists, Overwriting...');
            unlink($path);
        }

        if (! file_exists($this->novaSettingsPath())) {
            mkdir($this->novaSettingsPath());
        }

        file_put_contents(
            $path,
            str_replace(
                ['{{namespace}}', '{{resource_name}}', '{{setting_name}}', '{{field_label}}', '{{field_attribute}}', '{{title_name}}', '{{order}}', '{{description}}'],
                [$this->getNamespace(), $resource_name, $setting_name, $name.' Field Label', str($name)->trim()->snake()->slug(), $resource_name, $countResource, Inspiring::quotes()->random(preserveKeys: true)],
                $this->getStub('SettingResource')
            )
        );

        $this->info('Resource created successfully.');
        $this->line('Resource path: '.str($path)->replace(base_path(), '')->replaceFirst('/', ''));
    }
}
