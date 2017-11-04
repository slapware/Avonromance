<?php
/* Template name: Sweepstake */
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
<script>
$(document).ready(function() {
	$("#frm_field_15_container input").val($("#sweeptitle").text());
	
	$("#frm_field_16_container input[type='checkbox']").prop('checked', true);

});
</script>
<div class="span8 sweepstakes">
<div class="floral">
<h3>Enter To Win</h3>
</div>
<?php 
		$query_webinars = new WP_query(array(
		'posts_per_page' => '1',
		'post_type' => 'sweepstake',
		'meta_query' => array(
			array(
				'key' => 'expiry_date',
				'value' => date('Ymd'),
				'compare' => '>=',
				'type' => 'DATE'
			)
		)
		)
		);
       
		if ($query_webinars->have_posts()) :
		while ($query_webinars->have_posts()) : $query_webinars->the_post(); 

		$date = DateTime::createFromFormat('Ynd', get_field('expiry_date')); ?>

		  
		<h1 id="sweeptitle"><?php the_title(); ?></h1><br/>
		<?php the_content(); ?>
		<?php echo FrmFormsController::show_form(3, $key = '', $title=false, $description=false); ?>
		 

		<?php endwhile;

		else : ?>
		  <div class="no-posts">Sorry There are currently no Sweepstakes. Check back in soon.</div>
		
		<?php endif; 
		wp_reset_postdata(); ?>	
				
</div>


<div class="span4">
<?php get_sidebar(); ?>
</div>
<div class="cf"></div>

<?php get_footer(); ?>
