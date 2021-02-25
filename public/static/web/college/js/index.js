// 禁止右键点击
$('#video').bind('contextmenu',function() { return false; });
// 首页获取视频列表
function getvideo(page,type,categroup){
    if(page == undefined){
        page = 1;
    }
    $.post("/college/getvideo",{'page':page,'type':type,'categroup':categroup},function (result) {
        if(result['code'] == 200){
            var data = result['data']['data'];
            if(data != ''){
                $("#no_content").hide();
                // 课程列表
                html = '';
                for (var i=0; i<data.length; i++){
                    if (data[i]['video_img'] == '' || data[i]['video_img'] === null){
                        data[i]['video_img'] = '/static/web/college/images/fm_img.png';
                    }
                    if (data[i]['type'] == 1){
                        var video_type = 'ico_play';
                    } else {
                        data[i]['video_time'] = 0;
                        var video_type = 'ico_read';
                    }
                    html += "<div class='main' title='"+data[i]['name']+"'>\n" +
                        "        <a href='/college/read?id="+data[i]['id']+"'><div class='img_class' style='background-image: url(\""+data[i]['video_img']+"\");background-size: cover;-o-background-size: cover;background-position: center 0;'>\n" +
                        "            <div class='play "+video_type+"'></div>\n" +
                        "        </div></a>\n" +
                        "        <div class='video_name' style='margin: 10px;'>"+data[i]['name']+"</div>\n" +
                        "        <div style='margin: 0 40px 0px 40px'>\n" +
                        "            <span style='float: left'>"+data[i]['total']+" 人学过</span>\n" +
                        "            <span style='float: right'>"+data[i]['video_time']+" min</span>\n" +
                        "        </div>\n" +
                        "    </div>"
                }
                // 分页
                page_html = '';
                var last_page = result['data']['last_page'];
                // 当前为第一页
                if(page == 1){
                    page_html += "<div id='page-normal'>\n" +
                        "<a class='no_click' href='javascript:void(0)'>上一页<i></i></a>\n"
                } else {
                    page_html += "<div id='page-normal'>\n" +
                        "<a id='page-prev' href='javascript:void(0)' onclick=\"getvideo("+(page-1)+",'"+type+"',"+categroup+")\">上一页<i></i></a>\n"
                }
                // 总页数大于8页
                if(last_page > 8){
                    for (var ii=1; ii<=5; ii++){
                        if(page == ii){
                            page_html += "<a href='javascript:void(0)' id=\"page-current\" class='no_click'>"+ii+"</a>\n"
                        } else {
                            page_html += "<a href='javascript:void(0)' onclick=\"getvideo("+ii+",'"+type+"',"+categroup+")\">"+ii+"</a>\n"
                        }
                    }
                    if(page>5 && page<last_page-1){
                        page_html += "<a href='javascript:void(0)'>...</a>\n"
                        page_html += "<a href='javascript:void(0)' id=\"page-current\" class='no_click'>"+page+"</a>\n"
                    }
                    page_html += "<a href='javascript:void(0)'>...</a>\n"
                    if(page == (last_page-1)){
                        page_html += "<a href='javascript:void(0)' id=\"page-current\" class='no_click'>"+(last_page-1)+"</a>\n"
                    } else {
                        page_html += "<a href='javascript:void(0)' onclick=\"getvideo("+(last_page-1)+",'"+type+"',"+categroup+")\">"+(last_page-1)+"</a>\n"
                    }
                    if(page == last_page){
                        page_html += "<a href='javascript:void(0)' id=\"page-current\" class='no_click'>"+last_page+"</a>\n"
                    } else {
                        page_html += "<a href='javascript:void(0)' onclick=\"getvideo("+last_page+",'"+type+"',"+categroup+")\">"+last_page+"</a>\n"
                    }
                } else {
                    for (var ii=1; ii<=last_page; ii++){
                        if(page == ii){
                            page_html += "<a href='javascript:void(0)' id=\"page-current\" class='no_click'>"+ii+"</a>\n"
                        } else {
                            page_html += "<a href='javascript:void(0)' onclick=\"getvideo("+ii+",'"+type+"',"+categroup+")\">"+ii+"</a>\n"
                        }
                    }
                }
                // 当前为最后一页
                if(page == last_page){
                    page_html  +=    "<a class='no_click' href='javascript:void(0)'>下一页<i></i></a>\n" +
                        "</div>"
                } else {
                    page_html  +=    "<a id='page-next' href='javascript:void(0)' onclick=\"getvideo("+(page+1)+",'"+type+"',"+categroup+")\">下一页<i></i></a>\n" +
                        "</div>"
                }
                $("#video_model").html(html+page_html);
            } else {
                $("#video_model").html("")
                $("#no_content").show();
            }
        }
    });
}
