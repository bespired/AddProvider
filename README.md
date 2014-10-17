AddProvider
===========

laravel 4 command for adding serviceprovider to app/config/app.php

##### where to install
put in app/commands/

##### how to activate
Add command in app/start/artisan:

```php
Artisan::add( new AddProvider() );
