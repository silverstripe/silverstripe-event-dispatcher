<?php


namespace SilverStripe\EventDispatcher\Tests\Listener\GridField\Alteration;


use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;

class ActionProviderFake implements GridField_ActionProvider
{
    public function getActions($gridField)
    {
        return ['myaction'];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName === 'myaction') {
            return 'my action success';
        }
    }
}
