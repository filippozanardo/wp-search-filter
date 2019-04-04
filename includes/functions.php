<?php
function get_posts_years_array($post_type) {
    global $wpdb;
    $result = array();
    $post_in = array('page');
    $how_many = count($post_type);

    $placeholders = array_fill(0, $how_many, '%s');
    $format = implode(', ', $placeholders);


    $years = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT YEAR(post_date) FROM {$wpdb->posts} WHERE post_status = 'publish' AND  post_type IN ( $format ) GROUP BY YEAR(post_date) DESC",
            $post_type
        ),
        ARRAY_N
    );
    if ( is_array( $years ) && count( $years ) > 0 ) {
        foreach ( $years as $year ) {
            $result[] = $year[0];
        }
    }
    return $result;
}

function wpsf_pagination( $pages = '', $range = 4 ) {
    $showitems = ($range * 2)+1;

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }

     if(1 != $pages)
     {
      ?>
        <div class="pagination">
            <ul class="page-numbers">
              <?php
                if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li><a href='".get_pagenum_link(1)."'>&laquo; First</a></li>";
                if($paged > 1 && $showitems < $pages) echo "<li><a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a></li>";

                 for ($i=1; $i <= $pages; $i++)
                 {
                     if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
                     {
                         echo ($paged == $i)? "<li class=\"active\"><span class=\"active\">".$i."</span>":"<li><a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a></li>";
                     }
                 }

                 if ($paged < $pages && $showitems < $pages) echo "<li><a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a></li>";
                 if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<li><a href='".get_pagenum_link($pages)."'>Last &raquo;</a></li>";
              ?>
            </ul>
          </div>
        <?php
     }
}
