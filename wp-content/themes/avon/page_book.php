<?php
/* Template name: Book */
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
<div class="content floral-top">
	<div class="span8">
		<div class="group">
			<div class="floral bottom">
			<h4 class="label">Avon Books</h4>
			<ul class="dropdown-parent">
			<?php
				$sort_type = $_GET['q'];
				$view = $_GET['view'];
			?>
				<li><a <?php if($sort_type == ''){?>class="active"<?php } ?> href="/books">By Date</a></li>
				<li><a <?php if($sort_type == 'author'){?>class="active"<?php } ?> href="/books?q=author">By Author</a></li>
				<li class="dropdown">
				<a <?php if($sort_type == 'genre'){?>class="active"<?php } ?> href="/books?q=genre">By Genre</a>
				<ul>
					<li><a href="/books?q=genre&view=historical">Historical</a></li>
					<li><a href="/books?q=genre&view=contemporary">Contemporary</a></li>
					<li><a href="/books?q=genre&view=paranormal">Paranormal</a></li>
					<li><a href="/books?q=genre&view=suspense">Suspense</a></li>
					<li><a href="/books?q=genre&view=anthologies">Anthologies</a></li>
					<li><a href="/books?q=genre&view=erotica">Erotica</a></li>
					<li><a href="/books?q=genre&view=romance">Romance</a></li>
				</ul>
				</li>
<!--				<li class="dropdown">
				<a <?php if($sort_type == 'ebooks'){?>class="active"<?php } ?> href="/books?q=ebooks">Ebooks</a>
				<ul>
					<li><a href="/books?q=ebooks">Originals</a></li>
					<li><a href="/books?q=ebooks&view=lowprice">Deals</a></li>
				</ul>
				</li> -->
			</ul>
			</div>
		</div>
		<?php
		
		
		if($sort_type == ''){
		$args = array(
		'numberposts' => -1,
		'post_type' => 'book',
		'post_parent' => 0,
		'showposts' => 9,
		'paged' => $paged,
		'orderby' => 'date',
		'order' => 'DESC'
		);
		}
		
		if($sort_type == 'author'){
			

			$book_ids = getBookIdsSortByAuthor();
			
			foreach($book_ids as $book_id){
			$books_arr[] = $book_id->ID;
			}
			
			$args = array(
			'numberposts' => -1,
			'showposts' => 9,
			'post_type' => 'book',
			'post_parent' => 0,
			'paged' => $paged,
			'post__in' => $books_arr,
			'orderby' => 'post__in'
			);
			

		}
		if($sort_type == 'genre' && $view == ''){
			$args = array(
			'numberposts' => -1,
			'showposts' => 10,
			'post_type' => 'book',
			'paged' => $paged,
			'meta_key' => 'genre',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			);
		}
// 		if($sort_type == 'genre' && $view != ''){
// 			$args = array(
// 			'numberposts' => -1,
// 			'showposts' => 10,
// 			'post_type' => 'post',
// 			'paged' => $paged,
// 			'meta_key' => 'genre',
// 			'meta_value' => $view,
// 			'orderby' => 'meta_value',
// 			'order' => 'DESC',
// 			);
// 		}
// // NOTE: Modified Stephen La Pierre 11/26/14. At 11:51 AM GENRE FIX
		if($sort_type == 'genre' && $view != ''){
			$genre = $view;
			$args = array(
			'numberposts' => -1,
			'showposts' => 10,
			'post_type' => 'book',
			'paged' => $paged,
			'meta_query' => array(
				array(
					'key' => 'Genre',
					'value' => $view,
					'compare' => 'LIKE',
				),
			),
			'order' => 'DESC',
			);
		}
		
		if($sort_type == 'ebooks' && $view == ''){
			$today = date('Ymd');
			$args = array(
			'numberposts' => -1,
			'showposts' => 10,
			'post_type' => 'book',
			'paged' => $paged,
			'meta_query' => array(
				array(
					'key' => 'isbn_code_for_ebook',
					'value' => '',
					'compare' => '!=',
				),
				array(
					'key' => 'editorial_imprint',
					'value' => 'Avon Impulse',
					'compare' => '==',
				),
				array(
					'key' => 'sale_date',
					'value' => $today,
					'compare' => '>',
				)
			),
			'meta_key' => 'sale_date',
			'orderby' => 'meta_value',
			'order' => 'DESC',
			);
		}
		
		if($sort_type == 'ebooks' && $view == 'lowprice'){
			$today = date('Ymd');
			$args = array(
			'numberposts' => -1,
			'showposts' => 10,
			'post_type' => 'book',
			'paged' => $paged,
			'meta_query' => array(
				array(
					'key' => 'isbn_code_for_ebook',
					'value' => '',
					'compare' => '!=',
				),
				array(
					'key' => 'editorial_imprint',
					'value' => 'Avon Impulse',
					'compare' => '!=',
				),
				array(
					'key' => 'ebook_price',
					'value' => '2',
					'compare' => '<',
				),
				array(
					'key' => 'sale_date',
					'value' => $today,
					'compare' => '>',
				)
			),
			'meta_key' => 'sale_date',
			'orderby' => 'meta_value',
			'order' => 'DESC',
			);
		}
		
		//print_r($args);
		// get results
		$query_books = new WP_Query( $args );
//		echo $GLOBALS['wp_query']->request;
//		echo "<BR>" . var_dump($args);
	
		
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$catname = 'book';
		//$query_books = new WP_Query();
		//$query_books->query('post_type='.$catname.'&showposts=10'.'&paged='.$paged.'&orderby=date&order=DESC');
		
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
		    // no isbn CHECK
// 		    $posttags = get_the_tags($postID);
// 			if ($posttags) {
// 			var_dump($posttags);
// 			}å
//			echo "HERE IN BOOKS";
//			echo $query_books->post->ID;
			$pbase1 = get_the_permalink();

			$book_custom_fields = get_post_custom($query_books->post->ID);
			$book_author = get_field('book_author');
//			echo $query_books->post->ID;
			$talking_cnt = TalkingAboutCount(get_the_title($query_books->post->ID));
			$bookauthor = getAuthorName($query_books->post->ID);
			if (strlen($bookauthor) < 1)
//  				echo "author missing for " . $query_books->post->ID;
			$posttags = get_the_tags($query_books->post->ID);
			$cover = getStaticCover($query_books->post->ID);
//			var_dump($book_custom_fields);
			if(strlen($cover) < 13)
				$cover = $book_custom_fields['cover_photo_url'][0];
			if(strlen($cover) < 13) {
				$covert =  unserialize($book_custom_fields['images'][0]);
				$cover = $covert[0]['url'];
				}

			
			if($cover !='' && isset($cover)){ 		
			$title = get_the_title($query_books->post->ID);
		?>
		<div class="book <?php if($book_counter%3 == 0){echo "last";}?>">
<!--			<a href="<?php the_permalink(); ?>"><img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>"></a>  -->
			<a href="<?php echo $pbase1; ?>"><img class="book-180" src="<?php echo $cover; ?>"></a>
			<ul class="info">
				<li><a href="<?php echo $pbase1; ?>"><?php echo get_the_title($query_books->post->ID);?></a></li>
				<li><a href="<?php echo get_permalink(getAuthorID($query_books->post->ID)); ?>"><?php echo $bookauthor; ?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
				<?php if($genre !='' && $genre !='null'){ ?>
<!--				<li><a href="/books/genre/1?genre=<?php echo $genre; ?>">See more in <?php echo ucfirst($genre); ?></a></li> -->
				<li><a href="/books/genre/1?genre=<?php echo $genre; ?>">See more in <?php echo ucfirst($genre); ?></a></li>
				<?php } ?>
			</ul>
		</div>
		 <?php 
		 $book_counter++;
		 }
		 endwhile;
		else : ?>
		<div class="no-posts">Sorry, There are currently no Books. Check back in soon.</div>
		<?php endif; ?>	
		<div class="clear"></div>
		<h2 class="prev-page"><?php previous_posts_link('&laquo; Previous') ?></h2>
		<h2 class="next-page"><?php next_posts_link('Next &raquo;', $query_books->max_num_pages) ?></h2>
		<div class="clear"></div>
		<?php wp_reset_postdata(); ?>
	</div>
	<div class="span4">
	<?php get_sidebar();?>
	</div>	
</div>
<div class="cf"></div>
<?php get_footer(); ?>
