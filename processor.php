<?php
session_start();
//var_dump($_SESSION);

if(isset($_SESSION['invoiceRegister']) && isset($_SESSION['vendorInvoice'])){
    $evoTemp = $scTemp = $evo = $sc = array();

    $evoTemp = $_SESSION['invoiceRegister'];
    $scTemp = $_SESSION['vendorInvoice'];
    //var_dump("EVO START");
    foreach($evoTemp as $key => $line){
        //var_dump($line);
        $temp = explode('-',$line[0]);

        //Number name Total DateA DateB serviceA serviceB
        $evo[] = array('EVO', strtoupper(trim($temp[0])), trim($temp[1]), $line[6], $line[9], $line[1], $line[2]);
    }
    //var_dump("EVO FINISH");
    //var_dump("Evo Begin",$evo, "Evo End");
    //var_dump("SC BEGIN");
    foreach($scTemp as $key => $line){
        //var_dump($line);
        //Number name total date service
        $sc[] = array('SC',strtoupper(trim($line[8])), $line[7], $line[6], $line[1], $line[2]);
    }
    //var_dump("SC END");
    sort($sc);
    //var_dump("SwipeClock Begin", $sc, "Swipeclock End");
    $linesToCompare = array();
    foreach($evo as $key => $array){
        $linesToCompare[$array[1]][] = $array;

    }
    foreach($sc as $key => $array){
        $linesToCompare[$array[1]][] = $array;

    }

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
                $both[$key] = $arr;
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
    $empty = array();
    foreach($both as $key => $array){
        fputcsv($handle, $empty);
        foreach($array as $arr){
            fputcsv($handle,$arr,',');
        }
        fputcsv($handle, $empty);
    }
    fclose($handle);
    $_SESSION['bothFileName'] = $filename;

    $filename = '/var/www/html/vendorRecon/Processed_Files/vendorRecon_SwipeClock_Unmatched_File-'.$month.'-'.$day.'-'.$year.'-'.$time.'.csv';
    $handle = fopen($filename, 'w');
    $empty = array();
    foreach($scOnly as $key => $array){
        fputcsv($handle, $empty);
        foreach($array as $arr){
            fputcsv($handle,$arr,',');
        }
        fputcsv($handle, $empty);
    }
    fclose($handle);
    $_SESSION['scFileName'] = $filename;

    $filename = '/var/www/html/vendorRecon/Processed_Files/vendorRecon_EVO_Unmatched_File-'.$month.'-'.$day.'-'.$year.'-'.$time.'.csv';
    $handle = fopen($filename, 'w');
    $empty = array();
    foreach($linesToCompare as $key => $array){
        fputcsv($handle, $empty);
        foreach($array as $arr){
            fputcsv($handle,$arr,',');
        }
        fputcsv($handle, $empty);
    }
    fclose($handle);
    $_SESSION['evoFileName'] = $filename;
    header("location: index.php?processorData=Files Created Successfully");
}else{
    header("location: index.php?processorData=Files Not Uploaded");
}


?>