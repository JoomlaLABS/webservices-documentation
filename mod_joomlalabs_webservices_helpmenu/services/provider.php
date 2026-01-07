<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_joomlalabs_webservices_helpmenu
 *
 * @copyright   (C) 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\Joomla\\Module\\JoomlalabsWebservicesHelpmenu'));
        $container->registerServiceProvider(new HelperFactory('\\Joomla\\Module\\JoomlalabsWebservicesHelpmenu\\Administrator\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
