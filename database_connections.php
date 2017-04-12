<?php

$functionName = $_POST['functionName'];

switch ($functionName) {
    case "create_new_file":
        $fileName = $_POST['fileName'];
        create_new_file($fileName);
        break;
    case "getFileName":
        getFileName();
        break;
    case "getFileContent":
        $fileName = $_POST['fileName'];
        getFileContent($fileName);
        break;
    case "saveFileContent":
        $fileName = $_POST['fileName'];
        $fileContent = $_POST['fileContent'];
        $updateFile = intval($_POST['updateFile']);
        saveFileContent($fileName, $fileContent, $updateFile);
        break;

    case "saveToPdf":
        $fileName= $_POST['fileName'];
         $path_parts = pathinfo($fileName);
        $fileName = $path_parts['filename']; 
         $fileName1 = "C:\\xampp\\htdocs\\webdocument\\".$fileName.".pdf";
        $pdf_file_Name = $_POST['pdf_file_Name'];
        $fileContent = readPDF($fileName1);
        $updateFile = intval($_POST['updateFile']); 
         $path_parts = pathinfo($pdf_file_Name);
        $pdf_file_Name = $path_parts['filename'];
        $pdf_file_Name = $pdf_file_Name.".pdf";
        saveToPdf($pdf_file_Name, $fileContent, $updateFile);
        break;
}

function readPDF($filename) { 
    $handle = fopen($filename, "rb");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    return $contents;
}

$connections = null;

function connect_to_database() {
    global $connections;
    $DB = '//10.11.201.170:1521/orcl';
    $DB_USER = 'BIOTPL';
    $DB_PASS = 'biotpl';
    $DB_CHAR = 'AL32UTF8';

    $conn = oci_connect($DB_USER, $DB_PASS, $DB, $DB_CHAR);
    if ($conn) {
        $connections = $conn;
        return true;
    } else {
        $err = OCIError();
        //echo "Connection failed." . $err[text];
        return false;
    }
}

function create_new_file($fileName) {
    global $connections;

    if (connect_to_database()) {
        $sql = "INSERT INTO FILEDATA (PR_KEY,FILEDATA,FILENAME)
				VALUES (FILE_SEQ.nextval,EMPTY_CLOB(),'" . $fileName . "')";
        $stmt = oci_parse($connections, $sql);
        $result = oci_execute($stmt);
    }

    echo "File is created successfully";
}

function getFileName() {
    global $connections;
    $result_string = "";
    if (connect_to_database()) {
        $sql = "Select FILENAME from  FILEDATA";
        $stmt = oci_parse($connections, $sql);
        $result = oci_execute($stmt);

        while ($row = oci_fetch_array($stmt)) {
            $result_string = $result_string . "," . $row[0];
        }
        echo $result_string;
    }
}

function getFileContent($fileName) {
    global $connections;
    if (connect_to_database()) {
        $sql = "Select FILEDATA from FILEDATA where FILENAME = '" . $fileName . "'";
        $stmt = oci_parse($connections, $sql);
        $result = oci_execute($stmt);

        while ($row = oci_fetch_array($stmt)) {
            $result_string = $row[0]->load();
        }
        echo $result_string;
    }
}

function saveFileContent($fileName, $fileContent, $updateFile) {
    global $connections;
    if (connect_to_database()) {
        if ($updateFile == 0) {
            $sql = "Select FILENAME from FILEDATA where FILENAME = '" . $fileName . "'";
            $stmt = oci_parse($connections, $sql);
            $result = oci_execute($stmt);
            $counter = 0;
            while ($row = oci_fetch_array($stmt)) {
                ++$counter;
            }
            if ($counter != 0) {
                echo "There exists a file with the same name. Do you want to override ? ";
            } else {
                $sql = "INSERT INTO FILEDATA (PR_KEY,FILEDATA,FILENAME,CREATE_DATE)
                    VALUES (FILE_SEQ.nextval, EMPTY_CLOB(),'" . $fileName . "',sysdate) RETURNING FILEDATA INTO :myclob";

                $stid = oci_parse($connections, $sql);
                $clob = oci_new_descriptor($connections, OCI_D_LOB);
                oci_bind_by_name($stid, ":myclob", $clob, -1, OCI_B_CLOB);
                oci_execute($stid, OCI_NO_AUTO_COMMIT); // use OCI_DEFAULT for PHP <= 5.3.1
                $clob->save($fileContent);

                oci_commit($connections);
            }
        } else {
            $sql = "UPDATE FILEDATA SET FILEDATA = EMPTY_CLOB() where FILENAME = '" . $fileName . "' RETURNING FILEDATA INTO :myclob";

            $clob = oci_new_descriptor($connections, OCI_D_LOB);
            $stmt = oci_parse($connections, $sql);
            oci_bind_by_name($stmt, ':myclob', $clob, -1, OCI_B_CLOB);
            oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if ($clob->save($fileContent)) {
                OCICommit($connections);
                echo " Updated" . "\n";
            } else {
                echo " Problems: Couldn't update Clob.  This usually means the where condition had no match \n";
            }
            $clob->free();
            OCIFreeStatement($stmt);

            /* $sql = "Update FILEDATA 
              Set FILEDATA =  EMPTY_CLOB()
              where FILENAME='" . $fileName . "' RETURNING FILEDATA INTO :myclob";

              $stid = oci_parse($connections, $sql);
              $clob = oci_new_descriptor($connections, OCI_D_LOB);
              oci_bind_by_name($stid, ":myclob", $clob, -1, OCI_B_CLOB);
              oci_execute($stid, OCI_NO_AUTO_COMMIT); // use OCI_DEFAULT for PHP <= 5.3.1
              $clob->save($fileContent);

              oci_commit($connections);
              echo "Data Update is successful"; */
        }
    }
}

function saveToPdf($fileName, $fileContent, $updateFile) {
    global $connections;
    if (connect_to_database()) {
        if ($updateFile == 0) {
            $sql = "Select FILENAME from PDFDATA where FILENAME = '" . $fileName . "'";
            $stmt = oci_parse($connections, $sql);
            $result = oci_execute($stmt);
            $counter = 0;
            while ($row = oci_fetch_array($stmt)) {
                ++$counter;
            }
            if ($counter != 0) {
                echo "There exists a file with the same name. Do you want to override ? ";
            } else {
                $sql = "INSERT INTO PDFDATA (PRIMARY_KEY,PDF_FILES,FILENAME,CREATE_DATE)
                    VALUES (PDF_SEQ.nextval, EMPTY_BLOB(),'" . $fileName . "',sysdate) RETURNING PDF_FILES INTO :myblob";

                $stid = oci_parse($connections, $sql);
                $blob = oci_new_descriptor($connections, OCI_D_LOB);
                oci_bind_by_name($stid, ":myblob", $blob, -1, OCI_B_BLOB);
                oci_execute($stid, OCI_NO_AUTO_COMMIT); // use OCI_DEFAULT for PHP <= 5.3.1
                $blob->save($fileContent);

                oci_commit($connections);
            }
        } else {
            $sql = "UPDATE PDFDATA SET PDF_FILES= EMPTY_BLOB() where FILENAME = '" . $fileName . "' RETURNING PDF_FILES INTO :myblob";

            $blob = oci_new_descriptor($connections, OCI_D_LOB);
            $stmt = oci_parse($connections, $sql);
            oci_bind_by_name($stmt, ':myblob', $blob, -1, OCI_B_BLOB);
            oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if ($blob->save($fileContent)) {
                OCICommit($connections);
                echo " Updated" . "\n";
            } else {
                echo $sql; 
                echo " Problems: Couldn't update Clob.  This usually means the where condition had no match \n";
            }
            $blob->free();
            OCIFreeStatement($stmt);
        }
    }
}
