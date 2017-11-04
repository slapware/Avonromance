<?php
/*
Template Name: Impulse Static
*/

get_header(); ?>
<div class="floral mega">
<h2>Avon Impulse</h2>
</div>

<div class="avon-rom">
<?php get_sidebar('impulse') ?>

    <div class="arcontent">

        <h2><?php echo apply_filters( 'the_title', get_the_title( get_option( 'page_for_posts' ) ) ); ?></h2>
        <div class="soci">
            <div class="addthis_toolbox addthis_default_style ">
                <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                <a class="addthis_button_tweet"></a>
                <a class="addthis_button_email"></a>
            </div>
            <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
            <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
        </div>

        <div class="contentxt">
            <?php echo apply_filters( 'the_content', get_post_field( 'post_content', get_option( 'page_for_posts' ) ) ); ?>
        </div>
    </div>
</div>
<div class="cf"></div>
<?php get_footer(); ?>