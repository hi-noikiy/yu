// 选择账号
$(".user_list_headimg").click(function () {
    // 获取wxid
    wxid = $(this).find(".user_list_wxid").val();
    if($(this).attr("login_status") == '-1'){
        layer.alert("该账号登录失效<a href='javascript:void(0);' onclick='smartcloud_user_add(\"重新登录\",\"/smartcloud/login_two?wxid="+wxid+"\",\"1000\",\"800\")'>点击登录</a>")
        return false
    }
    // 选中状态
    $(this).find("img").addClass("user_list_active");
    $(this).siblings().find("img").removeClass("user_list_active");
    $(this).find(".msg_tips").hide()
    $(".hide_msg").show();
    $(".friend").show();
    $(".content").hide();
    var data_id = $(this).attr("data-id");
    history.replaceState(null, null, addParamsToUrl(window.location.href, 'list', data_id))
    $hearder_url = $(this).find('img').attr('src');
    var $nickname = $(this).find('.user_list_nikename').val();
    var $content_html = "<div style=\"text-align: center;margin-top: 30px\">\n" +
        "<img style=\"width:5%;border-radius: 50%;\" src=\""+$hearder_url+"\" alt=\"\">\n" +
        "<h5 style='font-weight: bold;font-size: 1.7rem'>Hey, "+$nickname+"</h5>\n" +
        "<h5>Please select a chat to start messaging.</h5>\n" +
        "</div>";
    $(".hide_msg").html($content_html)
    // 获取好友列表
    getFriendListAjax(wxid)
    // 消息列表
    getMsgListAjax(wxid)
});
// 获取当前选择账号
read();
function read() {
    var list = GetQueryString("list")
    //console.log(list);return;
    if(list == '' || list == null){
        return false;
    }
    $(".user_list_"+list).trigger("click");
    var type = GetQueryString("type")
    if(type == 'msg'){
        $(".text_sign").html('消息列表')
        $(".msg_list").show(500)
        $(".friend_list").hide(500)
    } else {
        $(".text_sign").html('联系人列表')
        $(".friend_list").show(500)
        $(".msg_list").hide(500)
    }
    setfriendmsg()
}

// 开启消息查询
start_socket()
function start_socket() {
    // 获取当前微信数量
    for (var i=0; i<$(".user_list_headimg").length; i++){
        (function(i) {
            var data = $(".user_list_headimg")[i];
            var wxid = $(data).find(".user_list_wxid").val();
            if($(data).attr("login_status") == '1'){
                setTimeout(function() {
                    socket(wxid,i)
                }, (i + 1) * 5000);
            }
        })(i)
    }
}


// 切换列表
function select_model(self) {
    $(self).addClass("text_friend_active").siblings().removeClass('text_friend_active')
    $data = $(self).attr('data');
    //console.log($data);return;
    //消息
    if($data == 'msg_btn'){

        history.replaceState(null, null, addParamsToUrl(window.location.href, 'type', 'msg'))
        $(".text_sign").html('消息列表')
        $(".msg_list").show(500)
        $(".friend_list").hide(500)
        $('.serch').hide(500);
        $('.serch_list').hide();
        var list = GetQueryString("list")
        //console.log(list);return;
        if(list != '' && list != null){
            $(".hide_msg").hide();
            $('.serch').hide(500);
            $(".content").show();
        }
        // getMsgListAjax(wxid)
    }
    //好友
    if($data == 'friend_btn'){
        history.replaceState(null, null, addParamsToUrl(window.location.href, 'type', 'friend'))
        $(".text_sign").html('联系人列表')
        $(".friend_list").show(500)
        $(".msg_list").hide(500)
        $('.serch').hide(500);
        $('.serch_list').hide();
        var list = GetQueryString("list")
        if(list != '' && list != null){
            $(".content").hide();
            $('.serch').hide();
            $(".hide_msg").show();

        }
    }
    //搜索
    if($data == 'serch_btn'){
        history.replaceState(null, null, addParamsToUrl(window.location.href, 'type', 'serch'))
        $(".text_sign").html('搜索联系人')
        $(".friend_list").hide(500)
        $(".msg_list").hide(500)
        $('.serch').show(500);
        var list = GetQueryString("list");
        if(list != '' && list != null){
            if($('.user_list_'+list).attr('login_status') == -1){
                $('.hide_msg').show(500);return;

            }
            $(".hide_msg").hide(500);
            $(".content").hide(500);
            var html = '<div style="text-align: center;margin-top: 150px;height: 50%;">\n' +
                '            <input class="serch_input" id="serch_input" type="text" value="" placeholder="请输入您要搜索的内容">\n' +
                '            <div style="margin-top: 2%">\n' +
                '                <button onclick="serch()" class="serch_btn">搜索</button>\n' +
                '            </div>\n' +
                '        </div>'
            $(".serch").html(html);
        }
    }
}
// 消息列表ajax
function getMsgListAjax(wxid) {
    $(".msg_list").html("")
    $.post("/smartcloud/getwxchatassociation",{'wxid':wxid},function (result) {
        //console.log(result);return;
        if(result['code'] == 200){
            getMsgList(result)
        } else {
            var $html = "<div style='text-align: center;width: 100px;margin: 50px auto;'>\n"+
                "<img src=\"/static/web/smartcloud/images/no_msg.png\" alt=\"\" style=\"width: 100px;\">\n" +
                "<div>暂无消息</div>\n"+
                "</div>"
            $(".msg_list").html($html);
        }
    })
}
// 消息列表
function getMsgList(result) {
    var $html = '';
    for (var i=0; i<result['data'].length; i++){

        if(result['data'][i]['remark'] == "" || result['data'][i]['remark'] == null){
            var name = result['data'][i]['nickname'];
        }else{
            var name = result['data'][i]['remark'];
        }
        if(result['data'][i]['status'] == '0'){
            var show_msg_tips = "style='display:none'";
        } else {
            var show_msg_tips = "style='display:block'";
        }
        $html +=    "<div class=\"card mb-6\" onclick='getfriendSendMsg(this)'>\n" +
            "<div class=\"card-body\">\n" +
            "<div class=\"media\">\n" +
            "<div class=\"msg_time_center\">\n" +
            "    <span class=\"mb-0\">"+result['data'][i]['timestr']+"</span>\n" +
            "    <div "+show_msg_tips+" class=\"badge badge-circle badge-primary badge-border-light badge-top-right\">\n" +
            "        <span></span>\n" +
            "    </div>\n" +
            "</div>\n" +
            "<div class=\"avatar\">\n" +
            "    <img class=\"avatar-img\" src=\""+result['data'][i]['headimgurl']+"\">\n" +
            "</div>\n" +
            "<div class=\"align-self-center\">\n" +
            "<h5 class=\"mb-0 friend_name\" style='font-weight: bold;'>"+name+"</h5>\n" +
            "<input type=\"text\"  name=\""+result['data'][i]['partyid']+"msg\" style=\"display: none\" disabled class=\"friend_wxid\" value=\""+result['data'][i]['partyid']+"\">"+
            "<h5 class=\"mb-0 last_msg\">"+result['data'][i]['content']+"</h5>\n" +
            "</div>\n" +
            "</div>\n" +
            "</div>\n" +
            "</div>";
    }
    $(".msg_list").html($html);
}
// 获取好友AJAX
function getFriendListAjax(wxid) {
    //console.log(wxid);
    $.post("/smartcloud/getwxfriendslist",{'wxid':wxid},function (result) {
        $(".friend_list").html("");
        if(result['code'] == 200){
            //console.log(result);return;
            getFriendList(result)
        } else {
            var $html = "<div style='text-align: center;width: 100px;margin: 50px auto;'>\n"+
                "<img src=\"/static/web/smartcloud/images/no_friend.png\" alt=\"\" style=\"width: 100px;\">\n" +
                "<div>暂无联系人</div>\n"+
                "</div>"
            $(".friend_list").html($html);
        }
    })
}
// 好友列表
function getFriendList(result) {
    var $html = '';
    for (var i=0; i<Object.keys(result['data'][0]).length; i++){
        //console.log(Object.keys(result['data'][0]));return;
        key = Object.keys(result['data'][0])[i]
        //console.log(key);
        $html +="<div class=\"mb-6 text-uppercase\">\n" +
            "<small>"+key+"</small>\n" +
            "</div>";
        for (var ii=0; ii<result['data'][0][key].length; ii++){
            if(result['data'][0][key][ii].remark != null && result['data'][0][key][ii].remark != ''){
                var name = result['data'][0][key][ii].remark;
            }else{
                var name = result['data'][0][key][ii].nickname;
            }
            $html +="<div class=\"card mb-6\" onclick='get_friend_info(this)'>\n" +
                "<div class=\"card-body\">\n" +
                "<div class=\"media\">\n" +
                "<div class=\"avatar avatar-online\">\n" +
                "<img class=\"avatar-img\" src=\""+result['data'][0][key][ii].headimgurl+"\">\n" +
                "</div>\n" +
                "<input type=\"text\"  name=\""+result['data'][0][key][ii].wxid+"friend\" style=\"display: none\" disabled class=\"user_wxid\" value=\""+result['data'][0][key][ii].wxid+"\">"+
                "<div class=\"align-self-center\">\n"+
                "<h5 class=\"mb-0 friend_name\" style='font-weight: bold;'>"+name+"</h5>\n"+
                "</div>\n"+
                "</div>\n" +
                "</div>\n" +
                "</div>";
        }
    }
    // 群
    var $qun_html = "";
    if(result['data'][1] != null){
        /*$qun_html +="<div class=\"mb-6 text-uppercase\">\n" +
            "<small>群</small>\n" +
            "</div>";*/
        $qun_html +="<div class=\"mb-6 text-uppercase\">\n" +
            "            <small>群</small>\n" +
            "        </div>"

        for (var s=0; s<result['data'][1].length; s++){
            $qun_html +="<div class=\"card mb-6\" onclick='get_friend_info(this)'>\n" +
                "<div class=\"card-body\">\n" +
                "<div class=\"media\">\n" +
                "<div class=\"avatar avatar-online\">\n" +
                "<img class=\"avatar-img\" src=\""+result['data'][1][s].headimgurl+"\" alt=\"Anna Bridges\">\n" +
                "</div>\n" +
                "<input type=\"text\"  name=\""+result['data'][1][s].wxid+"friend\" style=\"display: none\" disabled class=\"user_wxid\" value=\""+result['data'][1][s].wxid+"\">"+
                "<div class=\"align-self-center\">\n" +
                "<h5 class=\"mb-0 friend_name\" style='font-weight: bold;'>"+result['data'][1][s].nickname+"</h5>\n" +
                "</div>\n" +
                "</div>\n" +
                "</div>\n" +
                "</div>";
        }
    }
    $(".friend_list").html($qun_html+$html);
    // 公众号
    var $gzh_html = "";
    if(result['data'][2] != null){
        $gzh_html +="<div class=\"mb-6 text-uppercase\">\n" +
            "<small>公众号</small>\n" +
            "</div>";
        for (var ss=0; ss<result['data'][2].length; ss++){
            $gzh_html +="<div class=\"card mb-6\" onclick='get_friend_info(this)'>\n" +
                "<div class=\"card-body\">\n" +
                "<div class=\"media\">\n" +
                "<div class=\"avatar avatar-online\">\n" +
                "<img class=\"avatar-img\" src=\""+result['data'][2][ss].headimgurl+"\" alt=\"Anna Bridges\">\n" +
                "</div>\n" +
                "<input type=\"text\"  name=\""+result['data'][2][ss].wxid+"friend\" style=\"display: none\" disabled class=\"user_wxid\" value=\""+result['data'][2][ss].wxid+"\">"+
                "<div class=\"align-self-center\">\n" +
                "<h5 class=\"mb-0 friend_name\" style='font-weight: bold;'>"+result['data'][2][ss].nickname+"</h5>\n" +
                "</div>\n" +
                "</div>\n" +
                "</div>\n" +
                "</div>";
        }
    }
    $(".friend_list").append($gzh_html);
}
// 消息列表选择用户发送消息
function getfriendSendMsg(self) {
    $(".hide_msg").hide();
    $(".content").show();
    $(self).addClass('msg_friend_active').siblings().removeClass("msg_friend_active");
    var $friend_hearurl = $(self).find(".avatar-img").attr("src");
    var $friend_name = $(self).find(".friend_name").html();
    $(".content").find(".content_header .content_header_img").html("<img src=\""+$friend_hearurl+"\" alt=\"\">")
    $(".content").find(".content_header .content_friend_name").html($friend_name)
    $(self).find('.msg_time_center .badge').hide()
    var info = [];
    info['hearder_url'] = $hearder_url;
    info['friend_hearurl'] = $friend_hearurl;
    info['friend_name'] = $friend_name;
    var $friend_wxid = $(self).find(".friend_wxid").val();
    $(".content").find(".content_header .content_friend_wxid").html($friend_wxid)
    history.replaceState(null, null, addParamsToUrl(window.location.href, 'id', btoa(encodeURIComponent($friend_wxid))))
    var div = $('#up');
    var page = 1;
    getMsgContentAjax(wxid,$friend_wxid,info,page,1)
    // 监听滑动条
    // div.scroll(function () {
    //     if(div[0].scrollTop == 0){
    //         var page = $('#up').attr('data-page');
    //         $(".btn_getmsg_page").remove();
    //         $(".content_main").prepend("<div class='btn_getmsg_page' style='text-align: center' onclick='get_msg_page(this,"+page+")'>点击获取上一页</div>")
    //     }
    // })
}
// 点击获取消息
function get_msg_page(self,page) {
    var $friend_hearurl = $(self).parents(".content").find(".content_header .content_header_img img").attr('src');
    var $friend_name = $(self).parents(".content").find(".content_header .content_friend_name").text();
    var $friend_wxid = $(self).parents(".content").find(".content_header .content_friend_wxid").text();
    var info = [];
    info['hearder_url'] = $hearder_url;
    info['friend_hearurl'] = $friend_hearurl;
    info['friend_name'] = $friend_name;
    getMsgContentAjax(wxid,$friend_wxid,info,page,2)
    $(".btn_getmsg_page").remove();
}
// 获取聊天消息内容ajax
function getMsgContentAjax(wxid,$friend_wxid,info,page,type){
    $.post("/smartcloud/getwxchatrecord",{'ourid':wxid,'partyid':$friend_wxid,'page':page},function (result) {
        if(type != 2) {
            $(".content_main").html("")
        }
        if(result['code'] == 200 && result['data'].length>0){
            $(".content_main").attr("data-page",parseInt(page)+1)
            getMsgContent(result,info,type,page)
        }
    });
}
// 获取聊天消息内容
function getMsgContent(result,info,type,page){
    var $html = "<div class='btn_getmsg_page' style='text-align: center' onclick='get_msg_page(this,"+(parseInt(page)+1)+")'>点击获取上一页</div>";
    for (var i=0; i<result['data'].length; i++){
        // 消息类型 1:文字; 3:图片;34:语音;43:视频;47:表情,49分享;
        if(result['data'][i]['type'] == 1){
            var $content_html = result['data'][i]['content'];
        }
        if(result['data'][i]['type'] == 3){
            var $content_html = "<img onclick='forwardImg()' src=\""+result['data'][i]['img_base64']+"\" >";
        }
        if(result['data'][i]['type'] == 34){
            var $content_html = "[语音消息]"
        }
        if(result['data'][i]['type'] == 43){
            var $content_html = "[视频消息]"
        }
        if(result['data'][i]['type'] == 47){
            var $content_html = "<img src=\""+result['data'][i]['img_base64']+"\">";
        }
        if(result['data'][i]['type'] == 49){
            var $content_html = "[分享消息]"
        }
        // 是否群聊消息,显示群成员头像
        if(result['data'][i]['sp_type'] == 1){
            var friend_name = info['friend_name'];
            var friend_header = info['friend_hearurl'];
        } else {
            var friend_name = result['data'][i]['sp_nkname'];
            var friend_header = result['data'][i]['sp_header'];
        }
        // 判断发送方
        //console.log(result);

        //console.log(newmsgid);
        if(result['data'][i]['is_send'] == 1){
            var newmsgid = result['data'][i]['newmsgid'];

            $html +="<div class=\"me\" >\n" +
                "<div class=\"msg_content msg_withdraw\" data-id=\""+newmsgid+"\" onclick='revokeMsg(this)'>\n" +
                "<div style=\"margin-bottom: 5px;\">\n" +
                "<span>"+result['data'][i]['send_time']+"</span>\n" +
                "</div>\n" +
                "<br/>"+
                "<div style=\"text-align: left\">\n"+
                    "<span>"+$content_html+"</span>\n" +
                "</div>\n"+
                "</div>\n" +
                "<div><img class=\"content_me_headerimg\" src=\""+info['hearder_url']+"\"></div>\n" +
                "</div>";
        } else {
            $html +="<div class=\"you\">\n" +
                "<div><img class=\"content_you_headerimg\" src=\""+friend_header+"\" alt=\"\"></div>\n" +
                "<div class=\"msg_content\">\n" +
                "<div style=\"margin-bottom: 5px;\">\n" +
                "<span style=\"font-weight: bold\">"+friend_name+"</span> &nbsp;&nbsp;&nbsp; <span>"+result['data'][i]['send_time']+"</span>\n" +
                "</div>\n" +
                "<br/>\n" +
                "<span>"+$content_html+"</span>\n" +
                "</div>\n" +
                "</div>";
        }
    }
    if(type == 2){
        $(".content_main").html($html+$(".content_main").html())
        var div = document.getElementById('up');
        div.scrollTop = i*80;
    } else {
        $(".content_main").html($html)
        scrollToBottom()
    }
}
// 发送文本消息
function send_msg(self) {
    var content_msg = $(self).parents(".content_foot").find(".content_msg").text()
    if(content_msg == ''){
        layer.msg("请输入聊天内容")
        return false
    }
    var send_time = gettime();
    time = send_time
    var html =  "<div class=\"me\" '>\n" +
        "<div class=\"msg_content\" >\n" +
        "<div style=\"margin-bottom: 5px;\">\n" +
        "<span>"+send_time+"</span>\n" +
        "</div>\n" +
        "<br/>\n" +
        "<div style=\"text-align: left\">\n"+
            "<span>"+content_msg+"</span>\n" +
        "</div>\n" +
        "</div>\n" +
        "<div><img class=\"content_me_headerimg\" src=\""+$hearder_url+"\" alt=\"\"></div>\n" +
        "</div>";
    $(self).parents(".content_foot").find(".content_msg").val("")
    var friend_wxid = $(self).parents(".content").find(".content_header .content_friend_wxid").text()
    var type = "{$Request.param.type}"
    // type 消息类型 1:文字; 3:图片;34:语音;43:视频;47:表情;49:分享;
    $.post("/smartcloud/send",
        {"content":content_msg,"ourid":wxid,"partyid":friend_wxid,"type":1},
        function (result) {
            if(result['code'] != 200){
                layer.alert(result['msg'])
                return false
            }
            //start_socket();
            $(".content_main").append(html);
            scrollToBottom()
            $(self).parents(".content_foot").find(".content_msg").text("")
            if(GetQueryString("type") == 'friend'){
                $("#msg_btn").trigger('click')
            }
        })
}
// 联系人列表点击查看好友信息
function get_friend_info(self) {
    //console.log(self);return;
    $(".content").hide();
    $('.serch').hide();
    $(".hide_msg").show();
    //console.log(1);return;
    $(self).addClass('msg_friend_active').siblings().removeClass("msg_friend_active");
    var friend_name = $(self).find('.friend_name').text();
    var friend_header = $(self).find('img').attr('src');
    var user_wxid = $(self).find('.user_wxid').val();
    history.replaceState(null, null, addParamsToUrl(window.location.href, 'id', btoa(encodeURIComponent(user_wxid))))
    $.post("/smartcloud/getfriendinfo",{"friend_wxid":user_wxid},function (result) {
        if(result['code'] == 200){
            var sex = '未知'
            if(result['data']['sex'] == 1){
                sex = '男'
            } else if(result['data']['sex'] == 2) {
                sex = '女'
            }
            var addr = '';
            if(result['data']['country'] == '中国'){
                if(result['data']['province'] != '' && result['data']['province'] != null){
                    addr = result['data']['province'];
                    if(result['data']['city'] != '' && result['data']['city'] != null){
                        addr = result['data']['province'] + result['data']['city'];
                    }
                } else {
                    addr = result['data']['country'];
                }
            }else{
                addr = result['data']['country'];
            }
            var alias = '';
            if(result['data']['alias'] != null){
                alias = result['data']['alias'];
            } else {
                alias = user_wxid;
            }
            var $friend_info_html = "<div style=\"text-align: center;margin-top: 30px\">\n" +
                "<img class='user_info_header' style=\"width:5%;border-radius: 50%;\" src=\""+friend_header+"\" alt=\"\">\n" +
                "<h5 class='user_info_name' style=\"font-weight: bold;font-size: 2rem\">"+friend_name+"</h5>\n" +
                "<div style=\"font-size: 1.7rem;margin: 20px 0 0 0;\">\n" +
                "性别 : <b>"+sex+"</b>&nbsp;&nbsp;&nbsp;\n" +
                "地区 : <b>"+addr+"</b>&nbsp;&nbsp;&nbsp;\n" +
                "微信号 : <b>"+alias+"</b>&nbsp;&nbsp;&nbsp;\n" +
                "</div>\n" +
                "<div class=\"btn btn-success\" style=\"margin: 20px;\" onclick='friend_info_send(this,\""+user_wxid+"\")'>发消息</div>\n"+
                "</div>";
            $(".hide_msg").html($friend_info_html)
            if(result['data']['type'] == 2){
                $.post("/smartcloud/getgroupfriendlist",{"wxid":user_wxid},function (result2) {
                    var $qun_html = '';
                    if(result2['code'] == 200){
                        $qun_html +=    "<div style=\"text-align: center\">\n" +
                            "<span style=\"font-weight: bold;font-size: 1.5rem;\">群成员("+result2['data'].length+")</span>\n"+
                            "<div class='group_friend_list'>\n";
                        for (var ii=0; ii<result2['data'].length; ii++){
                            if(result2['data'][ii]['remark'].length>0){
                                var name = result2['data'][ii]['remark'];
                            } else {
                                var name = result2['data'][ii]['nickname']
                            }
                            $qun_html +=    "<div title='"+name+"' onclick='show_group_info(this)' style=\"display: inline-block;position:relative;\">\n" +
                                                "<img style=\"width: 60px;\" src=\""+result2['data'][ii]['headimgurl']+"\" alt=\"\">\n" +
                                                "<br>\n" +
                                                "<span class='group_friend_list_span'>"+name+"</span>\n" +
                                                "<div class='show_group_info' style='background-color:#f5f5f5;border: #d8c8c8 1px solid;width: 280px;border-radius: 10px;height: 115px;padding: 10px;z-index: 9999;display: none;position: absolute;'>" +
                                                    "<img style=\"width: 60px;\" src=\""+result2['data'][ii]['headimgurl']+"\" alt=\"\">" +
                                                    "<span style='margin-left: 10px' class=''>"+name+"</span>\n" +
                                                    "<span style='margin-left: 10px' class=''>"+sex+"</span><br/>\n" +
                                                    "<span style='margin-left: 70px' class=''>地区</span>\n" +
                                                "</div>\n" +
                                            "</div>\n";
                        }
                        $qun_html +=    "</div>\n" +
                                        "</div>";
                        $(".hide_msg").append($qun_html)
                    } else {
                        layer.msg(result['msg'])
                    }
                });
            }
        } else {
            layer.msg(result['msg'])
        }
    })
}
function show_group_info(self) {
    $(self).find(".show_group_info").toggle();
    $(self).siblings().find(".show_group_info").hide();
    // console.log($(self).parents(".group_friend_list")[0].offsetWidth) // 最外侧DIV宽度
    // console.log($(self).find(".show_group_info")[0].offsetWidth) // 弹窗的宽度
    // console.log($(self)[0].offsetLeft) // 父级距左边的距离
}
// 联系人信息点击发消息
function friend_info_send(self,user_wxid) {
    $(".content").show();
    $(".hide_msg").hide();
    var $friend_hearurl =$(self).parent("div").find('.user_info_header').attr("src");
    var $friend_name = $(self).parent("div").find(".user_info_name").html();
    $(".content").find(".content_header .content_header_img").html("<img src=\""+$friend_hearurl+"\" alt=\"\">")
    $(".content").find(".content_header .content_friend_name").html($friend_name)
    var info = [];
    info['hearder_url'] = $hearder_url;
    info['friend_hearurl'] = $friend_hearurl;
    info['friend_name'] = $friend_name;
    $(".content").find(".content_header .content_friend_wxid").html(user_wxid)
    var div = $('#up');
    var page = 1;
    getMsgContentAjax(wxid,user_wxid,info,page,1)
    // div.scroll(function () {
    //     if(div[0].scrollTop == 0){
    //         var page = $('#up').attr('data-page');
    //         getMsgContentAjax(wxid,user_wxid,info,page,2)
    //     }
    // })
}
// 获取地址栏参数
function GetQueryString(name){
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = decodeURI(window.location.search).substr(1).match(reg); //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}
// 绑定账号弹窗
function smartcloud_user_add(title,url,w,h){
    if (title == null || title == '') {
        title=false;
    };
    if (url == null || url == '') {
        url="404.html";
    };
    if (w == null || w == '') {
        w=800;
    };
    if (h == null || h == '') {
        h=($(window).height() - 50);
    };
    layer.open({
        type: 2,
        area: [w+'px', h +'px'],
        fix: false, //不固定
        maxmin: true,
        shade:0.4,
        title: title,
        content: url
    });
}
// 获取当前时间 格式YYYY-MM-DD HH:II:SS
function gettime(){
    Date.prototype.Format = function (fmt) {
        var o = {
            "M+": this.getMonth() + 1, //月份
            "d+": this.getDate(), //日
            "H+": this.getHours(), //小时
            "m+": this.getMinutes(), //分
            "s+": this.getSeconds(), //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    }
    var currtime = new Date().Format("yyyy-MM-dd HH:mm:ss");
    return currtime
}
// 设置url参数
function addParamsToUrl(url, key, val) {
    if(!val) {
        return url;
    }
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = url.indexOf('?') !== -1 ? "&" : "?";
    if (url.match(re)) {
        return url.replace(re, '$1' + key + "=" + val + '$2');
    }
    else {
        return url + separator + key + "=" + val;
    }
}
//div滚动条(scrollbar)保持在最底部
function scrollToBottom(){
    var div = document.getElementById('up');
    // div.innerHTML = div.innerHTML + '<br />';
    div.scrollTop = div.scrollHeight;
}
//获取地址栏里（URL）传递的参数
function GetRequest(localUrl) {
    //console.log(localUrl);
    //url例子：XXX.aspx?ID=" + ID + "&Name=" + Name；
    var url = localUrl; //获取url中"?"符以及其后的字串

    var addressParameter = {};
    if(url.indexOf("?") != -1)//url中存在问号，也就说有参数。
    {
        var str;
        if(url.indexOf("=")!=-1){
            str = url.substr(1,url.indexOf("=")-1);
        }else{
            str =  url.substr(1);
        }
        //console.log(str);
        //地址栏参数解密
        var addressUrl=decodeURIComponent(atob(str));
        var addressData = addressUrl.split('&');
        for(var i = 0; i < addressData.length; i ++){
            addressParameter[addressData[i].split("=")[0]]=unescape(addressData[i].split("=")[1]);
        }

    }
    return addressParameter;
}
// 刷新页面后打开刷新前窗口
function setfriendmsg(){
    setTimeout(function () {
        var wxid = atob(GetQueryString("id"))
        wxid = wxid.replace("%40","@");
        if(GetQueryString("type") == 'msg'){
            $("input[name='"+wxid+"msg']").parents(".card").trigger('click')
        } else if(GetQueryString("type") == 'friend') {
            $("input[name='"+wxid+"friend']").parents(".card").trigger('click')
        }
    },1500)
}

// socket 同步消息
function socket(wxid,i){
    var ws = [];
    ws[i] = new WebSocket(websocket_ip);
    ws[i].onopen = function() {
        ws[i].send(wxid);
    };
    ws[i].onmessage = function(e) {
        if(e.data == 1){
            console.log('正在连接');
        } else if(e.data == 2){
            console.log('断开连接')
        } else if(e.data == 3){
            console.log('连接成功')
        } else {
            var data = JSON.parse(e.data);
            if(data['code'] == 200){
                // 消息获取成功操作
                $(".user_list_"+i).find('.msg_tips').show()
                // 只刷新当前显示的窗口信息
                var list = GetQueryString("list");

                if(list != ''){
                    $(".user_list_"+list).find('.msg_tips').hide()
                    getMsgListAjax($(".user_list_"+list).find(".user_list_wxid").val())
                    if(GetQueryString("type") == 'msg'){
                        var wxid = atob(GetQueryString("id"));
                        wxid = wxid.replace("%40","@");
                        setTimeout(function () {
                            $("input[name='"+wxid+"msg']").parents(".card").trigger('click')
                        },500)
                    }
                }
            } else {
                // 无消息
                //console.log(data)
            }
        }
    };
}
// 发送图片, 拖动图片转base64
msg_image_base64()
function msg_image_base64(){
    // 阻止拖放的图片在新窗口中直接显示
    // 拖放的目标对象此时是document对象
    document.ondragover = function(e){
        e.preventDefault(); //使得ondrop可能触发
    }
    document.ondrop = function(e){
        e.preventDefault();//阻止浏览器在新窗口中打开本地图片
    }
    // 为#container做释放事件的监听
    up.ondragover = function(e){
        e.preventDefault();//使得ondrop可能触发
    }
    up.ondrop = function(e){
        //读取浏览器在源对象拖动时在“拖拉机”中保存的数据
        //console.log(e.dataTransfer);
        //console.log(e.dataTransfer.files); //FileList
        //用户拖动进来的第0张图片
        var f0 = e.dataTransfer.files[0];
        if(f0.type == 'image/gif'){
            layer.msg("暂不支持发送动态表情")
            return false
        }
        //创建一个文件内容读取器——FileReader
        var fr = new FileReader();
        //读取文件中的内容 —— DataURL：一种特殊的URL地址，本身包含着所有的数据
        fr.readAsDataURL(f0);
        fr.onload = function(){
            var friend_wxid = $(".content").find(".content_friend_wxid").text();
            var friend_name = $(".content").find(".content_friend_name").text();
            layer.confirm('确认发送图片给 '+friend_name+' 吗?<br /><img style="width: 50px;height: 30px;" src="'+fr.result+'">', {
                btn: ['确认','取消'] //按钮
            }, function(){
                // 发送图片
                // type 消息类型 1:文字; 3:图片;34:语音;43:视频;47:表情;49:分享;
                $.post("/smartcloud/send",
                    {"content":fr.result,"ourid":wxid,"partyid":friend_wxid,"type":3},
                    function (result) {
                        if(result['code'] != 200){
                            layer.alert(result['msg'])
                            return false
                        }
                        layer.msg('发送成功');
                        var send_time = gettime();
                        var html =  "<div class=\"me\">\n" +
                            "<div class=\"msg_content\">\n" +
                            "<div style=\"margin-bottom: 5px;\">\n" +
                            "<span>"+send_time+"</span>\n" +
                            "</div>\n" +
                            "<br/>\n" +
                            "<span><img src=\""+fr.result+"\" onclick='forwardImg()'></span>\n" +
                            "</div>\n" +
                            "<div><img class=\"content_me_headerimg\" src=\""+$hearder_url+"\" alt=\"\"></div>\n" +
                            "</div>";
                        $(".content_main").append(html);
                        scrollToBottom()
                        if(GetQueryString("type") == 'friend'){
                            $("#msg_btn").trigger('click')
                        }
                    })
            }, function(){
                // 取消
                layer.msg('已取消');
            });
        }
    }
}
// emoji开关
function emoji_btn() {
    $(".emoji").toggle();
}
// 获取emoji
$.getJSON("/static/web/smartcloud/js/emoji.json", function (data) {
    var $emoji_html = '';
    for (var i=0; i<data.length; i++){
        $emoji_html += "<div class=\"emoji_font\" onclick='select_emoji(this)' title='"+data[i]['name']+"'>"+data[i]['emoji']+"</div>";
    }
    $(".emoji").html($emoji_html)
});
// 发送emoji
function select_emoji(self) {
    var emoji = $(self).html()
    $("#div_content").append(emoji)
    $(".emoji").hide();
}


//消息撤回
function revokeMsg(e){
    var newmsgid =  $(e).attr('data-id');
    //console.log(newmsgid);
    $.post('/smartcloud/WXRevokeMsg',{newmsgid},function (res) {
        //console.log(res);return;
        if(res['code'] == 200){
            layer.msg(res['msg']);
        }
        if(res['code'] != 200){
            layer.msg(res['msg'])
        }
    });
}
//注销登录
function loginOut(e){
    var nikename = $(e).children('.user_list_nikename').attr('value')
    var wxid = $(e).children('.user_list_wxid').attr('value');
    var login_status=$(e).attr('login_status');
    //console.log(login_status);return;
    layer.confirm('是否注销 "'+ nikename + '" 的登录' , {
        btn: ['注销','取消'] //按钮
    }, function(){
        if(login_status == -1){
            layer.msg('登录之后,才能退出')
        }else{
            $.post('/smartcloud/logout',{wxid},function(res){
                if(res['code']==200 ){
                    layer.msg('注销成功');
                    $(e).attr('login_status','-1');
                    window.location.reload();
                }else{
                    layer.msg('注销失败')
                }
            })
        }
    }, function(){
        window.location.reload();
    });
}
//创建群聊
function createGroup(){
    layer.msg('正在更新')
}
//添加好友
function addFriend(){
    layer.msg('正在更新')
}
//搜索好友
function serch(){
    var v = $('.serch_input').val();
    if(v == '' || v == null){
        layer.tips('请在此处输入内容', '#serch_input', {
            tips: [1, '#0FA6D8'] //还可配置颜色
        });
    }else{
        var list = GetQueryString("list")
        //console.log(list);return;
        if(list == '' || list == null){
            layer.msg('账号异常');
        }else{
            var wxid = $('.user_list_'+list).children('.user_list_wxid').attr('value');
            $.post('/smartcloud/serch',{wxid,v},function (res) {
                if(res['code'] == 200 ){
                    var data = res['data'];
                    //console.log(data);
                    var html = [];
                    for(var i=0;i<data.length;i++){
                        var str = "<div class=\"card mb-6\" onclick=\"get_friend_info(this)\">\n" +
                            "<div class=\"card-body\">\n" +
                            "<div class=\"media\">\n" +
                            "<div class=\"avatar avatar-online\">\n" +
                            "<img class=\"avatar-img\" src=\" "+ res['data'][i]['headimgurl'] +" \">\n" +
                            "</div>\n" +
                            "<input type=\"text\" name=\""+res['data'][i]['wxid']+"friend\" style=\"display: none\" disabled=\"\" class=\"user_wxid\" value=\""+res['data'][i]['wxid']+ "\"><div class=\"align-self-center\">\n" +
                            "<h5 class=\"mb-0 friend_name\" style=\"font-weight: bold;\">" +res['data'][i]['nickname']+"</h5>\n" +
                            "</div>\n" +
                            "</div>\n" +
                            "</div>\n" +
                            "</div>";
                        html.push(str);
                    }
                    //console.log(html);return;
                    $('.serch_list').html(html).show();
                }else{
                    layer.msg(res['msg']);
                }
            })
        }
    }
}

/*
* 朋友圈
* */
function WechatMoments(e){
    //判断用户是否登录
    var list = GetQueryString("list")
    if(list == '' || list == null){
        layer.msg('请先登录');
    }else{
        console.log(1);
        var wxid = $('.user_list_'+list).children('.user_list_wxid').attr('value');
        $('.RightWindow').attr('style','display:inline-block');
        $('.conversation').hide(500);
        $('.RightWindow').show(500);
    }
}
