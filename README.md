About
=====

This middleware package adds attributes to your request object that describe the users browser. This will let you fine tune the templates or code to use based on the traits of the browser.

This project was forked from [django-minidetector](https://github.com/remohammadi/django-minidetector), and modified for PHP.

The following is added to the request:

Simple Device
-------------
	
	$request->isSimpleDevice

`true` for all non-desktop devices (browsers) without "modern" CSS and JS support. This includes non "smart" phones and simpler browsers like those found on game consoles and the kindle.

Touch Device
------------

	$request->isTouchDevice

`true` for devices that use touch events.

Wide Device
-----------

	$request->isWideDevice

`true` for devices that are wider than a common mobile phone. This covers tablets and desktop browsers.

Device Type
-----------

        $request->isSimpleDevice
        $request->isTouchDevice
        $request->isWideDevice
        $request->mobile
        $request->isWebkit
        $request->isIOSDevice
        $request->isAndroidDevice
        $request->isWebOSDevice
        $request->isWindowsPhoneDevice

`true` if the device is part of the given platform.

These give more granular information about modern smart devices. This is helpful if you want to target features to a specific device type.

Other Attributes
----------------

	$request->isWebkit

`true` if the browser is webkit (desktop or mobile.)

If you only have certain route that need the distinction all you need to do is adding the middleware to it:
```php
Route::get('/need-destination', [
   'middleware'=> 'mini-detector:viewNeedDestination',
   'uses' => 'NeedDestinationController@index',
]);
```

Of course this middleware can also be applied to a bunch of routes:

```php
Route::group(['prefix' => 'admin', 'middleware' => 'mini-detector:viewAdmin'], function() {

   //all the controllers of your admin section
   ...
   
});
```

## Install

You can install the package via composer:
``` bash
$ composer require torkashvand/laravel-minidevicedetector
```

Next, you must install the service provider:

```php
// config/app.php
'providers' => [
    ...
    Torkashvand\MiniDeviceDetector\MiniDeviceDetectorServiceProvider::class,
];
```

Next, the `\Torkashvand\MiniDeviceDetector\Middleware\MiniDeviceDetector::class`-middleware must be registered in the kernel:

```php
//app/Http/Kernel.php
protected $routeMiddleware = [
  ...
  'mini-detector' => \Torkashvand\MiniDeviceDetector\Middleware\MiniDeviceDetector::class,
];
```

Naming the middleware `mini-detector` is just a suggestion. You can give it any name you'd like.

## Usage

### Detecting device type

```php

Route::get('/need-destination', ['middleware'=> 'mini-detector','uses' => 'NeedToDetectController@show']);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

