<?php

if ( !empty($wpsf_param) ) {
  if ($wpsf_param->id != 0 ) {

    $nonce = wp_create_nonce('wpsfsearch_'.$wpsf_param->id);
    $post_type = carbon_get_post_meta($wpsf_param->id,'wpsf_post_type');
    $per_page = carbon_get_post_meta($wpsf_param->id,'wpsf_per_page');
    $orderby = carbon_get_post_meta($wpsf_param->id,'wpsf_orderby');
    $order = carbon_get_post_meta($wpsf_param->id,'wpsf_order');
    $button_text = carbon_get_post_meta($wpsf_param->id,'wpsf_button_text');
    $template = carbon_get_post_meta($wpsf_param->id,'wpsf_template');
    $method = 'method="get" action="'.home_url( '/' ).'"';

    $oldvalue = (isset($_REQUEST['wpsfkeyword'])) ? sanitize_text_field($_REQUEST['wpsfkeyword']) : '';


    if ( $post_type )  {
      $years = get_posts_years_array($post_type);
  ?>
    <div id="wpsf-<?php echo $wpsf_param->id; ?>">
      <form id="wpsf-form-<?php echo $wpsf_param->id; ?>" <?php echo $method; ?>>

        <?php if($formtitle) { ?>
          <div class="wpsf-title"><?php echo get_the_title($wpsf_param->id); ?></div>
        <?php } ?>

        <div class="wpsf-field">
          <label class="wpsf-label wpsf-keyword"><?php echo $button_text; ?></label>
          <input id="wpsf-key-<?php echo $wpsf_param->id; ?>" type="text" name="wpsfkeyword" class="wpsf-text-input" value="<?php echo $oldvalue; ?>" />
        </div>

        <?php
          $fields = carbon_get_post_meta($wpsf_param->id,'wpsf_search-field');

          if ( $fields) {

            $args = array(
      			  'public'   => true,
      			);
      			$output = 'names';
      			$taxonomies = get_taxonomies( $args, $output );

            $i = 1;
            foreach ($fields as $field) {
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
                    if ( isset($_REQUEST['wpsftax-'.$field['_type'].'-'.$i]) && !empty($_REQUEST['wpsftax-'.$field['_type'].'-'.$i]) ) {
                      $meold = sanitize_text_field($_REQUEST['wpsftax-'.$field['_type'].'-'.$i]);
                    }
                  ?>
                <div class="wpsf-form-group wpsf-year">
                  <?php if ( $field['wpsf_label'] ) { ?>
                    <span class="wpsf-label"><?php echo $field['wpsf_label']; ?></span>
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

                  <?php } ?>

                </div>
                <?php } ?>
              <?php } ?>

              <?php if ( $field['_type'] == 'year') { ?>

              <?php $oldyear = (isset($_REQUEST['wpsf-year'])) ? sanitize_text_field($_REQUEST['wpsf-year']) : ''; ?>

              <div class="wpsf-form-group wpsf-year">
                <?php if ( $years ) { ?>

                  <?php if ( $field['wpsf_label'] ) { ?>
                    <span class="wpsf-label"><?php echo $field['wpsf_label']; ?></span>
                  <?php } ?>


                  <?php if ( $field['wpsf_display'] == 'dropdown' ) { ?>
                    <?php
                      $dlabel = 'Select';
                      if ( !empty($field['wpsf_drop_label']) ) {
                        $dlabel = $field['wpsf_drop_label'];
                      }
                    ?>
                    <select id="wpsf-year-<?php echo $i; ?>" class="wpsf-select" name="wpsf-year">
                      <option <?php if ($oldyear == '' ) echo 'selected="true"'; ?> value=""><?php echo $dlabel; ?></option>
                      <?php foreach ($years as $year) { ?>
                        <?php if ( $year > 0 ) { ?>
                          <option value="<?php echo $year; ?>" <?php if ($oldyear == $year ) echo 'selected="true"'; ?>><?php echo $year; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>

                  <?php }elseif ( $field['wpsf_display'] == 'radio' ) { ?>
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
                  <?php } ?>
                <?php } ?>

              </div>

              <?php }

              if ( $field['_type'] == 'meta') {
              }


              $i++;

            }
          }
        ?>


        <input type="hidden" name="wpsfnonce" value="<?php echo $nonce; ?>" />
        <input type="hidden" name="wpsfid" value="wpsf-form-<?php echo $wpsf_param->id; ?>">
        <input type="hidden" name="wpsf_per_page" value="<?php echo $per_page; ?>" />
        <input type="hidden" name="s" value="wpsf_search_on" />

        <div class="wpsf-form-group" id="wpsf_submit">
          <input type="submit" id="wpsf_submit_btn" value="Search" alt="Search" class="wpsf-button" />
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
