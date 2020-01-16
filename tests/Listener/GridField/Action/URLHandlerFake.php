<?php


namespace SilverStripe\EventDispatcher\Tests\Listener\GridField\Action;


use SilverStripe\Forms\GridField\GridField_URLHandler;

class URLHandlerFake implements GridField_URLHandler
{
    public function getURLHandlers($gridField)
    {
        return [
            'mygrid' => 'handleMyGrid',
        ];
    }

    public function handleMyGrid()
    {
        return 'my grid success';
    }
}
