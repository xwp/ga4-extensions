# Google Analytics 4 Extensions

> Extends Google Analytics 4 by injecting custom data into the `window.dataLayer` for enhanced GA4 analytics.

## Features

- **User Properties:** Outputs `user_properties` with `is_subscriber` (0 or 1) on all pages.
- **Post Data:** On single post pages, outputs `post_author`, `post_category`, and `post_tags` to the `window.dataLayer`.
- **Early Script Injection:** Injects the `dataLayer.push` script early in the `<head>` section to ensure Google Site Kit GA4 captures the data.

## Usage

Once activated, the plugin automatically injects the necessary `dataLayer.push` scripts into your site's `<head>`. On all pages, it adds the `user_properties` field, and on single post pages, it additionally adds `post_author`, `post_category`, and `post_tags`.

Example code output:

```html
<script id="ga4-ext-data-layer-js-before">
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({"user_properties":{"is_subscriber":0},"post_author":"dev","post_category":"fashion travel","post_tags":"featured"});
</script>
```

## Configuration

If you don't already have GA4 tracking, you can enable it by following these steps:

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
