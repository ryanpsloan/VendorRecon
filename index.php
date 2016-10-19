<?php
session_start();
var_dump($_GET, $_FILES, $_POST);
$uploadStatus = '';
$clear = '';
if(isset($_GET['uploadStatus'])){
    $uploadStatus = $_GET['uploadStatus'];
    $clear = '<a href="clear.php">Clear Files</a>';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Vendor Recon</title>
    <script src="http://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<form action="upload.php" method="POST" enctype="multipart/form-data">
    <ul>
        <li><label for="invoiceRegister">Upload Net Clock Billing Audit from EVO</label></li>
        <li><input type="file" name="invoiceRegister"></li>
        <li><label for="vendorInvoice">Upload SwipeClock Vendor Invoice</label></li>
        <ol><li>Open the Swipeclock invoice in Excel</li><li>Delete the ExtDescription Column</li><li>Save as a .csv</li></ol>
        <li><input type="file" name="vendorInvoice"></li>
    </ul>
    <div><?php echo $uploadStatus; ?></div>
    <input type="submit" value="Upload" name="upload">
</form>
<form action="processor.php" method="POST">
    <input type="submit" value="Compare">
</form>
<div><?php echo $clear; ?></div>

<?php var_dump($_SESSION);?>
</body>
</html>

