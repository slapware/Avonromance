<?php
/* Template name: Sitemap */
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
<div class="floral">
<h4><?php the_title();?></h4>
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
$query_authors = new WP_Query();
$query_authors->query('post_type=book-author&posts_per_page=-1&orderby=title&order=ASC');

if ($query_authors->have_posts()) :
echo "<h2>Authors</h2>";
		    while ($query_authors->have_posts()) : $query_authors->the_post(); 
?>
<p><a title="<?php the_title();?>" href="<?php the_permalink();?>" class="author-link"><?php the_title();?></a></p>
<?php endwhile; endif;?>



<?php 
$query_books = new WP_Query();
$query_books->query('post_type=book&posts_per_page=-1&orderby=title&order=ASC');

if ($query_books->have_posts()) :
echo "<h2>Books</h2>";
		    while ($query_books->have_posts()) : $query_books->the_post(); 
?>
<p><a title="<?php the_title();?>" href="<?php the_permalink();?>" class="author-link"><?php the_title();?></a></p>
<?php endwhile; endif;?>

</div>
<div class="span4">
	<?php get_sidebar();?>
	</div>
<div class="cf"></div>
<?php get_footer(); ?>
