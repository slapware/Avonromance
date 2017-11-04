<?php
/* Template name: Tidal Contribute */
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

?>
<html>
<head>
<title>Contributor Dashboard</title>
<link href="http://c15132427.r27.cf2.rackcdn.com/avonromance-favicon.png" rel="shortcut icon">
<style type="text/css">
	body {
		padding:0;
		margin:0;
	}
</style>
</head>
<body >
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php the_content(); ?>
<?php endwhile; endif;?>
</body>
</html>