jQuery(document).ready(function($) {
  
  setTimeout( open_wcsd_modal, wcsd.delay*1000 );
  
  function open_wcsd_modal() {
    if( jQuery('#wcsd_modal').length > 0 ){
      var overlayClick = wcsd.overlay_click == 'yes' ? true : false;
      jQuery.magnificPopup.open({
        items: {
            src: '#wcsd_modal' 
        },
        type: 'inline',
        removalDelay: 1000,
        closeOnBgClick: overlayClick,
        callbacks: {
          beforeOpen: function() {
             this.st.mainClass = wcsd.effect;
          },
          beforeClose: function() {
            if( wcsd.hinge == 'yes' )
              this.content.addClass('hinge');
            else
              jQuery('.mfp-wrap').css('background', 'transparent');
          },
          close: function() {
            if( wcsd.hinge == 'yes' )
              this.content.removeClass('hinge');
            wcsd_setCookie( 'wcsd_closed', 'yes', wcsd.cookie_length );
          },
          open: function(){
            jQuery('.mfp-wrap').css('background', wcsd.overlayColor);
          }
        }
      });
    }
  }

  // Set Cookie
  function wcsd_setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires +"; path=/";
  }

  // Get Cookie
  function wcsd_getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1);
      if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
  }

  $("#wcsd_set_cook").click(function(){
    wcsd_setCookie( 'wcsd_closed', 'yes', wcsd.cookie_length );
  });

});	
