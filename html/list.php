<?php

use Plex\Core\Request;
use Plex\Template\Render;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
use Plex\Template\Display\Display;
use Plex\Modules\Database\FileListing;
use Plex\Template\Display\VideoDisplay;

require_once '_config.inc.php';

$fileinfo = new FileListing(new Request);
[$results,$pageObj,$uri] = $fileinfo->getVideoArray();

$request_key = uri_String($uri);
$redirect_string = __THIS_FILE__.$request_key;

if (array_key_exists('genre', $_REQUEST)) {
    $studio_url = urlQuerystring($redirect_string, 'genre');
}

$referer_url = '';
if ('home.php' != basename($_SERVER['HTTP_REFERER'])) {
    $referer_url = $_SERVER['HTTP_REFERER'];
}
Display::$CrubURL['grid'] = 'grid.php';

$vidInfo = (new VideoDisplay('List'))->init('filelist');
$body = $vidInfo->Display($results, [
    'total_files' => $pageObj->totalRecords,
    'redirect_string' => $redirect_string,
]);


// Header::Display();
Render::Display($body);
// Footer::Display();
