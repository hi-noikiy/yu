<?php
// 应用公共文件
/**
 * 请求OA羽化管理平台
 * @param $api  // api接口
 * @return mixed
 */
function adminrequestCurl($api,$method,$data=''){
    // 本地开发修改此地址
    $url = "http://crms.com/index.php".$api; // 本地
//    $url = "http://yuhsw.com/index.php".$api;  // 线上
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    if($method == 'POST' && !empty($data)){
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    $ret = curl_exec($curl);
    curl_close($curl);
    return json_decode($ret,true);
}

/**
 * 用户密码加密方法
 * @param  string $str      加密的字符串
 * @param  [type] $auth_key 加密符
 * @param  [string] $username 用户名
 * @return string           加密后长度为32的字符串
 */
function user_md5($str, $auth_key = '', $username = '')
{
    return '' === $str ? '' : md5(sha1($str) . md5($str.$auth_key));
}

/**
 * 获取salt
 * @return string
 */
function getsalt()
{
    $salt = "";
    for ($i = 0; $i < 6; $i++) {
        $salt .= substr("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",mt_rand(0,63),1);
    }
    return $salt;
}

/**
 * uuid生成
 * @return string
 */
function getuuid() {
    $charid = md5(uniqid(mt_rand(), true));

    $hyphen = chr(45);

    $uuid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
    return $uuid;
}

/**
 * Token 生成
 * @param int $adminId
 * @param string $type
 * @return mixed
 */
function createToken($adminId,$type)
{
    $secret = "YHKJ_TOKEN";      //密匙
    $payload = [
        'iss'=>'YuHuaKeJi',                //签发人(官方字段:非必需)
        'exp'=>time()+3600*5,     //过期时间(官方字段:非必需)
        'aud'=> $type,              //受众(官方字段:非必需)
        'nbf'=>time(),               //生效时间(官方字段:非必需)
        'iat'=>time(),               //签发时间(官方字段:非必需)
        'user_id'=>$adminId,        //自定义字段
    ];
    $token = \Firebase\JWT\JWT::encode($payload,$secret,'HS256');
    return $token;
}

/**
 * 解密Token 返回用户信息
 * @param $token
 * @return string
 */
function checkToken($token)
{
    try{
        $Result = \Firebase\JWT\JWT::decode($token,'YHKJ_TOKEN',['HS256']);
        if($Result->aud == 'web'){
            // 远程获取用户信息
            $result = adminrequestCurl('/api/remoteshare/RemoteGetUser',"POST", ['id' => $Result->user_id,]);
            $userinfo = [
                'id' => $result['data']['id'],
                'username' => $result['data']['username'],
                'truename' => $result['data']['username'],
                'groupid' => $result['data']['role_id'],
            ];
            //旧版
//            $userinfo = (new \app\web\model\WebUserModel())::get($Result->user_id);
        } else {
            $userinfo = (new \app\admin\model\AdminUserModel())::get($Result->user_id);
        }
        return $userinfo;
    }
    catch (Exception $e)
    {
        Session("web_token",null);
        return false;
    }
}
