<?php 
$files = glob('cached/*');
$c = 0;
foreach ($files as $file) { 
if (is_file($file)) { 
unlink($file); 
$c += 1;
}
}
echo "<h2>".$c." files deleted.</h2>";
 ?>

