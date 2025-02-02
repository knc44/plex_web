<?php

use Plex\Modules\Database\PlexSql;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;

require_once '_config.inc.php';

// define('BREADCRUMB', ['home' => "home.php"]);

$results = PlexSql::$DB->getArtists();

$VideoDisplay = new Functions();
$AristArray = [];
// $sortedArray[0]      = [];
function compareArtist(&$array, $artist)
{
    $keyName = strtolower(str_replace('.', '-', $artist));
    $keyName = strtolower(str_replace(' ', '_', $keyName));

    if (array_key_exists($keyName, $array)) {
        ++$array[$keyName];
    } else {
        $array[$keyName] = 1;
    }
}

foreach ($results as $k => $value) {
    if (null !== $value['artist']) {
        if (str_contains($value['artist'], ',')) {
            $name_arr = explode(',', $value['artist']);
            // if (null === $vname_arr) {
            //     utmdump($name_arr, $value['artist']);
            //     continue;
            // }
            foreach ($name_arr as $name) {
                compareArtist($AristArray, $name);
            }
        } else {
            compareArtist($AristArray, $value['artist']);
        }
    }
}

foreach ($AristArray as $artist => $num) {
    if ('' == $artist) {
        continue;
    }
    if ('missing' == $artist) {
        continue;
    }
    $sortedArray[$num][] = $artist;
}

$array = krsort($sortedArray, \SORT_NUMERIC);
// $array= ksort($sortedArray,SORT_NUMERIC);
$artist_html = '';
foreach ($sortedArray as $num => $artistArray) {
    $artist_box = [];
    $link_array = [];

    sort($artistArray);
    $artist_box['COUNT_HTML'] = Render::html('artist/artist_count', ['ARTIST_COUNT' => $num]);
    $artist_links = '';

    // foreach($artistArray as $artist)
    // {

    //    //$artist_links .= Render::html("artist/artist_link",['ARTIST'=>$artist,'ARTIST_NAME'=>$name]);
    //    //$artist_links .= Elements::keyword_cloud($name,'artist');
    // }
    $field = 'artist';
    $search_url = 'search.php?field='.$field.'&query=';
    // $last_letter = '';
    foreach ($artistArray as $k => $artist) {
        $letter = substr($artist, 0, 1);
        if (!isset($last_letter)) {
            $last_letter = $letter;
        }
        if ($letter != $last_letter) {
            $last_letter = $letter;
            $link_array[] = '</div><div class="d-flex flex-wrap mt-2">';
        }
        $name = strtolower(str_replace('-', '.', $artist));
        $name = strtolower(str_replace('_', ' ', $name));
        $link_array[] = Render::html(
            VideoCard::$template.'/search_link',
            [
                'KEY' => $field,
                'QUERY' => urlencode($name),
                'URL_TEXT' => $name,
                'CLASS' => ' class="badge fs-6 blueTable-thead" ',
            ]
        );
    }
    unset($last_letter);
    $artist_links = implode('  ', $link_array);
    // utmdd($link_array);
    $artist_box['ARTIST_LINKS'] = $artist_links;

    $artist_html .= Render::html('artist/artist_box', $artist_box);
}
$params['ARTIST_HTML'] = $artist_html;
$params['THUMBNAIL_HTML'] = '';
$sql = 'select m.title,v.id,v.thumbnail,v.filename from '.Db_TABLE_VIDEO_TAGS.' as m, '.Db_TABLE_VIDEO_FILE.' as v';
$sql = $sql." WHERE m.library = '".$_SESSION['library']."' and (m.artist is  null or m.artist = 'Missing' ) and (v.video_key = m.video_key)";
$results = $db->query($sql);
foreach ($results as $num => $artistArray) {
    $title = $artistArray['title'];
    $id = $artistArray['id'];
    $thumbnail = $artistArray['thumbnail'];
    $titleBg = '';
    if ('' == $artistArray['title']) {
        $title = str_replace('_', ' ', $artistArray['filename']);
        // $titleBg = ' bg-info ';
    }
    $params['THUMBNAIL_HTML'] .= Render::html(
        'artist/artist_thumbnail',
        [
            'MISSING_TITLE_BG' => $titleBg,
            'THUMBNAIL' => $VideoDisplay->fileThumbnail($id),
            'FILE_ID' => $id,
            'TITLE' => $title,
        ]
    );
    //  utmdd($artistArray);
}

Render::Display(Render::html('artist/cloud', $params));
// Render::echo("artist/main",$PARAMS);
