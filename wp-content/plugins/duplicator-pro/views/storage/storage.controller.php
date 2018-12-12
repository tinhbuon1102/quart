<style>
    div#dup-store-err-details {display:none}
</style>
<?php

defined("ABSPATH") or die("");
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/views/inc.header.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/lib/snaplib/class.snaplib.u.url.php');

$profile_url = DUP_PRO_U::getMenuPageURL(DUP_PRO_Constants::$STORAGE_SUBMENU_SLUG, false);
$storage_tab_url = SnapLibURLU::appendQueryValue($profile_url, 'tab', 'storage');

$edit_storage_url = SnapLibURLU::appendQueryValue($storage_tab_url, 'inner_page', 'edit');
$edit_default_storage_url = SnapLibURLU::appendQueryValue($storage_tab_url, 'inner_page', 'edit-default');

$inner_page = isset($_REQUEST['inner_page']) ? sanitize_text_field($_REQUEST['inner_page']) : 'storage';

try {
  
    switch ($inner_page) {
        case 'storage':
            include('storage.list.php');
            break;

        case 'edit':
            include('storage.edit.php');
            break;

        case 'edit-default':
            include('storage.edit.default.php');
            break;
    }
} 
catch (Exception $e) {
    echo '<div class="error-txt" style="margin:10px 0 20px 0; max-width:750px">';
        DUP_PRO_U::esc_html_e('An error has occurred while trying to read a storage item!  ');
        DUP_PRO_U::esc_html_e('To resolve this issue please delete the storage item and re-enter its information.  ');
        DUP_PRO_U::esc_html_e('If the problem persists please contact the support team.');
    echo '</div>';
    echo '<a href="javascript:void(0)" onclick="jQuery(\'#dup-store-err-details\').toggle();">';
        DUP_PRO_U::esc_html_e('Show Details');
    echo '</a>';
    echo '<div id="dup-store-err-details">' . $e->getMessage() 
            .  "<br/><br/><small>". $e->getTraceAsString(); "</small></div>";
}


