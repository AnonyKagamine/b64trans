<?php
function access_url($url) {
    $oCurl = curl_init();
    curl_setopt($oCurl,CURLOPT_URL,$url);
    curl_setopt($oCurl, CURLOPT_HEADER, true);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($oCurl, CURLOPT_POST, false);
    $sContent = curl_exec($oCurl);
    $headerSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
    $header = substr($sContent, 0, $headerSize);
    curl_close($oCurl);
    $d = substr($sContent,$headerSize+1);
    $headArr = explode("\r\n", $header);
    foreach ($headArr as $loop) {
        if(stripos($loop, "Content-type") !== false){
            $ContentType = trim(substr($loop, 13));
        }
    }

    return array($d,$ContentType);
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
function reverseString($s)
{
    $oldStrLength = strlen($s);
    $newstr = "";
    for ($i=0;$i<$oldStrLength;$i++)
    {
        $item = $s[$oldStrLength-$i-1];
        $newstr .= $item;
    }
    return $newstr;
}
?>
