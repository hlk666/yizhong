<?php
$dir=dirname(__DIR__)."\\".$_GET['dir'];

deleteDir($dir);
function deleteDir($dir){
$handle=opendir($dir);
while($file=readdir($handle)){
if($file!='.'&&$file!='..'){
$fullPath=$dir.'/'.$file;
if(is_dir($fullPath)){
deleteDir($fullPath);
}else{
unlink($fullPath);
}
}
}
closedir($handle);
}
echo 123;

?>