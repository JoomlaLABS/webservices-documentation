# Support

Need help with Web Services Documentation? We're here for you! 🆘

## 📚 Documentation

Before asking for help, please check:

- **[README](README.md)** - Project overview and basic usage
- **[INSTALLATION](INSTALLATION.md)** - Installation and setup instructions
- **[CHANGELOG](CHANGELOG.md)** - Version history and changes

## 💬 Getting Help

### 🐛 Found a Bug?

If you've found a bug, please help us fix it:

1. Check if it's already reported in [Issues](https://github.com/JoomlaLABS/webservices-documentation/issues)
2. If not, [open a new bug report](https://github.com/JoomlaLABS/webservices-documentation/issues/new?labels=bug&template=bug_report.md)
3. Include:
   - Clear description of the problem
   - Steps to reproduce
   - Expected vs actual behavior
   - Your environment (Joomla version, PHP version, browser)
   - Screenshots or error messages
   - Browser console errors (F12 → Console)

### 💡 Feature Request?

Have an idea for a new feature?

1. Check [existing feature requests](https://github.com/JoomlaLABS/webservices-documentation/issues?q=is%3Aissue+label%3Aenhancement)
2. If it's new, [submit a feature request](https://github.com/JoomlaLABS/webservices-documentation/issues/new?labels=enhancement&template=feature_request.md)
3. Describe:
   - What you want to achieve
   - Why it would be useful
   - How it might work
   - Any examples or mockups

### ❓ General Questions?

For general questions, usage help, or discussions:

- [Start a discussion](https://github.com/JoomlaLABS/webservices-documentation/discussions)
- Ask in the appropriate category (Q&A, Ideas, General)

## 📧 Direct Contact

For private inquiries, security issues, or business matters:

- **Email**: info@joomlalabs.com
- **LinkedIn**: [Luca Racchetti](https://www.linkedin.com/in/razzo/)

## 🔍 Common Issues

### Swagger UI shows "Failed to load API definition"

**Possible causes**:
1. Web Services not enabled in Joomla
2. API token not configured
3. OpenAPI spec file not found
4. Incorrect permissions

**Solutions**:
- Check **System → Global Configuration → API Settings** - enable Web Services
- Verify you have a valid API token
- Check file permissions for `/media/com_joomlalabs_webservices/`
- Review browser console for specific errors

## 🛠️ Troubleshooting

### Enable Debug Mode

1. Go to **System → Global Configuration → System**
2. Set **Debug System** to **Yes**
3. Check errors in:
   - Browser console (F12)
   - Joomla error logs (`administrator/logs/`)
   - PHP error logs

### Check File Permissions

Ensure these directories are writable:
```
administrator/cache/
administrator/logs/
```

### Verify Requirements

- Joomla 6.0+
- PHP 8.1+
- Web Services enabled
- Modern browser (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)

## 📖 Additional Resources

### Joomla Documentation

- [Joomla API Documentation](https://docs.joomla.org/J4.x:Joomla_Core_APIs)
- [Web Services Guide](https://docs.joomla.org/J4.x:Joomla_Core_APIs)
- [API Specifications](https://api.joomla.org/)

### OpenAPI & Swagger

- [OpenAPI Specification](https://swagger.io/specification/)
- [Swagger UI Documentation](https://swagger.io/tools/swagger-ui/)
- [Redoc Documentation](https://redocly.com/docs/redoc/)

## 🤝 Contributing

If you'd like to help improve this project:

- Read [CONTRIBUTING.md](CONTRIBUTING.md)
- Check [open issues](https://github.com/JoomlaLABS/webservices-documentation/issues)
- Submit pull requests with fixes or improvements

## 💝 Support the Project

If this project helped you, consider supporting its development:

- ⭐ [Star the repository](https://github.com/JoomlaLABS/webservices-documentation)
- 💰 [Sponsor on GitHub](https://github.com/sponsors/JoomlaLABS)
- 🍺 [Buy me a beer](https://buymeacoffee.com/razzo)
- 💳 [Donate via PayPal](https://www.paypal.com/donate/?hosted_button_id=4SRPUJWYMG3GL)

## ⏱️ Response Time

- **Bug reports**: We aim to respond within 48-72 hours
- **Feature requests**: Reviewed regularly, implementation depends on priority
- **Security issues**: Immediate priority, contact us directly at info@joomlalabs.com
- **General questions**: Usually answered within 24-48 hours

---

**Made with ❤️ for the Joomla! Community**
