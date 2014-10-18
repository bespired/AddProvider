AddProvider
===========

laravel 4 command for adding serviceprovider to app/config/app.php

##### where to install
put AddProvider.php in app/commands/AddProvider/

##### how to activate
Add command in app/start/artisan:

```php

Artisan::add( new AddProvider\AddProvider() );
```

##### how to use

```php
php artisan provider:add vendor/package --verbose
```

![alt text](http://oi58.tinypic.com/2m6rg5z.jpg "osx bash")


##### in composer.json

```
  "scripts": {
		"post-install-cmd": [
			"php artisan provider:add vendor/package",
			"php artisan clear-compiled",
			"php artisan optimize"
		]
	},
