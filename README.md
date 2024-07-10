# Nova Settings

Nova Settings is a Laravel Nova package that provides an intuitive interface for managing settings using Spatie's settings package.

### Installation

To install Nova Settings, you can use Composer:

```bash
composer require ferdiunal/nova-settings
```

After installing the package, you need to register the tool with Nova. Add the following to your **NovaServiceProvider**:

```php
// in app/Providers/NovaServiceProvider.php

use Ferdiunal\NovaSettings\NovaSettings;

public function tools()
{
    return [
        new NovaSettings,
    ];
}
```

### Create Settings Resource

```bash
php artisan make:settings-resource GeneralSettings --group General 
```

### Configuration
Make sure you have Spatie's settings package installed and configured in your Laravel application. You can follow the [official documentation](https://github.com/spatie/laravel-settings?tab=readme-ov-file#installation) for detailed instructions.

### Features
- Easy integration with Laravel Nova
- User-friendly settings management interface
- Compatible with Spatie's settings package

### Contributing
Contributions are welcome! Please feel free to submit a Pull Request or open an issue.

License
This package is open-sourced software licensed under the [MIT license](LICENSE).
