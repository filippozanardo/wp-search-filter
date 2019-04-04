<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class wpsf_widget extends WP_Widget {

  function __construct() {
      $widget_ops = array(
        'classname' => 'wpsf-widget',
        'description' => __( 'WP Search Filter', 'wp-search-filter' )
      );
      parent::__construct('wpsf_widget', __( 'WP Search Filter', 'wp-search-filter' ), $widget_ops );
  }

  function widget( $args, $instance ) {
    extract($args);

		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;

		if ( $title )
		{
			echo $before_title . $title . $after_title;
		}

		$wpsf = $instance['wpsf'];

	  echo do_shortcode('[wpsf id="'.$wpsf.'"]');

		echo $after_widget;
  }

  function update( $new_instance, $old_instance ) {
		// Save widget options
		$instance = $old_instance;

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['wpsf'] = ( ! empty( $new_instance['wpsf'] ) ) ? strip_tags( $new_instance['wpsf'] ) : '';

		return $instance;

	}

  function form( $instance )
	{

    $defaults = array(
			'title'            => '',
			'wpsf' => 0,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
    $wpsf = esc_attr($instance[ 'wpsf' ]);
    ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'wp-search-filter' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>"/>
		</p>
    <?php
    $custom_posts = new WP_Query('post_type=wpsf&post_status=publish&posts_per_page=-1');
    ?>
    <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'wpsf' ) ); ?>"><?php _e( 'Choose a Search Form:', 'wp-search-filter' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'wpsf' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'wpsf' ) ); ?>">
        <option value="0"><?php _e('Please choose','wp-search-filter'); ?></option>
        <?php while ($custom_posts->have_posts()) : $custom_posts->the_post(); ?>
				   <option value="<?php the_ID(); ?>" <?php if($wpsf==get_the_ID()){ echo ' selected="selected"'; } ?>><?php the_title(); ?></option>
        <?php endwhile; ?>
        <?php wp_reset_query(); ?>
			</select>
		</p>

		<?php
	}

}

function wpsf_register_widgets() {
	register_widget( 'wpsf_widget' );
}
add_action( 'widgets_init', 'wpsf_register_widgets' );
