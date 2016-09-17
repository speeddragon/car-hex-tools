<?php
	include('utils/HexUtils.php');

    if (!file_exists('cleaned')) {
        mkdir('cleaned');
    }
?>
<html>
    <head>
        <title>Hex Diff Tool</title>

        <link href="https://fonts.googleapis.com/css?family=Droid+Sans:400:700|Russo+One|Unica+One|Inconsolata|Lato:300,400,900|Oswald:400,300,700" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="style.css" type="text/css">

        <style>
            .hex {
                font-family: "Courier New", Courier, monospace;
                text-transform: uppercase;
            }
            .number span {
                margin-right: 8px;
            }
            form label {
                display: block;
                font-weight: bold;
                padding-top: 10px;
            }
        </style>
    </head>
    <body>
    	<div class="header">
            <h1>Dump cleaner</h1>

            <div>This will look at the crash file and clear file example, and will apply the sames changes to the new crash dump in order to clean it.</div>

            <form method="post" enctype="multipart/form-data">
                <div>
                    <label for="crashFile">Crash Dump (Example)</label>
                    <input type="file" name="crashFile" id="crashFile">
                </div>

                <div>
                    <label for="cleanFile">Clean Dump (Example)</label>
                    <input type="file" name="cleanFile" id="cleanFile">
                </div>

                <div>
                    <label for="toCleanFile">Crash Dump (to clean)</label>
                    <input type="file" name="toCleanFile" id="toCleanFile">
                </div>

                <input type="submit" value="Clean" name="submit">
            </form>
        </div>

        <div>
        	<?php 
	        	if (count($_POST)) {
	                $crashFile = $_FILES['crashFile']['tmp_name'];
	                $cleanFile = $_FILES['cleanFile']['tmp_name'];
	                $toCleanFile = $_FILES['toCleanFile']['tmp_name'];

	                try {
	                	$newFilename = HexUtils::cleanDump($crashFile, $cleanFile, $toCleanFile);

	                	if ($newFilename) {
	                		$pathInfo = pathinfo($_FILES['toCleanFile']['name']);
	                		$readableFilename = 'cleaned/' . $pathInfo['filename'] . '_CLEAN.' . $pathInfo['extension'];

	                		if (file_exists($readableFilename)) {
	                			unlink($readableFilename);
	                		}

	                		rename($newFilename, $readableFilename);
	                		echo '<a href="' . $readableFilename . '">Download clean dump</a>';
	                	}
	                } catch (Exception $e) {
	                	if ($e->getMessage() == "DIFFERENT_SIZES") {
	                		echo '<b>ERROR:</b> Files with different sizes!';
	                	}
	                }
	            }
            ?>
        </div>


    </body>
</html>