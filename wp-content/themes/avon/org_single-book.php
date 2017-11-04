<?php /**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
// TODO Move this to the header

//get_header(); 
include_once('header_book.php');
?>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52839b1a47fdc9ae"></script>
<script>
(function($){
var getStockInfo = function(elem, isbn, version){
window['hcDigitalRiverCallback' + isbn] = function(data) {
if (data.productInfo) {
var p = data.productInfo.product;
console.log(p);
$(elem).attr("href", "https://store.digitalriver.com/store/harperco/en_US/buy/externalrefid." + isbn + "/hcstoreID.Avon/themeid.36273000?externalstyleid=https%3A%2F%2Ftid.al%2Ffun%2Favonromance%2Fdigitalriver_cart.css");
$('.digital-river-name').text(p.displayName);
if (version == 'paperback') {
$('.digital-river-paperback-price').text("Paperback: " + p.price.unitPriceWithDiscount).show();
$('.digital-river-paperback-availability').text(p.stockStatus.status).show();
if (p.buyLink.text.preOrder === true) {
$(elem).text('Pre-order Paperback from Avon');
}
}
if (version == 'ebook') {
$('.digital-river-ebook-price').text("eBook: " + p.price.unitPriceWithDiscount).show();
$('.digital-river-ebook-availability').text(p.stockStatus.status).show();
if (p.buyLink.text.preOrder === true) {
$(elem).text('Pre-order Ebook from Avon');
}
}
$(elem).show();
}
}
var call = "https://store.digitalriver.com/store/harperco/DisplayDRProductInfo/version.2/output.json/externalReferenceID." + isbn + "/hcstoreID.Avon/externalrefid.9780062293015/themeid.36273000/content.displayName+price+buyLink+stockStatus/jsonp=hcDigitalRiverCallback" + isbn;
$.ajax({
type: 'GET',
url: call,
async: true,
contentType: "application/javascript",
dataType: 'jsonp',
jsonpCallback: "hcDigitalRiverCallback" + isbn,
success: function(jsonp) {
console.log("Digital River API success.");
},
error: function(e) {
console.log("Digital River API error.");
}
});
};
$(document).ready(function(){
$.each($('.digital-river-buy-button'), function(i, _e){
var isbn = $(_e).attr('isbn');
var version = $(_e).attr('version');
getStockInfo(_e, isbn, version);
});
});
})(jQuery);
$(document).ready(function() {
	$(document).click(function() {
			$('.lightbox').addClass('hidden');
	});
	$('.lightbox').click(function(event) {
			event.stopPropagation();
	});
	$('.buy-buttons a.buy').click(function() {
			$('.lightbox').addClass('hidden');
			var target = $(this).attr("href");
			$(target).removeClass('hidden');
			return false;
	});

	
	$('.close').click(function() {
			$(this).closest('.lightbox').addClass('hidden');
			return false;
	});
});
</script>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php
//print_r($post);
$book_custom_fields = get_post_custom($post->ID);
$post_vote_count = getvotescount($post->ID);

$book_author = get_field('book_author');
//print_r($book_author);
?>
<div class="content floral-top">
<div class="group shadow">
	<div class="span4-2">
	<img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>">
	</div>
	<div style="overflow:visible" class="span6 post">
		<h1 class="digital-river-name"><?php the_title(); ?></h1>
		<div class="publish-info">
		<h3><a href="<?php echo get_permalink($book_author[0]->ID); ?>"><?php echo $book_author[0]->post_title; ?></a></h3>
		<br>
		<p class="digital-river-paperback-price" style=""></p>
		<p class="digital-river-ebook-price" style=""></p>
		</div>
		<a style="margin-top:0;color:white !important; cursor:pointer;" data-slug="<?php echo $post->ID;?>" class="book-vote-link bubble">&hearts; <span data-slug="<?php echo $post->ID;?>" class="book-vote-count"><?php echo $post_vote_count;?></span> People Love This Book</a>
		<p><?php the_content(); ?></p>
		<!-- AddThis Button BEGIN -->		
		<div class="addthis_toolbox addthis_default_style"> 
		<a class="addthis_button_tweet"></a> 
		<a class="addthis_button_pinterest_pinit"  pi:pinit:layout="horizontal"></a> 
		<a class="addthis_counter addthis_pill_style addthis_nonzero"></a> 
		</div>
		
	</div>
	<div class="span3">
		<div class="buy-buttons">
		<?php if($book_custom_fields['isbn_code_for_paperback'][0] != '' && $book_custom_fields['isbn_code_for_paperback'][0] != '0') {?>
		<a isbn="<?php echo $book_custom_fields['isbn_code_for_paperback'][0]; ?>" class="digital-river-buy-button bubble" version="paperback" target="_blank" style="width: 172px; text-align: center;">Buy Paperback from Avon</a>
		<?php } ?>
		<?php if($book_custom_fields['isbn_code_for_ebook'][0] != '' && $book_custom_fields['isbn_code_for_ebook'][0] != '0') {?>
		<a isbn="<?php echo $book_custom_fields['isbn_code_for_ebook'][0]; ?>" class="digital-river-buy-button bubble" version="ebook" target="_blank" style="width: 172px; text-align: center;">Buy EBook from Avon</a>
		<?php } ?>
		<a class="buy bubble" style="width: 172px; text-align: center;" href="#buynow">Other Retailers &#x25BE</a>
		<div class="lightbox hidden" id="buynow">		
		<?php echo $book_custom_fields['other_retailers'][0]; ?>
		</div>
		</div>
		<!--
		<p><a href="#" class="read-more"><i>see all retailers</i></a></p>
		-->
	</div>
	<div class="cf"></div>

</div>

<div class="span8">
	<div class="floral">
	<h4>Posts on "<?php the_title();?>"</h4>
	</div>
	<style>
	.user-profile-image {
	display:block;
	height:90px;
	float:right;
	overflow:hidden;
	position:relative;
	width:90px;
	}
	.user-profile-image img {
	float:none;
	}
	</style>
	<?php
	$args=array(
	'tag' => $post->post_name,
	//'showposts'=>10,
	'caller_get_posts'=>1
	);
	$my_query = new WP_Query($args);
	if( $my_query->have_posts() ) {
	while ($my_query->have_posts()) : $my_query->the_post(); 
	
	
	$book_tag_info = getBooksByPostTags($post->ID);	
	$book_id = $book_tag_info['book_id'];
	$book_tag_count = $book_tag_info['count'];
	
	$book_info = get_post($book_id);
	//print_r($book_info);
	$book_custom_fields = get_post_custom($book_id);
	//print_r($book_custom_fields);
	$post_vote_count = getvotescount($post->ID);
	$book_vote_count = getvotescount($book_id);
	
	$post_book_author = get_field('book_author', $book_id);
	?>
	<div class="post">
		<div class="group bubble" style="background: none repeat scroll 0% 0% rgb(246, 127, 167);">
			<h2><strong><?php the_time('M d') ?></strong> - Book Review</h2>
			<div class="vote">
			<h2 class="pull-left">
			<a href="#" data-slug="<?php echo $post->ID;?>" class="vote-link">Love this post! <span data-slug="<?php echo $post->ID;?>" class="vote-count"><?php echo $post_vote_count;?></span> | &hearts;</a>
			</h2>
			<?php if($book_id!='' && $book_id!='0'){ ?>
			<h2 class="pull-right">
			<a href="#" data-slug="<?php echo $book_id;?>" class="book-vote-link">Love this book! <span data-slug="<?php echo $book_id;?>" class="book-vote-count"><?php echo $book_vote_count;?></span> | &hearts;</a>
			</h2>
			<?php } ?>
			</div>
		</div>
		<div class="span5">
			<h1>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h1>
			<div class="social"></div>
			<?php the_excerpt(); ?>	
			<p><a href="<?php the_permalink(); ?>"><i>read more</i></a></p>
			<div class="small" id="2ae284d637ece070fb33"></div>
			
			<div class="tags">						
				<?php the_tags( '<h5>Tags</h5><ul><li>', '</li><li>', '</li></ul>' ); ?>						
			</div>
		</div>
		<div class="span3">
			<div class="profile">
			<?php
			$author_user = get_the_author(); 
			$author_obj = get_page_by_title( $author_user, OBJECT, 'tidal_contributor' ); 
			$custom_fields = get_post_custom($author_obj->ID);
			$author_data = unserialize($custom_fields['badges'][0]);
			//print_r($author_user);
			$no_of_posts = getNoOfPostsByAuthorId($author_obj->post_name);
			?> 
			<h5 class="hidden">Contributor:</h5>
			<div class="badge-full">
			<div class="badge-addict">
			<?php if($author_data[1]['image']!=''){ ?>
			<img src="<?php echo $author_data[1]['image']; ?>">
			<?php } else{ ?>
			<img src="http://cdna.tid.al/e1990e097fb1829c98ffe5e74df489a2cc01b1d0_100.png">
			<?php } ?>
			</div>
			
			<?php if($custom_fields['profile_image'][0]!=''){ ?>			
			<img src="<?php echo $custom_fields['profile_image'][0]; ?>">
			<?php } else{ ?>
			<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png">
			<?php } ?>
	
			</div>
			<ul class="info">
			<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php the_author(); ?></a></li>
			<?php foreach($custom_fields['blog_name'] as $authorblog => $authorbloginfo){ ?>
			<li><a href="<?php echo $custom_fields['blog_url'][$authorblog]; ?>" target="_blank"><?php echo $authorbloginfo;?></a></li>
			<?php } ?>
			<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php echo $no_of_posts; ?> Posts</a></li>
			</ul>
			</div>
			<div>
			<?php 
			
	
			if($book_id !='' && $book_id !='0'){
			$talking_cnt = TalkingAboutCount($book_info->post_title);
			?>
			<h5 class="hidden">Book:</h5>
			<a href="<?php echo get_permalink($book_id); ?>">
			<img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>" style="width:100%">
			</a>
			<ul class="info">
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_info->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($post_book_author[0]->ID); ?>">By <?php echo $book_info->post_title; ?></a></li>
			<!--  <li><a href="<?php echo get_permalink($post_book_author[0]->ID); ?>">By <?php echo $post_book_author[0]->post_title; ?></a></li> -->
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
			
			</ul>
			<?php } ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php
	endwhile;
	} else {
			echo "No posts found.";
	}
	wp_reset_query();  // Restore global post data stomped by the_post().
	?>
</div>
<div class="span4">
	<div class="floral">
	<h3>More Books by This Author</h3>
	</div>
	<div class="group">
	<?php
		//echo $book_author[0]->post_title;		
		$book_custom_fields = get_post_custom($post->ID);
		//print_r($book_custom_fields['book_author'][0]);
		$query_author_books = new WP_query(array('posts_per_page' => '5','post_type' => 'book', 'meta_key' => 'book_author', 'meta_value'=> $book_custom_fields['book_author'][0]));
		$book_counter=1;
		$main_book_id = $post->ID;
		if ($query_author_books->have_posts()) :
		    while ($query_author_books->have_posts()) : $query_author_books->the_post(); 
			$author_book_custom_fields = get_post_custom($post->ID);
			$book_author = get_field('book_author');
			//print_r($book_author);
			//print_r($author_book_custom_fields);
			if($main_book_id == $post->ID)
			continue;
			
			
			$cover_pic = str_replace("/large/","/medium/",$author_book_custom_fields['cover_photo_url'][0]);
			?>
			<div class="book">
				<a href="<?php the_permalink(); ?>"><img src="<?php echo $cover_pic; ?>"></a>
				<ul class="info">
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<li><a href="<?php echo get_permalink($book_author[0]->ID);?>">By <?php echo $book_author[0]->post_title;?></a></li>
				<li><a href="/book/j-lynn-be-with-me">7 People Talking About</a></li>
				<!--
				<li><a href="/author/j-lynn?view=q-and-a">Q&A with Author</a></li>
				-->
				<?php if($author_book_custom_fields['genre'][0]!='' && $author_book_custom_fields['genre'][0]!='null'){ ?>
				<li><a href="/books/genre/1?genre=<?php echo $author_book_custom_fields['genre'][0]; ?>">See more in <?php echo ucfirst($author_book_custom_fields['genre'][0]); ?></a></li>
				<?php } ?>
				
				</ul>
			</div>		
		<?php 
		 $book_counter++;
		 endwhile;
		else : ?>
		<div class="no-posts">Sorry, There are currently no Books. Check back in soon.</div>
		<?php endif; 		
		//print_r($wpdb->queries);
		wp_reset_postdata();
		?>	
	</div>
	<?php get_sidebar();?>
</div>

</div>
<div class="cf"></div>
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php endif; ?>

<?php get_footer(); ?>
