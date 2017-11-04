<?php
/*
Template Name: Impulse
*/

global $wpdb, $wp, $current_user, $submission_target, $ok_filetypes, $theme_url;

global $length_options, $genre_options, $subcategory_options, $period_options;

global $form_email;

$impulse_nonce = 'uioqdyUYUYGdwbbwu';

$upload_dir = wp_upload_dir();

$submissions_table = 'submissions';

$submission_target = $upload_dir['basedir'] . '/submissions/';

$ok_filetypes = array(
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'text/plain',
        'application/pdf',
        'application/pdf;',
        'application/rtf',
        'text/rtf',
        'text/rtf;'
);

$max_file_size = sprintf('%s', 3 * pow(10,6));

$response_div = '';

$show_thankyou = false;

$genre_options = array(
        '-1'    => 'Choose one'
);

$genre_string = '';

$i = 1;

$sql = '
        SELECT
                description
        FROM
                bisac_codes
        WHERE
                1';

$sql = @$wpdb -> prepare($sql);

$results = $wpdb -> get_results($sql);

foreach($results as $result){

        if($result -> description == 'Adult'){ continue; }

        $genre_options[$i] = sprintf('%s', $result -> description);

        $i++;

}
$transfer_array = array();

$errors = array();

$fields_whitelist = array(
        'man_title'                     => array(1,'','%s'),
//        'man_email'                     => array(1,'','%s'),
        'man_name'                      => array(1,'','%s'),
        'finished'                      => array(1,'','%s'),
        'published'                     => array(1,'','%s'),
        'length'                        => array(1,'','%d'),
        'genre'                         => array(1,'','%d'),
        'subcategory'           => array(1,'','%d'),
        'period'                        => array(1,'','%d'),
        'submission_request'=> array(0,'','%s'),
        'synopsis'                      => array(1,'','%s'),
        'best_scene'            => array(1,'','%s'),
        'wp_nonce'                      => array(1,'','%s'),
        'query_letter'          => array(0,'','%s')
);

$fields_blacklist = array('wp_nonce', 'manuscript_file');

foreach($fields_whitelist as $key => $value_array){

        if(isset($_POST[$key]) && !empty($_POST[$key])){

                if(in_array($key, array('finished','published'))){

                        if(preg_match('/yes$/', $_POST[$key])){

                                $transfer_array[$key] = 1;

                        }else{

                                $transfer_array[$key] = 0;

                        }

                }else{

                        $transfer_array[$key] = sprintf($fields_whitelist[$key][2], $_POST[$key]);

                }

        }else{

                $transfer_array[$key] = $value_array[1];

                if($value_array[0] == 1){

                        $errors[$key] = 'The '.$key.' field must be set';

                }

        }

}

function add_buddypress_activity($submission_title){

        global $current_user;

        if(function_exists('bp_activity_add')){

                $user_url = bp_core_get_user_domain($current_user -> ID);

                $user_name = bp_core_get_user_displayname($current_user -> ID);

                if($user_name == $current_user -> user_login){

                        if(!empty($current_user -> nickname)){

                                $user_name = $current_user -> nickname;

                        } else if(!empty($current_user -> display_name)){

                                $user_name = $current_user -> display_name;

                        }

                }

                $user_link = '<a href="'.$user_url.'">'.$user_name.'</a>';

                $activity = array(
                        'user_id'               => $current_user -> ID,
                        'item_id'               => $current_user -> ID,
                        'component'             => 'submissions',
                        'primary_link'  => $user_url,
                        'type'                  => 'submission_added',
                        'action'                => $user_link.' submitted the manuscript "'.$submission_title.'"'
                );

                bp_activity_add($activity);

                return true;

        }

        return false;

}

function notify_via_email($transfer_array){

        notify_impulse_admin($transfer_array);

        notify_impulse_user($transfer_array);

}

function notify_impulse_admin($transfer_array){

        global $current_user, $theme_url;

//        wp_mail('bryan.owens@harpercollins.com', 'here' . $transfer_array['man_email'], 'goes' . $form_email);

        global $length_options, $genre_options, $subcategory_options, $period_options;

        $impulse_admin = array(
                //'email_address' => 'debug@gmail.com',
                'email_address' => 'impulse@harpercollins.com',
                'name'                  => 'Impulse'
        );

        $manuscript_link = make_manuscript_link($transfer_array['insert_id'], true);

        $subject_template = file_get_contents(ABSPATH.'wp-content/themes/avon/includes/impulse/template.admin_subject.txt');

        $body_template = file_get_contents(ABSPATH.'wp-content/themes/avon/includes/impulse/template.admin_body.txt');

        $replace_array = array(
                '%%%date%%%'                                    => date("Y-m-d H:i:s"),
                '%%%username%%%'                                => $transfer_array['man_name'],//$current_user -> user_login,
                '%%%email%%%'                                   => $_POST["man_email"],
                '%%%name%%%'                                    => $transfer_array['man_name'],//$current_user -> user_firstname.' '.$current_user -> user_lastname,
                '%%%man_title%%%'                               => $transfer_array['man_title'],
                '%%%manuscript_link%%%'                 => $manuscript_link,
                '%%%finished%%%'                                => $transfer_array['finished']?'yes':'no',
                '%%%published%%%'                               => $transfer_array['published']?'yes':'no',
                '%%%preferred_author_name%%%'   => $transfer_array['man_name'],
                '%%%length%%%'                                  => $length_options[$transfer_array['length']],
                '%%%genre%%%'                                   => $genre_options[$transfer_array['genre']],
                '%%%subcategory%%%'                             => $subcategory_options[$transfer_array['subcategory']],
                '%%%period%%%'                                  => $period_options[$transfer_array['period']],
                '%%%submission_request%%%'              => $transfer_array['submission_request'],
                '%%%synopsis%%%'                                => $transfer_array['synopsis'],
                '%%%scene%%%'                                   => $transfer_array['best_scene'],
                '%%%query_letter%%%'                    => $transfer_array['query_letter']
        );

        $subject = str_replace(array_keys($replace_array), array_values($replace_array), $subject_template);

        $body = str_replace(array_keys($replace_array), array_values($replace_array), $body_template);

        $submitter_subject = "Thank you for your manuscript submission!";
        $submitter_body = "Thank you for your submission to Avon Impulse. You can expect to hear back from us within eight to twelve weeks, however, due to the amount of submissions we receive, we are unable to respond personally to each query. Thank you again for your interest in Avon Impulse.";

        wp_mail($impulse_admin['email_address'], $subject, $body);
        wp_mail($_POST["man_email"], $submitter_subject, $submitter_body);

}

function make_manuscript_link($insert_id, $web_path = false){

        global $submission_target;

        if(!$web_path){

                return $submission_target.$insert_id.'.manuscript';

        }

        return get_template_directory_uri().'/includes/impulse/view_submission.php?sid='.$insert_id.'&ahash='.SUBMISSIONS_ADMIN_HASH;

}

function notify_impulse_user($transfer_array){

        global $current_user;

        $subject_template = file_get_contents(ABSPATH.'wp-content/themes/avon/includes/impulse/template.user_subject.txt');

        $body_template = file_get_contents(ABSPATH.'wp-content/themes/avon/includes/impulse/template.user_body.txt');

        $replace_array = array(

        );

        $subject = str_replace(array_keys($replace_array), array_values($replace_array), $subject_template);

        $body = str_replace(array_keys($replace_array), array_values($replace_array), $body_template);

        wp_mail($current_user -> user_email, $subject, $body);

}

function get_file_type($file) {
        $dump = shell_exec(sprintf('file -bi %s', $file));
        $info = explode(' ', $dump);
        return chop($info[0]);
}

function upload_submission($insert_id){

        global $ok_filetypes;

        $extension = determine_file_extension();

        $target_path = make_manuscript_link($insert_id);

        if(!is_file($target_path)){

                // if(in_array($_FILES['manuscript_file']['type'], $ok_filetypes)){

        //        if(in_array(get_file_type($_FILES['manuscript_file']['tmp_name']), $ok_filetypes)){

                        if(move_uploaded_file($_FILES['manuscript_file']['tmp_name'], $target_path.$extension)) {
                                return true;
                        }

            //    }else{

                        //echo "Unacceptable filetype: ".$_FILES['manuscript_file']['type']."\n";

              //  }

        }

        return false;

}

function determine_file_extension(){

        $filename = $_FILES['manuscript_file']['name'];

        $matches = array();

        if(preg_match('/(\.[^\.]*)$/', $filename, $matches)){

                if(!empty($matches)){

                        return $matches[0];

                }

        }

        return '.txt';


}

if(isset($_POST['wp_nonce'])){

        if(!wp_verify_nonce($transfer_array['wp_nonce'], $impulse_nonce)){
                $errors['security'] = 'The form you submitted contained a bad nonce value.  Please re-submit the form.  If this problem persists please contact the site admin.';

        }

        if(!is_user_logged_in()){

                $transfer_array['user_id'] = 9999; //$errors['login'] = 'You must be a logged-in in order to submit a manuscript.';
        }else{

                get_currentuserinfo();

                $transfer_array['user_id'] = sprintf('%d', $current_user -> ID);

        }

        if (filter_var($_POST['man_email'], FILTER_VALIDATE_EMAIL)) {

                $transfer_array['man_email'] = $_POST['man_email'];

        }else{

                $errors['security'] = 'You must supply a valid email.';

        }


        if(in_array($_FILES['manuscript_file']['error'], array('1','2'))){

                $errors['size'] = 'The file you uploaded exceeds the size limitations.  Please make sure your file is less than 3MB in size.';

        }else if($_FILES['manuscript_file']['error'] != '0'){

                $errors['size'] = 'The file upload was unsuccessful.  Please contact the site administrator.';

        }

        if(!empty($errors)){

                foreach($errors as $key => $value){

                        $response_div .= '<li>'.$value.'</li>';

                }

                $response_div = '<ul class="impulse_errors">'.$response_div.'</ul>';

        }

        if(empty($response_div)){

                foreach($fields_whitelist as $key => $value_array){

                        if(!in_array($key, $fields_blacklist)){

                                $sub_values_array[$key] = $transfer_array[$key];

                                $sub_values_types[] = $value_array[2];

                        }

                }

                $sub_values_array['user_id'] = $transfer_array['user_id'];

                $sub_values_types[] = '%d';

                $sub_values_array['date'] = date("Y-m-d H:i:s", current_time('timestamp'));

                $sub_values_types[] = '%s';

                $insert_result = $wpdb -> insert(
                        $submissions_table,
                        $sub_values_array,
                        $sub_values_types
                );

                if($insert_result){

                        $transfer_array['insert_id'] = $wpdb -> insert_id;

                        $file_upload_result = upload_submission($transfer_array['insert_id']);

                        if($file_upload_result == true){

                                notify_via_email($transfer_array);

                                add_buddypress_activity($transfer_array['man_title']);

                                $show_thankyou = true;

                        }else{

                                $wpdb -> query('
                                        DELETE FROM
                                                '.$submissions_table.'
                                        WHERE
                                                `id` = '.$transfer_array['insert_id']
                                );

                                $response_div = '<ul class="impulse_errors"><li>We are unable to process your upload. Submissions must be in .doc, .pdf or .rtf format.</li></ul>';

                        }

                }else{

                        $wpdb -> query('
                                DELETE FROM
                                        '.$submissions_table.'
                                WHERE
                                        `id` = '.$transfer_array['insert_id']
                        );
                        $response_div = '<ul class="impulse_errors"><li>We were unable to process your request.  Please contact the site administrator.</li></div>';

                }

        }

}

$overlay = '';

$nonce_value = wp_create_nonce($impulse_nonce);

foreach($transfer_array as $key => $value){

        $transfer_array[$key] = stripslashes($value);

}

?>

<?php get_header() ?>
<div class="floral mega">
<h2>Avon Impulse</h2>
</div>

<div class="avon-rom">
<?php get_sidebar('impulse') ?>

    <div class="arcontent">

        <h2>Submit Your Writing</h2>
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
<?= $response_div ?>
<?= $overlay ?>
<?php if(!$show_thankyou){ ?>
            <div class="impulse_form_div">
                <h4>Please complete the following form and make sure to hit submit</h4>
                <form name="impuse_form" id="impulse_form_id" enctype="multipart/form-data" action="" method="POST">
                    <ul>
                        <li>
                            <div class="error" id="man_title-error">The title must be set.</div>
                            <div class="text_with_checkboxes">
                                <label for="title_id">Title of Manuscript</label>
                                <input type="text" class="text" name="man_title" id="man_title_id" value="<?php echo filter_var($_POST['man_title'], FILTER_SANITIZE_STRING) ?>">
                            </div>
                            <div class="error" id="finished-error"></div>
                            <label for="finished_id">Is this manuscript finished?</label>
                            <input type="radio" class="radio" checked="checked" name="finished" value="finished_yes">Yes
                            <input type="radio" class="radio" name="finished" value="finished_no">No
                        </li>
                        <li>
                            <div class="error" id="man_name-error">Your prefered author name must be set.</div>
                            <div class="text_with_checkboxes">
                                <label for="name_id">Preferred Author Name</label>
                                <input type="text" class="text" name="man_name" id="man_name_id" value="<?php echo filter_var($_POST['man_name'], FILTER_SANITIZE_STRING) ?>">
                            </div>
                            <div class="error" id="published-error"></div>
                            <label for="published_id">Have you published before?</label>
                            <input type="radio" class="radio" checked="checked" name="published" value="published_yes" id="published_id">Yes
                            <input type="radio" class="radio" name="published" value="published_no" checked="checked">No
                        </li>
                        <li>
                            <label for="email_id">What is your email address?</label>
                            <input type="text" class="text" name="man_email" id="man_email_id" value="<?php echo filter_var($_POST['man_email'], FILTER_SANITIZE_EMAIL) ?>">
                        </li>
                        <li>
                            <div class="three_selects">
                                <div class="error" id="length-error">Please select a length category</div>
                                <label for="length_id">How long is the manuscript?</label>
                                <select name="length" class="select" id="length_id">
                                    <option value="-1">Choose one</option><option value="1">10-25K Words</option><option value="2">25-50K Words</option><option value="3">50-100K Words</option>                                                </select>
                            </div>
                            <div class="three_selects">
                                <div class="error" id="period-error">Please select a period</div>
                                <label for="period_id">Time period?</label>
                                <select name="period" class="select" id="period_id">
                                    <option value="-1">Choose one</option><option value="1">Prehistoric</option><option value="2">Medieval</option><option value="3">Regency</option><option value="4">Victorian</option><option value="5">Turn of the Century</option><option value="6">WWII</option><option value="7">50s-Present</option><option value="8">Future</option><option value="9">Other</option>                                                </select>
                            </div>
                        </li>
                        <li>
                            <div class="three_selects">
                                <div class="error" id="genre-error">Please select a genre</div>
                                <label for="genre_id">Genre?</label>
                                <select name="genre" class="select" id="genre_id">
                                    <option value="-1">Choose one</option><option value="1">General</option><option value="2">Contemporary</option><option value="3">Fantasy</option><option value="4">Gothic</option><option value="5">Historical</option><option value="6">Regional</option><option value="7">Regency</option><option value="8">Short Stories</option><option value="9">Time Travel</option><option value="10">Suspense</option><option value="11">Paranormal</option>                                                </select>
                            </div>
                            <div class="three_selects">
                                <div class="error" id="subcategory-error">Please select a subcategory</div>
                                <label for="subcategory_id">Subcategory?</label>
                                <select name="subcategory" class="select" id="subcategory_id">
                                    <option value="-1">Choose one</option><option value="1">Gothic</option><option value="2">Steampunk</option><option value="3">Shifter</option><option value="4">Vampire</option><option value="5">Ghost</option><option value="6">Magical</option><option value="7">Futuristic</option><option value="8">Time Travel</option><option value="9">Western</option><option value="10">Small Town</option><option value="11">Suspense</option><option value="12">Fantasy</option><option value="13">Other</option>                                                </select>
                            </div>
                        </li>
                        <li>
                            <label for="submission_request_id">Is this in reference to a specific submission request Avon posted? If so, which one?</label>
                            <textarea name="submission_request" id="submission_request_id"><?php echo filter_var($_POST['submission_request'], FILTER_SANITIZE_STRING) ?></textarea>
                        </li>
                        <li>
                            <div class="error" id="synopsis-error">Please enter a brief synopsis</div>
                            <label for="synopsis_id">Can you give us a brief synopsis? <span class="parenthetical">(less than 200 words)</span></label>
                            <textarea name="synopsis" id="synopsis_id"><?php echo filter_var($_POST['synopsis'], FILTER_SANITIZE_STRING) ?></textarea>
                        </li>
                        <li>
                            <div class="error" id="best_scene-error">Please post the best scene or the first 1000 words</div>
                            <label for="best_scene_id">Post the best scene or the first 1000 words <span class="parenthetical">(less than 1000 words)</span></label>
                            <textarea name="best_scene" id="best_scene_id"><?php echo filter_var($_POST['best_scene'], FILTER_SANITIZE_STRING) ?></textarea>
                        </li>
                        <li>
                            <div class="error" id="query_letter-error">Please post your query letter</div>
                            <label for="query_letter_id">Post your query letter <span class="parenthetical">(less than 750 words)</span></label>
                            <textarea name="query_letter" id="query_letter_id"><?php echo filter_var($_POST['query_letter'], FILTER_SANITIZE_STRING) ?></textarea>
                        </li>
                        <li>
                            <div class="error" id="manuscript_file-error">Upload a manuscript file</div>
                            <label for="manuscript_file_id">Upload your manuscript</label>
                            <input type="hidden" name="MAX_FILE_SIZE" value="3000000">
                            <input type="file" class="file" name="manuscript_file" id="manuscript_file_id">
                        </li>
                        <li>
                            <input type="submit" class="submit" value="">
			    <input type="hidden" name="wp_nonce" value="<?= $nonce_value ?>" />
                            <p>By clicking submit, I agree that HarperCollins has my permission to review my manuscript and contact me via the email address provided above.</p>
                        </li>
                    </ul>
                </form>
        </div>

        </div>
</div>

</div>
<?php  }else{ ?>
        <div class="impulse_thank_you">
                <h4 class="lightblue">Thank You for your submission!</h4>
                <p>Want to submit another?  <a href="<?php echo site_url(); ?>/impulse">Click here to reset the form.</a></p>
        </div>
<?php } ?>

<div class="cf"></div>
<?php get_footer(); ?>