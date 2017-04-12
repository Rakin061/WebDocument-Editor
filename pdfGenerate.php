<?php


require 'vendor/autoload.php';
use Dompdf\Dompdf;

$fileContent = $_POST['fileContent'];
// instantiate and use the dompdf class
$dompdf = new Dompdf(array('enable_remote' => true));
$dompdf->loadHtml($fileContent);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
//$dompdf->stream("test.pdf");
$con_images = ".pdf";
if (file_exists($con_images)) {
        $path_parts = pathinfo($con_images);
        $fileName = $path_parts['filename'];
        $extension = $path_parts['extension'];
        $count = 1; 
        while (true) {
            $con_images =$fileName.$count.".".$extension;
            if (file_exists($con_images))
                $count++;
            else {
                break;
            }
        }
    } 
file_put_contents($con_images, $dompdf->output());
//echo "PDF file named ' ";
echo $con_images;
//echo "  'generated successfully";
?>
    
