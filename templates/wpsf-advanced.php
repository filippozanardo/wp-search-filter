<?php get_header(); ?>

<div class="title <?php echo get_post_meta(get_the_ID(), 'class', true);?>">
  <div class ="container">
      <div class="row">
          <div class="col-xs-12 entry-header">
              <?php if ( is_search() ) { ?>

                <?php $search = get_search_query(); ?>
                <?php if ( $search != '' ) { ?>
                  <h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'wp-search-filter' ), get_search_query() ); ?></h1>
                <?php }else{ ?>
                  <h1 class="page-title"><?php echo __( 'Search Results', 'wp-search-filter' ); ?></h1>
                <?php } ?>

              <?php }else{ ?>

                <h1 class="entry-title"><?php the_title(); ?></h1>

              <?php } ?>

          </div>
      </div>
  </div>
</div>

<div class="container full-width">
  <div class="row">
    <div id="wpsfsearch" class="content-area col-md-3">
      <?php $wpsf_def_filter = carbon_get_theme_option('wpsf_def_filter'); ?>
      <?php if ( $wpsf_def_filter ) { ?>
        <?php WPSF()->template_loader->get_template_part( 'wpsf' ,'advanced-form',true ); ?>
      <?php } ?>

    </div>
    <div class="col-md-9">
      <main id="main" class="site-main" role="main">
        <?php WPSF()->template_loader->get_template_part( 'wpsf' ,'advanced-filter',true ); ?>
        <?php WPSF()->template_loader->get_template_part( 'wpsf' ,'advanced-content',true ); ?>
      </main>
    </div>
  </div>
</div>

<?php get_footer(); ?>
