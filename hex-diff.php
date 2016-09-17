<?php
	include('utils/HexUtils.php');
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
            <h1>Binary Diff Tool</h1>

            <form method="post" enctype="multipart/form-data">
                <div>
                    <label for="file1">File 1</label>
                    <input type="file" name="file1" id="file1">
                </div>

                <div>
                    <label for="file2">File 2</label>
                    <input type="file" name="file2" id="file2">
                </div>

                <input type="submit" value="Compare" name="submit">
            </form>
        </div>

        <?php
            if (count($_POST)) {
                $file1 = $_FILES['file1']['tmp_name'];
                $file2 = $_FILES['file2']['tmp_name'];

                $fileDiff1 = HexUtils::hexDiff($file1, $file2);
                $fileDiff2 = HexUtils::hexDiff($file2, $file1);

                if ($fileDiff1 == "-1") {
                    echo "Different file sizes!";
                } else {
                    // Line Numbers
                    echo '<div class="hex" style="padding-top: 20px; float: left; font-weight: normal; padding-right: 20px; padding-left: 15px; text-align: right;">';
                    for ($i = 0; $i < filesize($file1); $i = $i + 8) {
                        echo dechex($i) . "<br/>";
                    }
                    echo '</div>';

                    echo '<div class="number hex" style="float: left; padding-top: 20px;">';
                    $i = 0;

                    foreach ($fileDiff1 as $bytePos) {
                        if (is_array($bytePos)) {
                            echo '<span style="color: white; background: red;" title="' . $bytePos[0] . ' != ' . $bytePos[1] . '">' . $bytePos[0] . '</span>';
                        } else {
                            echo '<span>' . $bytePos . '</span>';
                        }
                        $i++;

                        if ($i > 7) {
                            echo '</br>';
                            $i = 0;
                        }
                    }
                    echo '</div>';

                    echo '<div class="number hex" style="padding-top: 20px; float: left; padding-right: 40px;">';
                    echo nl2br(HexUtils::printString(HexUtils::getHex($file1), 8));
                    echo '</div>';

                    echo '<div class="number hex" style="float: left; padding-top: 20px;">';
                    $i = 0;

                    foreach ($fileDiff2 as $bytePos) {
                        if (is_array($bytePos)) {
                            echo '<span style="color: white; background: red;" title="' . $bytePos[0] . ' != ' . $bytePos[1] . '">' . $bytePos[0] . '</span>';
                        } else {
                            echo '<span>' . $bytePos . '</span>';
                        }
                        $i++;

                        if ($i > 7) {
                            echo '</br>';
                            $i = 0;
                        }
                    }
                    echo '</div>';

                    echo '<div class="number hex" style="padding-top: 20px;">';
                    echo nl2br(HexUtils::printString(HexUtils::getHex($file2), 8));
                    echo '</div>';
                }
            }
        ?>

    </body>
</html>
