<?php
/**
 * plex web viewer
 */

require_once '_config.inc.php';
if (true == $_GET['q']) {
    $q    = $_GET['q'];

    $db->where('tag_name', '%'.$q.'%', 'like');
    $tags = $db->get('tags');

    if ($db->count > 0) {
        foreach ($tags as $tag) {
            echo $tag['tag_name']."\n";
        }
    }

    exit;
}
$t = new Template();
 if (array_key_exists('action', $_REQUEST)) {
    if($_REQUEST['action'] == 'refresh'){
        define('TITLE', 'Home');

        include __LAYOUT_HEADER__;
    }
 }

$forms = new ProcessForms($_REQUEST);
