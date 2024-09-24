<?php
abstract class Rewrite_Rules_Abstract {

    /**
	 * Name of the tag used as block delemiter.
	 */
    protected $tag_name = 'Quickwebp: rewrite rules for webp';

    /**
	 * Add new contents to the file.
	 */
	public function add() {
		$result = $this->insert_contents( $this->get_raw_new_contents() );

		if ( is_wp_error( $result ) ) {
			add_action( 'admin_notices', function() use ( $result ) {
				?>
					<div class="notice notice-error">
						<p><?php echo $result->get_error_message(); ?></p>
					</div>
				<?php
			});
		}

        return true;
	}

    /**
     * Remove contents from the file.
     */
    public function remove() {

        $result = $this->insert_contents( '' );

		if ( is_wp_error( $result ) ) {
			add_action( 'admin_notices', function() use ( $result ) {
				?>
					<div class="notice notice-error">
						<p><?php echo $result->get_error_message(); ?></p>
					</div>
				<?php
			});
		}

        return true;
    }

    /**
     * Insert new contents into the directory conf file.
	 * Replaces existing marked info. Creates file if none exists.
     */
    protected function insert_contents( $new_contents ) {
		$contents = $this->get_file_contents();

		if ( is_wp_error( $contents ) ) {
			return $contents;
		}

		$start_marker = '# BEGIN ' . $this->tag_name;
		$end_marker   = '# END ' . $this->tag_name;

		// Remove previous rules.
		$contents = preg_replace( '/\s*?' . preg_quote( $start_marker, '/' ) . '.*' . preg_quote( $end_marker, '/' ) . '\s*?/isU', "\n\n", $contents );
		$contents = trim( $contents );

		if ( $new_contents ) {
			$contents = $new_contents . "\n\n" . $contents;
		}

		return $this->put_file_contents( $contents );
	}

    /**
     * Get the path to the site's root.
	 * This is an improved version of get_home_path() that *should* work in almost every cases.
	 * Because creating a constant like ABSPATH was too simple.
     */
    protected function get_site_root() {

		$home    = set_url_scheme( untrailingslashit( get_option( 'home' ) ), 'http' );
		$siteurl = set_url_scheme( untrailingslashit( get_option( 'siteurl' ) ), 'http' );

		if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			$pos                 = strripos( str_replace( '\\', '/', ABSPATH ), trailingslashit( $wp_path_rel_to_home ) );
			$root_path           = substr( ABSPATH, 0, $pos );
			$root_path           = trailingslashit( wp_normalize_path( $root_path ) );
			return $root_path;
		}

		if ( ! defined( 'PATH_CURRENT_SITE' ) || ! is_multisite() || is_main_site() ) {
			$root_path = ABSPATH;
			return $root_path;
		}

		/**
		 * For a multisite in its own directory, get_home_path() returns the expected path only for the main site.
		 *
		 * Friend, each time an attempt is made to improve this method, and especially this part, please increment the following counter.
		 * Improvement attempts: 3.
		 */
		$document_root     = realpath( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ); // `realpath()` is needed for those cases where $_SERVER['DOCUMENT_ROOT'] is totally different from ABSPATH.
		$document_root     = trailingslashit( str_replace( '\\', '/', $document_root ) );
		$path_current_site = trim( str_replace( '\\', '/', PATH_CURRENT_SITE ), '/' );
		$root_path         = trailingslashit( wp_normalize_path( $document_root . $path_current_site ) );

		return $root_path;
	}

    /**
	 * Get the file contents.
	 */
	protected function get_file_contents() {

        $file_path  	= $this->get_file_path();
		$file_exists	= file_exists( $file_path );
		if ( ! $file_exists ) {
			$dir_name = dirname( $file_path );
			if ( ! file_exists( $dir_name ) ){
				mkdir( $dir_name, 0755, true );
			}
			touch( $file_path );
		}
		$writable   	= wp_is_writable( $file_path );

		if ( is_wp_error( $writable ) ) {
			return $writable;
		}

		$contents = @file_get_contents( $file_path );

		if ( false === $contents ) {
			return new \WP_Error(
				'not_read',
				sprintf(
					__( 'The %s file could not be read.', QUICKWEBP_TEXT_DOMAIN ),
					'<code>' . esc_html( $file_path ) . '</code>'
				)
			);
		}

		return $contents;
	}

    /**
     * Write contents to the file.
     */
    protected function put_file_contents( $contents ) {

        $file_path  = $this->get_file_path();
        $result     = $this->put_contents( $file_path, $contents );

        if ( $result ) {
			return true;
		}

        return new \WP_Error(
			'edition_failed',
			sprintf(
				__( 'Could not write into the %s file.', QUICKWEBP_TEXT_DOMAIN ),
				'<code>' . esc_html( $file_path ) . '</code>'
			)
		);
    }

    /**
     * Write contents to the file.
     */
    protected function put_contents( $file, $contents, $mode = false ) {

        $fp = @fopen( $file, 'wb' );

		if ( ! $fp ) {
			return false;
		}

		mbstring_binary_safe_encoding();

		$data_length = strlen( $contents );

		$bytes_written = fwrite( $fp, $contents );

		reset_mbstring_encoding();

		fclose( $fp );

		if ( $data_length !== $bytes_written ) {
			return false;
		}

        $chmod_file = fileperms( ABSPATH . 'index.php' ) & 0777 | 0644;
        chmod( $file, $chmod_file );

        return true;
    }

}