      <!-- sidebar -->
      <div id="sidebar">
        <?php if(function_exists('wp_content_slider')) { wp_content_slider(); } ?>
      <?php if ( is_active_sidebar( 'sidebar-1' ) ) : 
        dynamic_sidebar( 'sidebar-1' );
      else: ?>
        <div class="widget">
          <h2>No Widget</h2>
          <p>ウィジットは設定されていません。</p>
        </div>
      <?php
      endif;
      ?>  
      </div>
      <!-- /sidebar -->