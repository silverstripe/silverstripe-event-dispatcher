<?php

namespace SilverStripe\EventDispatcher\Tests\Dispatch;

use SilverStripe\EventDispatcher\Event\EventContextInterface;

class MockEvent implements EventContextInterface
{
    public $result = '';

    private $action = null;

    public function __construct($action = null)
    {
        $this->action = $action;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function get(string $name)
    {
        return null;
    }
}
