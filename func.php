<?php
function access_url($url) {
    $d = "";
    $fo = fopen($url,'rb');
    if($fo){
        while(!feof($fo)) {
            $d .= fgets($fo);
        }
    }
    fclose($fo);
    //@$nn = implode("|",$http_response_header);
    //@$hrh = explode("|",$nn);
    unset($nn);
    return array($d,$http_response_header);
} 
function gzgetcont($f) {
    $d = "";
    $fo = gzopen($f,'r');
    if($fo){
        while(!gzeof($fo)) {
            $d .= gzgets($fo);
        }
    }
    gzclose($fo);
    return $d;
}

function urlsafe_b64encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
}
function urlsafe_b64decode($string) {
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}
function getheader($headersArray,$header)
{
    $length = strlen($header);
    $headerValue = "";
    foreach ($headersArray as $loop) {
        if(stripos($loop,$header) !== false){
            $headerValue = trim(substr($loop, 13));
            return $headerValue;
        }
    }
    return $headerValue;
}
if (!file_exists(CACHING_DICTIONARY))
{
    mkdir(CACHING_DICTIONARY);
}
?>
