<?php
/**
 * plex web viewer
 */

if (APP_AUTHENTICATION == true) {
    if (isset($_SESSION['auth'])) {
        $_SESSION['expire'] = ALLOWED_INACTIVITY_TIME;
    }
    generate_csrf_token();
    check_remember_me();

    if (array_key_exists(basename(__THIS_FILE__, '.php'), __AUTH_FUNCTION__)) {
        __AUTH_FUNCTION__[basename(__THIS_FILE__, '.php')]();
    } else {
        check_verified();
    }
} else {
    $_SESSION['auth'] = 'verified';
}
$params['APP_DESCRIPTION'] = APP_DESCRIPTION;
$params['APP_OWNER']       = APP_OWNER;
$params['__URL_HOME__']    = __URL_HOME__;
$params['TITLE']           = TITLE;
$params['APP_NAME']        = APP_NAME;
$params['__LAYOUT_URL__']  = __LAYOUT_URL__;

// if (!defined('NONAVBAR')) {
$css_dir                   = __LAYOUT_PATH__.'/external/css/theme/';
$files                     = RoboLoader::get_filelist($css_dir, 'bootstrap.min.css', 0);

foreach ($files as $stylesheet) {
    $dirArray             = explode('/', $stylesheet);
    array_pop($dirArray);
    $theme                = end($dirArray);
    Render::$CSS_THEMES[] = $theme;
    $stylesheet           = str_replace(__LAYOUT_PATH__, __LAYOUT_URL__, $stylesheet);

    // $name =
    $css_html .= process_template('base/header/header_css_link', ['CSS_NAME' => $theme, 'CSS_URL' => $stylesheet]);
}
$params['CSS_HTML']        = $css_html;
$params['JS_CSS_SWITCHER'] = '<script src="'.__LAYOUT_URL__.'js/styleswitch.js?fsdsfd" type="text/javascript"></script>'.\PHP_EOL;
// }
$params['SCRIPTS']         = process_template('base/header/header_scripts', $params);

// if (!defined('VIDEOINFO')) {
$params['SCRIPTS'] .= process_template('base/header/header_filelist', ['__LAYOUT_URL__' => __LAYOUT_URL__]);


$params['THEME_JS']        = process_template('base/header/header_themejs', []);
// } else {
//     $params['SCRIPTS']  .= process_template('base/header/header_videoinfo', ['__LAYOUT_URL__' => __LAYOUT_URL__]);
//  //     $params['ONLOAD'] = 'onbeforeunload="refreshAndClose();"';
// }

if (defined('GRID_VIEW')) {
    $params['SCRIPTS'] .= process_template('base/header/header_grid', ['__LAYOUT_URL__' => __LAYOUT_URL__]);
}

Template::echo('base/header/header', $params);

if (!defined('NONAVBAR')) {
    $crumbs = Render::createBreadcrumbs();
    define('BREADCRUMB', $crumbs);

    require __LAYOUT_NAVBAR__;
} else {
    $crumbs = Render::display_theme_dropdown();
    define('THEME_SWITCHER', $crumbs);
}
