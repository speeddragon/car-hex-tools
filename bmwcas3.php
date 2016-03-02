<?php
	require('utils.php');

	$targetDir = "uploads/";
	if (count($_POST)) {
		$targetFile = $_FILES['fileToUpload']['tmp_name']; //$$_FILES["fileToUpload"]["name"]);

		if (!file_exists($targetFile)) {
			#echo "File not found!";
		} else {

			$isn = getISN($targetFile);
			$vin = getVIN($targetFile);
		}
	}
?>
<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Droid+Sans:400:700|Russo+One|Unica+One|Inconsolata|Lato:300,400,900|Oswald:400,300,700" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="style.css" type="text/css">
	</head>
	<body>
		<div class="header">
        		<h1>BMW CAS3 Viewer</h1>
        		<div>
                		<p>This website help to visualize the binary data from a dump of a BMW/MINI CAS3 module.</p>
        		</div>
        		<form method="post" enctype="multipart/form-data">
                		<input type="file" name="fileToUpload" id="fileToUpload"><br />
                		<input type="submit" value="Upload" name="submit">
        		</form>
		</div>

<? if (count($_POST) && file_exists($targetFile)) {?>
<div id="info_decoded">
	<div>VIN: <b><?php echo $vin; ?></b></div>
	<div>ISN: <b><?php echo $isn; ?></b></div>

	<div>HW Version: <b><?php echo getHardwareVersion($targetFile); ?></b></div>
        <div>DME: <b><?php echo getDME($targetFile); ?></b></div>
        <div>EGS: <b><?php echo getEGS($targetFile); ?></b></div>
        <div>Paint Code: <b><?php echo getPaintCode($targetFile); ?></b></div>
        <div>Programing Date: <b><?php echo getProgramingDate($targetFile); ?></b></div>

	<div>
		<?php $keys = getKeys($targetFile); ?>
		<h2>Keys</h2>
		<table class="keys">
			<thead>
				<tr>
					<td>Serial Number</td>
					<td>Crypto Low</td>
					<td>Crypto High</td>
					<td>Configuration</td>
					<td>Key Status</td>
					<td>Number</td>
					<td>Remote Crypto Low</td>
					<td>Remote Crypto High</td>
					<td>Synchronization</td>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($keys as $key) { ?>
				<tr>
					<td><?php echo $key['id']; ?></td>
					<td><?php echo $key['cryptLow']; ?></td>
					<td><?php echo $key['cryptHigh']; ?></td>
					<td><?php echo $key['configuration']; ?></td>
					<td><?php echo $key['keyStatus']; ?></td>
					<td><?php echo $key['number']; ?></td>
                    <td><?php echo $key['remoteCryptLow']; ?></td>
                    <td><?php echo $key['remoteCryptHigh']; ?></td>
                    <td><?php echo $key['synchronization']; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

	<h3>File</h3>
	<div style="padding-right: 20px; float: left; font-family: monospace;"><?php echo nl2br(printHexGraphic(getHex($targetFile), 32)); ?>
	</div>
	<div style="font-family:monospace;"><?php echo nl2br(printStr(getHex($targetFile), 32)); ?>
	</div>
</div>
<?php } else if (count($_POST)) { ?>
<div style="background-color: red; color: white; padding: 25px;">
	<p>File not found!</p>
</div>
<?php } ?>

</body>
</html>
