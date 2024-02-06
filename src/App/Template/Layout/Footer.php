<?php
namespace Plex\Template\Layout;
/**
 * plex web viewer
 */

use Plex\Template\Display\Display;
use Plex\Template\Template;

class Footer
{
    public static function Display()
    {
        global $pageObj,$url_array;
        $sort_html = '';
        $page_html = '';
        $navbar    = '';
        $js_html   = '';
        if (!defined('__BOTTOM_NAV__')) {
            define('__BOTTOM_NAV__', true);
        }
        if (!defined('__SHOW_SORT__')) {
            define('__SHOW_SORT__', true);
        }
        if (!defined('__SHOW_PAGES__')) {
            define('__SHOW_PAGES__', true);
        }
        if (!defined('NONAVBAR')) {
            if (__BOTTOM_NAV__ == 1) {
                if (__SHOW_SORT__ == true && isset($pageObj)) {
                    $sort_html = Template::return('base/footer/sort', 
                    ['SORT_HTML' => Display::sort_options($url_array)]);
                }

                if (__SHOW_PAGES__ == true && isset($pageObj)) {
                    $page_html = $pageObj->toHtml();
                }

                $footer_nav = ['FOOTER_NAV' => $sort_html.$page_html];
                $navbar     = Template::return('base/footer/navbar', $footer_nav);
            }
        }
        Template::echo('base/footer/main', ['FOOT_NAVBAR' => $navbar]);
    }
}
