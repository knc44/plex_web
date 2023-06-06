<?php
require_once("_config.inc.php");

define('TITLE', "tags");

if (isset($_SESSION['sort'])) {
    $uri['sort'] = $_SESSION['sort'];
}

if (isset($_SESSION['direction'])) {
    $uri['direction'] = $_SESSION['direction'];
}

if (isset($_REQUEST['query'])) {
    $uri['query'] = $_REQUEST['query'];
}

if (isset($uri)) {
    $request_key   = uri_String($uri);
}

$redirect_string = 'search.php' . $request_key;
$field = 'genre';

$sql = "SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING_INDEX(".$field.", ',', n.digit+1), ',', -1) val
 FROM metatags_filedb INNER JOIN (SELECT 0 digit UNION ALL SELECT 
 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6) n 
 ON LENGTH(REPLACE(".$field.", ',' , '')) <= LENGTH(".$field.")-n.digit WHERE library = '". $_SESSION['library']."' ORDER BY `val` ASC";
$results = $db->query($sql);

include_once __LAYOUT_HEADER__;

template::echo("base/page", ['BODY' => keyword_cloud($results, $field)]);

include_once __LAYOUT_FOOTER__;
