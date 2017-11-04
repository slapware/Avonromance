<?php get_header(); ?>
<div class="featured-content">
	<div class="span8 images">
		<?php
		$catname = 'post';
		$query_posts = new WP_Query();
		$query_posts->query('category_name=Uncategorized&post_type='.$catname.'&orderby=date&order=DESC');
		//echo $query_posts->request;
		
		$cnt = 0;
		$navigation_links = '';
		$right_block = '';
		$slide_count=0;
		if ($query_posts->have_posts()) :
		    while ($query_posts->have_posts()) : $query_posts->the_post(); 
			
			$book_tag_info = getBooksByPostTags($post->ID);	
			$book_id = $book_tag_info['book_id'];
			
			$book_info = get_post($book_id);
			$book_author = get_field('book_author', $book_id);
			//print_r($book_info);
			$book_custom_fields = get_post_custom($book_id);
			//print_r($book_info);
			//print_r($book_custom_fields['cover_photo_url']);
			print_r($book_custom_fields);
			echo "-----------------------";
			
			if($book_custom_fields['cover_photo_url'][0] != '')
			{
				$slide_count++;
			}
			else
			continue;
			
			if($slide_count > 8)
			break;
			
			$author_user = get_the_author(); 
			$author_obj = get_page_by_title( $author_user, OBJECT, 'tidal_contributor' ); 
			
			$custom_fields = get_post_custom($author_obj->ID);					
			$author_data = unserialize($custom_fields['badges'][0]);
					
			$nav_class = '';
			$right_class = 'hidden';
			if($cnt == 0){
			$class = 'previous first';
			}
			else if($cnt == 1){
			$class = 'active';
			$nav_class = 'active';
			$right_class = 'active';
			}
			else if($cnt == 2){
			$class = 'next';
			}
			else
			$class = 'hidden';
			
			$blog_url = '';
			
			if(count($custom_fields['blog_name'])>0){
			$blog_url = '<a href="'.$custom_fields['blog_url'][0].'" target="_blank">'.$custom_fields['blog_name'][0].'</a>';
			}
			
			$post_content = strip_tags(substr(get_the_excerpt(),0,250))."...";
			
			$navigation_links .= '<a href="#" id="'.$book_info->post_name.'" class="'.$nav_class.'">&#8226;</a>';
			
			$right_block .= '<div class="floral bottom book-info '.$right_class.'" id="'.$book_info->post_name.'"><h2>'.$book_info->post_title.'</h2><h3 class="author"><a href="'.get_permalink($book_author[0]->ID).'">'.$book_author[0]->post_title.'</a></h3><p class="caps"><a href="/tidal_contributor/'.$author_obj->post_name.'">Review by '.get_the_author().'</a></p><p class="caps pink margin-bottom">'.$blog_url.'</p><p class="margin-bottom">'.$post_content.'</p><p class="pink"><a href="'.get_permalink().'"><i>read more</i></a></p></div>';
			
		?>
			
		<div class="image <?php echo $class;?> ">
			<a data-href="<?php echo get_permalink(); ?>" href="<?php echo $book_info->post_name; ?>" id="<?php echo $book_info->post_name; ?>">
			<img src="<?php print $book_custom_fields['cover_photo_url'][0]; ?>"></a>
		</div>
		<?php
		$cnt++;
		endwhile;
		else : ?>
		<div class="no-posts">Sorry, There are currently no latest posts. Check back in soon.</div>
		<?php endif; ?>	
		<div class="featured-navigation">
			<?php echo $navigation_links;?>
		</div>
		<div class="featured-arrows">
			<a href="#" class="featured-next">&#8250;</a>
			<a href="#" class="featured-previous">&#8249;</a>
		</div>
	</div>
	
	<div class="span4 info">
		<div style="height:auto" class="floral bottom">
		<h3 class="caps">Passionate Reviewers'</h3>
		<h2 class="caps">Top Romance Books</h2>
		</div>		
		<?php print $right_block;?>		
	</div>

</div>
<div class="nav-fixxer"></div>
<div class="span8">
	<div class="floral">
	<h3>Latest Posts &amp; Book Reviews</h3>
	</div>
	
	<?php
	$catname = 'post';
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 2;
	$my_query = new WP_Query();
	//$my_query->query('post_type='.$catname.'&showposts=9&orderby=date&order=DESC');
	$my_query->query('category_name=Uncategorized&post_type='.$catname.'&showposts=9'.'&paged='.$paged.'&orderby=date&order=DESC');
		
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
			
	
			if($book_id !='' && $book_id !='0'){ ?>
			<h5 class="hidden">Book:</h5>
			<a href="<?php echo get_permalink($book_id); ?>">
			<img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>" style="width:100%">
			</a>
			<ul class="info">
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_info->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($post_book_author[0]->ID); ?>; ?>">By <?php echo $post_book_author[0]->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_tag_count;?> People Talking About</a></li>
			
			</ul>
			<?php } ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php
	endwhile;
	} 
	wp_reset_query();  // Restore global post data stomped by the_post().
	?>
	<div class="clear"></div>
	<h2 class="next-page"><a href="/posts/page/2">Next &raquo;</a></h2>
	<div class="clear"></div>
</div>

<div class="span4">
	<style>
	.avon-stars .profile {
	float:none;
	margin:0;
	padding:10px 0;
	width:100%;
	}
	.avon-stars .profile:hover {
	cursor:pointer;
	}
	.avon-stars .badge-full {
	margin:0 auto;
	position:relative;
	width:180px;
	}
	.avon-stars .badge-full:before {
	background:url(http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/avon-stars-crown.png) top center no-repeat;
	content:"";
	height:30px;
	left:0;
	position:absolute;
	top:2px;
	width:90px;
	z-index:1000;
	}
	.avon-stars .badge-full .user-image {
	height:90px;
	overflow:hidden;
	position:relative;
	width:90px;
	}
	.avon-stars .badge-full .user-image img {
	float:none;
	position:relative;
	}
	.avon-stars .badge-addict {
	float:left;
	position:relative;
	}
	.avon-stars .badge-addict:before {
	left:89px;
	z-index:1000;
	}
	.avon-stars h2 {
	color:#f67fa7;
	font-size:13px;
	font-family:Arial;
	font-weight: bold;
	line-height: 1.1em;
	padding:5px 0 0;
	text-align:center;
	text-transform: uppercase;
	}
	</style>
	<script type="text/javascript">
	$(document).ready(function() {
	$('.avon-stars .profile').click(function() {
	var url = $(this).find('a.user-link').attr('href');
	window.location = url;
	return false;
	});
	});
	</script>
	
	<div class="group avon-stars">
		<div class="floral">
		<h3>Our Top Contributors</h3>
		</div>
		<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/avon-stars-header-revised2.png" style="marign-top:-20px;margin-bottom:-5px;border-bottom:1px solid #999;">
		<?php
		$stars = getAvonStars();
		//print_r($stars);
		foreach($stars as $star){
			$the_slug = $star->username;
			$args=array(
			'name' => $the_slug,
			'post_type' => 'tidal_contributor',
			'post_status' => 'publish',
			'numberposts' => 1
			);
			$star_post = get_posts($args);
			//print_r($star_post);
			
			$custom_fields = get_post_custom($star_post[0]->ID);
			$author_data = unserialize($custom_fields['badges'][0]);
			?>
			<div class="profile">
				<div class="badge-full">
					<div class="badge-addict">
					<?php if($author_data[1]['image']!=''){ ?>
						<img src="<?php echo $author_data[1]['image']; ?>">
						<?php } else{ ?>
						<img src="http://cdna.tid.al/e1990e097fb1829c98ffe5e74df489a2cc01b1d0_100.png">
						<?php } ?>						
					</div>
					<div class="user-image">
						<img src="<?php echo $custom_fields['profile_image'][0]; ?>" class="cover" style="width: auto; max-width: none; height: 100%; max-height: none; position: relative; left: 0px;">
					</div>
					<a class="hidden user-link" href="/tidal_contributor/<?php echo $the_slug; ?>">View Profile</a>
				</div>
				<h2><a href="#"><?php echo $star_post[0]->post_title; ?></a></h2>
				<div class="cf"></div>
			</div>
		<?php
		}
		?>
	</div>
	<?php get_sidebar(); ?>
</div>
<div class="cf"></div>
<?php get_footer(); ?>