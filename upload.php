<?php
session_start();
var_dump($_FILES, $_POST);
if($_FILES['invoiceRegister']['error'] === 0 && $_FILES['vendorInvoice']['error'] === 0) { //Check to see if a file is uploaded
    try {
        /***
         * Read in First File - Netclock Billing Audit
         * Source: EVO
         */
        if (($log = fopen("log.txt", "w")) === false) { //open a log file
            //if unable to open throw exception
            throw new RuntimeException("Log File Did Not Open.");
        }
        $today = new DateTime('now'); //create a date for now
        fwrite($log, $today->format("Y-m-d H:i:s") . PHP_EOL); //post the date to the log
        fwrite($log, "--------------------------------------------------------------------------------" . PHP_EOL); //post to log
        $name = $_FILES['invoiceRegister']['name']; //get file name
        fwrite($log, "FileName: $name" . PHP_EOL); //write to log
        $type = $_FILES['invoiceRegister']["type"];//get file type
        fwrite($log, "FileType: $type" . PHP_EOL); //write to log
        $tmp_name = $_FILES['invoiceRegister']['tmp_name']; //get file temp name
        fwrite($log, "File TempName: $tmp_name" . PHP_EOL); //write to log
        $tempArr = explode(".", $_FILES['invoiceRegister']['name']); //set file name into an array
        $extension = end($tempArr); //get file extension
        fwrite($log, "Extension: $extension" . PHP_EOL); //write to log
        //If any errors throw an exception
        if (!isset($_FILES['invoiceRegister']['error']) || is_array($_FILES['invoiceRegister']['error'])) {
            fwrite($log, "Invalid Parameters - No File Uploaded." . PHP_EOL);
            throw new RuntimeException("Invalid Parameters - No File Uploaded.");
        }
        //switch statement to determine action in relationship to reported error
        switch ($_FILES['invoiceRegister']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                fwrite($log, "No File Sent." . PHP_EOL);
                throw new RuntimeException("No File Sent.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                fwrite($log, "Exceeded Filesize Limit." . PHP_EOL);
                throw new RuntimeException("Exceeded Filesize Limit.");
            default:
                fwrite($log, "Unknown Errors." . PHP_EOL);
                throw new RuntimeException("Unknown Errors.");
        }
        //check file size
        if ($_FILES['invoiceRegister']['size'] > 2000000) {
            fwrite($log, "Exceeded Filesize Limit." . PHP_EOL);
            throw new RuntimeException('Exceeded Filesize Limit.');
        }
        //define accepted extensions and types
        $goodExts = array("csv");
        $goodTypes = array("text/csv", "application/vnd.ms-excel");
        //test to ensure that uploaded file extension and type are acceptable - if not throw exception
        if (in_array($extension, $goodExts) === false || in_array($type, $goodTypes) === false) {
            fwrite($log, "This page only accepts .csv files, please upload the correct format." . PHP_EOL);
            throw new Exception("This page only accepts .csv files, please upload the correct format.");
        }
        //move the file from temp location to the server - if fail throw exception
        $directory = "/var/www/html/vendorRecon/Files";
        if (move_uploaded_file($tmp_name, "$directory/$name")) {
            fwrite($log, "File Successfully Uploaded." . PHP_EOL);
            //echo "<p>File Successfully Uploaded.</p>";
        } else {
            fwrite($log, "Unable to Move File to /Files." . PHP_EOL);
            throw new RuntimeException("Unable to Move File to /Files.");
        }
        //rename the file using todays date and time
        $month = $today->format("m");
        $day = $today->format('d');
        $year = $today->format('Y');
        $time = $today->format('H-i-s');
        $newName = "$directory/invoiceRegister-$month-$day-$year-$time.$extension";
        if ((rename("$directory/$name", $newName))) {
            fwrite($log, "File Renamed to: $newName" . PHP_EOL);
            //echo "<p>File Renamed to: $newName </p>";
        } else {
            fwrite($log, "Unable to Rename File: $name" . PHP_EOL);
            throw new RuntimeException("Unable to Rename File: $name");
        }

        //open the stream for file reading
        $handle = fopen($newName, "r");
        if ($handle === false) {
            fwrite($log, "Unable to Open Stream." . PHP_EOL);
            throw new RuntimeException("Unable to Open Stream.");
        } else {
            fwrite($log, "Stream Opened Successfully." . PHP_EOL);
            //echo "<p>Stream Opened Successfully.</p>";
        }
        //echo "<hr>";
        $fileData = array();

        //read the data in line by line
        //$header = fgets($handle);
        while (!feof($handle)) {
            $line_of_data = fgets($handle); //gets data from file one line at a time
            $line_of_data = trim($line_of_data); //trims the data
            $fileData[] = str_getcsv($line_of_data); //breaks the line up into pieces that the array can store
        }
        //close file reading stream
        fclose($handle);
        foreach($fileData as $key => $array){
            if(count($array) !== 10){
                unset($fileData[$key]);
            }
        }
        //var_dump("Invoice Register Begin", $fileData, "Invoice Register End");
        $_SESSION['invoiceRegister'] = $fileData;

        /*
         * Read in Second File - Swipeclock Vendor Invoice
         * Source: Swipeclock
         */

        $today = new DateTime('now'); //create a date for now
        fwrite($log, $today->format("Y-m-d H:i:s") . PHP_EOL); //post the date to the log
        fwrite($log, "--------------------------------------------------------------------------------" . PHP_EOL); //post to log
        $name = $_FILES['vendorInvoice']['name']; //get file name
        fwrite($log, "FileName: $name" . PHP_EOL); //write to log
        $type = $_FILES['vendorInvoice']["type"];//get file type
        fwrite($log, "FileType: $type" . PHP_EOL); //write to log
        $tmp_name = $_FILES['vendorInvoice']['tmp_name']; //get file temp name
        fwrite($log, "File TempName: $tmp_name" . PHP_EOL); //write to log
        $tempArr = explode(".", $_FILES['vendorInvoice']['name']); //set file name into an array
        $extension = end($tempArr); //get file extension
        fwrite($log, "Extension: $extension" . PHP_EOL); //write to log
        //If any errors throw an exception
        if (!isset($_FILES['vendorInvoice']['error']) || is_array($_FILES['vendorInvoice']['error'])) {
            fwrite($log, "Invalid Parameters - No File Uploaded." . PHP_EOL);
            throw new RuntimeException("Invalid Parameters - No File Uploaded.");
        }
        //switch statement to determine action in relationship to reported error
        switch ($_FILES['vendorInvoice']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                fwrite($log, "No File Sent." . PHP_EOL);
                throw new RuntimeException("No File Sent.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                fwrite($log, "Exceeded Filesize Limit." . PHP_EOL);
                throw new RuntimeException("Exceeded Filesize Limit.");
            default:
                fwrite($log, "Unknown Errors." . PHP_EOL);
                throw new RuntimeException("Unknown Errors.");
        }
        //check file size
        if ($_FILES['vendorInvoice']['size'] > 2000000) {
            fwrite($log, "Exceeded Filesize Limit." . PHP_EOL);
            throw new RuntimeException('Exceeded Filesize Limit.');
        }
        //define accepted extensions and types
        $goodExts = array("csv");
        $goodTypes = array("text/csv", "application/vnd.ms-excel");
        //test to ensure that uploaded file extension and type are acceptable - if not throw exception
        if (in_array($extension, $goodExts) === false || in_array($type, $goodTypes) === false) {
            fwrite($log, "This page only accepts .csv files, please upload the correct format." . PHP_EOL);
            throw new Exception("This page only accepts .csv files, please upload the correct format.");
        }
        //move the file from temp location to the server - if fail throw exception
        $directory = "/var/www/html/vendorRecon/Files";
        if (move_uploaded_file($tmp_name, "$directory/$name")) {
            fwrite($log, "File Successfully Uploaded." . PHP_EOL);
            //echo "<p>File Successfully Uploaded.</p>";
        } else {
            fwrite($log, "Unable to Move File to /Files." . PHP_EOL);
            throw new RuntimeException("Unable to Move File to /Files.");
        }
        //rename the file using todays date and time
        $month = $today->format("m");
        $day = $today->format('d');
        $year = $today->format('Y');
        $time = $today->format('H-i-s');
        $newName = "$directory/vendorInvoice-$month-$day-$year-$time.$extension";
        if ((rename("$directory/$name", $newName))) {
            fwrite($log, "File Renamed to: $newName" . PHP_EOL);
            //echo "<p>File Renamed to: $newName </p>";
        } else {
            fwrite($log, "Unable to Rename File: $name" . PHP_EOL);
            throw new RuntimeException("Unable to Rename File: $name");
        }

        //open the stream for file reading
        $handle = fopen($newName, "r");
        if ($handle === false) {
            fwrite($log, "Unable to Open Stream." . PHP_EOL);
            throw new RuntimeException("Unable to Open Stream.");
        } else {
            fwrite($log, "Stream Opened Successfully." . PHP_EOL);
            //echo "<p>Stream Opened Successfully.</p>";
        }
        //echo "<hr>";
        $fileData = array();

        //read the data in line by line
        //remove header
        $header = fgets($handle);
        //var_dump($header);
        while (!feof($handle)) {
            $line_of_data = fgets($handle); //gets data from file one line at a time
            $line_of_data = trim($line_of_data); //trims the data
            $fileData[] = str_getcsv($line_of_data); //breaks the line up into pieces that the array can store
        }
        //close file reading stream
        fclose($handle);
        foreach($fileData as $key => $array){
            //var_dump($array);
            if(count($array) !== 11){
                unset($fileData[$key]);
            }
        }
        //var_dump("Vendor Invoice Begin", $fileData,"Vendor Invoice End");
        $_SESSION['vendorInvoice'] = $fileData;
        fclose($log);

        header("location: index.php?uploadStatus=Files Uploaded");
    }catch(Exception $e){
        header("location: index.php?uploadStatus=". $e->getMessage());
    }
}else{
    try {
        throw new RuntimeException("Please select both files before pressing Upload");

    }catch(Exception $e){
        header("location: index.php?uploadStatus=". $e->getMessage());
    }
}
?>