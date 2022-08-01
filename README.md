# Event dispatcher for Silverstripe CMS

[![CI](https://github.com/silverstripe/silverstripe-event-dispatcher/actions/workflows/ci.yml/badge.svg)](https://github.com/silverstripe/silverstripe-event-dispatcher/actions/workflows/ci.yml)

This module provides a [PSR-14 event dispatcher](https://www.php-fig.org/psr/psr-14/) to Silverstripe CMS
applications. It is useful for [reactive programming](https://en.wikipedia.org/wiki/Reactive_programming)
paradigms in lieu of traditional imperative designs through hooks like `onAfterWrite`.

Most of the underlying work is handled by [Symfony's EventDispatcher](https://symfony.com/doc/current/components/event_dispatcher.html),
but this can be replaced with through dependency injection.

## Requirements

* silverstripe/framework: ^4.5

## Installation

`$ composer require silverstripe/event-dispatcher`

## Usage

There are three main components to the API:

* The `Dispatcher` is responsible for registering event listeners and triggering events
* `Listeners` are classes that trigger events, typically via extension hooks
* `Handlers` are classes that execute code in response to an event.

### Dispatching an event

```php
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Symfony\Event;

Dispatcher::singleton()->trigger('createOrder', Event::create(
    null,
    [
        'id' => $order->ID,
        'paymentMethod' => $order->PaymentMethod
    ]
));
```

The `trigger` function takes two arguments: `string $eventName` and `EventContextInterface $context`. The `$context`
object can pass arbitrary data to any handlers that are subscribed to the event.

### Event handling

Event handlers must implement `EventHandlerInterface`, which requires a `fire()` method.

```php
use SilverStripe\EventDispatcher\Dispatch\EventHandlerInterface;
use SilverStripe\EventDispatcher\Event\EventContextInterface;

class OrderHandler implements EventHandlerInterface
{
    public function fire(EventContextInterface $context): void
    {
        $orderID = $context->get('id');
        $paymentMethod = $context->get('paymentMethod');
        // Do something in response to the order being created
    }
}
```

### Subscribing to events

The best way to register your event handlers with the dispatcher is through config.

```yaml
SilverStripe\Core\Injector\Injector:
  SilverStripe\EventDispatcher\Dispatch\Dispatcher:
    properties:
      handlers:
        # Arbitrary key. Allows other config to override.
        orders:
          on: [ orderCreated ]
          handler: %$MyProject\MyOrderHandler
```

### Unsubscribing to events

To remove an event handler, override the config with an `off` node.

```yaml
SilverStripe\Core\Injector\Injector:
  SilverStripe\EventDispatcher\Dispatch\Dispatcher:
    properties:
      handlers:
        orders:
          off: [ orderCreated ]
```

### Managing events procedurally

To interact with the `Dispatcher` instance directly, use a `DispatcherLoaderInterface`.

```php
use SilverStripe\EventDispatcher\Dispatch\DispatcherLoaderInterface;
use SilverStripe\EventDispatcher\Dispatch\EventDispatcherInterface;

class MyLoader implements DispatcherLoaderInterface
{
    public function addToDispatcher(EventDispatcherInterface $dispatcher) : void
    {
        $dispatcher->addListener('myEvent', $myHandler);
        $dispatcher->removeListener('anotherEvent', $anotherHandler);
    }
}
```

Then, register the loader in `Injector`.

```yaml
SilverStripe\Core\Injector\Injector:
  SilverStripe\EventDispatcher\Dispatch\Dispatcher:
    properties:
      loaders:
        myLoader: %$MyProject\MyLoader
```

### Action identifiers and context

Each of these handlers is passed a context object that exposes an **action identifier**. This is a string that
provides specific information about what happened in the event that the handler can then use in its implementation.
For instance, if you want to write event handlers for form submissions, where some handlers are for all form submissions,
while others are for specific forms, you might pass the name of the form as an action identifier in your `EventContext`
object.

```php
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Symfony\Event;

Dispatcher::singleton()->trigger('formSubmitted', Event::create(
    'contact',
    [
        'name' => $formData['Name'],
        // etc..
    ]
));
```

Events are always called with `eventName.<action identifier>`. For instance `formSubmitted.contact`, allowing
the subscribers to only react to a specific subset of events.

```yaml
SilverStripe\Core\Injector\Injector:
  SilverStripe\EventDispatcher\Dispatch\Dispatcher:
    properties:
      handlers:
        forms:
          # handler for all form submissions
          on: [ formSubmitted ]
          handler: %$MyProject\MyFormHandler
        contactForm:
          # handler for all a specific form
          on: [ formSubmitted.contact ]
          handler: %$MyProject\MyContactHandler
```

In this case, a contact form submission results in two handlers firing, in order of specificity (`formSubmitted.contact`
first).

#### How to find your action identifier

The easiest way to debug events is to put breakpoints or logging into the `Dispatcher::trigger()` function. This
will provide all the detail you need about what events are triggered when, and with what context.

```php
public function trigger(string $event, EventContextInterface $context): void
{
    error_log($event);
    error_log($context->getAction());
    // ...
```

When the logging is in place you just go to the CMS and perform the action you are interested in.
This should narrow the list of identifier down to a much smaller subset.

#### Event context

In the above example, the contact form data is passed to the `Event` object as context.

```php
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Symfony\Event;

Dispatcher::singleton()->trigger('formSubmitted', Event::create(
    'contact',
    [
        'name' => $formData['Name'],
        // etc..
    ]
));
```

In a handler, this can be accessed using the `get(string $property)` method.

```php
public function fire(EventContextInterface $context): void
{
    $name = $context->get('Name');
    // do more stuff...
}
```

Note that `get` fails gracefully, and will return `null` when a property doesn't exist.

## License
See [License](license.md)

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: Silverstripe version, Browser, PHP version,
 Operating System, any installed Silverstripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
