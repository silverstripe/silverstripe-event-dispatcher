<?php

namespace SilverStripe\EventDispatcher\Dispatch;

interface DispatcherLoaderInterface
{
    public function addToDispatcher(EventDispatcherInterface $dispatcher): void;
}
