<?php

namespace modules\locomotive;

use Craft;
use modules\locomotive\twig\Extension;
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
        parent::init();

        // Declare aliases
        Craft::setAlias('@locomotive', __DIR__);
        Craft::setAlias('@modules/locomotive', __DIR__);

        // Declare the web/console controllers
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\locomotive\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\locomotive\\controllers';
        }

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function () {
            $this->registerTwigExtension();
        });
    }

    private function registerTwigExtension(): void
    {
        Craft::$app->getView()->registerTwigExtension(new Extension());
    }
}
