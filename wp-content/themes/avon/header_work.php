<!DOCTYPE html>
<html lang="en">
<head>
<link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Serif">
<title>
<?php
/*
 * Print the <title> tag based on what is being viewed.
 */	
global $page, $paged;

wp_title( '|', true, 'right' );

// Add the blog name.
bloginfo( 'name' );

// Add the blog description for the home/front page.
$site_description = get_bloginfo( 'description', 'display' );
if ( $site_description && ( is_home() || is_front_page() ) )
	echo " | $site_description";

// Add a page number if necessary:
if ( $paged >= 2 || $page >= 2 )
	echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

?>
</title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
<link href="<?php echo get_template_directory_uri(); ?>/custom.css" rel="stylesheet">
<link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
<link href="http://c15132427.r27.cf2.rackcdn.com/avonromance-favicon.png" rel="shortcut icon">
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/script.js"></script>
</head>

<body>

<div <?php if(!(is_front_page())){ ?>id="header"<?php } ?> class="header navbar">
	
	<a href="/"><img src="http://c15132427.r27.cf2.rackcdn.com/brand.png"></a>
	<?php get_search_form(); ?>
	
	<ul class="cart-nav">
	<li style="padding-top: 3px;"><a href="https://store.digitalriver.com/store?Action=DisplayPage&amp;id=QuickBuyCartPage&amp;Locale=en_US&amp;SiteID=harperco&amp;themeid=36273000&amp;externalstyleid=https%3A%2F%2Fstatic.tid.al%2Ffun%2Favonromance%2Fdigitalriver_cart.css" target="_blank" class="">Your Cart</a></li>
	<li style="padding-top: 3px;"><a href="https://store.digitalriver.com/store/harperco/en_US/DisplayHelpPage/ThemeID.36273000?externalstyleid=https%3A%2F%2Ftid.al%2Ffun%2Favonromance%2Fdigitalriver_cart.css" target="_blank" class="">Help</a></li>
	</ul>
	
	<ul>
	<li><a href="/">Home</a></li>
	<li class="nav-dropdown"><a href="/books" class="topBtn">Books</a><span class="fa fa-sort-asc"></span>
	<ul class="ul-dropdown">
		<li><a href="http://avonromance.hc.com/newbooks">New Releases</a> </li>|
		<li><a href="http://avonromance.hc.com/deals">Ebook Sale</a></li>
	</ul>
	</li>
	<li class="nav-dropdown"><a href="#" class="topBtn">Fun Stuff</a><span class="fa fa-sort-asc"></span>
	<ul class="ul-dropdown">
		<li><a href="/quiz/whos-your-valentine">Quiz</a></li> | 
		<li><a href="/sweepstakes">Sweeps</a></li>
	</ul>
	</li>
	<li class="nav-dropdown"><a href="#" class="topBtn">Events</a><span class="fa fa-sort-asc"></span>
	<ul class="ul-dropdown">
	<li><a href="/q-and-a/kayeryanchat">Laura Kaye &amp; Jennifer Ryan Summer Q&amp;A</a> </li> |
	<li><a href="/q-and-a">Previous Q&amp;As</a></li> | 
	<li><a href="http://avonromance.hc.com/avonblogtour/">Blog Tours</a></li> | 
	<li><a href="http://www.ustream.tv/channel/romancelive">Avon Romance Live</a></li>
	</ul>
	</li>
	<li><a href="/community">Community</a>
	</li><li><a href="/shareyourbook">Share Your Book</a></li>
	<li><a href="http://avonromance.hc.com/uk">Avon UK</a></li>
	<li><a href="/contribute">Log in</a></li>
	</ul>
	
	
	
	
</div>
<?php if(!(is_front_page())){ ?>
<div class="nav-fixxer"></div>
<?php } ?>