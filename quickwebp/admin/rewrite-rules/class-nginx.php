<?php

include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-rewrite-rules-abstract.php';

class Nginx extends Rewrite_Rules_Abstract {

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
        $extensions = 'jpg|jpeg|jpe|png';
		$home_root  = wp_parse_url( home_url( '/' ) );
		$home_root  = $home_root['path'];

		return trim( '
# BEGIN ' . $this->tag_name . '
location ~* ^(' . $home_root . '.+)\.(' . $extensions . ')$ {
	add_header Vary Accept;

	if ($http_accept ~* "webp"){
		set $imwebp A;
	}
	if (-f $request_filename.webp) {
		set $imwebp  "${imwebp}B";
	}
	if ($imwebp = AB) {
		rewrite ^(.*) $1.webp;
	}
}
# END ' . $this->tag_name . '');
	}


}