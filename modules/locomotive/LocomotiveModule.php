<?php

namespace locomotive;

use Craft;
use locomotive\twig\Extension;
use yii\base\Module as BaseModule;

/**
 * Locomotive module
 *
 * @method static LocomotiveModule getInstance()
 */
class LocomotiveModule extends BaseModule
{
    public function init(): void
    {
        Craft::setAlias('@locomotive', __DIR__);

        parent::init();
        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function() {
            $this->registerTwigExtension();
        });
    }

    private function registerTwigExtension(): void
    {
        Craft::$app->getView()->registerTwigExtension(new Extension);
    }
}
