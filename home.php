<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php"));

require_once("_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;

$sql = query_builder("studio","library = '" . $in_directory ."' ",'studio','studio ASC');
$result = $db->query($sql);

$sql = query_builder("DISTINCT(library) as library ");
$result2 = $db->query($sql);

?>

<main role="main" class="container">

	<?php
	foreach ($result2 as $k => $v) {
		echo "<a href='home.php?library=" . $v["library"] . "'>" . $v["library"] . "</a> | ";
	}
	echo "<ul> \n";
	foreach ($result as $k => $v) {

		if ($v["studio"] != "") {
			$sql = query_builder("count(studio) as cnt",$lib_where . ' studio like "' . $v['studio'].'" and substudio is null','studio','studio ASC');
	$rar = $db->rawQueryOne($sql);
	$cnt='';
	if(isset($rar['cnt'])) $cnt = " (".$rar['cnt'].") ";
			$sql = query_builder("count(substudio) as cnt, substudio",$lib_where . ' studio like "' . $v['studio'].'"','substudio','substudio ASC');

			
			$alt_result = $db->query($sql);

			$studio = str_replace(" ", "-", $v['studio']);
			$studio = str_replace("/", "_", $studio);

			echo "<li><a href='studio.php?studio=" . $studio . $lib_req . "'>" . $v["studio"] . "</a>" . $cnt . "<br>";

			if (count($alt_result) > 1) {
				echo "<ul>";

				foreach ($alt_result as $k_a => $v_a) {
					if ($v_a["substudio"] != "") {
						$cntv_a = " (".$v_a["cnt"].")";
						$substudio = str_replace(" ", "-", $v_a['substudio']);
						$substudio = str_replace("/", "_", $substudio);
						echo "<li><a href='studio.php?substudio=" . $substudio . $lib_req . "'>" . $v_a["substudio"] . "</a>" . $cntv_a . "<br>";
					}
				}
				echo "</ul>";
			}
		}
	}
	echo "</ul>";

	?>
	</ul>
</main>
<?php include __LAYOUT_FOOTER__;  ?>