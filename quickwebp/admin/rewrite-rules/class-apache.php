<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-rewrite-rules-abstract.php';

class Quickwebp_Apache extends Quickwebp_Rewrite_Rules_Abstract {

    /**
	 * Get the path to the file.
	 */
	protected function get_file_path() {
		$file_path = $this->get_site_root() . '.htaccess';

		return $file_path;
	}

    /**
	 * Get unfiltered new contents to write into the file.
	 */
	protected function get_raw_new_contents() {
		$home_root = wp_parse_url( home_url( '/' ) );
		$home_root = $home_root['path'];

		$content = '# BEGIN ' . $this->tag_name . "\n";
		$content .= "<IfModule mod_setenvif.c>";
		$content .= "\n\tSetEnvIf Request_URI \"\\.(jpg|jpeg|jpe|png)$\" REQUEST_image";
		$content .= "\n</IfModule>";

		$content .= "\n<IfModule mod_rewrite.c>";
		$content .= "\n\tRewriteEngine On";
		$content .= "\n\tRewriteBase $home_root";

		// Serve AVIF if browser supports it and file exists
		$content .= "\n\tRewriteCond %{HTTP_ACCEPT} image/avif";
		$content .= "\n\tRewriteCond %{REQUEST_FILENAME}.avif -f";
		$content .= "\n\tRewriteRule (.+)\\.(jpg|jpeg|jpe|png)$ $1.$2.avif [T=image/avif,NC,E=REQUEST_image:avif,L]";
	
		// Otherwise, serve WebP if browser supports it and file exists
		$content .= "\n\tRewriteCond %{HTTP_ACCEPT} image/webp";
		$content .= "\n\tRewriteCond %{REQUEST_FILENAME}.webp -f";
		$content .= "\n\tRewriteRule (.+)\\.(jpg|jpeg|jpe|png)$ $1.$2.webp [T=image/webp,NC,E=REQUEST_image:webp,L]";

		$content .= "\n</IfModule>";

		$content .= "\n<IfModule mod_headers.c>";
		$content .= "\n\tHeader append Vary Accept env=REQUEST_image";
		$content .= "\n</IfModule>";

		$content .= "\n<IfModule mod_mime.c>";
		$content .= "\n\tAddType image/webp .webp";
		$content .= "\n\tAddType image/avif .avif";
		$content .= "\n</IfModule>";
		$content .= "\n# END " . $this->tag_name;

        return trim( $content );
	}
}
