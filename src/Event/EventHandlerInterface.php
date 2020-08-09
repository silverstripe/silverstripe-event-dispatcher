<?php

namespace SilverStripe\EventDispatcher\Event;

interface EventHandlerInterface
{
    public function fire(EventContextInterface $context): void;
}
