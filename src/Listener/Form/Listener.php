<?php

namespace SilverStripe\EventDispatcher\Listener\Form;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Extension;
use SilverStripe\EventDispatcher\Dispatch\Dispatcher;
use SilverStripe\EventDispatcher\Symfony\Event;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormRequestHandler;

/**
 * Class Submission
 *
 * Snapshot action listener for form submissions
 *
 * @property FormRequestHandler|$this $owner
 */
class Listener extends Extension
{
    /**
     * Extension point in @see FormRequestHandler::httpSubmission
     * controller action via form submission action
     *
     * @param HTTPRequest $request
     * @param $funcName
     * @param $vars
     * @param Form $form
     */
    public function afterCallFormHandler(HTTPRequest $request, $funcName, $vars, $form): void
    {
        Dispatcher::singleton()->trigger(
            'formSubmitted',
            Event::create(
                $funcName,
                [
                    'form' => $form,
                    'request' => $request,
                    'vars' => $vars
                ]
            )
        );
    }
}
