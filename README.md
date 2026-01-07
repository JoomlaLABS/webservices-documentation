# Web Services Documentation for Joomla

![GitHub all releases](https://img.shields.io/github/downloads/JoomlaLABS/webservices-documentation/total?style=for-the-badge&color=blue)
![GitHub release (latest by SemVer)](https://img.shields.io/github/downloads/JoomlaLABS/webservices-documentation/latest/total?style=for-the-badge&color=blue)
![GitHub release (latest by SemVer)](https://img.shields.io/github/v/release/JoomlaLABS/webservices-documentation?sort=semver&style=for-the-badge&color=blue)

[![License](https://img.shields.io/badge/license-GPL%202.0%2B-green.svg)](LICENSE)
[![Joomla 6.0+](https://img.shields.io/badge/Joomla!-6.0+-1A3867?logo=joomla&logoColor=white)]()
[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php&logoColor=white)]()

## 📖 Description

**Web Services Documentation** is a comprehensive API documentation system for Joomla 6.0+. It provides interactive API documentation using **Swagger UI** and **Redoc**, with automatic **OpenAPI 3.1.0** specification generation from installed Joomla components.

Perfect for developers building integrations, testing APIs, or documenting custom components. Features intelligent hierarchical navigation, dark mode support, and seamless Joomla admin integration.

## ✨ Features

### 🎨 Dual Documentation Interfaces

**Swagger UI**
- Interactive API explorer with live testing
- Hierarchical navigation (up to 4-level tag tree)
- Request/response examples with syntax highlighting
- Built-in authentication with automatic token injection
- Filter parameter detection and documentation
- Custom hierarchical tags plugin for organized navigation

**Redoc**
- Clean, professional documentation interface
- Dark mode with automatic Joomla theme synchronization
- Responsive design optimized for all devices
- Fast rendering with virtual scrolling
- Three-panel layout with search functionality
- Sticky navigation for easy browsing

### 🔧 Automatic OpenAPI Generation

- **Dual-mode generator**: CLI script and web-accessible PHP
- **Component scanning**: Discovers all installed component endpoints
- **Plugin support**: Detects plugin-based API routes
- **Custom route parsing**: Handles complex Joomla routing patterns
- **Filter extraction**: Automatically documents query parameters
- **Smart deduplication**: Prevents duplicate parameter definitions
- **Hierarchical tags**: Generic path-based hierarchy (1-4 levels)

### 📊 Three Spec Options

1. **Static Spec**: Pre-generated core Joomla APIs
2. **Generated (Active Plugins)**: Dynamic spec with enabled plugins
3. **Generated (All Components)**: Complete spec including all installed components

### 🔐 Security & Authentication

- Automatic API token generation and management
- Secure token storage in Joomla database
- Token reuse for existing users
- Integration with Swagger UI authorization
- Proper permission checks throughout

### 🎯 Joomla Integration

**Component** (`com_joomlalabs_webservices`)
- Three views: Documentation, Swagger, Redoc
- Admin menu integration
- WebAssetManager for optimized asset loading
- PSR-4 namespacing
- Service Provider architecture

**Help Menu Module** (`mod_joomlalabs_webservices_helpmenu`)
- Quick access buttons in admin help menu
- Links to Documentation, Swagger, and Redoc
- Proper icon integration

**Dashboard Plugin** (`plg_system_joomlalabs_webservicesdashboard`)
- Two dashboard cards in Help dashboard
- API Documentation card
- API Explorer (Swagger) card
- Restricted to super admins

## 📋 Requirements

| Software | Minimum | Recommended |
|----------|---------|-------------|
| **Joomla!** | 6.0.0 | 6.0.1+ |
| **PHP** | 8.1+ | 8.2 or 8.3 |
| **Browser** | Modern browsers | Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ |

**Joomla Configuration:**
- Web Services must be enabled (System → Global Configuration → API Settings)
- User must have API token configured
- PHP extensions: json, curl, mbstring

## 📦 Installation

### Download & Install

1. Download the latest release from [GitHub Releases](https://github.com/JoomlaLABS/webservices-documentation/releases)
2. In Joomla Administrator, go to **System → Extensions → Install**
3. Upload the component package: `com_joomlalabs_webservices.zip`
4. Upload the module package: `mod_joomlalabs_webservices_helpmenu.zip`
5. Upload the plugin package: `plg_system_joomlalabs_webservicesdashboard.zip`
6. Enable the module and plugin from **System → Extensions → Manage**

### Initial Configuration

1. **Enable Web Services** (if not already enabled):
   - Go to **System → Global Configuration → API Settings**
   - Set **Enable Joomla's Web Services** to **Yes**
   - Save

2. **Configure API Token**:
   - The system will automatically generate a token on first access
   - Or manually create one: **System → API Tokens → New**

3. **Access Documentation**:
   - Via Help Menu: Click **Help → API Documentation/Swagger/Redoc**
   - Or direct URLs:
     - Documentation: `administrator/index.php?option=com_joomlalabs_webservices&view=documentation`
     - Swagger: `administrator/index.php?option=com_joomlalabs_webservices&view=swagger`
     - Redoc: `administrator/index.php?option=com_joomlalabs_webservices&view=redoc`

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md).

## 💡 Usage

### Browsing API Documentation

**Swagger UI:**
1. Navigate to Swagger view via Help Menu
2. Select spec option from dropdown (Static/Generated)
3. Expand endpoint groups using hierarchical navigation
4. Click endpoints to view details
5. Use "Try it out" to test APIs live

**Redoc:**
1. Navigate to Redoc view via Help Menu
2. Select spec option from dropdown
3. Browse using left sidebar navigation
4. Click endpoints for detailed documentation
5. Use search to find specific endpoints

### Testing APIs

1. Open Swagger UI
2. Click "Authorize" button (top right)
3. Your token is pre-filled, click "Authorize"
4. Navigate to any endpoint
5. Click "Try it out"
6. Fill in parameters
7. Click "Execute"
8. View response

### Generating Custom Specs

**Via Web Interface:**
- Access: `[site-url]/media/com_joomlalabs_webservices/generate-joomla-core-apis.php`
- Add `?all=1` to include all components

**Via CLI:**
```bash
php [joomla-root]/media/com_joomlalabs_webservices/generate-joomla-core-apis.php
```

## 🎨 Features Showcase

### Hierarchical Navigation

Endpoints are automatically organized in a tree structure:

```
Content
├── Articles
│   ├── GET /content/articles
│   ├── POST /content/articles
│   └── GET /content/articles/{id}
└── Fields
    ├── Articles
    │   ├── GET /content/fields/articles
    │   └── POST /content/fields/articles
    └── Groups
        └── Articles
            └── GET /content/fields/groups/articles
```

### Dark Mode Support

- Automatically detects Joomla admin theme
- Synchronized theme switching
- Optimized for both light and dark modes
- Smooth transitions between themes

### Smart Filter Detection

Automatically extracts and documents query parameters:
- `filter[search]` - Search filter
- `filter[published]` - Published status
- `filter[category_id]` - Category filter
- `page[offset]` - Pagination offset
- `page[limit]` - Results per page

## 🛠️ Technical Architecture

### Component Structure

```
com_joomlalabs_webservices/
├── admin/
│   ├── media/
│   │   ├── js/                          # JavaScript files
│   │   │   ├── swagger-ui-bundle.js
│   │   │   ├── swagger-ui-standalone-preset.js
│   │   │   ├── hierarchical-tags.js
│   │   │   ├── redoc.standalone.js
│   │   │   └── redark.js
│   │   ├── css/                         # Stylesheets
│   │   │   └── swagger-ui.css
│   │   ├── generate-joomla-core-apis.php  # Dynamic spec generator
│   │   └── joomla-core-apis.yaml        # Static OpenAPI spec
│   ├── src/
│   │   ├── Controller/
│   │   ├── View/
│   │   │   ├── Documentation/
│   │   │   ├── Swagger/
│   │   │   └── Redoc/
│   │   └── Extension/
│   ├── tmpl/                            # View templates
│   └── language/                        # Language files
└── api/                                 # API endpoints (if needed)
```

### Technologies Used

- **OpenAPI 3.1.0** - API specification standard
- **Swagger UI 5.x** - Interactive documentation
- **Redoc 2.x** - Professional documentation
- **Hierarchical Tags Plugin** - Tree navigation
- **Redark.js** - Dark mode support
- **Joomla WebAssetManager** - Asset optimization

## 🔧 Configuration Options

### Spec Selector

Choose between three spec options in both Swagger and Redoc:

1. **Static Spec** - Pre-generated, fastest loading
2. **Generated (Active Plugins)** - Dynamic, includes enabled plugins
3. **Generated (All Components)** - Complete, includes all components

### Generator Options

**Web Access:**
- `generate-joomla-core-apis.php` - Active plugins only
- `generate-joomla-core-apis.php?all=1` - All components

**CLI:**
```bash
# Active plugins only
php generate-joomla-core-apis.php

# All components
php generate-joomla-core-apis.php all
```

## 🐛 Troubleshooting

### Common Issues

**"Field 'uastring' doesn't have a default value"**
- Fixed in v1.0.0+
- Update to latest version

**"Failed to load API definition"**
- Check Web Services are enabled
- Verify API token exists
- Check file permissions for `/media/com_joomlalabs_webservices/`

**Dark mode not working**
- Clear browser cache
- Verify `redark.js` is loaded
- Check Joomla template supports `data-bs-theme`

**Hierarchical tags not showing**
- Clear Joomla cache
- Regenerate spec with "All Components" option
- Verify `hierarchical-tags.js` is loaded

For more help, see [SUPPORT.md](SUPPORT.md).

## 📚 Documentation

- **[INSTALLATION.md](INSTALLATION.md)** - Detailed installation guide
- **[SWAGGER_UI_IMPLEMENTATION.md](SWAGGER_UI_IMPLEMENTATION.md)** - Technical implementation
- **[CHANGELOG.md](CHANGELOG.md)** - Version history
- **[CONTRIBUTING.md](CONTRIBUTING.md)** - Contribution guidelines
- **[SUPPORT.md](SUPPORT.md)** - Getting help

## 🤝 Contributing

We welcome contributions! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for:

- How to report bugs
- How to suggest features
- Coding standards
- Pull request process

## 📄 License

This project is licensed under the **GNU General Public License v2.0+** - see the [LICENSE](LICENSE) file for details.

```
Copyright (C) 2026 Joomla!LABS

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

## 👥 Project Information

### 🏢 Project Owner

**Joomla!LABS** - [https://joomlalabs.com](https://joomlalabs.com)

[![Email](https://img.shields.io/badge/Email-info%40joomlalabs.com-red?style=for-the-badge&logo=gmail&logoColor=white)](mailto:info@joomlalabs.com)

### 👨‍💻 Contributors

**Luca Racchetti** - Lead Developer

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Luca%20Racchetti-blue?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/razzo/)
[![GitHub](https://img.shields.io/badge/GitHub-Razzo1987-black?style=for-the-badge&logo=github&logoColor=white)](https://github.com/Razzo1987)

## 🆘 Support

### 💬 Get Help

- 🐛 [Report a bug](https://github.com/JoomlaLABS/webservices-documentation/issues/new?labels=bug)
- 💡 [Request a feature](https://github.com/JoomlaLABS/webservices-documentation/issues/new?labels=enhancement)
- ❓ [Ask a question](https://github.com/JoomlaLABS/webservices-documentation/discussions)
- 📧 Email: info@joomlalabs.com

## 💝 Donate

If you find this project useful, consider supporting its development:

[![Sponsor on GitHub](https://img.shields.io/badge/Sponsor-GitHub-ea4aaa?style=for-the-badge&logo=github)](https://github.com/sponsors/JoomlaLABS)
[![Buy me a beer](https://img.shields.io/badge/🍺%20Buy%20me%20a-beer-FFDD00?style=for-the-badge&labelColor=FFDD00&color=FFDD00)](https://buymeacoffee.com/razzo)
[![Donate via PayPal](https://img.shields.io/badge/Donate-PayPal-0070BA?style=for-the-badge&logo=paypal&logoColor=white)](https://www.paypal.com/donate/?hosted_button_id=4SRPUJWYMG3GL)

Your support helps maintain and improve this project!

## 🌟 Related Projects

- [Swiper Slider Module](https://github.com/JoomlaLABS/swiperslider_module) - Modern slider for Joomla
- More coming soon...

---

**Made with ❤️ for the Joomla! Community**

**⭐ If this project helped you, please give it a star! ⭐**
