<?php

namespace SilverStripe\EventDispatcher\Event;

interface EventContextInterface
{
    /**
     * @return string|null
     */
    public function getAction(): ?string;

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name);
}
