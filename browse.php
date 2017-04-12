<?php
header("Content-Type: text/html; charset=utf-8\n");
header("Cache-Control: no-cache, must-revalidate\n");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// e-z params  
$dim = 150;         /* image displays proportionally within this square dimension ) */
$cols = 4;          /* thumbnails per row */
$thumIndicator = ''; /* e.g., *image123_th.jpg*) -> if not using thumbNails then use empty string */
?>  
<!DOCTYPE html>  
<html>  
    <head>  
        <title>browse file</title>  
        <meta charset="utf-8">  

        <style>  
            html,  
            body {padding:0; margin:0; background:black; }  
            table {width:100%; border-spacing:15px; }  
            td {text-align:center; padding:5px; background:#181818; }  
            img {border:5px solid #303030; padding:0; verticle-align: middle;}  
            img:hover { border-color:blue; cursor:pointer; }  
        </style>  

    </head>  


    <body>  

        <table>  

            <?php
            $dir = $_GET['dir'];

            $dir = rtrim($dir, '/'); // the script will add the ending slash when appropriate  

            $files = scandir($dir);

            $images = array();

            foreach ($files as $file) {
                // filter for thumbNail image files (use an empty string for $thumIndicator if not using thumbnails )
                 if (!preg_match('/' . $thumIndicator . '\.(jpg|jpeg|png|gif)$/i', $file))
                     continue;

                $thumbSrc = $dir . '/' . $file;
                $fileBaseName = str_replace('_th.', '.', $file);

                $image_info = getimagesize($thumbSrc);
                $_w = $image_info[0];
                $_h = $image_info[1];

                if ($_w > $_h) {       // $a is the longer side and $b is the shorter side
                    $a = $_w;
                    $b = $_h;
                } else {
                    $a = $_h;
                    $b = $_w;
                }

                $pct = $b / $a;     // the shorter sides relationship to the longer side

                if ($a > $dim)
                    $a = $dim;      // limit the longer side to the dimension specified

                $b = (int) ($a * $pct);  // calculate the shorter side

                $width = $_w > $_h ? $a : $b;
                $height = $_w > $_h ? $b : $a;

                // produce an image tag
                $str = sprintf('<img src="%s" width="%d" height="%d" title="%s" alt="">', $thumbSrc, $width, $height, $fileBaseName
                );

                // save image tags in an array
                $images[] = str_replace("'", "\\'", $str); // an unescaped apostrophe would break js  
            }

            $numRows = floor(count($images) / $cols);

            if (count($images) % $cols != 0)
                $numRows++;


// produce the correct number of table rows with empty cells
            for ($i = 0; $i < $numRows; $i++)
                echo "\t<tr>" . implode('', array_fill(0, $cols, '<td></td>')) . "</tr>\n\n";
            ?>  
        </table>  


        <script>

            // make a js array from the php array
            images = [
<?php
foreach ($images as $v)
    echo sprintf("\t'%s',\n", $v);
?>];

            tbl = document.getElementsByTagName('table')[0];

            td = tbl.getElementsByTagName('td');

            // fill the empty table cells with the img tags
            for (var i = 0; i < images.length; i++)
                td[i].innerHTML = images[i];


            // event handler to place clicked image into CKeditor
            tbl.onclick =
                    function (e) {

                        var tgt = e.target || event.srcElement,
                                url;

                        if (tgt.nodeName != 'IMG')
                            return;

                        url = "http://10.11.201.93:81/webdocument/" +'<?php echo $dir; ?>' + '/' + tgt.title;

                        this.onclick = null;

                        // $_GET['CKEditorFuncNum'] was supplied by CKeditor
                        window.opener.CKEDITOR.tools.callFunction(<?php echo $_GET['CKEditorFuncNum']; ?>, url);

                        window.close();
                    }
        </script>  
    </body>  
</html>  