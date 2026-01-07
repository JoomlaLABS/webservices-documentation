<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlalabs_webservices
 *
 * @copyright   (C) 2015 - 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\JoomlalabsWebservices\Administrator\View\Swagger;

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
 * View class for displaying Swagger API Explorer
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
        ToolbarHelper::title(Text::_('COM_WEBSERVICES_SWAGGER_TITLE'), 'icon-code');
        ToolbarHelper::help('', false, 'https://help.joomla.org/proxy?keyref=J4.x:Joomla_Core_APIs&lang=en');
    }
    
    /**
     * Prepare the document with Swagger UI assets and options
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
        
        // Register Swagger UI assets
        $wa->registerStyle(
            'swagger-ui',
            'com_joomlalabs_webservices/swagger-ui.css',
            [],
            ['relative' => true, 'version' => 'auto']
        );
        
        $wa->registerScript(
            'swagger-ui-bundle',
            'com_joomlalabs_webservices/swagger-ui-bundle.js',
            [],
            ['defer' => true, 'relative' => true, 'version' => 'auto']
        );
        
        $wa->registerScript(
            'swagger-ui-standalone',
            'com_joomlalabs_webservices/swagger-ui-standalone-preset.js',
            [],
            ['defer' => true, 'relative' => true, 'version' => 'auto'],
            ['swagger-ui-bundle']
        );
        
        $wa->registerScript(
            'swagger-hierarchical-tags',
            'com_joomlalabs_webservices/swagger-ui-plugin-hierarchical-tags.js',
            [],
            ['defer' => true, 'relative' => true, 'version' => 'auto'],
            ['swagger-ui-bundle', 'swagger-ui-standalone']
        );
        
        // Use registered assets
        $wa->useStyle('swagger-ui');
        $wa->useScript('swagger-ui-bundle');
        $wa->useScript('swagger-ui-standalone');
        $wa->useScript('swagger-hierarchical-tags');
        
        // Get base URL and API token
        $baseUrl = rtrim(Uri::root(), '/');
        
        try {
            $apiToken = $this->getApiToken();
        } catch (\Exception $e) {
            // If token generation fails, show error message and use empty token
            $app->enqueueMessage($e->getMessage(), 'warning');
            $apiToken = '';
        }
        
        // Pass options to JavaScript
        $doc->addScriptOptions('swagger-ui', [
            'baseUrl' => $baseUrl,
            'specUrls' => [
                'static' => 'joomla-core-apis.yaml',
                'generated-active' => 'generate-joomla-core-apis.php',
                'generated-all' => 'generate-joomla-core-apis.php?showAll=true',
            ],
            'apiToken' => $apiToken,
        ]);
    }
    
    /**
     * Get API token for current user
     *
     * @return  string  The API token
     *
     * @throws  \Exception  If token cannot be generated
     * @since   1.0.0
     */
    private function getApiToken(): string
    {
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        
        // Check if user is logged in
        if ($user->guest) {
            throw new \Exception('User must be logged in to generate API token');
        }
        
        $userId = $user->id;
        
        // Get database instance
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
        // Get site secret from configuration
        $siteSecret = $app->get('secret');
        
        // Get table prefix
        $prefix = $db->getPrefix();
        
        // Query to get token_seed and token_enabled from user_profiles
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('up_token.profile_value', 'token_seed'),
                $db->quoteName('up_enabled.profile_value', 'token_enabled')
            ])
            ->from($db->quoteName('#__users', 'u'))
            ->leftJoin(
                $db->quoteName('#__user_profiles', 'up_token'),
                $db->quoteName('u.id') . ' = ' . $db->quoteName('up_token.user_id') . 
                ' AND ' . $db->quoteName('up_token.profile_key') . ' = ' . $db->quote('joomlatoken.token')
            )
            ->leftJoin(
                $db->quoteName('#__user_profiles', 'up_enabled'),
                $db->quoteName('u.id') . ' = ' . $db->quoteName('up_enabled.user_id') . 
                ' AND ' . $db->quoteName('up_enabled.profile_key') . ' = ' . $db->quote('joomlatoken.enabled')
            )
            ->where($db->quoteName('u.id') . ' = ' . (int) $userId);
        
        $db->setQuery($query);
        $result = $db->loadObject();
        
        // Check if token_seed exists
        if (!$result || !$result->token_seed) {
            throw new \Exception(Text::sprintf('COM_JOOMLALABS_WEBSERVICES_ERROR_NO_TOKEN_CONFIGURED', $user->username));
        }
        
        // Check if token is enabled
        if ($result->token_enabled != '1') {
            throw new \Exception(Text::sprintf('COM_JOOMLALABS_WEBSERVICES_ERROR_TOKEN_DISABLED', $user->username));
        }
        
        // Generate bearer token using HMAC
        $algorithm = 'sha256';
        $tokenHMAC = hash_hmac($algorithm, base64_decode($result->token_seed), $siteSecret);
        $bearerToken = base64_encode("$algorithm:{$userId}:$tokenHMAC");
        
        return $bearerToken;
    }
}
