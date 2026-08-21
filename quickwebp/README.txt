=== QuickWebP - WebP & AVIF Image Optimizer, Compression & SEO for WordPress ===
Contributors: ludwigyou
Tags: webp, avif, image optimization, seo, performance
Requires at least: 6.8.2
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 4.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt

QuickWebP is a WordPress image optimizer that converts JPG and PNG to WebP, compresses uploads locally, improves image SEO, and unlocks AVIF with QuickWebP Pro.

== Description ==

QuickWebP helps you optimize images in WordPress as soon as they are uploaded. It converts JPG and PNG files to WebP, compresses them locally without any external API, cleans filenames for better image SEO, pre-fills media metadata, and resizes oversized uploads automatically.

If you want even smaller next-generation images, QuickWebP Pro adds AVIF conversion for supported servers. It is designed for faster pages, better Core Web Vitals, and a simpler image workflow inside the WordPress media library.

== QuickWebP Free vs Pro ==

= QuickWebP Free includes =

* Automatic WebP conversion on upload
* Local image compression without any external API
* SEO-friendly filename cleanup
* Automatic image metadata pre-fill for alt text, caption, description, legend, and title
* Automatic resize with maximum width and height controls
* Uses the native WordPress image editor with the best available server backend
* Bulk optimization for older media when originals are preserved
* Clipboard paste support inside the WordPress media frame
* Optimization preview inside the settings screen

= ⭐️ QuickWebP Pro includes ⭐️ =

* Everything included in QuickWebP Free
* AVIF conversion for supported servers with PHP 8.1+
* Smaller files than JPG, PNG, and typically smaller than WebP

🚀 Unlock AVIF conversion and more with QuickWebP Pro: [Upgrade now](https://solutions.leyoweb.com/products/quickwebp/?utm_source=quickwebp-plugin&utm_medium=documentation&utm_campaign=upgrade-pro&utm_content=readme-call-to-action)

== Important ==

QuickWebP is also available inside [WPMasterToolKit](https://wordpress.org/plugins/wpmastertoolkit/). If you prefer an all-in-one toolkit, install WPMasterToolKit and enable the Media Encoder module.

If you want the dedicated QuickWebP experience and access to AVIF, discover [QuickWebP Pro](https://solutions.leyoweb.com/products/quickwebp/?utm_source=quickwebp-plugin&utm_medium=documentation&utm_campaign=upgrade-pro&utm_content=readme-pro-section).

== Why site owners use QuickWebP ==

* Improve WordPress performance with lighter images
* Reduce page weight to help Core Web Vitals
* Keep image SEO cleaner with optimized filenames and metadata
* Convert and compress images directly on your server
* Manage image optimization from one simple settings page

== Installation ==

1. Install the plugin through the WordPress plugins screen.
2. Activate the plugin through the Plugins screen in WordPress.
3. Go to Media > QuickWebP and configure your optimization settings.
4. If you want AVIF, purchase QuickWebP Pro and activate your license from the QuickWebP License screen.

== Demos ==

**How to install QuickWebP**
[youtube https://www.youtube.com/watch?v=5Ja2engS5YA&rel=0]

**Paste a picture from the clipboard into WordPress media**
[youtube https://www.youtube.com/watch?v=N5Yc-D8Hhyw]

== Frequently Asked Questions ==

= What is included in the free version? =
The free version includes automatic WebP conversion, local compression, resize controls, filename cleanup for SEO, metadata pre-fill, bulk optimization, and clipboard paste support in the media library.

= What does QuickWebP Pro add? =
QuickWebP Pro adds AVIF conversion on supported servers with PHP 8.1+, stronger compression, and automatic fallback so older browsers still receive a compatible image format.

= Where can I get QuickWebP Pro? =
You can get it here: [QuickWebP Pro](https://solutions.leyoweb.com/products/quickwebp/?utm_source=quickwebp-plugin&utm_medium=documentation&utm_campaign=upgrade-pro&utm_content=readme-upgrade-link).

= Is QuickWebP still available in WPMasterToolKit? =
Yes. QuickWebP is also available inside [WPMasterToolKit](https://wordpress.org/plugins/wpmastertoolkit/) through the Media Encoder module.

= Does QuickWebP use an API such as TinyPNG or Imagify or ShortPixel? =
No. QuickWebP processes images locally on your server. No external compression API is required.

= What image formats are supported? =
QuickWebP converts JPG and PNG uploads to WebP. QuickWebP Pro also adds AVIF conversion on supported servers.

= Can I resize my images automatically? =
Yes. You can define maximum width and height values in the Resize settings.

= Does QuickWebP improve SEO? =
Yes. QuickWebP cleans image filenames and uses them to pre-fill attachment metadata such as alt text, caption, description, legend, and title. Lighter images can also improve page speed signals.

= Does QuickWebP work with caching plugins, CDNs, themes, page builders, and WooCommerce? =
Yes. QuickWebP is designed to work with standard WordPress setups, including caching plugins, CDNs, most themes, major page builders, and WooCommerce.

= Can I paste images directly into the media library? =
Yes. You can paste images from tools such as Photoshop, Illustrator, GIMP, Affinity, Finder, screenshots, and webpages directly into the WordPress media frame.

== Screenshots ==

1. QuickWebP WebP optimization results compared with the original image
2. QuickWebP settings page and an explanation of the available options

== More from Webdeclic ==

QuickWebP is developed by Webdeclic. If you want a broader WordPress toolbox, also check [Webdeclic plugins](https://wordpress.org/plugins/search/webdeclic/).

== Support ==

If you like QuickWebP, please leave a 5-star review on WordPress.org.

If you want to support the project, you can do it here: [Buy me a coffee](https://bmc.link/ludwig)

== Changelog ==

= 4.0.1 =
* Target blank on upgrade links to QuickWebP Pro in the settings page and license screen.
* Improved local image conversion compatibility by using the native WordPress image editor before falling back to direct GD handling.

= 4.0.0 =
* Added QuickWebP Pro positioning with the AVIF upgrade path.
* Reworked the README and plugin metadata to clarify Free vs Pro features.
* Updated the QuickWebP Pro product link.

= 3.3.1 =
* FIX: replace sanitize_filename() with sanitize_text_field() to preserve special characters in the filename for autocomplete seo features.


[See changelog for all versions.](https://plugins.svn.wordpress.org/quickwebp/trunk/changelog.txt)
