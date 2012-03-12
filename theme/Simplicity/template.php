<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>
<!DOCTYPE HTML>
<!--
Author: Mike Henken
Author URL: http://michaelhenken.com/
Theme Adapated From: http://www.html5webtemplates.co.uk/templates/simplestyle_4/index.html
-->
<html>

<head>
	<title><?php get_page_title(); ?></title>
	<?php get_header(); ?>
	<meta http-equiv="content-type" content="text/html; charset=windows-1252" />
	<link rel="stylesheet" type="text/css" href="<?php get_theme_url(); ?>/style/style.css" />
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <!-- class="logo_colour", allows you to change the colour of the text -->
          <h1><a href="<?php get_site_url(); ?>"><?php return_simplicity('websitetitle'); ?></a></h1>
          <h2><?php return_simplicity('websiteslogan'); ?></h2>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
         <?php get_navigation(return_page_slug()); ?>
        </ul>
      </div>
    </div>
    <div id="content_header"></div>
    <div id="site_content">
      <div id="sidebar_container">
	  <?php
			   $news_file = 'plugins/news_manager.php'; 
			   if(file_exists($news_file)) { 
		   ?>
        <div class="sidebar">
          <div class="sidebar_top"></div>
          <div class="sidebar_item">
            <h3>Latest News</h3>
            <?php nm_list_recent(); ?>
		  </div>
          <div class="sidebar_base"></div>
        </div>
        <?php } ?>
        <div class="sidebar">
          <div class="sidebar_top"></div>
          <div class="sidebar_item">
              <?php return_simplicity('sidebar1'); ?>
          </div>
          <div class="sidebar_base"></div>
        </div>
       <?php 
           $search_file = 'plugins/i18n_search.php'; 
           if(file_exists($search_file)) { 
       ?>
        <div class="sidebar">
          <div class="sidebar_top"></div>
          <div class="sidebar_item">
            <h3>Search</h3>
            <form method="post" action="index.php?id=<?php get_page_slug(); ?>" id="search_form">
              <p>
                <input class="search" type="text" name="words" value="Enter keywords....." onfocus="if(this.value == 'Enter keywords.....') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter keywords.....';}" />
                <input name="search" type="image" style="border: 0; margin: 0 0 -9px 5px;" src="<?php get_theme_url(); ?>/style/search.png" alt="Search" title="Search" />
              </p>
            </form>
          </div>
          <div class="sidebar_base"></div>
        </div>
       <?php } ?>
      </div>
      <div id="content">
	<h1><?php get_page_title(); ?></h1>
        <?php 
        if(!isset($_POST['words']))
        {
            get_page_content(); 
        }
        else 
        {
            get_i18n_search_results(array($_POST['words']=>''));
        }
        ?>
      </div>
    </div>
    <div id="content_footer"></div>
    <div id="footer">
        <div id="footer-inner">
            <?php return_simplicity('footer'); ?>
        </div>
    </div>
  </div>
</body>
</html>