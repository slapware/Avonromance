<?php /**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
// TODO Move this to the header

get_header(); ?>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52839b1a47fdc9ae"></script>

<div class="content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php
	$post_vote_count = getvotescount($post->ID);
	//$book_id = getBooksByPostTags($post->ID);
	
	$book_tag_info = getBooksByPostTags($post->ID);	
	$book_id = $book_tag_info['book_id'];
	$book_tag_count = $book_tag_info['count'];
	
	$book_info = get_post($book_id);
	$book_author = get_field('book_author', $book_id);
	//print_r($book_info);
	$book_custom_fields = get_post_custom($book_id);
	//print_r($book_custom_fields);
	//var_dump($book_tag_info);
	?>
	<div class="span8 floral-top single">
		<div class="post">
				<div class="group bubble" style="background: none repeat scroll 0% 0% rgb(246, 127, 167);">
					<h2><strong><?php the_time('M d') ?></strong> - Book Review </h2>
					<div class="vote">
					<h2 class="pull-left">
					<a href="#" data-slug="<?php echo $post->ID;?>" class="vote-link">Love this post! <span data-slug="<?php echo $post->ID;?>" class="vote-count"><?php echo $post_vote_count;?></span> | &hearts;</a>
					</h2>
					<h2 class="pull-right">
					<?php if($book_id !='' && $book_id !='0'){
					$book_vote_count = getvotescount($book_id);
					?>
					<a href="#" data-slug="<?php echo $book_id;?>" class="book-vote-link">Love this book! <span data-slug="<?php echo $book_id;?>" class="book-vote-count"><?php echo $book_vote_count;?></span> | &hearts;</a>
					<?php } ?>
					</h2>
					</div>
				</div>
				<div class="span5">
					<h1><?php the_title(); ?></h1>
					<div class="social">						
						<!-- Go to www.addthis.com/dashboard to customize your tools -->
						<div class="addthis_toolbox addthis_default_style"> 
						<a class="addthis_button_tweet"></a> 
						<a class="addthis_counter addthis_pill_style"></a> 
						</div>						
					</div>
					<div class="post-content">
						<?php the_content(); ?>
					</div>
					<div class="small" id="2ae284d637ece070fb33"></div>
					
					<div class="tags">						
						<?php the_tags( '<h5>Tags</h5><ul><li>', '</li><li>', '</li></ul>' ); ?>						
					</div>
					
					<style>
					.comment {
					margin:20px 0;
					padding-bottom:20px;
					border-bottom:1px solid #eee;
					}
					.comment p {
					width:80%;
					}
					textarea.commentbox {
					resize:none;
					padding:10px;
					-webkit-box-sizing:border-box;
					-moz-box-sizing:border-box;
					-ms-box-sizing:border-box;
					-o-box-sizing:border-box;
					box-sizing:border-box;
					margin-bottom:10px;
					width:100%;
					font-family:'PT Serif', Georgia;
					font-size:12px;
					}
					.content input[type="submit"] {
					margin-top:0px;
					}
					</style>
					<br>
					<br>
					<h2 id="comments">Comments:</h2>
					<!--<div class="comment">
					<p><em>Be the first to comment!</em></p>
					</div>
					<textarea disabled="" class="commentbox" placeholder="Connect with Facebook then write your comment here."></textarea>-->
					<!--<span class="pull-right">
					<fb:login-button scope="email,user_birthday,user_location,publish_stream" onlogin="checkLoginState();">Connect with Facebook</fb:login-button>
					</span>-->
					<div class="fb-comments" data-href="<?php the_permalink();?>" data-numposts="5" data-colorscheme="light"></div>
				</div>
				<div class="span3">
				<?php
					$author_user = get_the_author(); 
					$author_obj = get_page_by_title( $author_user, OBJECT, 'tidal_contributor' ); 
					$custom_fields = get_post_custom($author_obj->ID);					
					$author_data = unserialize($custom_fields['badges'][0]);
					//echo "<pre>";print_r($author_data);echo "</pre>";
					//echo "<pre>";$book_custom_fields['cover_photo_url'][0];echo "</pre>";
					//print_r($book_custom_fields);
					
					$no_of_posts = getNoOfPostsByAuthorId($author_obj->post_name);
					?> 
					<div class="profile">
					
					<h5 class="hidden">Contributor:</h5>
					<div class="badge-full">
					<div class="badge-addict">
					<?php if($author_data[0]['image']!=''){ ?>
					<img src="<?php echo $author_data[0]['image']; ?>">
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
					<?php
					if(count($custom_fields['blog_name'])>0){
					foreach($custom_fields['blog_name'] as $authorblog => $authorbloginfo){ ?>
					<li><a href="<?php echo $custom_fields['blog_url'][$authorblog]; ?>" target="_blank"><?php echo $authorbloginfo;?></a></li>
					<?php }} ?>
					<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php echo $no_of_posts; ?> Posts</a></li>
					<!--
					<li><a href="#">32 Comments</a></li>
					-->
					</ul>
					</div>
					<div>
					<!-- <?php if($book_id !='' && $book_id !='0'){ -->
					<?php if($cover !='' && $cover !='0'){ 
					$talking_cnt = TalkingAboutCount($book_info->post_title); ?>
					<h5 class="hidden">Book:</h5>
					<a href="<?php echo get_permalink($book_id); ?>">
					<?php $covert =  unserialize($book_custom_fields['images'][0]);
					$cover = $covert[0][url]; ?>
					<img src="<?php echo $cover; ?>" style="width:100%">

					<!-- <img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>" style="width:100%"> -->
					</a>
					<ul class="info">
					<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_info->post_title; ?></a></li>
					<li><a href="<?php echo get_permalink($book_author[0]->ID); ?>">By <?php echo $book_author[0]->post_title; ?></a></li>
					<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
					
					</ul>
					<?php } ?>
					</div>
				</div>
		</div>
	</div>
	<div class="span4">
	<?php get_sidebar(); ?>
	</div>
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php endif; ?>
</div>
<div class="cf"></div>

<?php get_footer(); ?>
