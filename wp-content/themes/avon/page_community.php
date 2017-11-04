<?php
/* Template name: Community */
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
<div class="content community">
	<div class="span8">
		<div class="floral">
		<h4 class="full-center">Authors</h4>
		</div>
		
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		//$query_books = new WP_query(array('posts_per_page' => '1','post_type' => 'book', 'paged'=>$paged));
		$catname = 'book-author';
		$query_books = new WP_Query();
		$query_books->query('post_type='.$catname.'&showposts=6'.'&paged='.$paged.'&orderby=date&order=DESC');
//		global $post;
		
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
//			$book_author = get_the_title();
			$authorid = get_field('book_author');
			$authorgid = get_field('Author_GID');
			$book_author = getAuthorNameByID((int)$authorid[0]);
//			$no_of_books = getNoOfBooksByAuthorId($book_author);
			$no_of_books = getNoOfBooksByAuthorGID($authorgid);
			$pbase1 = get_the_permalink();
//			$pbase2 = str_replace("community", "book-author/", $pbase1);
			$post_author = get_post($query_books->the_post()->ID);
//			var_dump($authorgid);
//			$authslug = $post_author->post_name;
//			$authlink = $pbase2 . $authslug;
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
				<li><a href="<?php echo $pbase1; ?>"><?php echo $book_author;?></a></li> <!-- from the_title() -->
				<li><a href="<?php echo $pbase1; ?>?view=books">View Books</a></li>
				<li><a href="<?php the_permalink(); ?>?view=books"><?php echo $no_of_books;?> Books</a></li>				
			</ul>
		</div>
		<?php if($book_counter%3 == 0){ ?>
		<div class="clear"></div>
		<?php } ?>
		 <?php 
		 $book_counter++;
		 endwhile;
		 ?>
		 <div class="clear"></div>
		 <a class="view-more-link" href="/book-authors">View All Authors &#8250;</a>
		 <?php
			
		else : ?>
		<div class="no-posts">Sorry, There are currently no authors. Check back in soon.</div>
		<?php endif; ?>	

		<div class="clear"></div>
		<div class="floral margin-top">
			<h4 class="full-center">Avon Addicts</h4>
		</div>
		
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$catname = 'tidal_contributor';
		//$query_books = new WP_Query();
		//$query_books->query('post_type='.$catname.'&showposts=6'.'&paged='.$paged.'&orderby=date&order=DESC');
		
		
		$args = array(
		'numberposts' => -1,
		'showposts' => 6,
		'post_type' => 'tidal_contributor',
		'meta_query'  	=> array(
				  array(
					 'key'           => 'badges',
					 'value'         => '"Avon Addict"',//quotes to make sure category 23 does not match category 123, 230 etc
					 'compare'       => 'LIKE'
				  )
			   ),
		'orderby' => 'date',
		'order' => 'DESC'
		);

			// get results
			$query_books = new WP_Query( $args );
		
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_the_title();
			
			$no_of_books = getNoOfPostsByAuthorId($post->post_name);
		?>
		<div class="profile <?php if($book_counter%3 == 0){echo "last";}?>">
			<a href="<?php the_permalink(); ?>" class="image">
			<?php if($book_custom_fields['profile_image'][0] != '') { ?>
				<img src="<?php echo $book_custom_fields['profile_image'][0]; ?>">
				<?php } else { ?>
				<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png"><?php } ?>
				<div class="badge-addict"></div>
			</a>
			<ul class="info">
				<li><a href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo $book_custom_fields['blog_name'][0]; ?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo $no_of_books;?> Posts</a></li>				
			</ul>
		</div>
		<?php if($book_counter%3 == 0){ ?>
		<div class="clear"></div>
		<?php } ?>
		 <?php 
		 $book_counter++;
		 endwhile;
		 ?>
		 <div class="clear"></div>
		 <a class="view-more-link" href="/avon-addicts">View All Avon Addicts &#8250;</a>
		 <?php
			
		else : ?>
		<div class="no-posts">Sorry, There are currently no addicts.</div>
		<?php endif; ?>	
		
		<div class="clear"></div>
		<div class="floral margin-top">
			<h4 class="full-center">More Top Contributors</h4>
		</div>
		
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$catname = 'tidal_contributor';
		//$query_books = new WP_Query();
		//$query_books->query('post_type='.$catname.'&showposts=6'.'&paged='.$paged.'&orderby=date&order=DESC');
		
		
		$args = array(
		'numberposts' => -1,
		'showposts' => 6,
		'post_type' => 'tidal_contributor',
		'meta_query'  	=> array(
				  array(
					 'key'           => 'badges',
					 'value'         => '"Member"',//quotes to make sure category 23 does not match category 123, 230 etc
					 'compare'       => 'LIKE'
				  )
			   ),
		'orderby' => 'date',
		'order' => 'DESC'
		);

			// get results
			$query_books = new WP_Query( $args );
		
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_the_title();
			
			$no_of_books = getNoOfPostsByAuthorId($post->post_name);
			
			//echo $post->ID;
			//echo "ss".$user_post_count = count_user_posts( $post->ID );
		?>
		<div class="profile <?php if($book_counter%3 == 0){echo "last";}?>">
			<a href="<?php the_permalink(); ?>" class="image">
			<?php if($book_custom_fields['profile_image'][0] != '') { ?>
				<img src="<?php echo $book_custom_fields['profile_image'][0]; ?>">
				<?php } else { ?>
				<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png"><?php } ?>
				<div class="badge-member"></div>
			</a>
			<ul class="info">
				<li><a href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo $book_custom_fields['blog_name'][0]; ?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo $no_of_books;?> Posts</a></li>				
			</ul>
		</div>
		<?php if($book_counter%3 == 0){ ?>
		<div class="clear"></div>
		<?php } ?>
		 <?php 
		 $book_counter++;
		 endwhile;
		 ?>
		 <div class="clear"></div>
		 <div class="clear"></div>
		 <a class="view-more-link" href="/avon-users">View All Users &#8250;</a>
		 <?php
			
		else : ?>
		<div class="no-posts">Sorry, There are currently no users.</div>
		<?php endif; ?>	
	</div>
	<div class="span4">
	<?php get_sidebar();?>
	</div>	
</div>
<div class="cf"></div>
<?php get_footer(); ?>
