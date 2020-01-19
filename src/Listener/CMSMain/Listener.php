<?php

namespace SilverStripe\EventDispatcher\Listener\CMSMain;

use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Extension;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Symfony\Event;

if (!class_exists(CMSMain::class)) {
    return;
}

/**
 * Class CMSMainAction
 *
 * Snapshot action listener for CMS main actions
 *
 * @property CMSMain|$this $owner
 */
class Listener extends Extension
{
    /**
     * Extension point in @see CMSMain::handleAction
     *
     * @param HTTPRequest $request
     * @param $action
     * @param $result
     */
    public function afterCallActionHandler(HTTPRequest $request, $action, $result): void
    {
        Dispatcher::singleton()->trigger(
            'cmsAction',
            Event::create(
                $action,
                [
                    'result' => $result,
                    'treeClass' => $this->owner->config()->get('tree_class'),
                    'id' => $request->requestVar('ID'),
                ]
            )
        );
    }
}
