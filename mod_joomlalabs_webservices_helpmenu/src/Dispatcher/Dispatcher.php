<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_joomlalabs_webservices_helpmenu
 *
 * @copyright   (C) 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Module\JoomlalabsWebservicesHelpmenu\Administrator\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Dispatcher class for mod_joomlalabs_webservices_helpmenu
 *
 * @since  1.0.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   1.0.0
     */
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();

        $data['buttons'] = $this->getHelperFactory()
            ->getHelper('WebservicesHelpmenuHelper')
            ->getButtons($data['params'], $this->getApplication());

        return $data;
    }
}
