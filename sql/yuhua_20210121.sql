/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80012
 Source Host           : localhost:3306
 Source Schema         : yuhua

 Target Server Type    : MySQL
 Target Server Version : 80012
 File Encoding         : 65001

 Date: 21/01/2021 08:54:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for yh_admin_area
-- ----------------------------
DROP TABLE IF EXISTS `yh_admin_area`;
CREATE TABLE `yh_admin_area`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(11) NULL DEFAULT NULL COMMENT '父id',
  `shortname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '简称',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `merger_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '全称',
  `level` tinyint(4) NULL DEFAULT NULL COMMENT '层级 0 1 2 省市区县',
  `pinyin` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '拼音',
  `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '长途区号',
  `zip_code` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮编',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '全国省市县区域表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_admin_globalcode
-- ----------------------------
DROP TABLE IF EXISTS `yh_admin_globalcode`;
CREATE TABLE `yh_admin_globalcode`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `two_code` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '二位代码',
  `china_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '中文名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 249 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '全球国家二字母代码表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `yh_admin_group`;
CREATE TABLE `yh_admin_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '角色名',
  `role` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '权限',
  `create_at` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '角色表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `yh_admin_menu`;
CREATE TABLE `yh_admin_menu`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `menu_id` int(11) NULL DEFAULT NULL COMMENT '编号',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '名称',
  `parent_id` int(11) NULL DEFAULT NULL COMMENT '父级ID',
  `iconfont` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '图表',
  `is_show` smallint(6) NULL DEFAULT NULL COMMENT '是否显示:0不显示,1显示',
  `level` int(11) NULL DEFAULT NULL COMMENT '等级:1:一级菜单,2:二级菜单',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `route` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '路由地址',
  `create_at` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 24 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '后台菜单表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `yh_admin_user`;
CREATE TABLE `yh_admin_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `salt` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '盐值',
  `truename` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '真实姓名',
  `sex` smallint(6) NULL DEFAULT 0 COMMENT '性别:0:未知,1:男,2:女',
  `age` int(11) NULL DEFAULT NULL COMMENT '年龄',
  `userphone` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '手机号码',
  `group` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '分组',
  `groupid` int(11) NULL DEFAULT NULL COMMENT '分组ID',
  `status` smallint(6) NULL DEFAULT NULL COMMENT '状态:1启用,2禁用',
  `create_at` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '后台用户表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_college_category
-- ----------------------------
DROP TABLE IF EXISTS `yh_college_category`;
CREATE TABLE `yh_college_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '栏目名称',
  `pid` int(11) NULL DEFAULT NULL COMMENT '父级ID',
  `level` smallint(6) NULL DEFAULT NULL COMMENT '等级',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '视频栏目' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_college_operation
-- ----------------------------
DROP TABLE IF EXISTS `yh_college_operation`;
CREATE TABLE `yh_college_operation`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' ',
  `userid` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `course_id` int(11) NULL DEFAULT NULL COMMENT '课程id',
  `course_type` int(11) NULL DEFAULT NULL COMMENT '课程类型:1 视频;2:文本',
  `type` smallint(6) NULL DEFAULT NULL COMMENT '类型:1点赞;2收藏;3评价;',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '评价内容',
  `create_at` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '视频相关操作表(点赞,收藏.评论)' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_college_rolecategroup
-- ----------------------------
DROP TABLE IF EXISTS `yh_college_rolecategroup`;
CREATE TABLE `yh_college_rolecategroup`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `categroup` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for yh_college_rotation
-- ----------------------------
DROP TABLE IF EXISTS `yh_college_rotation`;
CREATE TABLE `yh_college_rotation`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '轮播图名称',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '轮播图地址',
  `sort` smallint(6) NULL DEFAULT NULL COMMENT '排序',
  `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '跳转地址',
  `status` smallint(6) NULL DEFAULT NULL COMMENT '状态:1正常;2异常',
  `create_at` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '学院模块轮播图' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_college_video
-- ----------------------------
DROP TABLE IF EXISTS `yh_college_video`;
CREATE TABLE `yh_college_video`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '视频名称',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '视频地址',
  `course_text` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '文本内容',
  `video_time` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '视频时长',
  `video_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '视频封面照片',
  `total` int(11) NULL DEFAULT NULL COMMENT '播放次数',
  `fabulous` int(11) NULL DEFAULT NULL COMMENT '点赞数',
  `owner_id` int(11) NULL DEFAULT NULL COMMENT '所有者ID',
  `category_id` int(11) NULL DEFAULT NULL COMMENT '栏目ID',
  `file_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '附件名称',
  `file_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '附件地址',
  `status` smallint(6) NULL DEFAULT NULL COMMENT '状态: 1上架.2:下架',
  `uploader_at` int(11) NULL DEFAULT NULL COMMENT '上传时间',
  `type` smallint(6) NULL DEFAULT NULL COMMENT '类型:1视频类型,2文本类型,3单课件',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 41 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '学院课件表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_login_log
-- ----------------------------
DROP TABLE IF EXISTS `yh_login_log`;
CREATE TABLE `yh_login_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `username` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'IP',
  `login_at` int(11) NULL DEFAULT NULL COMMENT '登录时间',
  `status` smallint(6) NULL DEFAULT NULL COMMENT '状态:1 登录成功,2登录失败',
  `type` smallint(6) NULL DEFAULT NULL COMMENT '类型:1前台,2:后台',
  `loginmsg` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '结果消息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 187 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '登录日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_sc_chatassociation
-- ----------------------------
DROP TABLE IF EXISTS `yh_sc_chatassociation`;
CREATE TABLE `yh_sc_chatassociation`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ourid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '我方微信ID',
  `partyid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '对方微信ID',
  `is_delete` smallint(6) NULL DEFAULT 0 COMMENT '是否删除:0:未删除,1:删除',
  `status` smallint(6) NULL DEFAULT 0 COMMENT '状态:0已读,1未读',
  `time` int(11) NULL DEFAULT NULL COMMENT '聊天时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '聊天关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for yh_sc_chatrecord
-- ----------------------------
DROP TABLE IF EXISTS `yh_sc_chatrecord`;
CREATE TABLE `yh_sc_chatrecord`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '聊天内容',
  `img_base64` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '图片消息:图片base64, 表情消息:表情地址',
  `newmsgid` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信返回消息ID',
  `type` smallint(6) NULL DEFAULT NULL COMMENT '消息类型 1:文字; 3:图片;34:语音;43:视频;47:动画表情;49:分享;',
  `partyid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '对方微信ID',
  `ourid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '我方微信ID',
  `is_send` smallint(6) NULL DEFAULT NULL COMMENT '是否我方发送 1:我方发送; 2:对方发送;',
  `send_time` int(11) NULL DEFAULT NULL COMMENT '发送时间',
  `is_withdraw` smallint(6) NULL DEFAULT 0 COMMENT '是否是撤回消息 0否;1:是; ',
  `is_delete` smallint(6) NULL DEFAULT 0 COMMENT '是否删除(聊天记录不显示) 0:否; 1:是;',
  `sp_send` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '群聊消息发送者',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '聊天记录表\r\n' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_sc_contactinfo
-- ----------------------------
DROP TABLE IF EXISTS `yh_sc_contactinfo`;
CREATE TABLE `yh_sc_contactinfo`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信ID',
  `nickname` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信昵称',
  `headimgurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信头像',
  `alias` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信号',
  `country` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '国家',
  `province` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '省份',
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '城市',
  `sex` smallint(6) NULL DEFAULT NULL COMMENT '性别:1男,2女',
  `type` smallint(6) NULL DEFAULT NULL COMMENT '类型: 1个人; 2群',
  `py` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '昵称拼音',
  `update_at` int(11) NULL DEFAULT NULL COMMENT '最后一次更新数据时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信联系人信息' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_sc_friend
-- ----------------------------
DROP TABLE IF EXISTS `yh_sc_friend`;
CREATE TABLE `yh_sc_friend`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerwxid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '所有者微信ID',
  `friendwxid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '好友微信ID',
  `is_delete` smallint(6) NULL DEFAULT NULL COMMENT '是否删除:1是; 2:否;',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `remarkpy` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注拼音',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '微信好友关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_sc_user
-- ----------------------------
DROP TABLE IF EXISTS `yh_sc_user`;
CREATE TABLE `yh_sc_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '微信guid',
  `wxid` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '微信ID',
  `wxpassword` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '随机微信密码',
  `wx62data` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '62数据',
  `nickname` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信昵称',
  `headimgurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信头像',
  `alias` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信号',
  `country` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '国家代码',
  `province` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '省份',
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '城市',
  `sex` smallint(6) NULL DEFAULT NULL COMMENT '性别:1男,2女',
  `snsbgImg` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '朋友圈背景图片',
  `uuid` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '微信uuid',
  `status` smallint(6) NULL DEFAULT NULL COMMENT '状态: 1:正常,2:异常',
  `login_at` int(11) NULL DEFAULT NULL COMMENT '最近一次登录时间',
  `uid` int(11) NULL DEFAULT NULL COMMENT '关联表用户ID',
  `uname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '关联表用户名',
  `create_at` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '微信用户表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for yh_web_role
-- ----------------------------
DROP TABLE IF EXISTS `yh_web_role`;
CREATE TABLE `yh_web_role`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `status` smallint(6) NULL DEFAULT NULL,
  `create_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 43 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for yh_web_user
-- ----------------------------
DROP TABLE IF EXISTS `yh_web_user`;
CREATE TABLE `yh_web_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `salt` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '盐值',
  `truename` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '真实姓名',
  `sex` smallint(6) NULL DEFAULT NULL COMMENT '性别:1男,2女',
  `age` int(11) NULL DEFAULT NULL COMMENT '年龄',
  `userphone` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '手机号码',
  `group` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '分组',
  `groupid` int(11) NULL DEFAULT NULL COMMENT '分组ID',
  `status` smallint(6) NULL DEFAULT NULL COMMENT '状态:1启用,2禁用',
  `create_at` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '前台用户表' ROW_FORMAT = DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;
