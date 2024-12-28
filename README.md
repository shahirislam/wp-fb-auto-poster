# WordPress Facebook Auto Poster

## Overview
The **WordPress Facebook Auto Poster** is a plugin that automatically shares your WordPress posts to your Facebook page. It simplifies social media sharing by posting your content directly to Facebook whenever you publish a new post.

## Features
- Automatically shares WordPress posts to a specified Facebook page.
- Uses the Facebook Graph API for seamless integration.
- Lightweight and easy to configure.
- Custom error logging for troubleshooting.

## Requirements
- WordPress 5.0 or higher
- PHP 7.2 or higher
- Facebook Page Access Token

## Installation
1. Download the plugin files and upload them to your WordPress installation.
   - Clone the repository: `git clone https://github.com/shahirislam/wp-fb-auto-poster.git`
   - Alternatively, download the ZIP file and upload it via the WordPress Admin Dashboard.
2. Activate the plugin in the WordPress Admin Dashboard under **Plugins** > **Installed Plugins**.
3. Navigate to the plugin settings page under **Settings** > **Facebook Auto Post**.
4. Enter your Facebook Page Access Token and Page ID, then save the settings.

## Configuration
1. Go to **Settings** > **Facebook Auto Post**.
2. Fill in the following details:
   - **Access Token**: Your Facebook Page Access Token (generate it from the [Facebook Graph API Explorer](https://developers.facebook.com/tools/explorer/)).
   - **Page ID**: Your Facebook Page ID.
3. Save the settings.

## Usage
Once activated and configured, the plugin automatically shares your posts to the specified Facebook page when you publish a new post.

## Customization
### Disable Auto Post for Specific Posts
To disable auto-posting for a specific post:
1. In the WordPress post editor, look for the **Facebook Auto Post** meta box.
2. Check the box labeled **Disable Facebook Auto Post**.
3. Publish or update the post.

## Error Handling
If the plugin fails to post to Facebook:
- Check the error log in the WordPress debug log (`wp-content/debug.log`).
- Ensure your Access Token and Page ID are correctly configured.
- Verify your server can connect to the Facebook Graph API.

## FAQ
### How do I generate a Facebook Page Access Token?
You can generate an Access Token using the [Facebook Graph API Explorer](https://developers.facebook.com/tools/explorer/). Ensure you have the required permissions for the page.

### Can I use this plugin to post to multiple pages?
Currently, the plugin supports posting to a single Facebook page. You can customize the code to handle multiple pages.

### Does this plugin support custom post types?
By default, the plugin works with standard WordPress posts. You can modify the `publish_post` hook to include custom post types.

## Contributing
Contributions are welcome! Feel free to fork the repository, make your changes, and submit a pull request.

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Author
Developed by **Shahir Islam**.

## Links
- [GitHub Repository](https://github.com/shahirislam/wp-fb-auto-poster/)
- [WordPress Documentation](https://wordpress.org/support/)
- [Facebook Graph API Documentation](https://developers.facebook.com/docs/graph-api/)
