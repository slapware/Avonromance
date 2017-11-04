<div class="group signup">
<div class="floral <?php if($post->post_name == 'books'){ ?>bottom <?php } ?>">
<h3>Submit to your Desire</h3>
</div>
<h2>Submit your manuscript to the Avon Editorial Team</h2>
<a class="bubble" href="/impulse">Click here to get started &#8594;</a>
</div>

<div class="group signup">
<div class="floral ">
<h3>Become a Contributor</h3>
</div>
<h2>Are you a passionate romance fan with a blog? Sign up now to contribute to the Avon Romance community.</h2>
<a class="bubble" href="/signup">Contribute</a>
<a style="margin:0;" class="bubble" href="/contribute">Already a member? Login here</a>
</div>
<?php if((is_front_page())){
$mostbelovedstroyresult = getMostBelovedStory();
if($mostbelovedstroyresult[0]->ID !='' && $mostbelovedstroyresult[0]->ID != '0'){
$mostbelovedstroy = get_post($mostbelovedstroyresult[0]->ID);

$mostloved_custom_fields = get_post_custom($mostbelovedstroyresult[0]->ID);
$sidebar_mostloved_author_slug = $mostloved_custom_fields['tidal_contributor'][0];

$args=array(
'name' => $sidebar_mostloved_author_slug,
'post_type' => 'tidal_contributor',
'post_status' => 'publish',
'numberposts' => 1
);
$mostloved_author_posts = get_posts($args);
		
		
?>
<style>
.most-beloved-book .bubble {
display: inline-block;
font-size: 12px;
margin-top: 10px;
padding: 6px 9px;
}
.most-beloved-book .author {
font-size:16px;
}
.most-beloved-book a:hover {
color:#ff286f;
}
</style>

<div class="group most-beloved-book">
<div class="floral">
<h3>Most Beloved Story</h3>
</div>
<div class="entry">
<h2 class="title">
<a href="/<?php echo $mostbelovedstroy->post_name;?>"><?php echo $mostbelovedstroy->post_title;?></a>
</h2>
<h3 class="author">
by <a href="/tidal_contributor/<?php echo $mostloved_author_posts[0]->post_name; ?>"><?php echo $mostloved_author_posts[0]->post_title; ?></a>
</h3>
<p>on <?php echo date('M d, Y', strtotime($mostbelovedstroyresult[0]->post_date));?></p>
<div class="bubble">
&hearts; <?php echo $mostbelovedstroyresult[0]->loves;?></div>
</div>
</div>
<?php } } ?>

<div class="group">
<div class="floral">
<h3>Avon Romance on Twitter</h3>
</div>
<a data-widget-id="344876794454872064" href="https://twitter.com/avonbooks" class="twitter-timeline" width="300">Tweets by @avonbooks</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>

<div class="group newsletter">
<div class="floral-top">
<h3>From the Heart</h3>
</div>
<iframe class="hidden" name="newsletteriframe"></iframe>
<div class="signup-form">
<p>Sign me up to receive news featuring the best of contemporary woman's fiction and romance.</p>
<form action="http://services.harpercollins.com/widgets/subscription/subscribe.aspx" method="post" target="newsletteriframe">
<label>E-mail address:</label>
<input type="email" required="required" name="Email">
<label>Date of Birth:</label><br>
<select class="month required" required="required">
<option value="month">Month</option>
<?php for($m=0;$m<=11;$m++){?>
<option value="<?php echo $m;?>"><?php echo date('F', mktime(0, 0, 0, $m+1, 1));?></option>
<?php } ?>
</select>
<select class="day required" required="required">
<option value="day">Day</option>
<?php for($i=1;$i<=31;$i++){?>
<option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php } ?>
</select>
<select class="year required" required="required">
<option value="year">Year</option>
<?php for($i=date('Y')-2;$i>=1900;$i--){?>
<option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php } ?>
</select>
<div class="parent-email hidden">
<label>Parent E-mail address:</label>
<input type="text" name="ParentEmail">
</div>
<input type="hidden" value="1" name="WidgetId">
<input type="hidden" value="2" name="SiteId">
<input type="hidden" value="nl" name="ProgramType">
<input type="hidden" value="n11" name="ProgramValue">
<input type="hidden" value="default" name="DesignName">
<input type="hidden" value="http%3A%2F%2Fwww.avonromance.com" name="SourceLoc">
<input type="hidden" value="Tidal" name="MID">
<input type="submit" value="Submit">
</form>
</div>
</div>