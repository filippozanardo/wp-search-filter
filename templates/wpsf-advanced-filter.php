<div class="wpsfadv-filter">
<?php
  $requiem = $_REQUEST;
  if(!empty($requiem) && $requiem['wpsfs'] == 'wpsf_search_adv' ) {
  ?>
  <ul>
  <?php
    $i = 0;
    foreach ($requiem as $rkey => $wpsfalue ) {

      if ( substr($rkey, 0,5) == 'wpsftax' ) {
        $pieces = explode("-", $rkey);
        if ( $wpsfalue ) {
          if ( is_array($wpsfalue) ) {
            foreach ($wpsfalue as $kk => $vv) {
               $nombre = get_term_by('id',$vv,$pieces[1]);
               if ( $nombre ) {
                 $i++;
                 echo '<li><a class="wpsf-remove-facet" href="" data-key="'.$rkey.'[]" data-value="'.$vv.'">'.$nombre->name.'</a></li>';
               }
            }
          }else{
            $nombre = get_term_by('id',$wpsfalue,$pieces[1]);
            if ( $nombre ) {
              $i++;
              echo '<li><a class="wpsf-remove-facet" href="" data-key="'.$rkey.'" data-value="'.$wpsfalue.'">'.$nombre->name.'</a></li>';
            }
          }
        }
      }elseif(substr($rkey, 0,7) == 'wpsf-year' ) {
        if ( $wpsfalue ) {
            $i++;
            echo '<li><a class="wpsf-remove-facet" href="" data-key="'.$rkey.'" data-value="'.$wpsfalue.'">'.$wpsfalue.'</a></li>';
        }
      }

    }
    if ( $i > 0 ) echo '<li><a href="#" class="wpsf-remove-all" id="removeall">Clear all filters</a></li>';
    ?>
    </ul>
    <?php if ( $i > 0) { ?>
      <span class="filter-number">There <?php if ( $i >= 2 ) { echo 'are'; }else{ echo 'is'; } ?> <span class="wpsf-number"><?php echo $i; ?></span> active filter<?php if ( $i >= 2 ) echo 's'; ?></span>
    <?php } ?>
    <?php
  }
?>
</div>
