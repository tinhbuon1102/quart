<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title><?php wp_title( '|', true, 'right' ); bloginfo('name'); ?></title>
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico">
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" media="screen">
    <link href="http://fonts.googleapis.com/css?family=Josefin+Sans:400,600,700" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Nixie+One|Playfair+Display:700,400italic|Raleway|Purple+Purse|Oswald|Comfortaa' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Josefin+Sans' rel='stylesheet' type='text/css'>
    <?php if ( is_singular() ) wp_enqueue_script( "comment-reply" );
    wp_head(); ?>

  </head>
  <body <?php body_class(); ?>>
    <div id="container">
    
      <!-- header -->
      <div id="header" class="clearfix">
      
        
          <h1 id="logo">
            <a href="<?php echo home_url('/'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/logo.png"/></span></a>
          </h1>
        
        
       
        
        <!-- Navigation -->
        <nav class="clearfix" id="mainnv">
    <?php wp_nav_menu( array(
            'theme_location'=>'header-navi', 
            'container'     =>'', 
            'menu_class'    =>'',
            'items_wrap'    =>'<ul id="main-nav">%3$s<div class="clear"></div></ul>'));
    ?>
</nav>
        <!-- /Navigation -->
        <div class="clear"></div>
      </div>
      <!-- /header -->