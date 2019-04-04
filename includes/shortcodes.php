<?php

function wpsf_shortcode( $atts, $content = null ) {
  ob_start();
  WPSF()->template_loader->set_template_data( $atts ,'wpsf_param' )->get_template_part( 'shortcodes/wpsf' ,'shortcode',true );
  $output = ob_get_clean();
	return $output;
}
add_shortcode( 'wpsf', 'wpsf_shortcode' );
