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
else if ( ( is_home() || is_front_page() ) )
	echo " | avonromance.com";

// Add a page number if necessary:
if ( $paged >= 2 || $page >= 2 )
	echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

?>
</title>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php
//print_r($post);
$book_custom_fields = get_post_custom($post->ID);
$post_vote_count = getvotescount($post->ID);

$book_author = get_field('book_author');
//print_r($book_author);
endwhile;
endif;
?>
<meta charset="UTF-8">
<meta content="<?php echo get_the_title();?>" property="og:title">
<meta content="<?php echo get_the_title();?>" property="twitter:title">
<meta content="Check in daily to read new romance book reviews, posts from your favorite authors, samples of books, exciting digital first publications and and ebook specials -- all curated by the romance editors of Avon Books." property="og:description">
<meta content="Check in daily to read new romance book reviews, posts from your favorite authors, samples of books, exciting digital first publications and and ebook specials -- all curated by the romance editors of Avon Books." property="twitter:description">
<meta content="Check in daily to read new romance book reviews, posts from your favorite authors, samples of books, exciting digital first publications and and ebook specials -- all curated by the romance editors of Avon Books." name="description">
<meta content="Avon Romance" property="og:site_name">
<link href="http://www.avonromance.com/book/jay-crownover-rowdy" rel="canonical">
<meta content="noindex,nofollow" name="robots">
<meta content="<?php echo get_the_content();?>" property="og:description">
<meta content="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>" property="og:image">


<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
<link href="<?php echo get_template_directory_uri(); ?>/custom.css" rel="stylesheet">
<link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
<link href="http://c15132427.r27.cf2.rackcdn.com/avonromance-favicon.png" rel="shortcut icon">
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/script.js"></script>
</head>

<body>
<script>
  
 // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      testAPI();
    } else if (response.status === 'not_authorized') {
      // The person is logged into Facebook, but not your app.
      //document.getElementById('status').innerHTML = 'Please log ' + 'into this app.';
    } else {
      // The person is not logged into Facebook, so we're not sure if
      // they are logged into this app or not.
      //document.getElementById('status').innerHTML = 'Please log ' + 'into Facebook.';
    }
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '965691493447812',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.1' // use version 2.1
  });
  
  

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
  
  
  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
     // document.getElementById('status').innerHTML = 'Thanks for logging in, ' + response.name + '!';
    });
  }
</script>

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function()
	{ (i[r].q=i[r].q||[]).push(arguments)}
		,i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', 'UA-19207200-1', 'auto');
	ga('send', 'pageview');
</script>

<div <?php if(!(is_front_page())){ ?>id="header"<?php } ?> class="header navbar">
	
	<a href="/"><img src="http://c15132427.r27.cf2.rackcdn.com/brand.png"></a>
	<?php //get_search_form(); ?>
	
	
	<form action="/search" class="searchform" id="searchform" method="get" role="search">
		<div>
			<label for="q" class="screen-reader-text">Search for:</label>
			<input type="text" placeholder="Search" id="q" name="q" value="">
			<input type="submit" value="" id="searchsubmit">
		</div>
	</form>
	
	<ul class="cart-nav">
	<li style="padding-top: 3px;"><a href="https://store.digitalriver.com/store?Action=DisplayPage&amp;id=QuickBuyCartPage&amp;Locale=en_US&amp;SiteID=harperco&amp;themeid=36273000&amp;externalstyleid=https%3A%2F%2Fstatic.tid.al%2Ffun%2Favonromance%2Fdigitalriver_cart.css" target="_blank" class="">Your Cart</a></li>
	<li style="padding-top: 3px;"><a href="https://store.digitalriver.com/store/harperco/en_US/DisplayHelpPage/ThemeID.36273000?externalstyleid=https%3A%2F%2Ftid.al%2Ffun%2Favonromance%2Fdigitalriver_cart.css" target="_blank" class="">Help</a></li>
	<li>
	<div class="fb-like" data-share="false" data-width="450" data-show-faces="true" data-action="like" data-layout="button_count" class="fb-like fb_iframe_widget"></div>
	
	
	</li>
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
<!--		<li><a href="/sweepstakes">Sweeps</a></li>-->
		<li><a href="http://avonromance.hc.com/fromtheheart">Sweeps</a></li>
	</ul>
	</li>
	<li><a href="http://www.ustream.tv/channel/romancelive" class="topBtn">Events</a>
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