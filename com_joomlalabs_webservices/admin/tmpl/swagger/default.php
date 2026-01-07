<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlalabs_webservices
 *
 * @copyright   (C) 2015 - 2026 Joomla!LABS. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @var \Joomla\Component\JoomlalabsWebservices\Administrator\View\Swagger\HtmlView $this
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$doc = Factory::getApplication()->getDocument();
$wa  = $doc->getWebAssetManager();

// Add custom styles for dark mode detection and Swagger UI customization
$wa->addInlineStyle('
/* Ensure Swagger UI takes full width */
#swagger-ui {
    width: 100%;
}

/* Dark mode styles */
html.dark-mode .swagger-ui {
    background: transparent;
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
', ['name' => 'swagger-custom-styles']);

// Add initialization script
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
    
    // Watch for theme changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === "attributes" && mutation.attributeName === "data-bs-theme") {
                updateDarkMode();
            }
        });
    });
    
    observer.observe(htmlElement, {
        attributes: true,
        attributeFilter: ["data-bs-theme"]
    });
    
    const options = window.Joomla.getOptions("swagger-ui");
    
    if (!options) {
        console.error("Swagger UI options not found");
        return;
    }
    
    // Build the full URLs to the spec files
    // After installation, media files are in media/com_joomlalabs_webservices/
    const mediaPath = options.baseUrl + "/media/com_joomlalabs_webservices/";
    const specUrls = {};
    
    // Build URLs for each spec type
    for (const [key, value] of Object.entries(options.specUrls)) {
        specUrls[key] = mediaPath + value;
    }
    
    // Default to generated-active spec
    let currentSpecUrl = specUrls["generated-active"];
    
    // Function to initialize Swagger UI
    function initSwaggerUI(url) {
        window.ui = SwaggerUIBundle({
            url: url,
            dom_id: "#swagger-ui",
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset.slice(1) // here we remove the topbar preset
        ],
        plugins: [
            SwaggerUIBundle.plugins.DownloadUrl,
            HierarchicalTagsPlugin
        ],
        hierarchicalTagSeparator: /\|/,
        layout: "StandaloneLayout",
        displayRequestDuration: true,
        filter: true,
        syntaxHighlight: {
            activate: true,
            theme: "monokai"
        },
        // Pre-authorize with Bearer token
        onComplete: function() {
            if (options.apiToken) {
                // Note: preauthorizeApiKey adds "Bearer " prefix automatically for Bearer auth
                window.ui.preauthorizeApiKey("BearerAuth", options.apiToken);
            }
        },
        // Set server URL dynamically
        servers: [
            {
                url: options.baseUrl + "api/index.php/v1",
                description: "Current Joomla site"
            }
        ]
        });
    }
    
    // Initialize with default spec
    initSwaggerUI(currentSpecUrl);
    
    // Add spec selector change handler
    const specSelector = document.getElementById("spec-type-selector");
    if (specSelector) {
        specSelector.addEventListener("change", function() {
            const selectedType = this.value;
            currentSpecUrl = specUrls[selectedType];
            initSwaggerUI(currentSpecUrl);
        });
    }
});
', ['position' => 'after', 'name' => 'swagger-init'], [], ['swagger-ui-standalone']);

?>

<!-- Spec Selector -->
<div id="spec-selector-container">
    <div id="spec-selector">
        <label for="spec-url-selector">
                    <?php echo \Joomla\CMS\Language\Text::_('COM_WEBSERVICES_SELECT_SPEC'); ?>:
                </label>
        <select id="spec-type-selector" class="form-select form-select-sm">
            <option value="generated-active" selected>Generated (Active Plugins)</option>
            <option value="generated-all">Generated (All Components)</option>
            <option value="static">Static (Manual)</option>
        </select>
    </div>
</div>

<div id="swagger-ui"></div>
