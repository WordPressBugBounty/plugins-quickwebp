<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-rewrite-rules-abstract.php';

class Quickwebp_Nginx extends Quickwebp_Rewrite_Rules_Abstract {

	/**
	 * Get the path to the file.
	 */
	protected function get_file_path() {
		$file_path = $this->get_site_root() . 'conf/quickwebp.conf';

		return $file_path;
	}

    /**
	 * Get unfiltered new contents to write into the file.
	 *
	 * @since  1.9
	 * @access protected
	 * @author Grégory Viguier
	 *
	 * @return string
	 */
	protected function get_raw_new_contents() {
		$home_root = wp_parse_url( home_url( '/' ) );
		$home_root = $home_root['path'];

		$content  = "# BEGIN " . $this->tag_name . "\n";
		$content .= "location ~* ^($home_root.+)\\.(jpg|jpeg|jpe|png)$ {";
		$content .= "\n\tadd_header Vary Accept;";
	
		// Check for AVIF support and file existence
		$content .= "\n\n\tif (\$http_accept ~* \"avif\") {";
		$content .= "\n\t\tset \$imavif A;";
		$content .= "\n\t}";
		$content .= "\n\tif (-f \$request_filename.avif) {";
		$content .= "\n\t\tset \$imavif \"\${imavif}B\";";
		$content .= "\n\t}";
		$content .= "\n\tif (\$imavif = AB) {";
		$content .= "\n\t\trewrite ^(.*) \$1.avif break;";
		$content .= "\n\t}";
	
		// Check for WebP support and file existence
		$content .= "\n\n\tif (\$http_accept ~* \"webp\") {";
		$content .= "\n\t\tset \$imwebp A;";
		$content .= "\n\t}";
		$content .= "\n\tif (-f \$request_filename.webp) {";
		$content .= "\n\t\tset \$imwebp \"\${imwebp}B\";";
		$content .= "\n\t}";
		$content .= "\n\tif (\$imwebp = AB) {";
		$content .= "\n\t\trewrite ^(.*) \$1.webp break;";
		$content .= "\n\t}";
	
		$content .= "\n}";
		$content .= "\n# END " . $this->tag_name;

        return trim( $content );
	}
}
