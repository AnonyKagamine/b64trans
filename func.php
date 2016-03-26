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
    @$nn = implode("|",$http_response_headers);
    @$hrh = explode("|",$nn);
    unset($nn);
    foreach ($hrh as $aa)
    {
        if (stristr($aa,"Content-Type:"))
        {
            $ContentType = substr($aa,13);
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
