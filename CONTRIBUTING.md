# Contributing to Web Services Documentation

Thank you for your interest in contributing to the Web Services Documentation project! 🎉

We welcome contributions from everyone and appreciate your help in making this project better.

## 🔄 How to Contribute

### Reporting Bugs

If you find a bug, please [open an issue](https://github.com/JoomlaLABS/webservices-documentation/issues/new?labels=bug&template=bug_report.md) with:

- A clear, descriptive title
- Detailed steps to reproduce the issue
- Expected behavior vs actual behavior
- Your environment (Joomla version, PHP version, browser)
- Screenshots if applicable
- Any error messages from browser console or Joomla logs

### Suggesting Enhancements

Have an idea for a new feature? [Open an enhancement issue](https://github.com/JoomlaLABS/webservices-documentation/issues/new?labels=enhancement&template=feature_request.md) with:

- A clear description of the feature
- Why this feature would be useful
- How it should work
- Examples or mockups if possible

### Pull Requests

1. **🍴 Fork** the repository
2. **🌿 Create** a feature branch from `main`:
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **✨ Make** your changes following our coding standards
4. **🧪 Test** your changes thoroughly
5. **💾 Commit** your changes with clear messages:
   ```bash
   git commit -m 'Add amazing feature: description'
   ```
6. **🚀 Push** to your branch:
   ```bash
   git push origin feature/amazing-feature
   ```
7. **📮 Submit** a pull request to the `main` branch

## 📋 Coding Standards

### PHP Code

- Follow **PSR-12** coding standards
- Use **PSR-4** autoloading
- Follow Joomla coding standards where applicable
- Write clear, self-documenting code
- Add inline comments for complex logic
- Use type hints for method parameters and return types

### JavaScript Code

- Follow modern ES6+ standards
- Use meaningful variable and function names
- Add JSDoc comments for functions
- Ensure browser compatibility
- Minimize inline scripts, prefer external files

### Documentation

- Use clear, concise language
- Update README.md if adding features
- Add inline code comments
- Update CHANGELOG.md following Keep a Changelog format
- Document configuration options
- Provide usage examples

## 🧪 Testing

Before submitting a pull request:

1. **Test thoroughly** on a clean Joomla installation
2. **Verify** all features work as expected
3. **Check** browser console for JavaScript errors
4. **Test** on multiple browsers if possible
5. **Validate** that no existing features are broken
6. **Test** with different Joomla configurations (multilanguage, different templates)

## 📝 Commit Message Guidelines

Write clear commit messages that explain what and why:

```
Add feature: brief description

- Detailed point 1
- Detailed point 2
- Why this change was needed
```

Good examples:
```
Fix token generation for special characters in username

- Escape special characters in SQL query
- Add validation for token format
- Prevents SQL injection vulnerability
```

```
Add dark mode support for Redoc interface

- Integrate redark.js library
- Add theme detection and switching
- Synchronize with Joomla admin theme
```

## 🏗️ Project Structure

Understanding the project structure helps you navigate and contribute effectively.

### Component Structure
```
com_joomlalabs_webservices/
├── admin/
│   ├── services/
│   │   └── provider.php                          # Service Provider (DI)
│   ├── src/
│   │   ├── Extension/
│   │   │   └── JoomlalabsWebservicesComponent.php # Component bootstrap
│   │   ├── Controller/
│   │   │   └── DisplayController.php             # Main controller
│   │   └── View/
│   │       ├── Documentation/
│   │       │   └── HtmlView.php                  # Documentation view
│   │       ├── Swagger/
│   │       │   └── HtmlView.php                  # Swagger UI integration
│   │       └── Redoc/
│   │           └── HtmlView.php                  # Redoc integration
│   ├── tmpl/
│   │   ├── documentation/
│   │   │   └── default.php                       # Documentation template
│   │   ├── swagger/
│   │   │   └── default.php                       # Swagger UI template
│   │   └── redoc/
│   │       └── default.php                       # Redoc template
│   ├── media/
│   │   ├── js/
│   │   │   ├── swagger-ui-bundle.js              # Swagger UI core
│   │   │   ├── swagger-ui-standalone-preset.js   # Swagger UI preset
│   │   │   ├── hierarchical-tags.js              # Custom navigation plugin
│   │   │   └── redoc.standalone.js               # Redoc standalone
│   │   ├── css/
│   │   │   └── swagger-ui.css                    # Swagger UI styles
│   │   ├── generate-joomla-core-apis.php         # Dynamic spec generator
│   │   └── joomla-core-apis.yaml                 # Static OpenAPI spec
│   └── language/
│       └── en-GB/
│           ├── com_joomlalabs_webservices.ini    # Frontend strings
│           └── com_joomlalabs_webservices.sys.ini # Backend strings
└── joomlalabs_webservices.xml                    # Manifest file
```

### Module Structure
```
mod_joomlalabs_webservices_helpmenu/
├── services/
│   └── provider.php                              # Service Provider (DI)
├── src/
│   ├── Dispatcher/
│   │   └── Dispatcher.php                        # Module dispatcher
│   └── Helper/
│       └── WebservicesHelpmenuHelper.php         # Help menu integration
├── tmpl/
│   └── default.php                               # Module template
├── language/
│   └── en-GB/
│       ├── mod_joomlalabs_webservices_helpmenu.ini    # Frontend strings
│       └── mod_joomlalabs_webservices_helpmenu.sys.ini # Backend strings
└── mod_joomlalabs_webservices_helpmenu.xml       # Manifest file
```

### Key Files Explained

**Component:**
- `provider.php` - Registers component services in Joomla DI container
- `JoomlalabsWebservicesComponent.php` - Component bootstrap and registration
- `DisplayController.php` - Routes requests to appropriate views
- `HtmlView.php` files - Prepare data and assets for each view
- `default.php` templates - Render HTML output for each view
- `generate-joomla-core-apis.php` - CLI/web script for OpenAPI generation
- `hierarchical-tags.js` - Custom Swagger UI plugin for tree navigation

**Module:**
- `provider.php` - Registers module services
- `Dispatcher.php` - Handles module rendering lifecycle
- `WebservicesHelpmenuHelper.php` - Builds help menu button array
- `default.php` - Renders module output (usually empty for help menu)

## 🔐 Security

- **Never** commit sensitive data (API keys, passwords, configuration.php)
- Use **parameterized queries** for all database operations
- **Escape output** using appropriate methods
- **Validate** all user input
- Follow Joomla security best practices
- Report security issues privately to info@joomlalabs.com

## 📄 License

By contributing, you agree that your contributions will be licensed under the **GNU General Public License v2.0+**.

## 📋 Documentation

For complete documentation, see the official repository:
- **[README.md](https://github.com/JoomlaLABS/webservices-documentation/blob/main/README.md)** - Full project documentation
- **[INSTALLATION.md](https://github.com/JoomlaLABS/webservices-documentation/blob/main/INSTALLATION.md)** - Installation guide
- **[CHANGELOG.md](https://github.com/JoomlaLABS/webservices-documentation/blob/main/CHANGELOG.md)** - Version history
- **[CONTRIBUTING.md](https://github.com/JoomlaLABS/webservices-documentation/blob/main/CONTRIBUTING.md)** - Contribution guidelines

## 💬 Questions?

- 💡 **Need help?** [Start a discussion](https://github.com/JoomlaLABS/webservices-documentation/discussions)
- 📧 **Private inquiry?** Contact us at info@joomlalabs.com

## 🙏 Thank You!

Your contributions help make this project better for the entire Joomla! community.

## 👥 Project Information

### 🏢 Project Owner

**Joomla!LABS** - [https://joomlalabs.com](https://joomlalabs.com)

[![Email](https://img.shields.io/badge/Email-info%40joomlalabs.com-red?style=for-the-badge&logo=gmail&logoColor=white)](mailto:info@joomlalabs.com)

*Joomla!LABS is the company that owns and maintains this project.*

### 👨‍💻 Contributors

**Luca Racchetti** - Lead Developer

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Luca%20Racchetti-blue?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/razzo/)
[![GitHub](https://img.shields.io/badge/GitHub-Razzo1987-black?style=for-the-badge&logo=github&logoColor=white)](https://github.com/Razzo1987)

*Full-Stack Developer passionate about creating modern, efficient web applications and tools for the Joomla! community*

---

**Made with ❤️ for the Joomla! Community**
