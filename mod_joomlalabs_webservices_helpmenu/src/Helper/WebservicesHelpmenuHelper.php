<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_joomlalabs_webservices_helpmenu
 *
 * @copyright   (C) 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Joomla\Module\JoomlalabsWebservicesHelpmenu\Administrator\Helper;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_joomlalabs_webservices_helpmenu
 *
 * @since  1.0.0
 */
class WebservicesHelpmenuHelper
{
    /**
     * Get the help menu items for Web Services
     *
     * @param   Registry                   $params  Module parameters
     * @param   CMSApplicationInterface    $app     Application instance
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getButtons(Registry $params, CMSApplicationInterface $app): array
    {
        $buttons = [];

        // API Documentation menu item
        /*
        $buttons[] = [
            'link'  => Route::_('index.php?option=com_joomlalabs_webservices&view=documentation'),
            'image' => 'icon-book',
            'text'  => Text::_('MOD_JOOMLALABS_WEBSERVICES_HELPMENU_API_DOCUMENTATION'),
            'id'    => 'helpmenu_joomlalabs_api_documentation',
            'group' => 'MOD_JOOMLALABS_WEBSERVICES_HELPMENU',
        ];
        */

        // Swagger UI menu item
        $buttons[] = [
            'link'  => Route::_('index.php?option=com_joomlalabs_webservices&view=swagger'),
            'image' => 'icon-code',
            'text'  => Text::_('MOD_JOOMLALABS_WEBSERVICES_HELPMENU_SWAGGER'),
            'id'    => 'helpmenu_joomlalabs_swagger',
            'group' => 'MOD_JOOMLALABS_WEBSERVICES_HELPMENU',
        ];

        // Redoc menu item
        $buttons[] = [
            'link'  => Route::_('index.php?option=com_joomlalabs_webservices&view=redoc'),
            'image' => 'icon-file-alt',
            'text'  => Text::_('MOD_JOOMLALABS_WEBSERVICES_HELPMENU_REDOC'),
            'id'    => 'helpmenu_joomlalabs_redoc',
            'group' => 'MOD_JOOMLALABS_WEBSERVICES_HELPMENU',
        ];

        return $buttons;
    }
}
