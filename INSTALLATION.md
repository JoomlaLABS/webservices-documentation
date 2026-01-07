# Installation Instructions

> **For production use, download from the official repository:** [JoomlaLABS/webservices-documentation](https://github.com/JoomlaLABS/webservices-documentation/releases)

## Development Installation

This workspace is for development purposes. To install from this workspace:

1. **Enable Web Services** (if not already enabled):
   - Go to **System → Global Configuration → API Settings**
   - Set **Enable Joomla's Web Services** to **Yes**
   - Save

2. **Install the Component:**
   - Create ZIP: `com_joomlalabs_webservices.zip` from `com_joomlalabs_webservices/` folder
   - Go to **System → Extensions → Install**
   - Upload and install the ZIP

3. **Install the Module:**
   - Create ZIP: `mod_joomlalabs_webservices_helpmenu.zip` from `mod_joomlalabs_webservices_helpmenu/` folder
   - Go to **System → Extensions → Install**
   - Upload and install the ZIP
   - Go to **System → Extensions → Modules**
   - Find "Joomla!LABS Web Services Help Menu" and publish it

4. **Configure API Token:**
   - The system will automatically generate a token on first access to Swagger/Redoc
   - Or manually create one: **System → API Tokens → New**

## Symlink Installation (Development)

For active development, you can symlink the extensions:

### Component

**Windows (run as Administrator):**
```bash
cd C:\path\to\joomla\administrator\components
mklink /D com_joomlalabs_webservices C:\path\to\workspace\com_joomlalabs_webservices\admin
mklink /D ..\..\media\com_joomlalabs_webservices C:\path\to\workspace\com_joomlalabs_webservices\admin\media
```

**Linux/Mac:**
```bash
cd /path/to/joomla/administrator/components
ln -s /path/to/workspace/com_joomlalabs_webservices/admin com_joomlalabs_webservices
ln -s /path/to/workspace/com_joomlalabs_webservices/admin/media ../../media/com_joomlalabs_webservices
```

### Module

**Windows (run as Administrator):**
```bash
cd C:\path\to\joomla\administrator\modules
mklink /D mod_joomlalabs_webservices_helpmenu C:\path\to\workspace\mod_joomlalabs_webservices_helpmenu
```

**Linux/Mac:**
```bash
cd /path/to/joomla/administrator/modules
ln -s /path/to/workspace/mod_joomlalabs_webservices_helpmenu mod_joomlalabs_webservices_helpmenu
```

### Discovery Installation

Then use Discovery to install:
- **System → Extensions → Install → Discover**
- Click "Discover"
- Select the extensions and click "Install"

---

# Usage

## Via Help Menu (Recommended)

1. Click **Help** in the admin toolbar (top right)
2. Select one of the three options:
   - **API Documentation** - Getting started guide
   - **API Explorer (Swagger)** - Interactive testing interface
   - **API Reference (Redoc)** - Clean documentation view

## Direct URLs

Access views directly:
- Documentation: `administrator/index.php?option=com_joomlalabs_webservices&view=documentation`
- Swagger UI: `administrator/index.php?option=com_joomlalabs_webservices&view=swagger`
- Redoc: `administrator/index.php?option=com_joomlalabs_webservices&view=redoc`

## Testing APIs with Swagger UI

1. Open Swagger UI via Help menu
2. Select spec option (Static/Generated) from dropdown
3. Browse endpoints using hierarchical navigation
4. Click any endpoint to expand details
5. Click "Try it out" to test live
6. Authorization is automatic (token pre-filled)
7. Fill parameters and click "Execute"
8. View response with syntax highlighting

## Generating Custom Specs

### Web Interface

- Active plugins: `[site-url]/media/com_joomlalabs_webservices/generate-joomla-core-apis.php`
- All components: `[site-url]/media/com_joomlalabs_webservices/generate-joomla-core-apis.php?showAll=true`

### CLI

```bash
cd [joomla-root]/media/com_joomlalabs_webservices
php generate-joomla-core-apis.php         # Active plugins only
php generate-joomla-core-apis.php --all   # All components
```

1. Go to `System → Maintenance → Database`
2. Check for issues
3. Click "Fix" if any problems found

## Testing Checklist

After installation, verify:

- [ ] Plugin shows as "Enabled" in Plugin Manager
- [ ] Component appears in Components menu (should be "Web Services")
- [ ] Help Dashboard shows two new cards
- [ ] Clicking "API Documentation" opens iframe with docs
- [ ] Clicking "API Explorer" shows placeholder page
- [ ] No PHP errors in browser console
- [ ] No 404 errors

## Need Help?

If you encounter issues:

1. Check Joomla error logs: `administrator/logs/error.php`
2. Enable Joomla Debug mode: `System → Global Configuration → System → Debug System = Yes`
3. Check browser console for JavaScript errors
4. Open an issue in the GitHub repository with:
   - Joomla version
   - PHP version
   - Error message
   - Steps to reproduce

## Next Steps

After successful installation:

1. Explore the API Documentation view
2. Review the Swagger placeholder
3. Check the code structure for customization
4. Consider contributing to Swagger implementation
