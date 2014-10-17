AddProvider
===========

laravel 4 command for adding serviceprovider to app/config/app.php

##### where to install
put in app/commands/

##### how to activate
Add command in app/start/artisan:

```php
Artisan::add( new AddProvider() );
```

##### how to use

```php
php artisan provider:add vendor/package --verbose
```

![alt text](http://oi61.tinypic.com/8xk11x.jpg "osx bash")


##### in composer.json

```json
  "scripts": {
		"post-install-cmd": [
			"php artisan provider:add vendor/package",
			"php artisan clear-compiled",
			"php artisan optimize"
		]
	},
