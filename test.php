<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php") );
DEFINE('__DISPLAY__', ["sort" => true , "page" => true ]);

require_once("_config.inc.php");
define('TITLE', "Test Page");


include __LAYOUT_HEADER__;


?>
    
<main role="main" class="container">
<a href="home.php">back</a>
<br>
<br>

<?php

$results = $db->where($lib_where)->get(Db_TABLE_FILEDB,$limit_array);

echo display_filelist($results);

 ?>
 </main>
 <?php include __LAYOUT_FOOTER__;  ?>