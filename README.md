# yuhua

#### 介绍
羽化生物科技有限公司
#### 软件架构
- 后台 /admin
- 学院模块 /college
- 智能云模块 /smartcloud



#### 使用说明
- 数据表说明
        
        前台中间件判断是否为 OA羽化平台管理系统 跳转
        前台用户表,角色表调用 OA羽化平台管理系统 的数据表
        本地使用时请先配置 OA羽化平台管理系统
        本地开发时修改 app/common 中的地址
        以下功能调用接口
            前台登录验证,用户信息获取调用接口
            前台角色管理列表调用接口
        前台验证接口 /api/remoteshare/RemoteCheckLogin (参数: username password)
        获取角色接口 /api/remoteshare/RemoteGetList (后台查看前台角色列表才获取更新)
        获取指定用户信息 /api/remoteshare/RemoteGetUser (参数: id(用户ID))
        根据Token获取用户信息 api/remoteshare/RemoteGetToken (参数: token(OA系统获取))

- 学院模块
    - 视频上传采用webuploader分片上传,使用ffmpeg进行切片转M3U8类型(本地需安装ffmpeg)

- 智能云模块
    - 开启websocket同步新消息
        
            php think worker:server   端口号9512

- 数据库文件 /sql 中
    - yh_admin_area_20201221.sql 全国地区表
    - yh_admin_globalcode_20201221_sql 全球二字母简称表
    - yuhua.sql 所有数据表结构(无数据)
    
- 生成后台超级管理员(生成账号 : admin密码 : admin@123)
    
      php think createuser
      
- 后台菜单
    - 菜单配置 /app/command/LoadMenu.php 文件,增加菜单完成之后必须执行 加载菜单命令
    
          加载菜单  php think loadmenu
