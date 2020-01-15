<?php

namespace SilverStripe\EventDispatcher\Listener\GridField\Action;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Event\EventContextInterface;
use SilverStripe\Forms\GridField\GridField;

/**
 * Class UrlHandlerAction
 *
 * Snapshot action listener for grid field actions
 *
 * @property GridField|$this $owner
 */
class Listener extends Extension
{

    /**
     * Extension point in @see GridField::handleRequest
     * GridField action via custom URL handler
     * covers action which are implemented via @see GridField_URLHandler
     *
     * @param HTTPRequest $request
     * @param $action
     * @param $result
     */
    public function afterCallActionURLHandler(HTTPRequest $request, $action, $result): void
    {
        Dispatcher::singleton()->trigger(
            'gridFieldAction',
            Injector::inst()->create(
                EventContextInterface::class,
                $action,
                [
                    'request' => $request,
                    'result' => $result,
                    'gridField' => $this->owner
                ]
            )
        );
    }
}