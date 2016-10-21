<?php
session_start();
var_dump($_GET);
$uploadStatus = '';
$clear = '';
$processorData = '';
$comparisonFile = $scFile = $evoFile = '';
if(isset($_GET['uploadStatus'])){
    $uploadStatus = $_GET['uploadStatus'];
    if($uploadStatus === 'Files Uploaded') {
        $clear = '<a href="clear.php">Clear Files</a>';
    }
}
if(isset($_GET['processorData'])){
    $processorData = $_GET['processorData'];
}
if(isset($_SESSION['bothFileName'])){
    $comparisonFile = '<p><a href="bothDownload.php">Download Comparison File</a></p>';
    $clear = '<a href="clear.php">Clear Files</a>';
}
if(isset($_SESSION['scFileName'])){
    $scFile = '<p><a href="scDownload.php">Download SC Mismatch File</a></p>';
    $clear = '<a href="clear.php">Clear Files</a>';
}
if(isset($_SESSION['evoFileName'])){
    $evoFile = '<p><a href="evoDownload.php">Download Evo Mismatch File</a></p>';
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
    <style>
        body{
            background-color: lightblue;
        }
        .border{
            border: 1px solid black;
            border-radius: 5px;

        }
        .center{
            text-align: center;
        }
        ul li {
            padding: 5px 2px 5px 2px;
            color: whitesmoke;
        }
        .red{
            color: red;
        }
        .green{
            color: green;
        }
    </style>
    <script>
        $(document).ready(function(){
            var compareBtnDiv = $('#compareBtnDiv');
            var uploadStatus = "<?php if($uploadStatus !== ''){ echo $uploadStatus;}else{echo "text";} ?>";
            if(uploadStatus == 'Files Uploaded'){
                compareBtnDiv.show();
            }else{
                compareBtnDiv.hide();
            }


        });

    </script>
</head>
<body>
<div class="container-fluid">

<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="border">
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <ul>
                <li><label for="invoiceRegister">Upload Net Clock Billing Audit from EVO (.csv)</label></li>
                <li><input type="file" name="invoiceRegister"></li>
                <li><label for="vendorInvoice">Upload SwipeClock Vendor Invoice (.csv)</label></li>
                <ol><li>Open the Swipeclock invoice in Excel</li><li>Delete the ExtDescription Column</li><li>Save as a .csv</li></ol>
                <li><input type="file" name="vendorInvoice"></li>
            </ul>

            <?php if(isset($_GET['uploadStatus'])) {
                if ($_GET['uploadStatus'] === 'Files Uploaded') {
                    echo '<div class="center green">' . $uploadStatus . '</div>';
                } else {
                    echo '<div class="center red">' . $uploadStatus . '</div><div class="center"><input class="btn btn-default" type="submit" value="Upload" name="upload"></div>';
                }
            }else{
                echo '<div class="center"><input class="btn btn-default" type="submit" value="Upload" name="upload"></div>';
            }?>
            <div class="center"><?php echo $clear;
                echo '<p class="red">'.$processorData.'</p>';
                echo $comparisonFile;
                echo $scFile;
                echo $evoFile;
                ?></div>
        </form>
        </div>
        <div class="border center" id="compareBtnDiv">

        <form action="processor.php" method="POST">
            <p></p>
            <p><input class="btn btn-primary padded" id="compareBtn" type="submit" value="Compare"></p>


        </form>
        </div>

    </div>
    <div class="col-md-4"></div>
</div>
</div>
<?php ?>
</body>
</html>

