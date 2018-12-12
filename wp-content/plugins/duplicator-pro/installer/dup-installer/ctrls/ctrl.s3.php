<?php
defined("DUPXABSPATH") or die("");
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

//-- START OF ACTION STEP 3: Update the database
require_once($GLOBALS['DUPX_INIT'].'/classes/config/class.archive.config.php');
require_once($GLOBALS['DUPX_INIT'].'/classes/config/class.wp.config.tranformer.php');
require_once($GLOBALS['DUPX_INIT'].'/classes/utilities/class.u.multisite.php');

/** JSON RESPONSE: Most sites have warnings turned off by default, but if they're turned on the warnings
  cause errors in the JSON data Here we hide the status so warning level is reset at it at the end */
$ajax3_start		 = DUPX_U::getMicrotime();
$ajax3_error_level	 = error_reporting();
error_reporting(E_ERROR);

//POST PARAMS
$_POST['blogname']				 = isset($_POST['blogname']) ? htmlspecialchars($_POST['blogname'], ENT_QUOTES) : 'No Blog Title Set';
$_POST['postguid']				 = isset($_POST['postguid']) && $_POST['postguid'] == 1 ? 1 : 0;
$_POST['fullsearch']			 = isset($_POST['fullsearch']) && $_POST['fullsearch'] == 1 ? 1 : 0;
$_POST['fixpartials']			 = isset($_POST['fixpartials']) && $_POST['fixpartials'] == 1 ? 1 : 0;

if (isset($_POST['path_old'])) {
	$post_path_old = DUPX_U::sanitize_text_field($_POST['path_old']);
	$_POST['path_old'] = trim($post_path_old);
} else {
	$_POST['path_old'] = null;
}

if (isset($_POST['path_new'])) {
	$post_path_new = DUPX_U::sanitize_text_field($_POST['path_new']);
	$_POST['path_new'] = trim($post_path_new);
} else {
	$_POST['path_new'] = null;
}

if (isset($_POST['siteurl'])) {
	$post_site_user = DUPX_U::sanitize_text_field($_POST['siteurl']);
	$_POST['siteurl'] = rtrim(trim($post_site_user), '/');
} else {
	$_POST['siteurl'] = null;
}

$_POST['tables']				 = isset($_POST['tables']) && is_array($_POST['tables']) ? array_map('DUPX_U::sanitize_text_field', $_POST['tables']) : array();

if (isset($_POST['url_old'])) {
	$post_url_old = DUPX_U::sanitize_text_field($_POST['url_old']);
	$_POST['url_old'] = trim($post_url_old);
} else {
	$_POST['url_old'] = null;
}

if (isset($_POST['url_new'])) {
    $post_url_new = DUPX_U::sanitize_text_field($_POST['url_new']);
    $_POST['url_new'] = rtrim(trim($post_url_new), '/');
} else {
	$_POST['url_new'] = null;
}
$_POST['subsite-id']			 = isset($_POST['subsite-id']) ? intval($_POST['subsite-id']) : -1;
$_POST['ssl_admin']				 = (isset($_POST['ssl_admin'])) ? true : false;
$_POST['auth_keys_and_salts']	 = (isset($_POST['auth_keys_and_salts'])) ? true : false;
$_POST['cache_wp']				 = (isset($_POST['cache_wp'])) ? true : false;
$_POST['cache_path']			 = (isset($_POST['cache_path'])) ? true : false;
$_POST['empty_schedule_storage'] = (isset($_POST['empty_schedule_storage']) && $_POST['empty_schedule_storage'] == '1') ? true : false;
$_POST['replace_mode']           = isset($_POST['replace_mode']) ? DUPX_U::sanitize_text_field($_POST['replace_mode']) : "legacy";
$_POST['remove_redundant']       = isset($_POST['remove_redundant']) ? DUPX_U::sanitize_text_field($_POST['remove_redundant']) : 0;
$_POST['wp_debug'] = (isset($_POST['wp_debug']) && 1 == $_POST['wp_debug']) ? 1 : 0;
$_POST['wp_debug_log'] = (isset($_POST['wp_debug_log']) && 1 == $_POST['wp_debug_log']) ? 1 : 0;
$_POST['exe_safe_mode']	= isset($_POST['exe_safe_mode']) ? DUPX_U::sanitize_text_field($_POST['exe_safe_mode']) : 0;
$subsite_id	 = (int)$_POST['subsite-id'];


//MYSQL CONNECTION
$post_dbpass = trim(DUPX_U::wp_unslash($_POST['dbpass']));
$dbh		 = DUPX_DB::connect($_POST['dbhost'], $_POST['dbuser'], $post_dbpass, $_POST['dbname'], $_POST['dbport']);
$dbConnError = (mysqli_connect_error()) ? 'Error: '.mysqli_connect_error() : 'Unable to Connect';

if (!$dbh) {
	$msg = "Unable to connect with the following parameters: <br/> <b>HOST:</b> {$post_db_host}<br/> <b>DATABASE:</b> {$post_db_name}<br/>";
	$msg .= "<b>Connection Error:</b> {$dbConnError}";
	DUPX_Log::error($msg);
}

$charset_server	 = @mysqli_character_set_name($dbh);
$db_max_time = mysqli_real_escape_string($dbh, $GLOBALS['DB_MAX_TIME']);
@mysqli_query($dbh, "SET wait_timeout = ".mysqli_real_escape_string($dbh, $db_max_time));

$post_db_charset = DUPX_U::sanitize_text_field($_POST['dbcharset']);
$post_db_collate = DUPX_U::sanitize_text_field($_POST['dbcollate']);
DUPX_DB::setCharset($dbh, $post_db_charset, $post_db_collate);
$charset_client	 = @mysqli_character_set_name($dbh);

//LOGGING
$date = @date('h:i:s');
$log  = <<<LOG
\n\n
********************************************************************************
DUPLICATOR PRO INSTALL-LOG
STEP-3 START @ {$date}
NOTICE: Do NOT post to public sites or forums
********************************************************************************
CHARSET SERVER:\t{$charset_server}
CHARSET CLIENT:\t{$charset_client}\n
LOG;
DUPX_Log::info($log);

$POST_LOG = $_POST;
unset($POST_LOG['tables']);
unset($POST_LOG['plugins']);
unset($POST_LOG['dbpass']);
ksort($POST_LOG);

//Detailed logging
$log = "--------------------------------------\n";
$log .= "POST DATA\n";
$log .= "--------------------------------------\n";
$log .= print_r($POST_LOG, true);
$log .= "--------------------------------------\n";
$log .= "TABLES TO SCAN\n";
$log .= "--------------------------------------\n";
$log .= (isset($_POST['tables']) && count($_POST['tables'] > 0)) ? print_r($_POST['tables'], true) : 'No tables selected to update';
$log .= "--------------------------------------\n";
$log .= "KEEP PLUGINS ACTIVE\n";
$log .= "--------------------------------------\n";
$log .= (isset($_POST['plugins']) && count($_POST['plugins'] > 0)) ? print_r($_POST['plugins'], true) : 'No plugins selected for activation';
DUPX_Log::info($log, 2);


//===============================================
//UPDATE ENGINE
//===============================================
$log = "--------------------------------------\n";
$log .= "SERIALIZER ENGINE\n";
$log .= "[*] scan every column\n";
$log .= "[~] scan only text columns\n";
$log .= "[^] no searchable columns\n";
$log .= "--------------------------------------";
DUPX_Log::info($log);

//CUSTOM REPLACE -> REPLACE LIST
if (isset($_POST['search'])) {
	$search_count = count($_POST['search']);
	if ($search_count > 0) {
		for ($search_index = 0; $search_index < $search_count; $search_index++) {
			$search_for		 = DUPX_U::sanitize_text_field($_POST['search'][$search_index]);
			$replace_with	 = DUPX_U::sanitize_text_field($_POST['replace'][$search_index]);

			if (trim($search_for) != '') {
                DUPX_U::queueReplacementWithEncodings($search_for, $replace_with);
			}
		}
	}
}

//MULTISITE REPLACE -> REPLACE LIST
if (isset($_POST['mu_search']) && $_POST['replace_mode'] == "mapping") {
    $mu_search_count = count($_POST['mu_search']);
    if ($mu_search_count > 0) {
        for ($mu_search_index = 0; $mu_search_index < $mu_search_count; $mu_search_index++) {
            $mu_search_for		 = DUPX_U::sanitize_text_field($_POST['mu_search'][$mu_search_index]);
            $mu_replace_with	 = DUPX_U::sanitize_text_field($_POST['mu_replace'][$mu_search_index]);

            if (trim($mu_search_for) != '') {
                DUPX_U::queueReplacementWithEncodings($mu_search_for, $mu_replace_with);
                
                if($GLOBALS['DUPX_AC']->mu_mode == 1) {
                    $mu_search_host = parse_url($mu_search_for,PHP_URL_HOST);
                    $mu_replace_host = parse_url($mu_replace_with,PHP_URL_HOST);
                    array_push($GLOBALS['REPLACE_LIST'], array('search' => $mu_search_host, 'replace' => $mu_replace_host));
                }
            }
        }
    }
}

//MULTI-SITE SEARCH AND REPLACE LIST
// -1: Means network install so skip this
//  1: Root subsite so don't do this swap
DUPX_Log::info("Subsite id={$subsite_id}");

if ($subsite_id > 1) {
	DUPX_Log::info("####1");
	$ac = DUPX_ArchiveConfig::getInstance();

	foreach ($ac->subsites as $subsite) {
		DUPX_Log::info("####2");
		if ($subsite->id == $subsite_id) {
			DUPX_Log::info("####3");
			if ($GLOBALS['DUPX_AC']->mu_mode == DUPX_MultisiteMode::Subdomain) {

				DUPX_Log::info("#### subdomain mode");
				$old_subdomain = preg_replace('#^https?://#', '', rtrim($subsite->name, '/'));
				$new_url = DUPX_U::sanitize_text_field($_POST['url_new']);
				$newval	 = preg_replace('#^https?://#', '', rtrim($new_url, '/'));


                DUPX_U::queueReplacementWithEncodings($old_subdomain, $newval);

                //FORCE NEW PROTOCOL "//"
                $url_new_info   = parse_url($new_url);
                $url_new_domain = $url_new_info['scheme'].'://'.$url_new_info['host'];

                if ($url_new_info['scheme'] == 'http') {
                    $url_new_wrong_protocol = 'https://'.$url_new_info['host'];
                } else {
                    $url_new_wrong_protocol = 'http://'.$url_new_info['host'];
                }
                DUPX_U::queueReplacementWithEncodings($url_new_wrong_protocol, $url_new_domain);
                
            } else if ($GLOBALS['DUPX_AC']->mu_mode == DUPX_MultisiteMode::Subdirectory) {

				DUPX_Log::info("#### subdirectory mode");
				$pattern_matched = preg_match('/^\//',$subsite->name);
                $is_path = !empty($pattern_matched);
				if($is_path){
					$post_url_old = DUPX_U::sanitize_text_field($_POST['url_old']);
                    $old_subdirectory_url = $post_url_old.$subsite->name;
                }else{
                    $old_subdirectory_url = $subsite->name;
                }

				$post_url_old = DUPX_U::sanitize_text_field($_POST['url_old']);
				$post_url_old = DUPX_U::esc_html($post_url_old);

				$post_url_new = DUPX_U::sanitize_text_field($_POST['url_new']);
				$post_url_new = DUPX_U::esc_html($post_url_new);

				DUPX_Log::info("#### trying to replace $old_subdirectory_url ({$post_url_old},{$subsite->name}) { with {$post_url_new}");

                $old_subdomain_subdir = preg_replace('#^https?://#', '', rtrim($old_subdirectory_url  , '/'));

                DUPX_U::queueReplacementWithEncodings('https://'.$old_subdomain_subdir, $post_url_new);
                DUPX_U::queueReplacementWithEncodings('http://'.$old_subdomain_subdir, $post_url_new);
                
			} else {
				DUPX_Log::info("#### neither mode {$GLOBALS['DUPX_AC']->mu_mode}");
			}

			// Need to swap the subsite prefix for the main table prefix
			$subsite_uploads_dir = "/uploads/sites/{$subsite_id}";
			$subsite_prefix		 = "{$GLOBALS['DUPX_AC']->wp_tableprefix}{$subsite_id}_";

            DUPX_U::queueReplacementWithEncodings($subsite_uploads_dir, '/uploads');

			array_push($GLOBALS['REPLACE_LIST'],
				//array('search' => $subsite_uploads_dir, 'replace' => '/uploads'),
				array('search' => $subsite_prefix, 'replace' => $GLOBALS['DUPX_AC']->wp_tableprefix));

			break;
		}
	}

	DUPX_Log::info("####4");
	$post_path_new = DUPX_U::sanitize_text_field($_POST['path_new']);
    $new_content_dir = (substr($post_path_new,-1,1) == '/') ? "{$post_path_new}{$GLOBALS['DUPX_AC']->relative_content_dir}"
                        : "{$post_path_new}/{$GLOBALS['DUPX_AC']->relative_content_dir}";

	try {
		DUPX_Log::info("####5");
		$post_subsite_id = intval($_POST['subsite-id']);
		$post_remove_redundant = DUPX_U::sanitize_text_field($_POST['remove_redundant']);
		DUPX_MU::convertSubsiteToStandalone($post_subsite_id, $dbh, $GLOBALS['DUPX_AC'], $new_content_dir, $post_remove_redundant);
	} catch (Exception $ex) {
		DUPX_Log::info("####6");
		DUPX_Log::error("Problem with core logic of converting subsite into a standalone site.<br/>".$ex->getMessage().'<br/>'.$ex->getTraceAsString());
	}

	// Since we are converting subsite to multisite consider this a standalone site
	$GLOBALS['DUPX_AC']->mu_mode = DUPX_MultisiteMode::Standalone;
	DUPX_Log::info("####7");

    //Replace WP 3.4.5 subsite uploads path in DB
    if($GLOBALS['DUPX_AC']->mu_generation === 1){
		$post_subsite_id = intval($_POST['subsite-id']);
        $blogs_dir = 'blogs.dir/'.$post_subsite_id.'/files';
        $uploads_dir = 'uploads';

        DUPX_U::queueReplacementWithEncodings($blogs_dir, $uploads_dir);

//        array_push($GLOBALS['REPLACE_LIST'],
//            array('search' => $blogs_dir,   'replace' => $uploads_dir)
//        );

		$post_url_new = DUPX_U::sanitize_text_field($_POST['url_new']);
        $files_dir = "{$post_url_new}/files";
        $uploads_dir = "{$post_url_new}/{$GLOBALS['DUPX_AC']->relative_content_dir}/uploads";

        DUPX_U::queueReplacementWithEncodings($files_dir, $uploads_dir);

//        array_push($GLOBALS['REPLACE_LIST'],
//            array('search' => $files_dir,   'replace' => $uploads_dir)
//        );
    }
}else if($subsite_id == 1){
    // Since we are converting subsite to multisite consider this a standalone site
    $GLOBALS['DUPX_AC']->mu_mode = DUPX_MultisiteMode::Standalone;
    DUPX_Log::info("####4");
	$post_path_new = DUPX_U::sanitize_text_field($_POST['path_new']);
    $new_content_dir = (substr($post_path_new,-1,1) == '/') ? "{$post_path_new}{$GLOBALS['DUPX_AC']->relative_content_dir}"
        : "{$post_path_new}/{$GLOBALS['DUPX_AC']->relative_content_dir}";
    try {
        DUPX_Log::info("####5");
		$post_subsite_id = intval($_POST['subsite-id']);
		$post_remove_redundant = DUPX_U::sanitize_text_field($_POST['remove_redundant']);
        DUPX_MU::convertSubsiteToStandalone($post_subsite_id, $dbh, $GLOBALS['DUPX_AC'], $new_content_dir, $post_remove_redundant);
    } catch (Exception $ex) {
        DUPX_Log::info("####6");
        DUPX_Log::error("Problem with core logic of converting subsite into a standalone site.<br/>".$ex->getMessage().'<br/>'.$ex->getTraceAsString());
    }
}else{
    //$mu_mode:
    //0=(no multisite);
    //1=(multisite subdomain);
    //2=(multisite subdirectory)
    if ($GLOBALS['DUPX_AC']->mu_mode == 1 && $_POST['replace_mode'] == "legacy") {
		$post_url_new = DUPX_U::sanitize_text_field($_POST['url_new']);
		$post_url_old = DUPX_U::sanitize_text_field($_POST['url_old']);

        $mu_newDomain		 =  DUPX_U::getDomain($post_url_new);
        $mu_oldDomain		 =  DUPX_U::getDomain($post_url_old);
        
        array_push($GLOBALS['REPLACE_LIST'], array('search' => ('.'.$mu_oldDomain), 'replace' => ('.'.$mu_newDomain)));

        $mu_oldDomain_json	 = str_replace('"', "", json_encode('.'.$mu_oldDomain));
        $mu_newDomain_json	 = str_replace('"', "", json_encode('.'.$mu_newDomain));

        array_push($GLOBALS['REPLACE_LIST'], array('search' => $mu_oldDomain_json, 'replace' => $mu_newDomain_json));
    }
}

//GENERAL -> REPLACE LIST
$path_old_json	 = str_replace('"', "", json_encode($_POST['path_old']));
$path_new_json	 = str_replace('"', "", json_encode($_POST['path_new']));


//DIRS PATHS
$post_path_old = DUPX_U::sanitize_text_field($_POST['path_old']);
$post_path_new = DUPX_U::sanitize_text_field($_POST['path_new']);
DUPX_U::queueReplacementWithEncodings($post_path_old , $post_path_new);
$path_old_unsetSafe = rtrim(DUPX_U::unsetSafePath($_POST['path_old']), '\\');
$path_new_unsetSafe = rtrim($_POST['path_new'], '/');
DUPX_U::queueReplacementWithEncodings($path_old_unsetSafe , $path_new_unsetSafe );

$post_url_old = DUPX_U::sanitize_text_field($_POST['url_old']);
$post_url_new = DUPX_U::sanitize_text_field($_POST['url_new']);

//SEARCH WITH NO PROTOCAL: RAW "//"
$url_old_raw = str_ireplace(array('http://', 'https://'), '//', $post_url_old);
$url_new_raw = str_ireplace(array('http://', 'https://'), '//', $post_url_new);
DUPX_U::queueReplacementWithEncodings($url_old_raw , $url_new_raw);

//FORCE NEW PROTOCOL "//"
$url_new_info = parse_url($post_url_new);
$url_new_domain = $url_new_info['scheme'].'://'.$url_new_info['host'];

if ($url_new_info['scheme'] == 'http') {
    $url_new_wrong_protocol = 'https://'.$url_new_info['host'];
} else {
    $url_new_wrong_protocol = 'http://'.$url_new_info['host'];
}

DUPX_U::queueReplacementWithEncodings($url_new_wrong_protocol , $url_new_domain);


/*if ($GLOBALS['DUPX_AC']->mu_mode == 1) {
	DUPX_Log::info('#### mu mode is 1');
	$mu_oldStrippedDomainHost		 = parse_url($_POST['url_old'])['host'];

	if(stripos($mu_oldStrippedDomainHost, 'www.') === 0) {
		$mu_oldStrippedDomainHost = substr($mu_oldStrippedDomainHost, 4, (strlen($mu_oldStrippedDomainHost) - 4));

		$mu_newDomainHost		 = parse_url($_POST['url_new'])['host'];

		DUPX_Log::info("#### searching for {$mu_oldStrippedDomainHost} replacing with {$mu_newDomainHost}");

		array_push($GLOBALS['REPLACE_LIST'], array('search' => ('.'.$mu_oldStrippedDomainHost), 'replace' => ('.'.$mu_newDomainHost)));
	}
	else {
		DUPX_Log::info("#### doesnt contain www");
	}

}*/

/*=============================================================
 * REMOVE TRAILING SLASH LOGIC:
 * In many cases the trailing slash of a url or path causes issues in some
 * enviroments; so by default all trailing slashes have been removed.
 * This has worked well for several years.  However, there are some edge
 * cases where removing the trailing slash will cause issues such that
 * the following will happen:
	http://www.mysite.com  >>>>  http://C:/xampp/apache/htdocs/.mysite.com
 * So the edge case array is a place older for these types of issues.
*/
$GLOBALS['REPLACE_LIST_EDGE_CASES'] = array('/www/');
$_dupx_tmp_replace_list = $GLOBALS['REPLACE_LIST'];
foreach ($_dupx_tmp_replace_list as $key => $val) {
	foreach ($GLOBALS['REPLACE_LIST_EDGE_CASES'] as $skip_val) {
		$search  = $GLOBALS['REPLACE_LIST'][$key]['search'];
		$replace = $GLOBALS['REPLACE_LIST'][$key]['replace'];
		if (strcmp($skip_val, $search) !== 0) {
			$GLOBALS['REPLACE_LIST'][$key]['search']  = rtrim($search, '\/');
			$GLOBALS['REPLACE_LIST'][$key]['replace'] = rtrim($replace, '\/');
		} else {
			DUPX_Log::info("NOTICE: Edge case for path trimming detected on {$skip_val}");
		}
	}
}

DUPX_Log::info("Final replace list: \n". print_r($GLOBALS['REPLACE_LIST'], true),3);
$report = DUPX_UpdateEngine::load($dbh, $GLOBALS['REPLACE_LIST'], $_POST['tables'], $_POST['fullsearch'], $_POST['fixpartials']);

if($_POST['fixpartials']){
    DUPX_Log::info("Partial on");
}else{
    DUPX_Log::info("Partial off");
}


//BUILD JSON RESPONSE
$JSON						 = array();
$JSON['step1']				 = json_decode(urldecode($_POST['json']));
$JSON['step3']				 = $report;
$JSON['step3']['warn_all']	 = 0;
$JSON['step3']['warnlist']	 = array();

DUPX_UpdateEngine::logStats($report);
DUPX_UpdateEngine::logErrors($report);

//===============================================
//REMOVE LICENSE KEY
//===============================================
if(isset($GLOBALS['DUPX_AC']->brand) && isset($GLOBALS['DUPX_AC']->brand->enabled) && $GLOBALS['DUPX_AC']->brand->enabled)
{
    $license_check	 = mysqli_query($dbh, "SELECT COUNT(1) AS count FROM `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` WHERE `option_name` LIKE 'duplicator_pro_license_key' ");
	$license_row	 = mysqli_fetch_row($license_check);
	$license_count	 = is_null($license_row) ? 0 : $license_row[0];
    if ($license_count > 0) {
        mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET `option_value` = '' WHERE `option_name` LIKE 'duplicator_pro_license_key'");
    }
}

//===============================================
//CREATE NEW ADMIN USER
//===============================================
if (strlen($_POST['wp_username']) >= 4 && strlen($_POST['wp_password']) >= 6) {
	$wp_username = mysqli_real_escape_string($dbh, $_POST['wp_username']);
	$newuser_check	 = mysqli_query($dbh, "SELECT COUNT(*) AS count FROM `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."users` WHERE user_login = '{$wp_username}' ");
	$newuser_row	 = mysqli_fetch_row($newuser_check);
	$newuser_count	 = is_null($newuser_row) ? 0 : $newuser_row[0];

	if ($newuser_count == 0) {

		$newuser_datetime = @date("Y-m-d H:i:s");
		$newuser_datetime = mysqli_real_escape_string($dbh, $newuser_datetime);
		$newuser_security = mysqli_real_escape_string($dbh, 'a:1:{s:13:"administrator";b:1;}');
		
		$post_wp_username = DUPX_U::sanitize_text_field($_POST['wp_username']);
		$post_wp_password = DUPX_U::sanitize_text_field($_POST['wp_password']);

        $post_wp_mail = DUPX_U::sanitize_text_field($_POST['wp_mail']);
		$post_wp_nickname = DUPX_U::sanitize_text_field($_POST['wp_nickname']);
        if (empty($post_wp_nickname)) {
            $post_wp_nickname = $post_wp_username;
        }
        $post_wp_first_name = DUPX_U::sanitize_text_field($_POST['wp_first_name']);
		$post_wp_last_name = DUPX_U::sanitize_text_field($_POST['wp_last_name']);

		$wp_username = mysqli_real_escape_string($dbh, $post_wp_username);
		$wp_password = mysqli_real_escape_string($dbh, $post_wp_password);
        $wp_mail = mysqli_real_escape_string($dbh, $post_wp_mail);
		$wp_nickname = mysqli_real_escape_string($dbh, $post_wp_nickname);
        $wp_first_name = mysqli_real_escape_string($dbh, $post_wp_first_name);
		$wp_last_name = mysqli_real_escape_string($dbh, $post_wp_last_name);
		
		$newuser1 = @mysqli_query($dbh,
				"INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."users`
				(`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_registered`, `user_activation_key`, `user_status`, `display_name`)
				VALUES ('{$wp_username}', MD5('{$wp_password}'), '{$wp_username}', '{$wp_mail}', '{$newuser_datetime}', '', '0', '{$wp_username}')");

		$newuser1_insert_id = mysqli_insert_id($dbh);
		$newuser1_insert_id = intval($newuser1_insert_id);

		$newuser2 = @mysqli_query($dbh,
				"INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta`
				(`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', '".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."capabilities', '{$newuser_security}')");

		$newuser3 = @mysqli_query($dbh,
				"INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta`
				(`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', '".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."user_level', '10')");

		//Misc Meta-Data Settings:
		@mysqli_query($dbh, "INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'rich_editing', 'true')");
		@mysqli_query($dbh, "INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'admin_color',  'fresh')");
		@mysqli_query($dbh, "INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'nickname', '{$wp_nickname}')");
        @mysqli_query($dbh, "INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'first_name', '{$wp_first_name}')");
        @mysqli_query($dbh, "INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser1_insert_id}', 'last_name', '{$wp_last_name}')");



		//Add super admin permissions
		if ($GLOBALS['DUPX_AC']->mu_mode > 0 && $subsite_id == -1){
			$site_admins_query	 = mysqli_query($dbh,"SELECT meta_value FROM `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."sitemeta` WHERE meta_key = 'site_admins'");
			$site_admins		 = mysqli_fetch_row($site_admins_query);
			$site_admins[0] = stripslashes($site_admins[0]);
			$site_admins_array	 = unserialize($site_admins[0]);
			
			array_push($site_admins_array,$_POST['wp_username']);
			
			$site_admins_serialized	 = serialize($site_admins_array);
			
			@mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."sitemeta` SET meta_value = '{$site_admins_serialized}' WHERE meta_key = 'site_admins'");
			// Adding permission for each sub-site to the newly created user
			$admin_user_level = 10; // For wp_2_user_level
			$sql_values_array = array();
			$sql_values_array[] = "('{$newuser1_insert_id}', 'primary_blog', '{$GLOBALS['DUPX_AC']->main_site_id}')";
			foreach ($GLOBALS['DUPX_AC']->subsites as $subsite_info) {
				// No need to add permission for main site
				if ($subsite_info->id == $GLOBALS['DUPX_AC']->main_site_id) {
					continue;
				}

				$cap_meta_key = $subsite_info->blog_prefix.'capabilities';
				$sql_values_array[] = "('{$newuser1_insert_id}', '{$cap_meta_key}', '{$newuser_security}')";
				
				$user_level_meta_key = $subsite_info->blog_prefix.'user_level';
				$sql_values_array[] = "('{$newuser1_insert_id}', '{$user_level_meta_key}', '{$admin_user_level}')";
			}
			$sql = "INSERT INTO ".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."usermeta (user_id, meta_key, meta_value) VALUES ".implode(', ', $sql_values_array);
			@mysqli_query($dbh, $sql);
		}
		
		DUPX_Log::info("\nNEW WP-ADMIN USER:");
		if ($newuser1 && $newuser_test2 && $newuser3) {
			DUPX_Log::info("- New username '{$_POST['wp_username']}' was created successfully allong with MU usermeta.");
		} elseif ($newuser1) {
			DUPX_Log::info("- New username '{$_POST['wp_username']}' was created successfully.");
		} else {
			$newuser_warnmsg = "- Failed to create the user '{$_POST['wp_username']}' \n ";
			$JSON['step3']['warnlist'][] = $newuser_warnmsg;
			DUPX_Log::info($newuser_warnmsg);
		}
	} else {
		$newuser_warnmsg = "\nNEW WP-ADMIN USER:\n - Username '{$_POST['wp_username']}' already exists in the database.  Unable to create new account.\n";
		$JSON['step3']['warnlist'][] = $newuser_warnmsg;
		DUPX_Log::info($newuser_warnmsg);
	}
}

//===============================================
//CONFIGURATION FILE UPDATES
//===============================================
DUPX_Log::info("\n====================================");
DUPX_Log::info('CONFIGURATION FILE UPDATES:');
DUPX_Log::info("====================================\n");

$mu_newDomain		 = parse_url($_POST['url_new']);
$mu_oldDomain		 = parse_url($_POST['url_old']);
$mu_newDomainHost	 = $mu_newDomain['host'];
$mu_oldDomainHost	 = $mu_oldDomain['host'];
$mu_newUrlPath		 = parse_url($_POST['url_new'], PHP_URL_PATH);
$mu_oldUrlPath		 = parse_url($_POST['url_old'], PHP_URL_PATH);

if (empty($mu_newUrlPath) || ($mu_newUrlPath == '/')) {
	$mu_newUrlPath = '/';
} else {
	$mu_newUrlPath = rtrim($mu_newUrlPath, '/').'/';
}

if (empty($mu_oldUrlPath) || ($mu_oldUrlPath == '/')) {
	$mu_oldUrlPath = '/';
} else {
	$mu_oldUrlPath = rtrim($mu_oldUrlPath, '/').'/';
}

// UPDATE WP-CONFIG FILE
$patterns = array("/('|\")WP_HOME.*?\)\s*;/",
	"/('|\")WP_SITEURL.*?\)\s*;/",
	"/('|\")DOMAIN_CURRENT_SITE.*?\)\s*;/",
	"/('|\")PATH_CURRENT_SITE.*?\)\s*;/");

$replace = array("'WP_HOME', '{$_POST['url_new']}');",
	"'WP_SITEURL', '{$_POST['url_new']}');",
	"'DOMAIN_CURRENT_SITE', '{$mu_newDomainHost}');",
	"'PATH_CURRENT_SITE', '{$mu_newUrlPath}');");

if ($subsite_id != -1) {
	DUPX_Log::info("####10");

	array_push($patterns, "/('|\")WP_ALLOW_MULTISITE.*?\)\s*;/");
	array_push($patterns, "/('|\")MULTISITE.*?\)\s*;/");
	array_push($replace, "'ALLOW_MULTISITE', false);");
	array_push($replace, "'MULTISITE', false);");

	DUPX_Log::info('####patterns');
	DUPX_Log::info(print_r($patterns, true));
	DUPX_Log::info('####replace');
	DUPX_Log::info(print_r($replace, true));
}

if ($GLOBALS['DUPX_AC']->mu_mode !== DUPX_MultisiteMode::Standalone) {
	array_push($patterns, "/('|\")NOBLOGREDIRECT.*?\)\s*;/");
	array_push($replace, "'NOBLOGREDIRECT', '{$_POST['url_new']}');");
}

DUPX_WPConfig::updateVars($patterns, $replace);
//@todo: integrate all logic into DUPX_WPConfig::updateVars
$root_path		= $GLOBALS['DUPX_ROOT'];
//$wpconfig_path	= "{$root_path}/wp-config.php";
$wpconfig_ark_path	= "{$root_path}/dup-wp-config-arc__{$GLOBALS['DUPX_AC']->package_hash}.txt";
$wpconfig_ark_contents	= @file_get_contents($wpconfig_ark_path, true);
$wpconfig_ark_contents	= preg_replace($patterns, $replace, $wpconfig_ark_contents);

// Redundant - already processed in updateVars();
////WP_CONTENT_DIR
//if (isset($defines['WP_CONTENT_DIR'])) {
//	$new_path = str_replace($_POST['path_old'], $_POST['path_new'], DUPX_U::setSafePath($defines['WP_CONTENT_DIR']), $count);
//	if ($count > 0) {
//		array_push($patterns, "/('|\")WP_CONTENT_DIR.*?\)\s*;/");
//		array_push($replace, "'WP_CONTENT_DIR', '{$new_path}');");
//	}
//}
//
////WP_CONTENT_URL
//// '/' added to prevent word boundary with domains that have the same root path
//if (isset($defines['WP_CONTENT_URL'])) {
//    $_POST['url_old']=trim($_POST['url_old'],'/');
//    $_POST['url_new']=trim($_POST['url_new'],'/');
//	$new_path = str_replace($_POST['url_old'], $_POST['url_new'], $defines['WP_CONTENT_URL'], $count);
//	if ($count > 0) {
//		array_push($patterns, "/('|\")WP_CONTENT_URL.*?\)\s*;/");
//		array_push($replace, "'WP_CONTENT_URL', '{$new_path}');");
//	}
//}
//
////WP_TEMP_DIR
//if (isset($defines['WP_TEMP_DIR'])) {
//	$new_path = str_replace($_POST['path_old'], $_POST['path_new'], DUPX_U::setSafePath($defines['WP_TEMP_DIR']) , $count);
//	if ($count > 0) {
//		array_push($patterns, "/('|\")WP_TEMP_DIR.*?\)\s*;/");
//		array_push($replace, "'WP_TEMP_DIR', '{$new_path}');");
//	}
//}

if (!is_writable($wpconfig_ark_path)) {
	$err_log = "\nWARNING: Unable to update file permissions and write to dup-wp-config-arc__[HASH].txt.  ";
	$err_log .= "Check that the wp-config.php is in the archive.zip and check with your host or administrator to enable PHP to write to the wp-config.php file.  ";
	$err_log .= "If performing a 'Manual Extraction' please be sure to select the 'Manual Archive Extraction' option on step 1 under options.";
	chmod($wpconfig_ark_path, 0644) ? DUPX_Log::info("File Permission Update: dup-wp-config-arc__[HASH].txt set to 0644") : DUPX_Log::error("{$err_log}");
}

$wpconfig_ark_contents = preg_replace($patterns, $replace, $wpconfig_ark_contents);
file_put_contents($wpconfig_ark_path, $wpconfig_ark_contents);


$config_transformer = new WPConfigTransformer($wpconfig_ark_path);

$config_transformer->update('constant', 'DB_NAME', trim(DUPX_U::wp_unslash($_POST['dbname'])));
$config_transformer->update('constant', 'DB_USER', trim(DUPX_U::wp_unslash($_POST['dbuser'])));
$config_transformer->update('constant', 'DB_PASSWORD', trim(DUPX_U::wp_unslash($_POST['dbpass'])));
$config_transformer->update('constant', 'DB_HOST', $_POST['dbhost']);

$licence_type = $GLOBALS['DUPX_AC']->getLicenseType();
if ($licence_type >= DUPX_LicenseType::Freelancer) {
	if ($_POST['auth_keys_and_salts']) {
		$need_to_change_const_keys = array(
			'AUTH_KEY',
			'SECURE_AUTH_KEY',
			'LOGGED_IN_KEY',
			'NONCE_KEY',
			'AUTH_SALT',
			'SECURE_AUTH_SALT',
			'LOGGED_IN_SALT',
			'NONCE_SALT',
		);
		foreach ($need_to_change_const_keys as $const_key) {
			$is_const_key_exists = $config_transformer->exists('constant', $const_key);
			$key = DUPX_WPConfig::generatePassword(64, true, true);
			
			if ($is_const_key_exists) {
				$config_transformer->update('constant', $const_key, $key);
			} else {
				$config_transformer->add('constant', $const_key, $key);
			}
		}
	}
}


$is_wp_debug_exists = $config_transformer->exists('constant', 'WP_DEBUG');
$wp_debug_as_str = (1 == $_POST['wp_debug']) ? 'true' : 'false';
if ($is_wp_debug_exists) {
	$config_transformer->update('constant', 'WP_DEBUG', $wp_debug_as_str, array('raw' => true));
} else {
    if (1 == $_POST['wp_debug']) {
        $config_transformer->add('constant', 'WP_DEBUG', $wp_debug_as_str, array('raw' => true));
    }
}

$is_wp_debug_log_exists = $config_transformer->exists('constant', 'WP_DEBUG_LOG');
$wp_debug_log_as_str = (1 == $_POST['wp_debug_log']) ? 'true' : 'false';
if ($is_wp_debug_log_exists) {
	$config_transformer->update('constant', 'WP_DEBUG_LOG', $wp_debug_log_as_str, array('raw' => true));
} else {
    if (1 == $_POST['wp_debug_log']) {
        $config_transformer->add('constant', 'WP_DEBUG_LOG', $wp_debug_log_as_str, array('raw' => true));
    }
}

DUPX_Log::info("UPDATED WP-CONFIG ARK FILE:\n - 'dup-wp-config-arc__[HASH].txt'");
DUPX_Log::info("SETTING WP DEBUG CONFIG constants");

if($_POST['retain_config']) {
	$new_htaccess_name = '.htaccess';
} else {
	$new_htaccess_name = 'htaccess.orig' . rand();
}

if(DUPX_ServerConfig::renameHtaccess($GLOBALS['DUPX_ROOT'], $new_htaccess_name)){
	DUPX_Log::info("\nReseted original .htaccess content from htaccess.orig");
}

//Web Server Config Updates
if (!isset($_POST['url_new']) || $_POST['retain_config']) {
	DUPX_Log::info("\nNOTICE: Retaining the original .htaccess, .user.ini and web.config files may cause");
	DUPX_Log::info("issues with the initial setup of your site.  If you run into issues with your site or");
	DUPX_Log::info("during the install process please uncheck the 'Config Files' checkbox labeled:");
	DUPX_Log::info("'Retain original .htaccess, .user.ini and web.config' and re-run the installer.");    
} else {
	DUPX_ServerConfig::setup($GLOBALS['DUPX_AC']->mu_mode, $GLOBALS['DUPX_AC']->mu_generation, $dbh, $root_path);
}

//===============================================
//GENERAL UPDATES & CLEANUP
//===============================================
DUPX_Log::info("\n====================================");
DUPX_Log::info('GENERAL UPDATES & CLEANUP:');
DUPX_Log::info("====================================\n");

$blog_name   = mysqli_real_escape_string($dbh, $_POST['blogname']);
$plugin_list = (isset($_POST['plugins'])) ? $_POST['plugins'] : array();
// Force Duplicator Pro active so we the security cleanup will be available
if(($GLOBALS['DUPX_AC']->mu_mode > 0) && ($subsite_id == -1))
{
	$multisite_plugin_list=array();
	foreach($plugin_list as $get_plugin)
	{
		$multisite_plugin_list[$get_plugin] = time();
	}

	if (!array_key_exists('duplicator-pro/duplicator-pro.php', $multisite_plugin_list)) {
		$multisite_plugin_list['duplicator-pro/duplicator-pro.php'] = time();
	}

	$serial_plugin_list	 = @serialize($multisite_plugin_list);
}
else
{
	if (!in_array('duplicator-pro/duplicator-pro.php', $plugin_list)) {
		$plugin_list[] = 'duplicator-pro/duplicator-pro.php';
	}
	$serial_plugin_list	 = @serialize($plugin_list);
}

/** FINAL UPDATES: Must happen after the global replace to prevent double pathing
  http://xyz.com/abc01 will become http://xyz.com/abc0101  with trailing data */
mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET option_value = '".mysqli_real_escape_string($dbh, $blog_name)."' WHERE option_name = 'blogname' ");
if(($GLOBALS['DUPX_AC']->mu_mode > 0) && ($subsite_id == -1))
{
	mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."sitemeta` SET meta_value = '".mysqli_real_escape_string($dbh, $serial_plugin_list)."'  WHERE meta_key = 'active_sitewide_plugins' ");
}
else
{
	mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET option_value = '".mysqli_real_escape_string($dbh, $serial_plugin_list)."'  WHERE option_name = 'active_plugins' ");
}
mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET option_value = '".mysqli_real_escape_string($dbh, $_POST['url_new'])."'  WHERE option_name = 'home' ");
mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` SET option_value = '".mysqli_real_escape_string($dbh, $_POST['siteurl'])."'  WHERE option_name = 'siteurl' ");
mysqli_query($dbh, "INSERT INTO `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` (option_value, option_name) VALUES('".mysqli_real_escape_string($dbh, $_POST['exe_safe_mode'])."','duplicator_pro_exe_safe_mode')");
//Reset the postguid data
if ($_POST['postguid']) {
	mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."posts` SET guid = REPLACE(guid, '".mysqli_real_escape_string($dbh, $_POST['url_new'])."', '".mysqli_real_escape_string($dbh, $_POST['url_old'])."')");
	$update_guid = @mysqli_affected_rows($dbh) or 0;
	DUPX_Log::info("Reverted '{$update_guid}' post guid columns back to '{$_POST['url_old']}'");
}


$mu_updates = @mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."blogs` SET domain = '".mysqli_real_escape_string($dbh, $mu_newDomainHost)."' WHERE domain = '".mysqli_real_escape_string($dbh, $mu_oldDomainHost)."'");
if ($mu_updates) {
	DUPX_Log::info("- Update MU table blogs: domain {$mu_newDomainHost} ");
}

if ($GLOBALS['DUPX_AC']->mu_mode == 2) {
	// _blogs update path column to replace /oldpath/ with /newpath/ */
	$result = @mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."blogs` SET path = CONCAT('".mysqli_real_escape_string($dbh, $mu_newUrlPath)."', SUBSTRING(path, LENGTH('".mysqli_real_escape_string($dbh, $mu_oldUrlPath)."') + 1))");
	if ($result === false) {
		DUPX_Log::error("Update to blogs table failed\n".mysqli_error($dbh));
	}
}


if (($GLOBALS['DUPX_AC']->mu_mode == 1) || ($GLOBALS['DUPX_AC']->mu_mode == 2)) {
	// _site update path column to replace /oldpath/ with /newpath/ */
	$result = @mysqli_query($dbh, "UPDATE `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."site` SET path = CONCAT('".mysqli_real_escape_string($dbh, $mu_newUrlPath)."', SUBSTRING(path, LENGTH('".mysqli_real_escape_string($dbh, $mu_oldUrlPath)."') + 1)), domain = '".mysqli_real_escape_string($dbh, $mu_newDomainHost)."'");
	if ($result === false) {
		DUPX_Log::error("Update to site table failed\n".mysqli_error($dbh));
	}
}

//SCHEDULE STORAGE CLEANUP
if (($_POST['empty_schedule_storage']) == true || (DUPX_U::$on_php_53_plus == false)) {

	$dbdelete_count	 = 0;
	$dbdelete_count1 = 0;
	$dbdelete_count2 = 0;

	@mysqli_query($dbh, "DELETE FROM `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."duplicator_pro_entities` WHERE `type` = 'DUP_PRO_Storage_Entity'");
	$dbdelete_count1 = @mysqli_affected_rows($dbh);

	@mysqli_query($dbh, "DELETE FROM `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."duplicator_pro_entities` WHERE `type` = 'DUP_PRO_Schedule_Entity'");
	$dbdelete_count2 = @mysqli_affected_rows($dbh);

	$dbdelete_count = (abs($dbdelete_count1) + abs($dbdelete_count2));
	DUPX_Log::info("- Removed '{$dbdelete_count}' schedule storage items");
}

//===============================================
//NOTICES TESTS
//===============================================
DUPX_Log::info("\n====================================");
DUPX_Log::info("NOTICES");
DUPX_Log::info("====================================\n");
$config_vars	= array('WPCACHEHOME', 'COOKIE_DOMAIN', 'WP_SITEURL', 'WP_HOME', 'WP_TEMP_DIR');
$config_found	= DUPX_U::getListValues($config_vars, $wpconfig_ark_contents);

//Files
if (! empty($config_found)) {
	$msg   = "WP-CONFIG NOTICE: The wp-config.php has following values set [".implode(", ", $config_found)."].  \n";
	$msg  .= "Please validate these values are correct by opening the file and checking the values.\n";
	$msg  .= "See the codex link for more details: https://codex.wordpress.org/Editing_wp-config.php";
	$JSON['step3']['warnlist'][] = $msg;
	DUPX_Log::info($msg);
}

//Database
$result = @mysqli_query($dbh, "SELECT option_value FROM `".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options` WHERE option_name IN ('upload_url_path','upload_path')");
if ($result) {
	while ($row = mysqli_fetch_row($result)) {
		if (strlen($row[0])) {
			$msg  = "MEDIA SETTINGS NOTICE: The table '".mysqli_real_escape_string($dbh, $GLOBALS['DUPX_AC']->wp_tableprefix)."options' has at least one the following values ['upload_url_path','upload_path'] \n";
			$msg .=	"set please validate settings. These settings can be changed in the wp-admin by going to /wp-admin/options.php'";
			$JSON['step3']['warnlist'][] = $msg;
			DUPX_Log::info($msg);
			break;
		}
	}
}

if (empty($JSON['step3']['warnlist'])) {
	DUPX_Log::info("No General Notices Found\n");
}

$JSON['step3']['warn_all'] = empty($JSON['step3']['warnlist']) ? 0 : count($JSON['step3']['warnlist']);

mysqli_close($dbh);

//-- Finally, back up the old wp-config and rename the new one

$wpconfig_path	= "{$root_path}/wp-config.php";
$wpconfig_orig_path	= "{$root_path}/wp-config.duporig";

/*
if(file_exists($wpconfig_path)) {	
	if (!is_writable($wpconfig_path)) {
		$err_log = "\nWARNING: Unable to update file permissions and write to {$wpconfig_path}.  ";
		$err_log .= "Check that the wp-config.php is in the archive.zip and check with your host or administrator to enable PHP to write to the wp-config.php file.  ";
		$err_log .= "If performing a 'Manual Extraction' please be sure to select the 'Manual Archive Extraction' option on step 1 under options.";
		chmod($wpconfig_path, 0644) ? DUPX_Log::info("File Permission Update: {$wpconfig_path} set to 0644") : DUPX_Log::error("{$err_log}");
	}

	if(rename($wpconfig_path, $wpconfig_orig_path) === false) {
		DUPX_Log::error("Unable to rename {$wpconfig_path} top {$wpconfig_orig_path}");
	}
}
*/

$wpconfig_path = "{$GLOBALS['DUPX_ROOT']}/wp-config.php";

if (copy($wpconfig_ark_path, $wpconfig_path) === false) {

	DUPX_Log::error("ERROR: Unable to copy 'dup-wp-config-arc__[HASH].txt' to 'wp-config.php'. Check server permissions for more details see FAQ: https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-055-q");

}

//Cleanup any tmp files a developer may have forgotten about
//Lets be proactive for the developer just in case
$wpconfig_path_bak	= "{$GLOBALS['DUPX_ROOT']}/wp-config.bak";
$wpconfig_path_old	= "{$GLOBALS['DUPX_ROOT']}/wp-config.old";
$wpconfig_path_org	= "{$GLOBALS['DUPX_ROOT']}/wp-config.org";
$wpconfig_path_orig	= "{$GLOBALS['DUPX_ROOT']}/wp-config.orig";
$wpconfig_safe_check = array($wpconfig_path_bak, $wpconfig_path_old, $wpconfig_path_org, $wpconfig_path_orig);
foreach ($wpconfig_safe_check as $file) {
	if(file_exists($file)) {
		$tmp_newfile = $file . uniqid('_');
		if(rename($file, $tmp_newfile) === false) {
			DUPX_Log::info("WARNING: Unable to rename '{$file}' to '{$tmp_newfile}'");
		}
	}
}

if (isset($_POST['remove_redundant']) && $_POST['remove_redundant']) {		
	$licence_type = $GLOBALS['DUPX_AC']->getLicenseType();		
	if ($licence_type >= DUPX_LicenseType::Freelancer) {
		// Need to load if user selected redundant-data checkbox
		require_once($GLOBALS['DUPX_INIT'].'/classes/utilities/class.u.remove.redundant.data.php');

		$new_content_dir = (substr($_POST['path_new'], -1, 1) == '/') ? "{$_POST['path_new']}{$GLOBALS['DUPX_AC']->relative_content_dir}"
		: "{$_POST['path_new']}/{$GLOBALS['DUPX_AC']->relative_content_dir}";
		
		try {
			DUPX_Log::info("#### Recursively deleting redundant plugins");
			DUPX_RemoveRedundantData::deleteRedundantPlugins($new_content_dir, $GLOBALS['DUPX_AC'], $subsite_id);
		} catch (Exception $ex) {
			// Technically it can complete but this should be brought to their attention
			DUPX_Log::error("Problem deleting redundant plugins");
		}

		try {
			DUPX_Log::info("#### Recursively deleting redundant themes");
			DUPX_RemoveRedundantData::deleteRedundantThemes($new_content_dir, $GLOBALS['DUPX_AC'], $subsite_id);
		} catch (Exception $ex) {
			// Technically it can complete but this should be brought to their attention
			DUPX_Log::error("Problem deleting redundant themes");
		}
	}
}

$ajax3_sum = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $ajax3_start);
DUPX_Log::info("\nSTEP-3 COMPLETE @ ".@date('h:i:s')." - RUNTIME: {$ajax3_sum} \n\n");

$JSON['step3']['pass'] = 1;
error_reporting($ajax3_error_level);
die(json_encode($JSON));
