<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlalabs_webservices
 *
 * @copyright   (C) 2015 - 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @var \Joomla\Component\JoomlalabsWebservices\Administrator\View\Redoc\HtmlView $this
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$doc = Factory::getApplication()->getDocument();
$wa  = $doc->getWebAssetManager();

// Register and use Redoc themes script
$wa->registerAndUseScript(
    'redoc-themes',
    'com_joomlalabs_webservices/redoc-themes.js',
    [],
    ['relative' => true, 'version' => 'auto']
);

// Add custom styles for Redoc
$wa->addInlineStyle('
/* Container for Redoc */
#redoc-container {
    width: 100%;
    min-height: calc(100vh - 200px);
}

/* Spec selector container */
#spec-selector-container {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 15px 0;
    margin-bottom: 15px;
    border-bottom: 1px solid var(--bs-border-color);
}

/* Spec selector styles */
#spec-selector {
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

#spec-selector label {
    margin: 0;
    font-weight: 500;
    color: var(--bs-body-color);
    white-space: nowrap;
}

#spec-selector select {
    padding: 5px 10px;
    border: 1px solid var(--bs-border-color);
    border-radius: 4px;
    background: var(--bs-body-bg);
    color: var(--bs-body-color);
    cursor: pointer;
    min-width: 250px;
}

#spec-selector select:hover {
    border-color: var(--bs-primary);
}

#spec-selector select:focus {
    outline: none;
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
}
');

// Add inline JavaScript for Redoc initialization
$wa->addInlineScript('
document.addEventListener("DOMContentLoaded", function() {
    // Monitor dark mode changes and sync with Swagger UI
    const htmlElement = document.documentElement;
    
    // Function to update dark-mode class
    function updateDarkMode() {
        if (htmlElement.getAttribute("data-bs-theme") === "dark") {
            htmlElement.classList.add("dark-mode");
        } else {
            htmlElement.classList.remove("dark-mode");
        }
    }
    
    // Set initial state
    updateDarkMode();
    
    // Watch for theme changes and reinitialize Redoc
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === "attributes" && mutation.attributeName === "data-bs-theme") {
                updateDarkMode();
                
                // Reinitialize Redoc with new theme
                const container = document.getElementById("redoc-container");
                container.innerHTML = "";
                initRedoc(currentSpecUrl);
            }
        });
    });
    
    observer.observe(htmlElement, {
        attributes: true,
        attributeFilter: ["data-bs-theme"]
    });

    const options = Joomla.getOptions("redoc");
    
    if (!options) {
        console.error("Redoc options not found");
        return;
    }
    
    if (!window.RedocThemes) {
        console.error("Redoc themes not loaded");
        return;
    }
    
    let currentSpecUrl = options.defaultSpec;
    
    // Function to get current theme based on Bootstrap theme
    function getCurrentTheme() {
        const isDark = htmlElement.getAttribute("data-bs-theme") === "dark";
        return isDark ? window.RedocThemes.dark : window.RedocThemes.light;
    }
    
    // Initialize Redoc with spec and current theme
    function initRedoc(specUrl) {
        const theme = getCurrentTheme();
        
        Redoc.init(
            specUrl,
            {
                scrollYOffset: 70,
                hideDownloadButton: false,
                disableSearch: false,
                expandResponses: "200,201",
                jsonSampleExpandLevel: 2,
                hideSingleRequestSampleTab: true,
                menuToggle: true,
                nativeScrollbars: false,
                pathInMiddlePanel: true,
                requiredPropsFirst: true,
                sortPropsAlphabetically: true,
                theme: theme
            },
            document.getElementById("redoc-container")
        );
    }
    
    // Handle spec selector change
    const specSelector = document.getElementById("spec-url-selector");
    if (specSelector) {
        specSelector.addEventListener("change", function() {
            currentSpecUrl = this.value;
            // Clear and reinitialize Redoc
            const container = document.getElementById("redoc-container");
            container.innerHTML = "";
            initRedoc(currentSpecUrl);
        });
    }
    
    // Initialize with default spec
    initRedoc(currentSpecUrl);
});
', [], ['type' => 'module']);

?>

<div class="row">
    <div class="col-12">
        <!-- Spec Selector -->
        <div id="spec-selector-container">
            <div id="spec-selector">
                <label for="spec-url-selector">
                    <?php echo \Joomla\CMS\Language\Text::_('COM_WEBSERVICES_SELECT_SPEC'); ?>:
                </label>
                <select id="spec-url-selector" class="form-select form-select-sm">
                    <?php
                    $options = $doc->getScriptOptions('redoc');
                    if ($options && isset($options['specUrls'])) :
                        foreach ($options['specUrls'] as $spec) :
                    ?>
                        <option value="<?php echo htmlspecialchars($spec['url']); ?>" 
                                <?php echo $spec['url'] === $options['defaultSpec'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($spec['name']); ?>
                        </option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
        </div>

        <!-- Redoc Container -->
        <div id="redoc-container"></div>
    </div>
</div>
