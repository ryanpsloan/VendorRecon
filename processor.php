<?php
session_start();
//var_dump($_SESSION);

if(isset($_SESSION['invoiceRegister']) && isset($_SESSION['vendorInvoice'])){
    $evoTemp = $scTemp = $evoArr = $evo = $scArr = $sc = array();

    $evoTemp = $_SESSION['invoiceRegister'];
    $scTemp = $_SESSION['vendorInvoice'];
    //var_dump("EVO START");
    //$evo[] = array("EVO","CO#", "Company Name", "Total", "Invoice Date", "CheckDate", "Service Name");
    foreach($evoTemp as $key => $line){
        //var_dump($line);
        $co = explode("-",$line[0]);
        //"CO#", "Company Name", "Total", "Invoice Date", "CheckDate", "Service Name"
        $evoArr[] = array('EVO', preg_replace("/#/","",strtoupper(trim($co[0]))), trim($co[1]), $line[6], $line[9]); //, $line[9], $line[7], $line[1] . " - " . $line[2]);

    }
    //sort($evo);
    //var_dump("EVO FINISH");
    //var_dump("Evo Begin",$evoArr, "Evo End");
    //var_dump("SC BEGIN");
    //$sc[] = array("SC", "Client#", "Client Name", "Total", "Item Date", "Service Name");
    foreach($scTemp as $key => $line){
        //var_dump($line);
        if($line[0] === '' && $line[1] === '' && $line[2] === ''){
            continue;
        }else {
            //Number name total date service
            $scArr[] = array('SC', preg_replace("/#/","",strtoupper(trim($line[2]))), trim($line[8]), -$line[7]); //, $line[1], $line[3]);

        }
    }
    //var_dump("SC END");
    //sort($sc);
    //var_dump("SwipeClock Begin", $scArr, "Swipeclock End");
    foreach($evoArr as $key => $array){
        $evo[$array[1]][] = $array;
    }
    //var_dump($evo);

    foreach($evo as $key => $array){
        $sum = 0;
        for($i = 0; $i < count($array); $i++){
            $sum += (float) $array[$i][3];
        }


        $evo[$key]['sum'] = $sum;
    }
    //var_dump("EVO3",$evo, "EVO3 END");
    foreach($evo as $key => $array){
        unset($evo[$key]);
        $evo[$key] = array("EVO", $array[0][1], $array[0][2], $array['sum'], $array[0][4]);
    }
    //var_dump($evo);
    foreach($scArr as $key => $array){
        $sc[$array[1]][] = $array;
    }
    foreach($sc as $key => $array){
        $sum = 0;
        for($i = 0; $i < count($array); $i++){
            $sum += (float) $array[$i][3];
        }


        $sc[$key]['sum'] = $sum;
    }
    //var_dump("EVO3",$evo, "EVO3 END");
    foreach($sc as $key => $array){
        unset($sc[$key]);
        $sc[$key] = array("SC", $array[0][1], $array[0][2], $array['sum']);
    }
    $linesToCompare = array();
    foreach($evo as $key => $array){
        $linesToCompare[$array[1]][] = $array;

    }

    foreach($sc as $key => $array){
        $linesToCompare[$array[1]][] = $array;

    }
    //var_dump($linesToCompare);
    function in_multiarray($elem, $array)
    {
        $top = sizeof($array) - 1;
        $bottom = 0;
        while($bottom <= $top)
        {
            if($array[$bottom] == $elem)
                return true;
            else
                if(is_array($array[$bottom]))
                    if(in_multiarray($elem, ($array[$bottom])))
                        return true;

            $bottom++;
        }
        return false;
    }

    $both = $scOnly = $evoOnly = array();
    foreach($linesToCompare as $key => $arr){

        if(in_multiarray('EVO', $arr) === true && in_multiarray('SC', $arr) === true){
            $both[$key] = array(array($arr[0][1], $arr[0][2], $arr[0][3], $arr[1][3], number_format($arr[0][3] + (float)$arr[1][3], 2), $arr[0][4]));
        }else if(in_multiarray('SC', $arr)){
            $scOnly[$key] = $arr;
        }else{
            $evoOnly[$key] = $arr;
        }

    }
    //var_dump($both, $scOnly, $evoOnly);
    $today = new DateTime('now');
    $month = $today->format('m');
    $day = $today->format('d');
    $year = $today->format('y');
    $time = $today->format('His');
    $filename = '/var/www/html/vendorRecon/Processed_Files/vendorRecon_Comparison_File-'.$month.'-'.$day.'-'.$year.'-'.$time.'.csv';
    $handle = fopen($filename, 'w');
    sort($both);
    fputcsv($handle,array("CO#", "CO NAME", "EVO", "SC", "GRAND TOTAL", "INVOICE DATE"), ",");
    foreach($both as $key => $array){

        foreach($array as $arr){
            fputcsv($handle,$arr,',');
        }

    }
    fclose($handle);
    $_SESSION['bothFileName'] = $filename;

    $filename = '/var/www/html/vendorRecon/Processed_Files/vendorRecon_SwipeClock_Unmatched_File-'.$month.'-'.$day.'-'.$year.'-'.$time.'.csv';
    $handle = fopen($filename, 'w');
    sort($scOnly);
    fputcsv($handle,array("SC", "CO#", "CO NAME", "TOTAL"), ",");
    foreach($scOnly as $key => $array){

        foreach($array as $arr){
            fputcsv($handle,$arr,',');
        }

    }
    fclose($handle);
    $_SESSION['scFileName'] = $filename;

    $filename = '/var/www/html/vendorRecon/Processed_Files/vendorRecon_EVO_Unmatched_File-'.$month.'-'.$day.'-'.$year.'-'.$time.'.csv';
    $handle = fopen($filename, 'w');
    sort($evoOnly);
    fputcsv($handle,array("EVO", "CO#", "CO NAME", "TOTAL", "INVOICE DATE"), ",");
    foreach($evoOnly as $key => $array){

        foreach($array as $arr){
            fputcsv($handle,$arr,',');
        }

    }
    fclose($handle);
    $_SESSION['evoFileName'] = $filename;
    header("location: index.php?processorData=Files Created Successfully");
}else{
    header("location: index.php?processorData=Files Not Uploaded");
}


?>