<?php
$wpsf_def_filter = carbon_get_theme_option('wpsf_def_filter');

if ( !empty($wpsf_def_filter) ) {
  if ($wpsf_def_filter ) {
    $post_type = carbon_get_post_meta($wpsf_def_filter,'wpsf_post_type');

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    //var_dump($post_type);
    $args = array (
       'post_status'=>'publish',
  	   'post_type' => $post_type,
  	   'posts_per_page'=> 20,
       'paged' => $paged
  	    //'order' => 'ASC',
  	    //'orderby' => 'title'
  	);
    $requiem = $_REQUEST;
    if(!empty($requiem) && $requiem['wpsfs'] == 'wpsf_search_adv' || is_search()) {

      $formid = absint(str_replace('wpsf-form-','',$_GET['wpsfid']));

      $relation = carbon_get_post_meta($formid,'wpsf_relation');
      if ( empty($relation) ) $relation = 'OR';

      if ( is_search() ) {
        $keyword = !empty($_GET['s']) ? $_GET['s'] : null;
      }else{
        $keyword = !empty($_GET['wpsfkeyword']) ? sanitize_text_field($_GET['wpsfkeyword']) : null;
      }
      //var_dump($keyword);
      if ( $keyword ) {
        $args['s'] = $keyword;
      }

      if ( !empty($_GET['wpsf-year-from']) && !empty($_GET['wpsf-year-to']) ) {
        if ($_GET['wpsf-year-to'] > $_GET['wpsf-year-from'] ) {
          $args['date_query'] = array(
            array(
              'after' => array(
                'year' => $_GET['wpsf-year-from']
              ),
              'before' => array(
                'year' => $_GET['wpsf-year-to']
              ),
              'inclusive' => true,
            )
          );
        }else{
          $args['date_query'] = array(
            array(
              'after' => array(
                'year' => $_GET['wpsf-year-from']
              ),
              'inclusive' => true,
            )
          );
        }
      }elseif(!empty($_GET['wpsf-year-from'])) {
        $args['date_query'] = array(
          array(
            'after' => array(
              'year' => $_GET['wpsf-year-from']
            ),
            'inclusive' => true,
          )
        );
      }elseif(!empty($_GET['wpsf-year-to'])) {
        $args['date_query'] = array(
          array(
            'before' => array(
              'year' => $_GET['wpsf-year-to']
            ),
            'inclusive' => true,
          )
        );
      }



      if ( !empty($_GET['wpsf-year']) ) {
        if ( is_array( $_GET['wpsf-year'] ) ) {
          foreach ($_GET['wpsf-year'] as $yv ) {
            $args['date_query'][] = array( 'year' => $yv );
          }
          if ( !empty($args['date_query']) ) $args['date_query']['relation'] = $relation;
        }else{
          $args['date_query'] = array( 'year' => $_GET['wpsf-year'] );
        }

      }

      $taxonomies = get_taxonomies( array('public'=> true), 'names' );


      foreach ($_GET as $gkey => $gvalue) {
        $gpieces = explode("-", $gkey);

        if ( isset($gpieces[0]) && $gpieces[0] == 'wpsftax' ) {
          if ( isset($gpieces[1]) ) {
            if ( in_array($gpieces[1], $taxonomies)) {
              if (!empty($gvalue)) {
                $args['tax_query'][] =
                  array(
                      'taxonomy' => $gpieces[1],
                      'field'    => 'term_id',
                      'terms'    => $gvalue,
                  );
              }
            }
          }
        }
        if ( !empty($args['tax_query']) ) $args['tax_query']['relation'] = 'AND';
      }
    }

    //var_dump($args);

    $args2 = $args;
    $args2['posts_per_page'] = -1;
    unset($args2['paged']);
    $wpsf_query2= null;
  	$wpsf_query2 = new WP_Query();

    //
  	$wpsf_query2->query($args2);
    $total = $wpsf_query2->found_posts;

    wp_reset_query();

  	$wpsf_query= null;
  	$wpsf_query = new WP_Query();
    //
  	$wpsf_query->query($args);


?>

<div class="wpsfadv-content">
  <div class="found-number">
    <p><span class="total-number"><?php echo $total; ?></span> result<?php if ( $total >= 2 ) echo 's'; ?> found</p>
  </div>
  <?php
  if( $wpsf_query->have_posts() ) {
    //var_dump($wpsf_query);

    while($wpsf_query->have_posts()):$wpsf_query->the_post();
    ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
          <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

          <?php if ( 'post' == get_post_type() ) : ?>
          <div class="entry-meta">
            <?php dndi_posted_on(); ?>
          </div><!-- .entry-meta -->
          <?php endif; ?>
        </header><!-- .entry-header -->

        <div class="entry-summary">
          <?php the_excerpt(); ?>
        </div><!-- .entry-summary -->

        <footer class="entry-footer">
          <?php dndi_entry_footer(); ?>
        </footer><!-- .entry-footer -->
      </article><!-- #post-## -->
    <?php
    endwhile;
    $g = $wpsf_query->max_num_pages;
    wp_reset_query();

    //var_dump();
    if ( function_exists("wpsf_pagination") ) {
        wpsf_pagination($g);
    }

  }else{
    ?>
    <section class="<?php if ( is_404() ) { echo 'error-404'; } else { echo 'no-results'; } ?> not-found">
      <div class="index-box">
      	<header class="entry-header">
      		<h1 class="entry-title">
                <?php
                  if ( is_search() ) {
                          	/* translators: %s = search query */
                          	printf( __( 'Nothing found for %s', 'dndi'), '<em>' . get_search_query() . '</em>' );
                    } else {
                          	_e( 'Nothing Found', 'dndi' );
                  }
                ?>
          </h1>
      	</header><!-- .page-header -->

      	<div class="entry-content">

      		<?php if ( is_search() ) : ?>

      			<p><?php _e( 'Nothing matched your search terms. Check out the most recent articles below or try searching for something else:', 'dndi' ); ?></p>
      			<?php get_search_form(); ?>

      		<?php else : ?>

      			<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'dndi' ); ?></p>
      			<?php get_search_form(); ?>

      		<?php endif; ?>
      	</div><!-- .entry-content -->
      </div><!-- .index-box -->

        <?php
        if ( is_404() || is_search() ) {
            the_widget( 'WP_Widget_Recent_Posts' );
            if ( dndi_categorized_blog() ) : // Only show the widget if site has multiple categories.
                echo '<div class="widget widget_categories"><h2 class="widget-title">';
                esc_html_e( 'Most Used Categories', 'dndi' );
                echo '</h2><ul>';
                wp_list_categories( array(
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'show_count' => 1,
                    'title_li'   => '',
                    'number'     => 10,
                ) );
                echo '</ul></div><!-- .widget -->';
                endif;

                /* translators: %1$s: smiley */
                $archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives.', 'dndi' )) . '</p>';
                the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );

                the_widget( 'WP_Widget_Tag_Cloud' );
            } ?>
    </section>
  <?php
  }
  ?>
</div>
<?php

  }
}
?>
