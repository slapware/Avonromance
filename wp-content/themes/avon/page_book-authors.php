<?php
/* Template name: Book Authors */
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
 * BOOK SINGLE AUTHOR PAGE
 */

get_header(); ?>
<div class="content community">
	<div class="span8">
		<div class="floral">
			<h4 class="full-center">Authors</h4>
		</div>
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		//$query_books = new WP_query(array('posts_per_page' => '1','post_type' => 'book', 'paged'=>$paged));
		$catname = 'book-author';
//		$catname = 'author';
		$query_books = new WP_Query();
		$query_books->query('post_type='.$catname.'&showposts=21'.'&paged='.$paged.'&orderby=post_title&order=ASC');
//		$query_books->query('post_type='.$catname. '&paged='.$paged.'&orderby=post_title&order=ASC');
//		echo "Book Author";
		
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_the_title();
			// Modified Stephen La Pierre 1/31/15. At 1:02 PM
			$authorid = get_field('book_author');
//			$authorgid = get_field('Author_GID');
			$book_author = getAuthorNameByID((int)$authorid[0]);
			$pbase1 = get_the_permalink();
//			echo $pbase1;
//			echo "Here I Am";

//			var_dump(the_title());
//			var_dump( $authorid);
//			echo "Here I am";
			
// 			$find = "Author: " . $book_author;
//			$no_of_books = getNoOfBooksByAuthorId((int)$authorid[0]);
			$authGid = getAuthorGID($post->ID);
			$no_of_books = getNoOfBooksByAuthorGID($authGid);
		?>
		<div class="profile <?php if($book_counter%3 == 0){echo "last";}?>">
			<a href="<?php the_permalink(); ?>" class="image">
			<?php if($book_custom_fields['profile_image'][0] != '') { ?>
				<img src="<?php echo $book_custom_fields['profile_image'][0]; ?>">
				<?php } else { ?>
				<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png"><?php } ?>
				<div class="badge-author"></div>
			</a>
			<ul class="info">
				<li><a href="<?php echo $pbase1; ?>"><?php echo $book_author;?></a></li> <!-- from the_title -->
				<li><a href="<?php echo $pbase1; ?>?view=books">View Books</a></li>
				<li><a href="<?php the_permalink(); ?>?view=books"><?php echo $no_of_books;?> Books</a></li>				
			</ul>
		</div>
		 <?php 
		 $book_counter++;
		 endwhile;
		else :
		endif; ?>
		<div class="clear"></div>
		<h2 class="prev-page"><?php previous_posts_link('&laquo; Previous') ?></h2>
		<h2 class="next-page"><?php next_posts_link('Next &raquo;', 75) ?></h2>
		<div class="clear"></div>
		<?php wp_reset_query(); wp_reset_postdata(); ?>
	</div>
	<div class="span4">
	<?php get_sidebar();?>
	</div>	
</div>
<div class="cf"></div>
<?php get_footer(); ?>
