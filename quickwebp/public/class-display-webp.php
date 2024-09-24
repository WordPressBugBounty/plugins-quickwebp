<?php

/**
 * The handle the front-end display functionality of the plugin.
 *
 * @link       http://webdeclic.com
 * @since      1.0.0
 *
 * @package    Quickwebp
 * @subpackage Quickwebp/admin
 */
class Quickwebp_Display_Webp {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name			= $plugin_name;
		$this->version				= $version;

	}

    /**
     * Start buffering the page content
     */
    public function start_content_process() {

        $display_webp = get_option( 'quickwebp_settings_conversion_display_webp_mode', quickwebp_settings_default('quickwebp_settings_conversion_display_webp_mode') );

        if ( $display_webp != 'picture' ){
            return;
        }

        ob_start( array( $this, 'maybe_process_buffer' ) );        

    }

    /**
     * Maybe process the page content
     */
	public function maybe_process_buffer( $buffer ) {

        if ( ! $this->is_html( $buffer ) ) {
            return $buffer;
        }

        if ( strlen( $buffer ) <= 255 ) {
			// Buffer length must be > 255 (IE does not read pages under 255 c).
			return $buffer;
		}

        $buffer = $this->process_content( $buffer );

        return $buffer;
    }

    /**
     * Process the content
     */
    public function process_content( $content ) {

		$html_no_picture_tags   = $this->remove_picture_tags( $content );
        $images                 = $this->get_images( $html_no_picture_tags );

        if ( ! $images ) {
            return $content;
        }

        foreach ( $images as $image ) {
			$tag     = $this->build_picture_tag( $image );
			$content = str_replace( $image['tag'], $tag, $content );
		}

        return $content;
    }

    /**
     * Remove pre-existing <picture> tags.
     */
    private function remove_picture_tags( $html ) {

		$replace = preg_replace( '#<picture[^>]*>.*?<\/picture\s*>#mis', '', $html );

		if ( null !== $replace ) {
			return $html;
		}

		return $replace;
	}

    /**
     * Get a list of images in a content.
     */
    protected function get_images( $content ) {

		// Remove comments.
		$content = preg_replace( '/<!--(.*)-->/Uis', '', $content );

		if ( ! preg_match_all( '/<img\s.*>/isU', $content, $matches ) ) {
			return [];
		}

        $images = array_map( array( $this, 'process_image' ), $matches[0] );
        $images = array_filter( $images );

        if ( ! $images || ! is_array( $images ) ) {
			return [];
		}

		foreach ( $images as $i => $image ) {

            if ( empty( $image['src']['webp_exists'] ) || empty( $image['src']['webp_url'] ) ) {
				unset( $images[ $i ] );
				continue;
			}

			unset( $images[ $i ]['src']['webp_path'], $images[ $i ]['src']['webp_exists'] );

            if ( empty( $image['srcset'] ) || ! is_array( $image['srcset'] ) ) {
				unset( $images[ $i ]['srcset'] );
				continue;
			}

            foreach ( $image['srcset'] as $j => $srcset ) {

				if ( ! is_array( $srcset ) ) {
					continue;
				}

				if ( empty( $srcset['webp_exists'] ) || empty( $srcset['webp_url'] ) ) {
					unset( $images[ $i ]['srcset'][ $j ]['webp_url'] );
				}

				unset( $images[ $i ]['srcset'][ $j ]['webp_path'], $images[ $i ]['srcset'][ $j ]['webp_exists'] );
			}

        }

        return $images;
	}

    /**
     * Tell if a content is HTML
     */
    protected function is_html( $content ) {
		return preg_match( '/<\/html>/i', $content );
	}

    /**
     * Process an image tag and get an array containing some data.
     */
    protected function process_image( $image ) {

		$atts_pattern = '/(?<name>[^\s"\']+)\s*=\s*(["\'])\s*(?<value>.*?)\s*\2/s';

        if ( ! preg_match_all( $atts_pattern, $image, $tmp_attributes, PREG_SET_ORDER ) ) {
			// No attributes?
			return false;
		}

        $attributes = [];

        foreach ( $tmp_attributes as $attribute ) {
			$attributes[ $attribute['name'] ] = $attribute['value'];
		}

        if ( ! empty( $attributes['class'] ) && strpos( $attributes['class'], 'quickwebp-no-webp' ) !== false ) {
			// Has the 'quickwebp-no-webp' class.
			return false;
		}

        // Deal with the src attribute.
		$src_source = false;

		foreach ( [ 'data-lazy-src', 'data-src', 'src' ] as $src_attr ) {
			if ( ! empty( $attributes[ $src_attr ] ) ) {
				$src_source = $src_attr;
				break;
			}
		}

        if ( ! $src_source ) {
			// No src attribute.
			return false;
		}

        $extensions = 'jpg|jpeg|jpe|png';

        if ( ! preg_match( '@^(?<src>(?:(?:https?:)?//|/).+\.(?<extension>' . $extensions . '))(?<query>\?.*)?$@i', $attributes[ $src_source ], $src ) ) {
			// Not a supported image format.
			return false;
		}

		$webp_url  = $src['src'] . '.webp';
		$webp_path = $this->url_to_path( $webp_url );
        $webp_url .= ! empty( $src['query'] ) ? $src['query'] : '';

        $data = [
			'tag'              => $image,
			'attributes'       => $attributes,
			'src_attribute'    => $src_source,
			'src'              => [
				'url'         => $attributes[ $src_source ],
				'webp_url'    => $webp_url,
				'webp_path'   => $webp_path,
				'webp_exists' => $webp_path && @file_exists( $webp_path )
			],
			'srcset_attribute' => false,
			'srcset'           => []
		];

        // Deal with the srcset attribute.
		$srcset_source = false;

		foreach ( [ 'data-lazy-srcset', 'data-srcset', 'srcset' ] as $srcset_attr ) {
			if ( ! empty( $attributes[ $srcset_attr ] ) ) {
				$srcset_source = $srcset_attr;
				break;
			}
		}

        if ( $srcset_source ) {
			$data['srcset_attribute'] = $srcset_source;

			$srcset = explode( ',', $attributes[ $srcset_source ] );

            foreach ( $srcset as $srcs ) {
                $srcs = preg_split( '/\s+/', trim( $srcs ) );

                if ( count( $srcs ) > 2 ) {
					// Not a good idea to have space characters in file name.
					$descriptor = array_pop( $srcs );
					$srcs       = [ implode( ' ', $srcs ), $descriptor ];
				}

                if ( empty( $srcs[1] ) ) {
					$srcs[1] = '1x';
				}

                if ( ! preg_match( '@^(?<src>(?:https?:)?//.+\.(?<extension>' . $extensions . '))(?<query>\?.*)?$@i', $srcs[0], $src ) ) {
					// Not a supported image format.
					$data['srcset'][] = [
						'url'        => $srcs[0],
						'descriptor' => $srcs[1],
					];
					continue;
				}

                $webp_url  = $src['src'] . '.webp';
				$webp_path = $this->url_to_path( $webp_url );
				$webp_url .= ! empty( $src['query'] ) ? $src['query'] : '';

                $data['srcset'][] = [
					'url'         => $srcs[0],
					'descriptor'  => $srcs[1],
					'webp_url'    => $webp_url,
					'webp_path'   => $webp_path,
					'webp_exists' => $webp_path && @file_exists( $webp_path )
				];
            }
        }
        
        if ( ! $data || ! is_array( $data ) ) {
            return false;
        }

        if ( ! isset( $data['tag'], $data['attributes'], $data['src_attribute'], $data['src'], $data['srcset_attribute'], $data['srcset'] ) ) {
            return false;
        }

        return $data;
    }

    /**
     * Convert a file URL to an absolute path.
     */
    protected function url_to_path( $url ) {
        static $uploads_url;
		static $uploads_dir;
		static $root_url;
		static $root_dir;
		static $domain_url;

        if ( ! isset( $uploads_url ) ) {

            $uploads = wp_upload_dir();
            $uploads_url = false;
            $uploads_dir = false;

            if ( false === $uploads['error'] ) {
                $uploads_url = set_url_scheme( trailingslashit( $uploads['baseurl'] ) );
                $uploads_dir = wp_normalize_path( trailingslashit( $uploads['basedir'] ) );
            }

            $current_network = false;
            if ( function_exists( 'get_network' ) ) {
                $current_network = get_network();
            } elseif ( function_exists( 'get_current_site' ) ) {
                $current_network = get_current_site();
            }

            if ( ! is_multisite() || is_main_site() || ! $current_network ) {
                $root_url = home_url( '/' );
            } else {

                $root_url = is_ssl() ? 'https' : 'http';
                $root_url = set_url_scheme( 'http://' . $current_network->domain . $current_network->path, $root_url );
                $root_url = set_url_scheme( trailingslashit( $root_url ) );
            }

            $home    = set_url_scheme( untrailingslashit( get_option( 'home' ) ), 'http' );
		    $siteurl = set_url_scheme( untrailingslashit( get_option( 'siteurl' ) ), 'http' );

            if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {

                $wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
                $pos                 = strripos( str_replace( '\\', '/', ABSPATH ), trailingslashit( $wp_path_rel_to_home ) );
                $root_path           = substr( ABSPATH, 0, $pos );
                $root_dir            = trailingslashit( wp_normalize_path( $root_path ) );

            } elseif ( ! defined( 'PATH_CURRENT_SITE' ) || ! is_multisite() || is_main_site()) {

                $root_dir = trailingslashit( wp_normalize_path( ABSPATH ) );

            } else {

                $document_root     = realpath( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) );
                $document_root     = trailingslashit( str_replace( '\\', '/', $document_root ) );
                $path_current_site = trim( str_replace( '\\', '/', PATH_CURRENT_SITE ), '/' );
                $root_dir         = trailingslashit( wp_normalize_path( $document_root . $path_current_site ) );
            }

            $domain_url  = wp_parse_url( $root_url );

            if ( ! empty( $domain_url['scheme'] ) && ! empty( $domain_url['host'] ) ) {
				$domain_url = $domain_url['scheme'] . '://' . $domain_url['host'] . '/';
			} else {
				$domain_url = false;
			}

        }

        // Get the right URL format.
		if ( $domain_url && strpos( $url, '/' ) === 0 ) {
			// URL like `/path/to/image.jpg.webp`.
			$url = $domain_url . ltrim( $url, '/' );
		}

        $url = set_url_scheme( $url );

        // Return the path.
		if ( stripos( $url, $uploads_url ) === 0 ) {
			return str_ireplace( $uploads_url, $uploads_dir, $url );
		}

        if ( stripos( $url, $root_url ) === 0 ) {
			return str_ireplace( $root_url, $root_dir, $url );
		}

		return false;
    }

    /**
     * Build a <picture> tag to insert.
     */
    protected function build_picture_tag( $image ) {

        $to_remove = [
			'alt'              => '',
			'height'           => '',
			'width'            => '',
			'data-lazy-src'    => '',
			'data-src'         => '',
			'src'              => '',
			'data-lazy-srcset' => '',
			'data-srcset'      => '',
			'srcset'           => '',
			'data-lazy-sizes'  => '',
			'data-sizes'       => '',
			'sizes'            => '',
		];

        $attributes = array_diff_key( $image['attributes'], $to_remove );

        /**
		 * Remove Gutenberg specific attributes from picture tag, leave them on img tag.
		 * Optional: $attributes['class'] = 'imagify-webp-cover-wrapper'; for website admin styling ease.
		 */
		if ( ! empty( $image['attributes']['class'] ) && strpos( $image['attributes']['class'], 'wp-block-cover__image-background' ) !== false ) {
			unset( $attributes['style'] );
			unset( $attributes['class'] );
			unset( $attributes['data-object-fit'] );
			unset( $attributes['data-object-position'] );
		}

        $output = '<picture' . $this->build_attributes( $attributes ) . ">\n";
        $output .= $this->build_source_tag( $image );
		$output .= $this->build_img_tag( $image );
		$output .= "</picture>\n";

        return $output;
    }

    /**
     * Create HTML attributes from an array.
     */
    protected function build_attributes( $attributes ) {

		if ( ! $attributes || ! is_array( $attributes ) ) {
			return '';
		}

		$out = '';

		foreach ( $attributes as $attribute => $value ) {
			$out .= ' ' . $attribute . '="' . esc_attr( $value ) . '"';
		}

		return $out;
	}

    /**
     * Build the <source> tag to insert in the <picture>.
     */
    protected function build_source_tag( $image ) {

		$srcset_source = ! empty( $image['srcset_attribute'] ) ? $image['srcset_attribute'] : $image['src_attribute'] . 'set';
		$attributes    = [
			'type'         => 'image/webp',
			$srcset_source => [],
		];
        
		if ( ! empty( $image['srcset'] ) ) {
            foreach ( $image['srcset'] as $srcset ) {
                if ( empty( $srcset['webp_url'] ) ) {
                    continue;
				}
                
				$attributes[ $srcset_source ][] = $srcset['webp_url'] . ' ' . $srcset['descriptor'];
			}
		}

		if ( empty( $attributes[ $srcset_source ] ) ) {
			$attributes[ $srcset_source ][] = $image['src']['webp_url'];
		}

		$attributes[ $srcset_source ] = implode( ', ', $attributes[ $srcset_source ] );

		foreach ( [ 'data-lazy-srcset', 'data-srcset', 'srcset' ] as $srcset_attr ) {
			if ( ! empty( $image['attributes'][ $srcset_attr ] ) && $srcset_attr !== $srcset_source ) {
				$attributes[ $srcset_attr ] = $image['attributes'][ $srcset_attr ];
			}
		}

		if ( 'srcset' !== $srcset_source && empty( $attributes['srcset'] ) && ! empty( $image['attributes']['src'] ) ) {
			// Lazyload: the "src" attr should contain a placeholder (a data image or a blank.gif ).
			$attributes['srcset'] = $image['attributes']['src'];
		}

		foreach ( [ 'data-lazy-sizes', 'data-sizes', 'sizes' ] as $sizes_attr ) {
			if ( ! empty( $image['attributes'][ $sizes_attr ] ) ) {
				$attributes[ $sizes_attr ] = $image['attributes'][ $sizes_attr ];
			}
		}

		return '<source' . $this->build_attributes( $attributes ) . "/>\n";
	}

    /**
     * Build the <img> tag to insert in the <picture>.
     */
    protected function build_img_tag( $image ) {
		/**
		 * Gutenberg fix.
		 * Check for the 'wp-block-cover__image-background' class on the original image, and leave that class and style attributes if found.
		 */
		if ( ! empty( $image['attributes']['class'] ) && strpos( $image['attributes']['class'], 'wp-block-cover__image-background' ) !== false ) {
			$to_remove = [
				'id'     => '',
				'title'  => '',
			];

			$attributes = array_diff_key( $image['attributes'], $to_remove );
		} else {
			$to_remove = [
				'class'  => '',
				'id'     => '',
				'style'  => '',
				'title'  => '',
			];

			$attributes = array_diff_key( $image['attributes'], $to_remove );
		}

		return '<img' . $this->build_attributes( $attributes ) . "/>\n";
	}

}