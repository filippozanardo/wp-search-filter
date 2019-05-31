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

      $formid = absint(str_replace('wpsf-form-','',$_REQUEST['wpsfid']));

      $relation = carbon_get_post_meta($formid,'wpsf_relation');
      if ( empty($relation) ) $relation = 'OR';

      if ( is_search() ) {
        $keyword = !empty($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : null;
      }else{
        $keyword = !empty($_REQUEST['wpsfkeyword']) ? sanitize_text_field($_REQUEST['wpsfkeyword']) : null;
      }
      //var_dump($keyword);
      if ( $keyword ) {
        $args['s'] = $keyword;
      }

      $fromyear = !empty($_REQUEST['wpsf-year-from']) ? sanitize_text_field($_REQUEST['wpsf-year-from']) : null;
      $toyear = !empty($_REQUEST['wpsf-year-to']) ? sanitize_text_field($_REQUEST['wpsf-year-to']) : null;

      if ( !empty($fromyear) && !empty($toyear) ) {


        if ($fromyear > $toyear ) {

          $args['date_query'] = array(
            array(
              'after' => array(
                'year' => $fromyear
              ),
              'before' => array(
                'year' => $toyear
              ),
              'inclusive' => true,
            )
          );
        }else{
          $args['date_query'] = array(
            array(
              'after' => array(
                'year' => $fromyear
              ),
              'inclusive' => true,
            )
          );
        }
      }elseif( !empty($fromyear) ) {
        $args['date_query'] = array(
          array(
            'after' => array(
              'year' => $fromyear
            ),
            'inclusive' => true,
          )
        );
      }elseif( !empty($toyear) ) {
        $args['date_query'] = array(
          array(
            'before' => array(
              'year' => $toyear
            ),
            'inclusive' => true,
          )
        );
      }

      $wpsf_year = !empty($_REQUEST['wpsf-year']) ? $_REQUEST['wpsf-year'] : null;

      if ( !empty($wpsf_year) ) {
        if ( is_array( $wpsf_year ) ) {
          foreach ($wpsf_year as $yv ) {
            $args['date_query'][] = array( 'year' => $yv );
          }
          if ( !empty($args['date_query']) ) $args['date_query']['relation'] = $relation;
        }else{
          $args['date_query'] = array( 'year' => $wpsf_year );
        }

      }

      $taxonomies = get_taxonomies( array('public'=> true), 'names' );


      foreach ($_REQUEST as $gkey => $gvalue) {
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
        if ( !empty($args['tax_query']) ) $args['tax_query']['relation'] = $relation;
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

    while($wpsf_query->have_posts()):$wpsf_query->the_post();
    ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
          <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

          <?php if ( 'post' == get_post_type() ) : ?>
          <div class="entry-meta">
          </div>
          <?php endif; ?>
        </header>

        <div class="entry-summary">
          <?php the_excerpt(); ?>
        </div>

        <footer class="entry-footer">
        </footer>
      </article>
    <?php
    endwhile;
    $g = $wpsf_query->max_num_pages;
    wp_reset_query();


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
                          	printf( __( 'Nothing found for %s', 'wp-search-filter'), '<em>' . get_search_query() . '</em>' );
                    } else {
                          	_e( 'Nothing Found', 'wp-search-filter' );
                  }
                ?>
          </h1>
      	</header>

      	<div class="entry-content">

      		<?php if ( is_search() ) : ?>

      			<p><?php _e( 'Nothing matched your search terms. Check out the most recent articles below or try searching for something else:', 'wp-search-filter' ); ?></p>
      			<?php get_search_form(); ?>

      		<?php else : ?>

      			<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wp-search-filter' ); ?></p>
      			<?php get_search_form(); ?>

      		<?php endif; ?>
      	</div>
      </div>

    </section>
  <?php
  }
  ?>
</div>
<?php

  }
}
?>
