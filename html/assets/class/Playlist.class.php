<?php
/**
 * plex web viewer
 */

require_once 'Process.class.php';
class Playlist extends ProcessForms
{
    public object $db;

    public $data;
    public $library;
    public $playlist_id;

    public function __construct($data)
    {
        global $db,$_SESSION;

        $this->data    = $data;
        $this->db      = $db;
        $this->library = $_SESSION['library'];
        if (isset($data['playlist_id'])) {
            $this->playlist_id = $data['playlist_id'];
        }
    }

    public function addAllPlaylist()
    {
        $url = $this->createPlaylist();
        echo $this->myHeader($url);
    }

    public function createPlaylist()
    {
        // dump([__METHOD__,$this->data]);

        $name        = 'User Playlist';
        $studio      = [];

        if (key_exists('substudio', $this->data)) {
            $name      = '';
            $studio[]  = $this->data['substudio'];
        }
        if (key_exists('studio', $this->data)) {
            $name      = '';
            $studio[]  = $this->data['studio'];
        }

        $data        = [
            'name'            => $name.implode(' ', $studio),
            'genre'           => 'mmf,mff',
            'library'         => $this->library,
        ];

        $playlist_id = $this->db->insert(Db_TABLE_PLAYLIST_DATA, $data);

        if (!is_array($this->data['playlist'])) {
            $this->data['playlist'] = explode(',', $this->data['playlist']);
        }

        foreach ($this->data['playlist'] as $_ => $id) {
            $data = [
                'playlist_id'     => $playlist_id,
                'playlist_videos' => $id,
                'library'         => $this->library,
            ];
            $this->db->insert(Db_TABLE_PLAYLIST_VIDEOS, $data);
        }

        return __URL_HOME__.'/playlist.php?playlist_id='.$playlist_id.'';
    }

    public function addPlaylist()
    {
        // dump([__METHOD__,$this->data]);

        $data = [
            'playlist_id'     => $this->playlist_id,
            'playlist_videos' => $this->data['video_id'],
            'library'         => $this->library,
        ];
        $res  = $this->db->insert(Db_TABLE_PLAYLIST_VIDEOS, $data);

        return 0;
    }

    public function deletePlaylist()
    {
        // dump([__METHOD__,$this->playlist_id]);

        $sql     = 'delete d,v from '.Db_TABLE_PLAYLIST_DATA.'  d join '.Db_TABLE_PLAYLIST_VIDEOS.' v on d.id = v.playlist_id where d.id = '.$this->playlist_id.'';
        $results = $this->db->query($sql);
        $this->myHeader('playlist.php');

        return 0;
    }

    public function savePlaylist()
    {
        // dump([__METHOD__,$this->data]);
        if (isset($this->data['playlist_name'])) {
            $playlist_name = $this->data['playlist_name'];
            if ('' != $playlist_name) {
                $update[] = " name = '".$playlist_name."' ";
            }
        }

        if (isset($this->data['playlist_genre'])) {
            $playlist_genre = $this->data['playlist_genre'];
            if ('' != $playlist_genre) {
                $update[] = " genre = '".$playlist_genre."' ";
            }
        }

        if (isset($update)) {
            $update_str = implode(', ', $update);
            $sql        = 'UPDATE '.Db_TABLE_PLAYLIST_DATA.' SET '.$update_str.' WHERE id = '.$this->playlist_id.'';
            $results    = $this->db->query($sql);
        }

        if (isset($this->data['prune_playlist'])) {
            $video_ids     = $this->data['prune_playlist'];
            foreach ($video_ids as $_ => $id) {
                $video_id_array[] = $id;
            }
            $video_ids_str = implode(', ', $video_id_array);
            $sql           = 'delete from '.Db_TABLE_PLAYLIST_VIDEOS.' where id in ('.$video_ids_str.')';
            $results       = $this->db->query($sql);
        }

        $form_url = __URL_HOME__.'/playlist.php?playlist_id='.$this->playlist_id.'';
        $this->myHeader($form_url);
    }
}
