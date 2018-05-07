# Mobtexting notifications channel for Laravel 5.3+

This package makes it easy to send [Mobtexting notifications](https://mobtexting.com) with Laravel 5.3.

## Contents

- [Installation](#installation)
	- [Setting up your Mobtexting account](#setting-up-your-twilio-account)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

``` bash
composer require mobtexting/laravel
```

Add the service provider (only required on Laravel 5.4 or lower):

```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\Mobtexting\MobtextingProvider::class,
],
```

### Setting up your Mobtexting account

Add your Mobtexting Auth Token, and From Number (optional) to your `config/services.php`:

```php
// config/services.php
...
'mobtexting' => [
    'username' => env('MOBTEXTING_USERNAME'), // optional when using auth token
    'password' => env('MOBTEXTING_PASSWORD'), // optional when using auth token
    'token' => env('MOBTEXTING_AUTH_TOKEN'), // optional when using username and password
    'from' => env('TWILIO_FROM'), // optional
],
...
```

## Usage

Now you can use the channel in your `via()` method inside the notification:

``` php
use NotificationChannels\Mobtexting\MobtextingChannel;
use NotificationChannels\Mobtexting\MobtextingSmsMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [MobtextingChannel::class];
    }

    public function toMobtexting($notifiable)
    {
        return (new MobtextingSmsMessage())
            ->text("Your {$notifiable->service} account was approved!");
    }
}
```

In order to let your Notification know which phone are you sending/calling to, the channel will look for the `phone_number` attribute of the Notifiable model. If you want to override this behaviour, add the `routeNotificationForMobtexting` method to your Notifiable model.

```php
public function routeNotificationForMobtexting()
{
    return '+1234567890';
}
```

### Available Message methods

#### MobtextingSmsMessage

- `from('')`: Accepts a phone to use as the notification sender.
- `text('')`: Accepts a string value for the notification body.
- `to('')`: Accepts a string value for the notification to (over writes default).

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email support@mobtexting.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
