<?php
$wpsf_def_filter = carbon_get_theme_option('wpsf_def_filter');

if ( !empty($wpsf_def_filter) ) {
  if ($wpsf_def_filter ) {

    $wpsf_advanced_page = carbon_get_theme_option('wpsf_advanced_page');


    $nonce = wp_create_nonce('wpsfsearch');
    $add_textbox = carbon_get_post_meta($wpsf_def_filter,'wpsf_add_textbox');
    $post_type = carbon_get_post_meta($wpsf_def_filter,'wpsf_post_type');
    $per_page = carbon_get_post_meta($wpsf_def_filter,'wpsf_per_page');
    $orderby = carbon_get_post_meta($wpsf_def_filter,'wpsf_orderby');
    $order = carbon_get_post_meta($wpsf_def_filter,'wpsf_order');
    $button_text = carbon_get_post_meta($wpsf_def_filter,'wpsf_button_text');
    $template = carbon_get_post_meta($wpsf_def_filter,'wpsf_template');


    if ( is_search() ) {
      $faction = home_url( '/' );
      $method = 'method="get" action="'.$faction.'"';
      $namekey = 's';
      $oldvalue = (isset($_GET['s'])) ? sanitize_text_field($_GET['s']) : '';
    }else{
      $wpsf_perma = get_permalink($wpsf_advanced_page);
      $method = 'method="get" action="'.$wpsf_perma.'"';
      $namekey = 'wpsfkeyword';
      $oldvalue = (isset($_GET['wpsfkeyword'])) ? sanitize_text_field($_GET['wpsfkeyword']) : '';
    }


    if ( $post_type )  {
      $years = get_posts_years_array($post_type);
  ?>
    <div id="wpsfsf-<?php echo $wpsf_def_filter; ?>">
      <form id="wpsf-form-<?php echo $wpsf_def_filter; ?>" <?php echo $method; ?>>

        <?php if($formtitle) { ?>
          <div class="wpsf-title"><?php echo get_the_title($wpsf_def_filter); ?></div>
        <?php } ?>

        <?php if ( $add_textbox ) { ?>
        <div class="wpsf-field" style="margin-bottom: 28px;">
          <!-- <label class="wpsf-label wpsf-keyword"><?php echo $button_text; ?></label> -->
          <h4><?php echo $button_text; ?></h4>
          <input id="wpsf-key-<?php echo $wpsf_def_filter; ?>" type="text" name="<?php echo $namekey; ?>" class="wpsf-text-input" value="<?php echo $oldvalue; ?>" />
        </div>
        <?php } ?>

        <div class="wpsf-custom-filter">
          <div class="wpsf-form-title">
            <h4>Filter by:</h4>
          </div>
        <?php
          $fields = carbon_get_post_meta($wpsf_def_filter,'wpsf_search-field');
          //var_dump($fields);
          if ( $fields) {

            $args = array(
      			  'public'   => true,
      			);
      			$output = 'names';
      			$taxonomies = get_taxonomies( $args, $output );

            //var_dump($taxonomies);

            $i = 1;
            foreach ($fields as $field) {
              //var_dump($field);
              ?>

              <?php if ( in_array($field['_type'], $taxonomies)) { ?>
                  <?php
                  $args = array();

                  if ( $field['wpsf_hide_empty'] == 'yes' ) {
                    $args['hide_empty'] = true;
                  }else{
                    $args['hide_empty'] = false;
                  }

                  if ( $field['wpsf_taxsel'] ) {
                    $args[$field['wpsf_filter']] = $field['wpsf_taxsel'];
                  }

                  $terms = get_terms( $field['_type'], $args );
                  if ( $terms) {
                    $meold = false;
                    if ( isset($_GET['wpsftax-'.$field['_type'].'-'.$i]) && !empty($_GET['wpsftax-'.$field['_type'].'-'.$i]) ) {
                      $meold = $_GET['wpsftax-'.$field['_type'].'-'.$i];
                    }
                  ?>
                <div class="wpsf-form-group wpsf-year">
                  <?php if ( $field['wpsf_label'] ) { ?>
                    <span class="wpsf-label <?php if ($i == 1) echo 'first'; ?>"><?php echo $field['wpsf_label']; ?></span>
                  <?php } ?>

                  <?php if ( $field['wpsf_display'] == 'dropdown' ) { ?>
                    <?php
                      $dlabel = 'Select';
                      if ( !empty($field['wpsf_drop_label']) ) {
                        $dlabel = $field['wpsf_drop_label'];
                      }
                    ?>
                    <select id="wpsf-<?php echo $field['_type']; ?>-<?php echo $i; ?>" class="wpsf-select" name="wpsftax-<?php echo $field['_type']; ?>-<?php echo $i; ?>">
                      <?php
                      $selected = '';
                      if (!$meold && !is_array($meold)) {
                          $selected = 'selected="true"';
                      }
                      ?>
                      <option value="" <?php echo $selected; ?>><?php echo $dlabel; ?></option>

                      <?php foreach ($terms as $term) { ?>
                        <?php
                        $selected = '';
                        if ($meold && !is_array($meold)) {
                          if ( $meold == $term->term_id ) {
                            $selected = 'selected="true"';
                          }
                        }
                        ?>

                        <option value="<?php echo $term->term_id; ?>" <?php echo $selected; ?>><?php echo $term->name; ?></option>
                      <?php } ?>

                    </select>
                  <?php }elseif ( $field['wpsf_display'] == 'radio' ) { ?>

                    <?php foreach ($terms as $term) { ?>
                      <?php
                      $checked = '';
                      if ($meold && is_array($meold)) {
                        if ( in_array($term->term_id,$meold) ) {
                          $checked = 'checked';
                        }
                      }
                      ?>
                      <input type="radio" name="wpsftax-<?php echo $field['_type']; ?>-<?php echo $i; ?>[]" value="<?php echo $term->term_id; ?>" <?php echo $checked; ?>> <?php echo $term->name; ?> <br/>
                    <?php } ?>

                  <?php }elseif ( $field['wpsf_display'] == 'checkbox' ) { ?>

                    <?php foreach ($terms as $term) { ?>
                      <?php
                      $checked = '';
                      if ($meold && is_array($meold)) {
                        if ( in_array($term->term_id,$meold) ) {
                          $checked = 'checked';
                        }
                      }
                      ?>
                      <input type="checkbox" name="wpsftax-<?php echo $field['_type']; ?>-<?php echo $i; ?>[]" value="<?php echo $term->term_id; ?>" <?php echo $checked; ?>> <?php echo $term->name; ?> <br/>
                    <?php } ?>
                  <?php }elseif ( $field['wpsf_display'] == 'multiselect' ) { ?>
                    <?php
                      $dlabel = 'Select';
                      if ( !empty($field['wpsf_drop_label']) ) {
                        $dlabel = $field['wpsf_drop_label'];
                      }
                      $enable_search = $field['wpsf_enable_search'];

                      $wpsfclass = 'wpsf-select-multiple';
                      if ( $enable_search == 'yes' ) {
                        $wpsfclass = 'wpsf-select-multiple-search';
                      }
                    ?>

                    <select id="wpsf-<?php echo $field['_type']; ?>-<?php echo $i; ?>" class="<?php echo $wpsfclass; ?>" name="wpsftax-<?php echo $field['_type']; ?>-<?php echo $i; ?>[]" multiple="multiple">

                      <?php foreach ($terms as $term) { ?>
                        <?php

                        $selected = '';
                        if ($meold && !is_array($meold)) {
                          if ( $meold == $term->term_id ) {
                            $selected = 'selected="true"';
                          }
                        }elseif ( is_array($meold) ) {
                          if ( in_array($term->term_id,$meold) ) {
                            $selected = 'selected="true"';
                          }
                        }
                        ?>

                        <option value="<?php echo $term->term_id; ?>" <?php echo $selected; ?>><?php echo $term->name; ?></option>
                      <?php } ?>

                    </select>
                  <?php } ?>

                </div>
                <?php } ?>
              <?php } ?>

              <?php if ( $field['_type'] == 'year') { ?>

              <?php $oldyear = (isset($_GET['wpsf-year'])) ? sanitize_text_field($_GET['wpsf-year']) : ''; ?>
              <?php $oldfrom = (isset($_GET['wpsf-year-from'])) ? sanitize_text_field($_GET['wpsf-year-from']) : ''; ?>
              <?php $oldto = (isset($_GET['wpsf-year-to'])) ? sanitize_text_field($_GET['wpsf-year-to']) : ''; ?>

              <div class="wpsf-form-group wpsf-year">
                <?php if ( $years ) { ?>




                  <?php if ( $field['wpsf_display'] == 'dropdown' ) { ?>
                    <?php
                      $dlabel = 'Select';
                      if ( !empty($field['wpsf_drop_label']) ) {
                        $dlabel = $field['wpsf_drop_label'];
                      }
                    ?>
                    <?php if ( $field['wpsf_label'] ) { ?>
                      <span class="wpsf-label"><?php echo $field['wpsf_label']; ?></span>
                    <?php } ?>
                    <select id="wpsf-year-<?php echo $i; ?>" class="wpsf-select" name="wpsf-year">
                      <option <?php if ($oldyear == '' ) echo 'selected="true"'; ?> value=""><?php echo $dlabel; ?></option>
                      <?php foreach ($years as $year) { ?>
                        <?php if ( $year > 0 ) { ?>
                          <option value="<?php echo $year; ?>" <?php if ($oldyear == $year ) echo 'selected="true"'; ?>><?php echo $year; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>

                  <?php }elseif ( $field['wpsf_display'] == 'radio' ) { ?>
                    <?php if ( $field['wpsf_label'] ) { ?>
                      <span class="wpsf-label"><?php echo $field['wpsf_label']; ?></span>
                    <?php } ?>
                    <?php foreach ($years as $year) { ?>

                      <?php if ( $year > 0 ) { ?>
                        <?php
                        $checked = false;
                        if ( is_array($oldyear) ) {
                          if ( in_array($year,$oldyear) ) {
                            $checked = true;
                          }
                        }
                        ?>
                        <input type="radio" name="wpsf-year[]" value="<?php echo $year; ?>" <?php if ($checked) echo 'checked'; ?>> <?php echo $year; ?> <br/>
                      <?php } ?>
                    <?php } ?>
                  <?php }elseif ( $field['wpsf_display'] == 'checkbox' ) { ?>
                    <?php if ( $field['wpsf_label'] ) { ?>
                      <span class="wpsf-label"><?php echo $field['wpsf_label']; ?></span>
                    <?php } ?>
                    <?php foreach ($years as $year) { ?>
                      <?php if ( $year > 0 ) { ?>
                        <?php
                        $checked = false;
                        if ( is_array($oldyear) ) {
                          if ( in_array($year,$oldyear) ) {
                            $checked = true;
                          }
                        }
                        ?>
                        <input type="checkbox" name="wpsf-year[]" value="<?php echo $year; ?>" <?php if ($checked) echo 'checked'; ?>> <?php echo $year; ?> <br/>
                      <?php } ?>
                    <?php } ?>
                  <?php }elseif ( $field['wpsf_display'] == 'fromto' ) { ?>
                    <div class="wpsf-from">
                      <span class="wpsf-label">From</span>

                      <?php
                        $dlabel = 'Select';
                        if ( !empty($field['wpsf_drop_label']) ) {
                          $dlabel = $field['wpsf_drop_label'];
                        }
                      ?>
                      <select id="wpsf-year-<?php echo $i; ?>-from" class="wpsf-select" name="wpsf-year-from">
                        <option <?php if ($oldyear == '' ) echo 'selected="true"'; ?> value=""><?php echo $dlabel; ?></option>
                        <?php foreach ($years as $year) { ?>
                          <?php if ( $year > 0 ) { ?>
                            <option value="<?php echo $year; ?>" <?php if ($oldfrom == $year ) echo 'selected="true"'; ?>><?php echo $year; ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>

                    <div class="wpsf-to">
                      <span class="wpsf-label">To</span>

                      <select id="wpsf-year-<?php echo $i; ?>-to" class="wpsf-select" name="wpsf-year-to">
                        <option <?php if ($oldyear == '' ) echo 'selected="true"'; ?> value=""><?php echo $dlabel; ?></option>
                        <?php foreach ($years as $year) { ?>
                          <?php if ( $year > 0 ) { ?>
                            <option value="<?php echo $year; ?>" <?php if ($oldto == $year ) echo 'selected="true"'; ?>><?php echo $year; ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  <?php } ?>
                <?php } ?>

              </div>

              <?php }

              if ( $field['_type'] == 'meta') {
                //echo 'meta';
              }


              $i++;

            }
          }
        ?>
        </div>


        <input type="hidden" name="wpsfnonce" value="<?php echo $nonce; ?>" />
        <input type="hidden" name="wpsfid" value="wpsf-form-<?php echo $wpsf_def_filter; ?>">
        <input type="hidden" name="wpsf_per_page" value="<?php echo $per_page; ?>" />

        <input type="hidden" name="wpsfs" value="wpsf_search_adv" />


        <div class="wpsf-form-group" id="wpsf_submit">
          <input type="submit" id="wpsf_submit_btn" value="Search" alt="Search" class="btn btn-default wpsf-button wpsf-button-search" />
          <button type="reset" value="Reset" class="btn btn-default wpsf-button wpsf-button-reset" id="wpsfreset">Reset</button>
        </div>

      </form>
    </div>
  <?php
    }else{
      echo esc_html( __( 'Please select a post type', 'wp-search-filter' ) );
    }
  }else{
    echo esc_html( __( 'No Form Selected', 'wp-search-filter' ) );
  }
}else{
  echo esc_html( __( 'No Form Selected', 'wp-search-filter' ) );
}
