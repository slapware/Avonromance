<?php
/* Template name: Share Your Book */
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

get_header(); ?>

	<div class="span8">		
	<div style="text-transform:none;" class="floral">
	<h1 style="text-align:center;">What is everyone writing?</h1>
	<br>
	<p style="margin-bottom:10px">Any Avon Romance fan can post a writing sample below. Fans and writers alike: read the submissions below, vote on your favorites and offer feedback.</p>
	<p>Want to start a conversation? <a href="/contribute">Click here</a> to submit your own book.</p>
	<p>Questions? Email us at <a href="mailto:avon-romance@tid.al">avon-romance@tid.al</a></p>
	<br>
	<p>Check out <a href="/shareyourbook?sort=hot">this week's</a> or <a href="/shareyourbook?sort=top">this month's</a> hottest posts.</p>
	</div>
	
	<?php
	
	$view = $_GET['sort'];
			
	$catname = 'post';
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	function hot_filter_where( $where = '' ) {
		
		$start_date = date("Y-m-d",strtotime('-7 days'));
		$end_date = date("Y-m-d");
		
		$where .= " AND post_date >= '$start_date' AND post_date <= '$end_date'";
		return $where;
	}
	
	function top_filter_where( $where = '') {
		
		$start_date = date('Y-m-01',strtotime('this month'));
		$end_date = date('Y-m-t',strtotime('this month'));
		
		$where .= " AND post_date >= '$start_date' AND post_date <= '$end_date'";
		return $where;
	}


	if($view == 'hot' || $view == 'top'){
		
		$args = array(
			'numberposts' => -1,
			'showposts' => 9,
			'post_type' => $catname,
			'paged' => $paged,
			'category_name' => 'Manuscripts',
			'orderby' => 'post_date',
			'order' => 'ASC'
			);
			if($view == 'hot'){
				add_filter( 'posts_where', 'hot_filter_where' );
				$my_query = new WP_Query($args);
				remove_filter( 'posts_where', 'hot_filter_where' );
			}
			else{
				add_filter( 'posts_where', 'top_filter_where' );
				$my_query = new WP_Query($args);
				remove_filter( 'posts_where', 'top_filter_where' );
			}
			
			//$posts_arr = new array();
			$i=0;
			while ($my_query->have_posts()) : $my_query->the_post();
			$posts_arr[$i]['ID'] = $post->ID;
			$posts_arr[$i]['votes'] = getvotescount($post->ID);
			$i++;
			endwhile;
			
			usort($posts_arr, function($a, $b) {
				return $b['votes'] - $a['votes'];
			});
			
			
			foreach($posts_arr as $mpost_id){
			$mposts_arr[] = $mpost_id['ID'];
			}
			
			$args = array(
			'numberposts' => -1,
			'showposts' => 9,
			'post_type' => $catname,
			'paged' => $paged,
			'category_name' => 'Manuscripts',
			'post__in' => $mposts_arr,
			'orderby' => 'post__in'			
			);
			
			$my_query = new WP_Query($args);
			
		}else{
			$args = array(
			'numberposts' => -1,
			'showposts' => 9,
			'post_type' => $catname,
			'paged' => $paged,
			'category_name' => 'Manuscripts',
			'orderby' => 'date',
			'order' => 'DESC',
			);
			
			$my_query = new WP_Query($args);
		
		}	
		
	//$my_query->query('category_name=Manuscripts&post_type='.$catname.'&showposts=9'.'&paged='.$paged.'&orderby=date&order=DESC');
		
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
		<div class="span8">
			<h1 style="margin-bottom:0">
				<a href="<?php the_permalink(); ?>?from=your-books"><?php the_title(); ?></a>
			</h1>
			<?php
			$author_user = get_the_author(); 
			$author_obj = get_page_by_title( $author_user, OBJECT, 'tidal_contributor' ); 
			$custom_fields = get_post_custom($author_obj->ID);
			$author_data = unserialize($custom_fields['badges'][0]);
			//print_r($author_user);
			$no_of_posts = getNoOfPostsByAuthorId($author_obj->post_name);
			?> 
			<p style="color:#999;" class="byline">By <?php the_author(); ?></p>
			<div class="social"></div>
			<?php the_excerpt(); ?>	
			<p><a href="<?php the_permalink(); ?>?from=your-books"><i>read more</i></a></p>
			<div class="small" id="2ae284d637ece070fb33"></div>
			
			<div class="tags">						
				<?php the_tags( '<h5>Tags</h5><ul><li>', '</li><li>', '</li></ul>' ); ?>						
			</div>
		</div>		
		<div class="clear"></div>
	</div>
	
	<?php
	endwhile;
	} // Restore global post data stomped by the_post().
	 ?>	
		<div class="clear"></div>
		<h2 class="prev-page"><?php previous_posts_link('&laquo; Previous') ?></h2>
		<h2 class="next-page"><?php next_posts_link('Next &raquo;', $my_query->max_num_pages) ?></h2>
		<div class="clear"></div>
		<?php wp_reset_query(); wp_reset_postdata(); ?>
	</div>
	<div class="span4">
	<?php get_sidebar();?>
	</div>	

<div class="cf"></div>
<?php get_footer(); ?>
