<?php

include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-rewrite-rules-abstract.php';

class Apache extends Rewrite_Rules_Abstract {

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
		$extensions = 'jpg|jpeg|jpe|png';
		$home_root  = wp_parse_url( home_url( '/' ) );
		$home_root  = $home_root['path'];

		return trim( '
# BEGIN ' . $this->tag_name . '
<IfModule mod_setenvif.c>
	# Vary: Accept for all the requests to jpeg and png.
	SetEnvIf Request_URI "\.(' . $extensions . ')$" REQUEST_image
</IfModule>

<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase ' . $home_root . '

	# Check if browser supports WebP images.
	RewriteCond %{HTTP_ACCEPT} image/webp

	# Check if WebP replacement image exists.
	RewriteCond %{REQUEST_FILENAME}.webp -f

	# Serve WebP image instead.
	RewriteRule (.+)\.(' . $extensions . ')$ $1.$2.webp [T=image/webp,NC]
</IfModule>

<IfModule mod_headers.c>
	Header append Vary Accept env=REQUEST_image
</IfModule>

<IfModule mod_mime.c>
	AddType image/webp .webp
</IfModule>
# END ' . $this->tag_name .'' );
	}

}