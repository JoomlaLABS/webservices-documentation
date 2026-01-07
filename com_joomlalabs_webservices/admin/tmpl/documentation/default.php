<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlalabs_webservices
 *
 * @copyright   (C) 2015 - 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @var \Joomla\Component\JoomlalabsWebservices\Administrator\View\Documentation\HtmlView $this
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Language\Text;

?>

<div class="com-webservices-documentation">
    <div class="card">
        <div class="card-body">
            <h2><?php echo Text::_('COM_WEBSERVICES_DOCUMENTATION_HEADING'); ?></h2>
            
            <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span>
                <?php echo Text::_('COM_WEBSERVICES_DOCUMENTATION_INTRO'); ?>
            </div>

            <div class="ratio ratio-16x9" style="height: 80vh;">
                <iframe 
                    src="https://help.joomla.org/proxy?keyref=J4.x:Joomla_Core_APIs&lang=en" 
                    title="<?php echo Text::_('COM_WEBSERVICES_DOCUMENTATION_TITLE'); ?>"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>
