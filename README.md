# Google Analytics 4 Extensions

> Extends Google Analytics 4 by including custom data for enhanced GA4 analytics.

## Features

- **User Properties:** Sets `user_properties` with `is_subscriber` (0 or 1) on all pages.
- **Post Data:** On single post pages, outputs `post_author`, `post_category`, and `post_tags`.

## Usage

Example code output:

```html
<script id="ga4-ext-gtagjs-js-before">
    window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag("set", "linker", {
        "domains": ["example.com"]
    });
    gtag("js", new Date());
    gtag("config", "G-1234", {
        "post_author": "dev",
        "post_category": "news",
        "post_tags": "featured"
    });
    gtag("set", "user_properties", {
        is_subscriber: 0
    });
</script>
<script src="https://www.googletagmanager.com/gtag/js?id=G-1234" id="ga4-ext-gtagjs-js" defer data-wp-strategy="defer"></script>
```

## Installation

### Using Composer

To install the plugin via Composer, follow these steps:

1. **Add the Repository:**
   - Open your project's `composer.json` file.
   - Add the following under the `repositories` section:

     ```json
     "repositories": [
         {
             "type": "vcs",
             "url": "https://github.com/xwp/ga4-extensions"
         }
     ]
     ```

2. **Require the Plugin:**
   - Run the following command in your terminal:

     ```bash
     composer require xwp/ga4-extensions
     ```

3. **Activate the Plugin:**
   - Once installed, activate the plugin through the 'Plugins' menu in WordPress.

### Manual Installation

1. **Download the Plugin:**
   - Download the `ga4-extensions` plugin folder.

2. **Upload the Plugin:**
   - Add the `ga4-extensions` folder to the `/wp-content/plugins/` directory of your WordPress installation.

3. **Activate the Plugin:**
   - Activate the plugin through the 'Plugins' menu in WordPress.

## Configuration

To enable GA4 tracking, follow these steps:

- In your WordPress dashboard, go to **Settings** > **General**.
- Input your GA4 ID (e.g., `G-1234567`) into the **Google Analytics 4 ID** field.
- Click **Save Changes**.

## Google Analytics 4 - Configure Custom Dimensions

To use the custom data in Google Analytics 4, you need to create custom events and parameters in the GA4 dashboard. Here's how you can set it up:

1. Navigate to [Google Analytics](https://analytics.google.com/analytics/web/) and sign in with your account credentials with an Administrative role.
2. Select the property you want to track.
3. Access the Admin Panel - Click the Admin gear icon located in the bottom-left corner of the interface.
4. In the *Data display* section, click on `Custom definitions`.
5. For each custom dimension (post_author, post_category, post_tags), follow these steps:
  a. Click Create Custom Dimension.
  b. Dimension Name: Post XXX (e.g., Post Author)
  c. Scope: Event
  d. Event Parameter: post_author
  e. Click Save.

## Requirements

- **WordPress:** Version 6.5 or higher.
- **PHP:** Version 8.1 or higher.

## License

This plugin is licensed under the GPLv3 or later.
