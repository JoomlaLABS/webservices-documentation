<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_joomlalabs_webservices_helpmenu
 *
 * @copyright   (C) 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

return new class () implements InstallerScriptInterface {
    private string $minimumPhp = '8.1.0';
    private string $minimumJoomla = '6.0.0';

    public function install(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function update(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function uninstall(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        if (version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
            Factory::getApplication()->enqueueMessage(
                sprintf(Text::_('JLIB_INSTALLER_MINIMUM_PHP'), $this->minimumPhp),
                'error'
            );
            return false;
        }

        if (version_compare(JVERSION, $this->minimumJoomla, '<')) {
            Factory::getApplication()->enqueueMessage(
                sprintf(Text::_('JLIB_INSTALLER_MINIMUM_JOOMLA'), $this->minimumJoomla),
                'error'
            );
            return false;
        }

        return true;
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        if ($type === 'install') {
            $this->enableModule();
            $this->publishModule();
        }

        return true;
    }

    private function enableModule(): void
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__modules'))
            ->set($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('module') . ' = ' . $db->quote('mod_joomlalabs_webservices_helpmenu'));

        $db->setQuery($query);
        $db->execute();
    }

    private function publishModule(): void
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        // Get module ID
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('module') . ' = ' . $db->quote('mod_joomlalabs_webservices_helpmenu'));

        $db->setQuery($query);
        $moduleId = $db->loadResult();

        if (!$moduleId) {
            return;
        }

        // Set module style to "none" (System-none)
        $params = json_encode(['style' => 'System-none']);

        // Assign to position cpanel-help with style none and custom title
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__modules'))
            ->set($db->quoteName('title') . ' = ' . $db->quote('Web Services'))
            ->set($db->quoteName('position') . ' = ' . $db->quote('cpanel-help'))
            ->set($db->quoteName('ordering') . ' = 99')
            ->set($db->quoteName('params') . ' = ' . $db->quote($params))
            ->where($db->quoteName('id') . ' = ' . (int) $moduleId);

        $db->setQuery($query);
        $db->execute();

        // Assign to all admin pages
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__modules_menu'))
            ->where($db->quoteName('moduleid') . ' = ' . (int) $moduleId);

        $db->setQuery($query);
        $db->execute();

        // menuid = 0 means "on all pages"
        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__modules_menu'))
            ->columns([$db->quoteName('moduleid'), $db->quoteName('menuid')])
            ->values((int) $moduleId . ', 0');

        $db->setQuery($query);
        $db->execute();

        Factory::getApplication()->enqueueMessage(
            Text::_('MOD_JOOMLALABS_WEBSERVICES_HELPMENU_POSTINSTALL_MESSAGE'),
            'message'
        );
    }
};
