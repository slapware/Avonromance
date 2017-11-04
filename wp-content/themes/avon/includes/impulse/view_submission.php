<?php

include($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

global $current_user, $wpdb;

$submission_id = $_GET['sid'];

$contest = (isset($_GET['contest']) && !empty($_GET['contest']))?$_GET['contest']:null;

$valid_submission_id = null;

$my_userid = $current_user -> ID;

function get_valid_submission($submission_id, $contest = null){

        $valid_submission_id = false;

        if(is_null($contest)){

                $thisquery = '
                        SELECT
                                id
                        FROM
                                submissions
                        WHERE
                                user_id = "'.$my_userid.'"
                        AND
                                id = "'.$submission_id.'"
                        LIMIT 1';

        }else{

                switch($contest){

                        case 'mccomber':
                        case 'familyaffair':
                        default:

                                $thisquery = '
                                        SELECT
                                                id
                                        FROM
                                                dm_submissions
                                        WHERE
                                                user_id = "'.$current_user -> ID.'"
                                        AND
                                                id = "'.$submission_id.'"
                                        LIMIT 1';

                                break;

                }

        }

        // $thisquery =  $wpdb -> prepare($thisquery);

        $result =  mysql_query($thisquery);

        if($result && isset($result[0] -> id)){

                $valid_submission_id = $result[0] -> id;

        }

        return $valid_submission_id;

}

function build_submission_path($contest = null){

        $return_string = '';
        $upload_dir = wp_upload_dir();

        switch($contest){

                case 'mccomber':

                        $return_string = $upload_dir['basedir'] . '/submissions/maccombercontest/';

                        break;

                case 'familyaffair':

                        $return_string = $upload_dir['basedir'] . '/submissions/family_affair_contest/';

                        break;

                default:

                        $return_string = $upload_dir['basedir'] . '/submissions/';

                        break;

        }

        return $return_string;

}

function make_filepath($submission_id, $contest = null){

        $dirpath = build_submission_path($contest);

        $dir = opendir($dirpath);

        if($dir){

                while(($file = readdir($dir)) !== false) {

            if(preg_match('/^'.$submission_id.'\.manuscript(.*)$/', $file, $matches)){

                return $dirpath.$matches[0];

            }

        }

        closedir($dh);

        }

        return $dirpath.$submission_id.'.manuscript.txt';

}

if(isset($_GET['ahash']) && $_GET['ahash'] == SUBMISSIONS_ADMIN_HASH){

        $valid_submission_id = $submission_id;

}else{

        $valid_submission_id = get_valid_submission($submission_id, $contest);

}

if(!is_null($valid_submission_id) && is_numeric($valid_submission_id)){

        $filepath = make_filepath($valid_submission_id, $contest);
	//echo $_SERVER['DOCUMENT_ROOT'] . "<br>";
        //echo $filepath;

        if(is_file($filepath)) {

                header('Content-Description: File Transfer');

                header('Content-Type: application/octet-stream');

                header('Content-Disposition: attachment; filename='.basename($filepath));

                header('Content-Transfer-Encoding: binary');

                header('Expires: 0');

                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

                header('Pragma: public');

                header('Content-Length: ' . filesize($filepath));

                ob_clean();

                flush();

                readfile($filepath);

                exit;

        }

}

header('Location: '.home_url());

exit;
?>
