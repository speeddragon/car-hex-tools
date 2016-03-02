<?php
	include('utils.php');
?>
<style>
body {
	font-family: "Courier New", Courier, monospace;
	text-transform: uppercase;
}
.number span {
	padding-right: 8px;
}
</style>
<?php
	if (count($_POST)) {
		echo "<h1>Reading files ...</h1> ";
                $file1 = $_FILES['file1']['tmp_name'];
		$file2 = $_FILES['file2']['tmp_name'];

		$fileDiff = hexDiff($file1, $file2);
		echo '<div style="float: left; font-weight: bold; padding-right: 20px; text-align: right;">';
		for($i = 0; $i < filesize($file1); $i = $i + 8) {
			echo dechex($i) . "<br/>";
		}
		echo '</div>';
		echo '<div class="number">';
		$i = 0;
		foreach($fileDiff as $bytePos) {
			if (is_array($bytePos)) {
				echo '<span style="color: red;" title="'.$bytePos[0].' != '.$bytePos[1].'">'.$bytePos[1].'</span>';
			} else {
				echo '<span>'.$bytePos.'</span>';
			}
			$i++;

			if ($i > 7) {
				echo '</br>';
				$i = 0;
			}
		}
		echo '<div>';
	}
?>

<h2>Binary Diff Tool</h2>

<form method="post" enctype="multipart/form-data">
	<input type="file" name="file1" id="file1">
	<input type="file" name="file2" id="file2">

	<input type="submit" value="Upload Image" name="submit">
</form>
