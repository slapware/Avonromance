<?php get_header(); ?>
<div class="span8">
<div class="floral">
	<h3><?php the_title();?></h3>
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
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php the_title(); the_content(); ?>
<?php endwhile; endif;?>
</div>
<div class="span4">
	<?php get_sidebar();?>
	</div>
<div class="cf"></div>
<?php get_footer(); ?>