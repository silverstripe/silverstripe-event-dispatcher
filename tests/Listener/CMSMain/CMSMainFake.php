<?php


namespace SilverStripe\EventDispatcher\Tests\Listener\CMSMain;


use SilverStripe\CMS\Controllers\CMSMain;

class CMSMainFake extends CMSMain
{
    private static $allowed_actions = [
        'myaction',
    ];

    public function myaction()
    {
        return 'this is my action';
    }

    public function myFormAction($data, $form)
    {
        return 'submitted';
    }
}
