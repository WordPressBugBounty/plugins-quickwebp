<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-rewrite-rules-abstract.php';


class Quickwebp_IIS extends Quickwebp_Rewrite_Rules_Abstract {

	/**
	 * Get the path to the file.
	 */
	protected function get_file_path() {
		$file_path = $this->get_site_root() . 'web.config';

		return $file_path;
	}

    /**
     * Get unfiltered new contents to write into the file.
     */
    protected function get_raw_new_contents() {
		$extensions = 'jpg|jpeg|jpe|png';
		$home_root  = wp_parse_url( home_url( '/' ) );
		$home_root  = $home_root['path'];

		$content  = '# BEGIN ' . $this->tag_name . "\n";
		$content .= '<!-- @parent /configuration/system.webServer/rewrite/rules -->';

		// Serve AVIF first
		$content .= "\n<rule name=\"" . esc_attr( $this->tag_name ) . " AVIF\">";
		$content .= "\n\t<match url=\"^(" . $home_root . ".+)\\.(" . $extensions . ")$\" ignoreCase=\"true\" />";
		$content .= "\n\t<conditions logicalGrouping=\"MatchAll\">";
		$content .= "\n\t\t<add input=\"{HTTP_ACCEPT}\" pattern=\"image/avif\" ignoreCase=\"false\" />";
		$content .= "\n\t\t<add input=\"{DOCUMENT_ROOT}/{R:1}{R:2}.avif\" matchType=\"IsFile\" />";
		$content .= "\n\t</conditions>";
		$content .= "\n\t<action type=\"Rewrite\" url=\"{R:1}{R:2}.avif\" logRewrittenUrl=\"true\" />";
		$content .= "\n\t<serverVariables>";
		$content .= "\n\t\t<set name=\"ACCEPTS_IMAGE\" value=\"true\" />";
		$content .= "\n\t</serverVariables>";
		$content .= "\n</rule>";

		// Serve WebP if AVIF isn't available
		$content .= "\n\n<rule name=\"" . esc_attr( $this->tag_name ) . " WebP\">";
		$content .= "\n\t<match url=\"^(" . $home_root . ".+)\\.(" . $extensions . ")$\" ignoreCase=\"true\" />";
		$content .= "\n\t<conditions logicalGrouping=\"MatchAll\">";
		$content .= "\n\t\t<add input=\"{HTTP_ACCEPT}\" pattern=\"image/webp\" ignoreCase=\"false\" />";
		$content .= "\n\t\t<add input=\"{DOCUMENT_ROOT}/{R:1}{R:2}.webp\" matchType=\"IsFile\" />";
		$content .= "\n\t</conditions>";
		$content .= "\n\t<action type=\"Rewrite\" url=\"{R:1}{R:2}.webp\" logRewrittenUrl=\"true\" />";
		$content .= "\n\t<serverVariables>";
		$content .= "\n\t\t<set name=\"ACCEPTS_IMAGE\" value=\"true\" />";
		$content .= "\n\t</serverVariables>";
		$content .= "\n</rule>";

		$content .= "\n\n<!-- @parent /configuration/system.webServer/rewrite/outboundRules -->";
		$content .= "\n<rule preCondition=\"IsModernImage\" name=\"" . esc_attr( $this->tag_name ) . " Vary\">";
		$content .= "\n\t<match serverVariable=\"RESPONSE_Vary\" pattern=\".*\" />";
		$content .= "\n\t<action type=\"Rewrite\" value=\"Accept\" />";
		$content .= "\n</rule>";

		$content .= "\n<preConditions name=\"" . esc_attr( $this->tag_name ) . " Preconditions\">";
		$content .= "\n\t<preCondition name=\"IsModernImage\">";
		$content .= "\n\t\t<add input=\"{ACCEPTS_IMAGE}\" pattern=\"true\" ignoreCase=\"false\" />";
		$content .= "\n\t</preCondition>";
		$content .= "\n</preConditions>";

		$content .= "\n\n<!-- @parent /configuration/system.webServer -->";
		$content .= "\n<staticContent name=\"" . esc_attr( $this->tag_name ) . " Mime\">";
		$content .= "\n\t<mimeMap fileExtension=\".webp\" mimeType=\"image/webp\" />";
		$content .= "\n\t<mimeMap fileExtension=\".avif\" mimeType=\"image/avif\" />";
		$content .= "\n</staticContent>";

		$content .= "\n# END " . $this->tag_name;

		return trim( $content );
	}
}
