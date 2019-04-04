jQuery(document).ready(function($){
  $('.wpsf-select-multiple').multiselect({
    numberDisplayed: 1,
    nonSelectedText: 'Select'
  });

  $('.wpsf-select-multiple-search').multiselect({
    templates: {
        ul: '<ul class="multiselect-container dropdown-menu"></ul>',
        filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
        filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times-circle"></i></button></span>',
    },
    numberDisplayed: 1,
    enableFiltering: true,
    nonSelectedText: 'Select'
  });

  $('#wpsfreset').on('click', function(e) {
    e.preventDefault();
    var pathname = window.location.pathname.split("/");

    for (var i=0;i<pathname.length;i++) {
      if (pathname[i] == 'page' ) {
        pathname.splice(i,3);
        pathname.push('');
      }
    }

    var finalpath = pathname.join("/");

    var nopagingurl = window.location.protocol + '//' + window.location.hostname + finalpath+'?s=&wpsfs=wpsf_search_adv';

    window.location.href = nopagingurl;
    return false;
  });

  $('#removeall').on('click',function(e) {
    e.preventDefault();
    var pathname = window.location.pathname.split("/");

    for (var i=0;i<pathname.length;i++) {
      if (pathname[i] == 'page' ) {
        pathname.splice(i,3);
        pathname.push('');
      }
    }

    var finalpath = pathname.join("/");

    var nopagingurl = window.location.protocol + '//' + window.location.hostname + finalpath+'?s=&wpsfs=wpsf_search_adv';

    window.location.href = nopagingurl;
    return false;
  });

  $('.wpsf-remove-facet').on('click',function(e) {
    e.preventDefault();
    var ke = $(this).data('key');
    var value = $(this).data('value');

    var pathname = window.location.pathname.split("/");

    for (var i=0;i<pathname.length;i++) {
      if (pathname[i] == 'page' ) {
        pathname.splice(i,3);
        pathname.push('');
      }
    }

    var finalpath = pathname.join("/");

    var nopagingurl = window.location.protocol + '//' + window.location.hostname + finalpath;

    var query = decodeURIComponent(window.location.search.substring(1));
    var vars = query.split("&");

    for (var i=0;i<vars.length;i++) {
      var pair = vars[i].split("=");

      if ( value == pair[1] ) {

          if ( ke ==  pair[0] ) {
            vars.splice(i,1);
          }

      }
    }
    var finalsearch = vars.join("&");

    var finalurl = nopagingurl+"?"+finalsearch;

    window.location.href = finalurl;

  });

});
