# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Additional OpenAPI spec templates
- Enhanced filter parameter detection
- Support for custom API documentation
- Multi-language OpenAPI specifications
- Fix OpenAPI Specification: Joomla Core APIs - Active Plugins

## [Unreleased] - 2026-01-07

### Added

#### Component Features
- **Swagger UI Integration**: Interactive API documentation and testing interface
  - Hierarchical navigation with tree-style tag organization
  - Custom plugin for multi-level tag structure (up to 4 levels)
  - Three spec options: Static, Generated (Active Plugins), Generated (All Components)
  - Live API testing with automatic token injection
  - Request/response examples with syntax highlighting
  - Filter parameter detection and documentation
  
- **Redoc Integration**: Clean, professional API documentation view
  - Standalone Redoc interface with modern design
  - Dark mode support with automatic theme detection
  - Synchronized theme switching with Joomla admin
  - Multiple spec selector with dropdown menu
  - Optimized scrolling with sticky header support
  
- **Automatic OpenAPI 3.1.0 Generation**:
  - Dual-mode generator (CLI script and web-accessible PHP)
  - Dynamic endpoint discovery from installed components
  - Plugin and component scanning with route parsing
  - Custom route object parsing for accurate endpoint detection
  - Filter parameter extraction with automatic deduplication
  - Generic hierarchical tag generation based on path structure
  - Support for 1-4 level hierarchies (Component → Context → Resource → Sub-resource)
  
- **API Token Management**:
  - Automatic token generation for authenticated testing
  - Secure token storage in Joomla database
  - Token reuse for existing users
  - Integration with Swagger UI authorization
  
- **Three Documentation Views**:
  - **Documentation**: Overview and getting started guide
  - **Swagger**: Interactive API explorer and testing
  - **Redoc**: Clean documentation interface

#### Module Features
- **Help Menu Integration** (`mod_joomlalabs_webservices_helpmenu`):
  - Three menu buttons in Joomla admin help menu
  - Quick access to Documentation, Swagger, and Redoc
  - Proper icon integration
  - Respects user permissions
  
- **Dashboard Widget** (`plg_system_joomlalabs_webservicesdashboard`):
  - Two dashboard cards for Help dashboard
  - API Documentation card with official docs link
  - API Explorer (Swagger) card for quick testing
  - Restricted to users with `core.admin` permission

#### Technical Features
- **Joomla 6.0+ Compatibility**: Full support for latest Joomla version
- **Modern Architecture**:
  - PSR-4 namespacing (`Joomla\Component\JoomlalabsWebservices`)
  - Service Provider pattern
  - Dispatcher architecture
  - WebAssetManager integration
  
- **Asset Management**:
  - Proper WebAssetManager registration
  - Local script hosting (no CDN dependencies)
  - Optimized script loading with dependencies
  - Version management for cache busting
  
- **Security**:
  - Secure output escaping in all views
  - Parameterized database queries
  - API token encryption
  - Permission checks
  
- **Responsive Design**:
  - Mobile-friendly interface
  - Adaptive layouts for all screen sizes
  - Touch-optimized controls
  
- **Developer Tools**:
  - CLI script for spec generation
  - Debug mode for development
  - Comprehensive error handling
  - Extensible architecture

### Documentation
- Comprehensive README.md with:
  - Features overview
  - Installation instructions
  - Configuration guide
  - Usage examples
  - Troubleshooting section
  
- INSTALLATION.md with:
  - Step-by-step installation guide
  - Component and module setup
  - API token configuration
  - Verification steps
  
- SWAGGER_UI_IMPLEMENTATION.md with:
  - Technical architecture details
  - Hierarchical tags implementation
  - Filter extraction logic
  - Custom route parsing
  
- CONTRIBUTING.md with:
  - Contribution guidelines
  - Coding standards
  - Testing requirements
  - Pull request process
  
- SUPPORT.md with:
  - Help resources
  - Common issues and solutions
  - Contact information
  - Troubleshooting guide

### Known Limitations
- **Media Component**: Automatic endpoint discovery doesn't fully support Media component routes
- **Manual Spec Maintenance**: Static YAML spec requires manual updates when Joomla adds new endpoints
- **Component-Specific Routes**: Some components with complex routing may need custom parsing logic

## Release Notes

### Highlights

This is the **initial release** of the Web Services Documentation system for Joomla. It provides a complete solution for:

✅ **Interactive API Documentation** with Swagger UI and Redoc
✅ **Automatic OpenAPI Spec Generation** from installed components
✅ **Hierarchical Navigation** with intelligent tag organization
✅ **Dark Mode Support** synchronized with Joomla admin theme
✅ **Seamless Integration** with Joomla 6.0+ admin interface
✅ **Security-First Design** with proper authentication and permissions

### Requirements

- **Joomla**: 6.0.0 or higher
- **PHP**: 8.1 or higher
- **Browser**: Modern browser (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)
- **Joomla Settings**:
  - Web Services must be enabled (System → Global Configuration → API Settings)
  - User must have API token configured

### Installation

1. Download the extension packages from Github and create the zip files
2. Install via Joomla Administrator: **System → Extensions → Install**
3. Enable the module and plugin from Extensions Manager
4. Access via **Help Menu** or direct URLs:
   - Documentation: `index.php?option=com_joomlalabs_webservices&view=documentation`
   - Swagger: `index.php?option=com_joomlalabs_webservices&view=swagger`
   - Redoc: `index.php?option=com_joomlalabs_webservices&view=redoc`

### Breaking Changes

N/A - Initial release

### Migration Guide

N/A - Initial release

---

## Links

- [Repository](https://github.com/JoomlaLABS/webservices-documentation)
- [Issues](https://github.com/JoomlaLABS/webservices-documentation/issues)
- [Discussions](https://github.com/JoomlaLABS/webservices-documentation/discussions)
- [Releases](https://github.com/JoomlaLABS/webservices-documentation/releases)

---

**Made with ❤️ for the Joomla! Community**
