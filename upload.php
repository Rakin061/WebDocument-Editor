<?php

if (isset($_FILES['upload'])) {
    // ------ Process your file upload code -------
    $filen = $_FILES['upload']['tmp_name'];
	
    $con_images = "uploaded/" . $_FILES['upload']['name'];
	
	$con_images1 = "http://10.11.201.93:81/webdocument/"; 
    
    if (file_exists($con_images)) {
        $path_parts = pathinfo($con_images);
        $fileName = $path_parts['filename'];
        $extension = $path_parts['extension'];
        $count = 1; 
        while (true) {
            $con_images ="uploaded/" .$fileName.$count.".".$extension;
			//$conf_images ="10.11.201.93:81/uploaded/" .$fileName.$count.".".$extension;
            if (file_exists($con_images))
                $count++;
            else {
                break;
            }
        }
    } else {
        echo "Die Datei $filename existiert nicht";
    }
    move_uploaded_file($filen, $con_images);
	//move_uploaded_file($filen, $conf_images);
    $con_images1=$con_images1.$con_images;
	$url =  $con_images1;

    $funcNum = $_GET['CKEditorFuncNum'];
    // Optional: instance name (might be used to load a specific configuration file or anything else).
    $CKEditor = $_GET['CKEditor'];
    // Optional: might be used to provide localized messages.
    $langCode = $_GET['langCode'];

    // Usually you will only assign something here if the file could not be uploaded.
    $message = '';
    echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
}
?>