<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_joomlalabs_webservices_helpmenu
 *
 * @copyright   (C) 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

// Header icon
$headerIcon = $params->get('header_icon', 'icon-code');
?>
<?php if (!empty($buttons)) : ?>
<div class="card">
    <h2 class="card-header">
        <span class="<?php echo htmlspecialchars($headerIcon, ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></span>
        <?php echo htmlspecialchars($module->title, ENT_QUOTES, 'UTF-8'); ?>
    </h2>
    <ul class="list-group list-group-flush">
        <?php foreach ($buttons as $button) : ?>
            <li class="list-group-item d-flex align-items-center">
                <a class="flex-grow-1" href="<?php echo $button['link']; ?>">
                    <?php echo htmlspecialchars($button['text'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
