<?php
/**
 * 上传模块
 * Date: 2020/12/18
 */
namespace app\admin\controller;

use think\facade\View;
use think\Request;

class UploaderController
{
    public function index()
    {
        echo '上传功能';
    }

    /**
     * 切片上传
     * 上传文件函数，如过上传不成功打印$_FILES数组，查看error报错信息
     * 值：0; 没有错误发生，文件上传成功。
     * 值：1; 上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。
     * 值：2; 上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
     * 值：3; 文件只有部分被上传。
     * 值：4; 没有文件被上传。
     */
    public function webUploader(){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Content-type: text/html; charset=gbk32");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        $folder = input('folder');
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }
        if ( !empty($_REQUEST[ 'debug' ]) ) {
            $random = rand(0, intval($_REQUEST[ 'debug' ]) );
            if ( $random === 0 ) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }
        // header("HTTP/1.0 500 Internal Server Error");
        // exit;
        // 5 minutes execution time
        set_time_limit(5 * 60);
        // Uncomment this one to fake upload time
        usleep(5000);
        // Settings
        $request_name = $_REQUEST['name'];
        $targetDir = 'static/upload_tmp/college/';            //存放分片临时目录
        if($folder){
            $uploadDir = "static/upload/college/".date('Y-m-d')."/";
        }else{
            $uploadDir = "static/upload/college/".date('Y-m-d')."/";    //分片合并存放目录
        }
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds

        // Create target dir
        if (!file_exists($targetDir)) {
            mkdir($targetDir,0777,true);
        }
        // Create target dir
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir,0777,true);
        }
        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $oldName = $fileName;

        $fileName = iconv('UTF-8','gb2312',$fileName);
        $filePath = $targetDir .$fileName;
        // $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                return json([
                    'code' => 100,
                    'msg' => 'Failed to open temp directory111.'
                ]);
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp)$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        // Open temp file
        if (!$out = fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            return json([
                'code' => 102,
                'msg' => 'Failed to open output stream222.'
            ]);
        }
        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                return json([
                    'code' => 103,
                    'msg' => 'Failed to move uploaded file333.'
                ]);
            }
            // Read binary input stream and append it to temp file
            if (!$in = fopen($_FILES["file"]["tmp_name"], "rb")) {
                return json([
                    'code' => 101,
                    'msg' => 'Failed to open input stream444.'
                ]);
            }
        } else {
            if (!$in = fopen("php://input", "rb")) {
                return json([
                    'code' => 101,
                    'msg' => 'Failed to open input stream555.'
                ]);
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        fclose($out);
        fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        if ($done) {
            $pathInfo = pathinfo($fileName);
            $hashStr = substr(md5($pathInfo['basename']),8,16);
            $hashName = time() . $hashStr . '.' .$pathInfo['extension'];
            $hashName = getuuid() . '.' .$pathInfo['extension'];
            $uploadPath = $uploadDir . $hashName;
            if (!$out = fopen($uploadPath, "wb")) {
                return json([
                    'code' => 102,
                    'msg' => 'Failed to open output stream666.'
                ]);
            }
            //flock($hander,LOCK_EX)文件锁
            if ( flock($out, LOCK_EX) ) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            fclose($out);
            return json([
                'code' => 200,
                'data' => [
                    'file_path' => $uploadPath
                ],
                'msg' => "上传成功",
            ]);
        }

        // Return Success JSON-RPC response
        return json([
            'code' => '2.0',
            'msg' => '2.0'
        ]);
    }

    /**
     * 视频ffmpeg切片保存
     * @param Request $request
     * @return \think\response\Json
     */
    public function ffmpegVideo(Request $request){
        ignore_user_abort();//脱离客户端
        set_time_limit(0);//不限时间执行
        session_write_close();//session解锁
        // 获取处理视频地址
        $file = ltrim($request->param('file'),'/');
        $path_parts = pathinfo($file);
        $file_name = $path_parts['filename'];
        // 创建单独m3u8文件夹,每个视频一个文件夹
        $new_file_path = $path_parts['dirname'].'/'.$file_name.'/';
        if (!file_exists($new_file_path)) {
            mkdir($new_file_path,0777,true);
        }
        $shuiyin_img = 'static/admin/images/shuiyin.png';
        // 区分系统使用命令
        $os_name=PHP_OS;
        if(strpos($os_name,"Linux")!==false){
            // 截取封面图
            exec("/usr/local/ffmpeg/bin/ffmpeg -i ".$file." -f image2 -ss 10 -vframes 1 ".$new_file_path.$file_name.".jpg",$status,$a);
            // 转格式 30秒一个ts文件
            $new_file_m3u8 = $new_file_path.$file_name.'.m3u8';
            exec("/usr/local/ffmpeg/bin/ffmpeg -i ".$file." -threads 4 -preset ultrafast -b:v 2M -s 720*480 -vf \"movie=".$shuiyin_img."[awm];[in][awm] overlay=25:0 [out]\" -hls_time 30 -hls_list_size 0 -f hls ".$new_file_m3u8);
        }elseif(strpos($os_name,"WIN")!==false){
            // 截取封面图
            exec("ffmpeg -i ".$file." -f image2 -ss 10 -vframes 1 ".$new_file_path.$file_name.".jpg",$status,$a);
            // 转格式 30秒一个ts文件
            $new_file_m3u8 = $new_file_path.$file_name.'.m3u8';
            exec("ffmpeg -i ".$file." -threads 2 -preset ultrafast -b:v 2M -s 720*480 -vf \"movie=".$shuiyin_img."[awm];[in][awm] overlay=25:0 [out]\" -hls_time 30 -hls_list_size 0 -f hls ".$new_file_m3u8);
        }
        return json([
            'code' => 200,
            'msg' => '操作成功',
        ]);
    }

    /**
     * 获取视频转m3u8进度
     * @param Request $request
     * @return \think\response\Json
     */
    public function ffmpegVideoStatus(Request $request){
        // 获取源文件
        $file = ltrim($request->param('file'),'/');
        $path_parts = pathinfo($file);
        // 获取视频按30秒切片后个数
        $getID3 = new \getID3();
        $videoFile = $file;
        $videoFileInfo = $getID3->analyze($videoFile);
        $audioSecond = ceil($videoFileInfo['playtime_seconds']);
        $file_num = ceil($audioSecond/30) + 2; // 转换之后应该有的文件数量
        // 获取m3u8文件夹
        $dir_name = $path_parts['dirname']; // 文件夹路径
        $file_name = $path_parts['filename']; // 文件名不带后缀
        $m3u8_path = $dir_name.'/'.$file_name.'/';
        $new_file_num = count(scandir($m3u8_path))-2; // 已转换完成的文件数量
        $new_m3u8_path = $m3u8_path.$file_name.".m3u8";
        @$fp = file($new_m3u8_path);
        if(@(strcmp(trim($fp[count($fp)-1]),"#EXT-X-ENDLIST") == 0) || ($file_num == $new_file_num)){
            unlink($file);
            return json([
                'code' => 200,
                'data' => [
                    'video_time' => intval(ceil($audioSecond/60)),
                    'm3u8_path' => $new_m3u8_path,
                    'm3u8_img' => $m3u8_path.$file_name.".jpg",
                ],
                'msg' => '转换完成'
            ]);
        } else {
            $progress = intval(round($new_file_num/$file_num,2)*100);
            return json([
                'code' => 201,
                'data' => $progress,
                'msg' => '正在转换'
            ]);
        }
    }


}
