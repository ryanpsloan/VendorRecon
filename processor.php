<?php
session_start();
//var_dump($_SESSION);

if(isset($_SESSION['invoiceRegister']) && isset($_SESSION['vendorInvoice'])){
    $evoTemp = $scTemp = array();

    $evoTemp = $_SESSION['invoiceRegister'];
    $scTemp = $_SESSION['vendorInvoice'];
    var_dump("EVO START");
    foreach($evoTemp as $key => $line){
        var_dump($line);
        $temp = explode('-',$line[0]);
        $evo[] = array(preg_replace(array('/#/', '/_/'),'',$temp[0]), trim($temp[1]));
    }
    var_dump("EVO FINISH");
    var_dump("Evo Begin",$evo, "Evo End");
    var_dump("SC BEGIN");
    foreach($scTemp as $key => $line){
        var_dump($line);
        $sc[] = array(preg_replace(array('/#/', '/_/'),'',$line[8]), $line[7]);
    }
    var_dump("SC END");
    var_Dump("SwipeClock Begin", $sc, "Swipeclock End");
}else{
    header("location: index.php?processorData=Files Not Uploaded");
}


?>