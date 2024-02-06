<?php
namespace Plex\Template\Pageinate;

use Plex\Template\Pageinate\Pageinate;


class ArtistPagenate extends Pageinate
{
    public $table   = Db_TABLE_ARTISTS;
    public $library = false;

    public function __construct($currentPage, $urlPattern)
    {
        parent::__construct(false, $currentPage, $urlPattern);
    }
}
