<?php

use Plex\Core\FileListing;
use Plex\Template\Display\VideoDisplay;
use Plex\Template\Template;
use Plex\Template\HTML\Elements;

define('TITLE', 'Home');
define('NONAVBAR', true);
define('VIDEOINFO', true);
define('SHOW_RATING', true);

require_once '_config.inc.php';

$videoInfo = (new FileListing())->getVideoDetails($_REQUEST['id']);
$vidInfo = (new VideoDisplay())->init('videoinfo')->Display($videoInfo);

Template::echo('videoinfo/page', $vidInfo);
