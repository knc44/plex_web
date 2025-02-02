<?php

namespace Plex\Modules\Display;

use Plex\Modules\Database\PlexSql;
use Plex\Modules\Display\Display;
use Plex\Template\Functions\Functions;
use Plex\Template\Render;

class Layout
{
    public static function Header()
    {
        $params = [];
        if (OptionIsTrue(GRID_VIEW)) {
            $params['SCRIPTS'] .= Render::html('base/header/header_grid', ['__LAYOUT_URL__' => __LAYOUT_URL__]);
        }

        Render::echo('base/header/header', $params);

        if (OptionIsTrue(NAVBAR)) {
            $crumbs = (new Functions())->createBreadcrumbs();
            \define('BREADCRUMB', $crumbs);
            self::Navbar($params);
        }
    }

    public static function Navbar($params)
    {
        $db = PlexSql::$DB;
        $library_links = '';
        $sql = PlexSql::query_builder(Db_TABLE_VIDEO_TAGS, 'DISTINCT(library) as library ');
        foreach ($db->query($sql) as $k => $v) {
            $library_links .= Display::navbar_left_links('home.php?library='.$v['library'], $v['library']);
        }
        $library_links .= Display::navbar_left_links('home.php?library=All', 'All');

        $params['NAV_BAR_LEFT_LINKS'] = Render::html('base/navbar/library_menu',
            ['LIBRARY_SELECT_LINKS' => $library_links]);
        Render::echo('base/navbar/main', $params);
    }

    public static function Footer()
    {
        global $pageObj;
        $params = [];
        $page_html = '';
        $navbar = '';
        if (OptionIsTrue(BOTTOM_NAV)) {
            if (OptionIsTrue(SHOW_PAGES) && isset($pageObj)) {
                $page_html = $pageObj->toHtml();
            }

            $footer_nav = ['FOOTER_NAV' => $page_html];
            $navbar = Render::html('base/footer/navbar', $footer_nav);
        }

        Render::echo('base/footer/main', ['FOOT_NAVBAR' => $navbar]);
    }
}
