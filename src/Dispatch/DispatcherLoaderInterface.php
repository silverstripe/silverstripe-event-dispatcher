<?php


namespace SilverStripe\EventDispatcher\Dispatch;

interface DispatcherLoaderInterface
{
    public function addToDispatcher(Dispatcher $dispatcher): void;
}
