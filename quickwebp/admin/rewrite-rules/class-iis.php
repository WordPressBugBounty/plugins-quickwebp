<?php

include_once QUICKWEBP_PLUGIN_PATH . 'admin/rewrite-rules/class-rewrite-rules-abstract.php';


class IIS extends Rewrite_Rules_Abstract {

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

		return trim( '
# BEGIN ' . $this->tag_name . '
<!-- @parent /configuration/system.webServer/rewrite/rules -->
<rule name="' . esc_attr( $this->tag_name ) . ' 2">
	<match url="^(' . $home_root . '.+)\.(' . $extensions . ')$" ignoreCase="true" />
	<conditions logicalGrouping="MatchAll">
		<add input="{HTTP_ACCEPT}" pattern="image/webp" ignoreCase="false" />
		<add input="{DOCUMENT_ROOT}/{R:1}{R:2}.webp" matchType="IsFile" />
	</conditions>
	<action type="Rewrite" url="{R:1}{R:2}.webp" logRewrittenUrl="true" />
	<serverVariables>
		<set name="ACCEPTS_WEBP" value="true" />
	</serverVariables>
</rule>

<!-- @parent /configuration/system.webServer/rewrite/outboundRules -->
<rule preCondition="IsWebp" name="' . esc_attr( $this->tag_name ) . ' 3">
	<match serverVariable="RESPONSE_Vary" pattern=".*" />
	<action type="Rewrite" value="Accept"/>
</rule>
<preConditions name="' . esc_attr( $this->tag_name ) . ' 4">
	<preCondition name="IsWebp">
		<add input="{ACCEPTS_WEBP}" pattern="true" ignoreCase="false" />
	</preCondition>
</preConditions>

<!-- @parent /configuration/system.webServer -->
<staticContent name="' . esc_attr( $this->tag_name ) . ' 1">
	<mimeMap fileExtension=".webp" mimeType="image/webp" />
</staticContent>
# END ' . $this->tag_name . '');
	}

}