<?php
use think\facade\Route;

/**
 * web 前台
 */
//Route::group('/', [
//    '/' => 'web/Index/index',
//])->middleware(['web_check']);
// 前台登录
Route::group('/login', [
    '/' => 'web/Login/index',
    '/checklogin' => 'web/Login/checkLogin',
    '/loginout' => 'web/Login/loginOut',
]);


/**
 * college 学院模块
 */
Route::group('/', [
    '/' => 'college/Index/index',
])->middleware(['web_check']);
Route::group('/college',[
    '/' => 'college/Index/index',
    '/getvideo' => 'college/Index/getVideo',  // 首页视频获取
    '/read' => 'college/Index/read',  // 查看
    '/setfabulous' => 'college/Index/setFabulous',  // 点赞
    '/categroup' => 'college/Index/CateGroup', // 栏目
    '/getsubcategroup' => 'college/Index/getSubCateGroup',
])->middleware(['web_check']); //->middleware(['web_check'])


/**
 * admin 后台
 */
Route::group('/admin',[
    '/' => 'admin/Index/index',
    '/msg' => 'admin/Index/msg',
])->middleware(['admin_check']);
// 后台登录
Route::group('/admin/login',[
    '/' => 'admin/Login/index',
    '/checklogin' => 'admin/Login/checkLogin',
    '/loginout' => 'admin/Login/loginOut',
]);
// 后台用户
Route::group('/admin/user',[
    '/' => 'admin/User/index',
    "/create" => "admin/User/create",
    "/save" => "admin/User/save",
    "/setstatus" => "admin/User/setStatus",
    "/edit" => "admin/User/edit",
    "/update" => "admin/User/update",
])->middleware(['admin_check']);
// 角色管理
Route::group("/admin/group",[
    "/" => "admin/Group/index",
    "/create" => "admin/Group/create",
    "/save" => "admin/Group/save",
    "/edit" => "admin/Group/edit",
    "/update" => "admin/Group/update",
])->middleware(['admin_check']);
// 前台用户管理
Route::group("/admin/webuser",[
    "/" => "admin/WebUser/index",
    "/create" => "admin/WebUser/create",
    "/save" => "admin/WebUser/save",
    "/setstatus" => "admin/WebUser/setStatus",
    "/edit" => "admin/WebUser/edit",
    "/update" => "admin/WebUser/update",
])->middleware(['admin_check']);
// 前台角色管理
Route::group("/admin/webrole",[
    "/" => "admin/WebRole/index",
    "/create" => "admin/WebRole/create",
    "/save" => "admin/WebRole/save",
    "/edit" => "admin/WebRole/edit",
    "/update" => "admin/WebRole/update",
])->middleware(['admin_check']);
// 系统设置
Route::group('/admin/system',[
//    '/' => 'admin/System/index',
    '/loginlog' => 'admin/System/loginLog',
])->middleware(['admin_check']);
// 上传功能
Route::group('/admin/uploader',[
//    '/' => 'admin/Uploader/index',
    '/webuploader' => 'admin/Uploader/webUploader', // 图片,文件,视频切片上传
    '/ffmpeg_video' => 'admin/Uploader/ffmpegVideo', // 视频切片保存
    '/ffmpeg_video_status' => 'admin/Uploader/ffmpegVideoStatus', // 视频切片进度查询
])->middleware(['admin_check']);
//学院模块管理
Route::group('/admin/college',[
    '/' => 'admin/College/index',
    "/create" => "admin/College/create",
    "/save" => "admin/College/save",
    "/setstatus" => "admin/College/setStatus",
    "/edit" => "admin/College/edit",
    "/update" => "admin/College/update",
    // 栏目管理
    "/category" => "admin/College/category",
    "/category_create" => "admin/College/categoryCreate",
    "/category_save" => "admin/College/categorySave",
    "/category_edit" => "admin/College/categoryEdit",
    "/category_update" => "admin/College/categoryUpdate",
    // 轮播图管理
    '/rotation' => 'admin/College/rotation',
    '/rotation_create' => 'admin/College/rotationCreate',
    '/rotation_save' => 'admin/College/rotationSave',
    '/rotation_status' => 'admin/College/rotationStatus',
    '/rotation_edit' => 'admin/College/rotationEdit',
    '/rotation_update' => 'admin/College/rotationUpdate',
])->middleware(['admin_check']);
// 智能云管理
Route::group('/admin/smartcloud',[
    '/' => 'admin/SmartCloud/index',
    '/friendlist' => 'admin/SmartCloud/FriendList',
    '/friendchatrecord' => 'admin/SmartCloud/FriendChatRecord',
])->middleware(['admin_check']);

/**
 * 智能云模块
 */
Route::group('/smartcloud',[
    '/' => 'smartcloud/Index/index', // 首页
    '/bingding' => 'smartcloud/Index/bingding',  // 绑定账号页面
    '/getwxfriendslist' => 'smartcloud/Index/getWxFriendsList', // 获取好友
    '/getwxchatassociation' => 'smartcloud/Index/getWxChatAssociation', // 获取聊天列表
    '/getwxchatrecord' => 'smartcloud/Index/getWxChatrecord', // 获取聊天记录
    '/send' => 'smartcloud/Index/send', // 发送消息
    '/getfriendinfo' => 'smartcloud/Index/getFriendInfo', // 获取联系人信息
    '/getgroupfriendlist' => 'smartcloud/Index/getGroupFriendList', // 获取群聊人员信息
    '/login_two' => 'smartcloud/Index/loginTwo', // 二次登录
    '/WXRevokeMsg'=>'smartcloud/Index/WXRevokeMsg',//消息撤回
    '/logout'=>'smartcloud/Index/WxLonOut',//注销登录
    '/serch'=>'smartcloud/Index/Serch',//sousuo
    '/wxFriendCircle'=>'smartcloud/Index/wxFriendCircle'//朋友圈页面
])->middleware(['web_check']);
// 微信API
Route::group('/smartcloud/wxapi',[
    '/wxloginqrcode' => 'smartcloud/WxApi/WXLoginQrcode', // 获取二维码
    '/wxcheckloginqrcode' => 'smartcloud/WxApi/WXCheckLoginQrcode', // 获取扫码状态
    '/wxloginmanual' => 'smartcloud/WxApi/WXLoginManual', // 人工登录
    '/wxinitcontact' => 'smartcloud/WxApi/WXInitContact', // 初始化微信好友
    '/wxsyncmsg' => 'smartcloud/WxApi/WXSyncMsg', // 同步微信消息
    '/wxpushloginqrcode' => 'smartcloud/WxApi/WXPushLoginQrcode', // 二次登陆请求
])->middleware(['web_check']);

