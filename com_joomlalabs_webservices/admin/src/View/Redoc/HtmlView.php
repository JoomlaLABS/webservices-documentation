<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlalabs_webservices
 *
 * @copyright   (C) 2015 - 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\JoomlalabsWebservices\Administrator\View\Redoc;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for displaying Redoc API Documentation
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($tpl = null): void
    {
        $this->addToolbar();
        $this->prepareDocument();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_WEBSERVICES_REDOC_TITLE'), 'icon-book');
        ToolbarHelper::help('', false, 'https://help.joomla.org/proxy?keyref=J4.x:Joomla_Core_APIs&lang=en');
    }
    
    /**
     * Prepare the document with Redoc script
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function prepareDocument(): void
    {
        $app = Factory::getApplication();
        $doc = $app->getDocument();
        $wa  = $doc->getWebAssetManager();
        
        // Register Redoc standalone script
        $wa->registerScript(
            'redoc-standalone',
            'com_joomlalabs_webservices/redoc.standalone.js',
            [],
            ['defer' => true, 'relative' => true, 'version' => 'auto']
        );
        
        // Use the script
        $wa->useScript('redoc-standalone');
        
        // Get base URL
        $baseUrl = rtrim(Uri::root(), '/');
        
        // Build spec URLs - use correct media path
        $specUrls = [
            [
                'name' => 'Static Spec',
                'url' => $baseUrl . '/media/com_joomlalabs_webservices/joomla-core-apis.yaml'
            ],
            [
                'name' => 'Generated (Active Plugins)',
                'url' => $baseUrl . '/media/com_joomlalabs_webservices/generate-joomla-core-apis.php'
            ],
            [
                'name' => 'Generated (All Components)',
                'url' => $baseUrl . '/media/com_joomlalabs_webservices/generate-joomla-core-apis.php?showAll=true'
            ],
        ];
        
        // Pass configuration to JavaScript
        $doc->addScriptOptions('redoc', [
            'baseUrl' => $baseUrl,
            'specUrls' => $specUrls,
            'defaultSpec' => $specUrls[0]['url'], // Default to static spec
        ]);
    }
}
