<?php
readPDF("C:\\xampp\\htdocs\\webdocument\\29.pdf");
function readPDF($filename) { 
    $handle = fopen($filename, "rb");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
	echo $contents;
    return $contents;
}
?>