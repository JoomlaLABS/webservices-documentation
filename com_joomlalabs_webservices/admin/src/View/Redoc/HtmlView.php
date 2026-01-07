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
        
        // Get base URL and API token
        $baseUrl = rtrim(Uri::root(), '/');
        
        try {
            $apiToken = $this->getApiToken();
        } catch (\Exception $e) {
            // If token generation fails, show error message and use empty token
            $app->enqueueMessage($e->getMessage(), 'warning');
            $apiToken = '';
        }
        
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
                'url' => $baseUrl . '/media/com_joomlalabs_webservices/generate-joomla-core-apis.php?all=1'
            ],
        ];
        
        // Pass configuration to JavaScript
        $doc->addScriptOptions('redoc', [
            'baseUrl' => $baseUrl,
            'apiToken' => $apiToken,
            'specUrls' => $specUrls,
            'defaultSpec' => $specUrls[1]['url'], // Default to generated active
        ]);
    }
    
    /**
     * Get or create an API token for the current user
     *
     * @return  string  The API token
     *
     * @since   1.0.0
     * @throws  \Exception
     */
    protected function getApiToken(): string
    {
        $user = Factory::getApplication()->getIdentity();
        
        if ($user->guest) {
            throw new \Exception(Text::_('COM_WEBSERVICES_ERROR_NOT_LOGGED_IN'));
        }
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
        // Check if user already has a token
        $query = $db->getQuery(true)
            ->select($db->quoteName('token'))
            ->from($db->quoteName('#__user_keys'))
            ->where($db->quoteName('user_id') . ' = ' . (int) $user->id)
            ->where($db->quoteName('series') . ' = ' . $db->quote('api-token'));
        
        $db->setQuery($query);
        $token = $db->loadResult();
        
        if ($token) {
            return $token;
        }
        
        // Generate new token
        $token = bin2hex(random_bytes(32));
        
        // Insert the token
        $data = [
            'user_id' => (int) $user->id,
            'series' => 'api-token',
            'token' => $token,
            'time' => time(),
        ];
        
        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__user_keys'))
            ->columns($db->quoteName(array_keys($data)))
            ->values(implode(',', array_map([$db, 'quote'], $data)));
        
        $db->setQuery($query);
        $db->execute();
        
        return $token;
    }
}
