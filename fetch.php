<?php
//The main part of B64Trans
define("ENABLE_CACHE",1);
define("GZ_WRITE_MODE","w7");
define("CACHING_DICTIONARY",'cached');
define("USING_CURL",false);
require "func.php";
require "encoder.php";
$rawRequest = $_REQUEST["url"];
$reqm = $_REQUEST["meth"];
$reqmode = $_REQUEST["mode"];
$h = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
//header("Cache-control: max-age=1200");
//header("Transfer-Encoding: utf-8");

function readCache($fn,$mode) {
    //
    $content = gzgetcont($fn);
    $index1 = strpos($content,"[") + 1;
    $index2 = strpos($content,"]");
    $cont = substr($content,$index1,$index2-$index1);
    $index3 = strpos($content,"(") + 1;
    $index4 = strpos($content,")");
    $jsonStr = substr($content,$index3,$index4-$index3);
    $arrayData = json_decode($jsonStr,true);
    $ContentType = $arrayData["Content-type"];
    $Filename = $arrayData["Filename"];
    header("Content-type: ".$ContentType,true);
    header("Filename: ".$Filename,true);
    if ($mode == "enc") {
        print '['.encodeString($cont).']';
        print '('.base64_encode(jsonStr).')';
    } else if($mode == "raw") {
        print base64_decode($cont);
    } else if ($mode == "script") {
        $out = 'eval(Base64.decode("'.$cont.'"));';
        print $out;
    }
}
function checkCache() {
    clearstatcache();
    $ch = array(".","..");
    $d = scandir("cached");
    foreach($d as $b) {
        $c = "./cached/".$b;
        $t = time() - filectime($c);
        if ($t > 14400 && !in_array($b,$ch)) {
            unlink($c);
        }
    }
}
function fetchPage($r,$f,$m,$mode) {
    $c = access_url($r);
    //$output will be written in a file.
    //$out will be printed.
    $encoded = '';
    if ($mode == "enc") {
        $output = base64_encode($c[0]);
        $encoded .= encodeString($output);
        $out = "[".$encoded."]";
    } else if($mode == "raw") {
        $out = $c[0];
        $output = base64_encode($c[0]);
    } else if ($mode == "script") {
        $output = base64_encode($c[0]);
        $out = 'eval(Base64.decode("'.$output.'"));';
    }
    $hrh = $c[1];
    if (strlen($encoded) == 0) {$encoded = encodeString($output);}
    $ContentType = getheader($hrh,"Content-type");
    $Filename = getheader($hrh,"Filename");
    //Generate json data
    $arrayData = array();
    $arrayData["Content-type"] = $ContentType;
    $arrayData["Request-url"] = $r;
    $arrayData["md5sum"] = md5($encoded);
    $arrayData["Filename"] = $Filename;
    $jsonData = json_encode($arrayData);
    if (!$mode == "enc")
    {
        print "(".base64_encode($jsonData).")\n";
    } else {
        header("Content-type: ".$ContentType,true);
        header("Filename: ".$Filename,true);
    }
    
    if (ENABLE_CACHE) 
    {
        $o = gzopen($f,GZ_WRITE_MODE);
        @gzwrite($o,"[");@gzwrite($o,$output);@gzwrite($o,"]");
        @gzwrite($o,"(");@gzwrite($o,$jsonData);@gzwrite($o,")");
        @gzclose($o);
    }
    echo $out;
}

$reqa = decodeString($rawRequest);
$req = base64_decode($reqa);
$fn = CACHING_DICTIONARY."/".md5($req).".b64";
if ($reqmode == "loader") {
    echo '<script src="base64.js"></script>'."\n";
    echo '<script src="browser.js"></script>'."\n";
    echo '<script>';
    echo "var f = '".$h."';"."\n";
    echo 'var host = Base64.decode(reverseString("'.$rawRequest.'"));'."\n";
    echo 'window.fetcherUrl = f;window.hostUrl = host;';
    echo "fetch(host);"."\n";
    echo "</script>";
} else if ($reqmode == "p2get_p") {
    if (strstr($req,"?")) {
        $query = "";
    } else { $query = "?"; }
    foreach ($_POST as $k=>$v) {
        $kv = $k."=".$v.'&';
        $query .= $kv;
    }
    $redir = $req.$query;
    $rs = encodeString(urlsafe_b64encode($redir));
    echo "<script>window.location = 'fetch.php?mode=loader&url=".$rs."';</script>";
} else if (file_exists($fn) && $reqm == "get") {
    readCache($fn,$reqmode);
    checkCache();
} else {
    fetchPage($req,$fn,$reqm,$reqmode);
}
?>
