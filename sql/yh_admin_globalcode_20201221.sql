/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : yuhua

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 21/12/2020 14:24:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
-- Records of yh_admin_globalcode
-- ----------------------------
INSERT INTO `yh_admin_globalcode` VALUES (1, 'AD', '安道尔');
INSERT INTO `yh_admin_globalcode` VALUES (2, 'AE', '阿联酋');
INSERT INTO `yh_admin_globalcode` VALUES (3, 'AF', '阿富汗');
INSERT INTO `yh_admin_globalcode` VALUES (4, 'AG', '安提瓜和巴布达');
INSERT INTO `yh_admin_globalcode` VALUES (5, 'AI', '安圭拉');
INSERT INTO `yh_admin_globalcode` VALUES (6, 'AL', '阿尔巴尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (7, 'AM', '亚美尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (8, 'AO', '安哥拉');
INSERT INTO `yh_admin_globalcode` VALUES (9, 'AQ', '南极洲');
INSERT INTO `yh_admin_globalcode` VALUES (10, 'AR', '阿根廷');
INSERT INTO `yh_admin_globalcode` VALUES (11, 'AS', '美属萨摩亚');
INSERT INTO `yh_admin_globalcode` VALUES (12, 'AT', '奥地利');
INSERT INTO `yh_admin_globalcode` VALUES (13, 'AU', '澳大利亚');
INSERT INTO `yh_admin_globalcode` VALUES (14, 'AW', '阿鲁巴');
INSERT INTO `yh_admin_globalcode` VALUES (15, 'AX', '奥兰群岛');
INSERT INTO `yh_admin_globalcode` VALUES (16, 'AZ', '阿塞拜疆');
INSERT INTO `yh_admin_globalcode` VALUES (17, 'BA', '波黑');
INSERT INTO `yh_admin_globalcode` VALUES (18, 'BB', '巴巴多斯');
INSERT INTO `yh_admin_globalcode` VALUES (19, 'BD', '孟加拉');
INSERT INTO `yh_admin_globalcode` VALUES (20, 'BE', '比利时');
INSERT INTO `yh_admin_globalcode` VALUES (21, 'BF', '布基纳法索');
INSERT INTO `yh_admin_globalcode` VALUES (22, 'BG', '保加利亚');
INSERT INTO `yh_admin_globalcode` VALUES (23, 'BH', '巴林');
INSERT INTO `yh_admin_globalcode` VALUES (24, 'BI', '布隆迪');
INSERT INTO `yh_admin_globalcode` VALUES (25, 'BJ', '贝宁');
INSERT INTO `yh_admin_globalcode` VALUES (26, 'BL', '圣巴泰勒米岛');
INSERT INTO `yh_admin_globalcode` VALUES (27, 'BM', '百慕大');
INSERT INTO `yh_admin_globalcode` VALUES (28, 'BN', '文莱');
INSERT INTO `yh_admin_globalcode` VALUES (29, 'BO', '玻利维亚');
INSERT INTO `yh_admin_globalcode` VALUES (30, 'BQ', '荷兰加勒比区');
INSERT INTO `yh_admin_globalcode` VALUES (31, 'BR', '巴西');
INSERT INTO `yh_admin_globalcode` VALUES (32, 'BS', '巴哈马');
INSERT INTO `yh_admin_globalcode` VALUES (33, 'BT', '不丹');
INSERT INTO `yh_admin_globalcode` VALUES (34, 'BV', '布韦岛');
INSERT INTO `yh_admin_globalcode` VALUES (35, 'BW', '博茨瓦纳');
INSERT INTO `yh_admin_globalcode` VALUES (36, 'BY', '白俄罗斯');
INSERT INTO `yh_admin_globalcode` VALUES (37, 'BZ', '伯利兹');
INSERT INTO `yh_admin_globalcode` VALUES (38, 'CA', '加拿大');
INSERT INTO `yh_admin_globalcode` VALUES (39, 'CC', '科科斯群岛');
INSERT INTO `yh_admin_globalcode` VALUES (40, 'CF', '中非');
INSERT INTO `yh_admin_globalcode` VALUES (41, 'CH', '瑞士');
INSERT INTO `yh_admin_globalcode` VALUES (42, 'CL', '智利');
INSERT INTO `yh_admin_globalcode` VALUES (43, 'CM', '喀麦隆');
INSERT INTO `yh_admin_globalcode` VALUES (44, 'CO', '哥伦比亚');
INSERT INTO `yh_admin_globalcode` VALUES (45, 'CR', '哥斯达黎加');
INSERT INTO `yh_admin_globalcode` VALUES (46, 'CU', '古巴');
INSERT INTO `yh_admin_globalcode` VALUES (47, 'CV', '佛得角');
INSERT INTO `yh_admin_globalcode` VALUES (48, 'CX', '圣诞岛');
INSERT INTO `yh_admin_globalcode` VALUES (49, 'CY', '塞浦路斯');
INSERT INTO `yh_admin_globalcode` VALUES (50, 'CZ', '捷克');
INSERT INTO `yh_admin_globalcode` VALUES (51, 'DE', '德国');
INSERT INTO `yh_admin_globalcode` VALUES (52, 'DJ', '吉布提');
INSERT INTO `yh_admin_globalcode` VALUES (53, 'DK', '丹麦');
INSERT INTO `yh_admin_globalcode` VALUES (54, 'DM', '多米尼克');
INSERT INTO `yh_admin_globalcode` VALUES (55, 'DO', '多米尼加');
INSERT INTO `yh_admin_globalcode` VALUES (56, 'DZ', '阿尔及利亚');
INSERT INTO `yh_admin_globalcode` VALUES (57, 'EC', '厄瓜多尔');
INSERT INTO `yh_admin_globalcode` VALUES (58, 'EE', '爱沙尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (59, 'EG', '埃及');
INSERT INTO `yh_admin_globalcode` VALUES (60, 'EH', '西撒哈拉');
INSERT INTO `yh_admin_globalcode` VALUES (61, 'ER', '厄立特里亚');
INSERT INTO `yh_admin_globalcode` VALUES (62, 'ES', '西班牙');
INSERT INTO `yh_admin_globalcode` VALUES (63, 'FI', '芬兰');
INSERT INTO `yh_admin_globalcode` VALUES (64, 'FJ', '斐济群岛');
INSERT INTO `yh_admin_globalcode` VALUES (65, 'FK', '马尔维纳斯群岛（ 福克兰）');
INSERT INTO `yh_admin_globalcode` VALUES (66, 'FM', '密克罗尼西亚联邦');
INSERT INTO `yh_admin_globalcode` VALUES (67, 'FO', '法罗群岛');
INSERT INTO `yh_admin_globalcode` VALUES (68, 'FR', '法国');
INSERT INTO `yh_admin_globalcode` VALUES (69, 'GA', '加蓬');
INSERT INTO `yh_admin_globalcode` VALUES (70, 'GD', '格林纳达');
INSERT INTO `yh_admin_globalcode` VALUES (71, 'GE', '格鲁吉亚');
INSERT INTO `yh_admin_globalcode` VALUES (72, 'GF', '法属圭亚那');
INSERT INTO `yh_admin_globalcode` VALUES (73, 'GH', '加纳');
INSERT INTO `yh_admin_globalcode` VALUES (74, 'GI', '直布罗陀');
INSERT INTO `yh_admin_globalcode` VALUES (75, 'GL', '格陵兰');
INSERT INTO `yh_admin_globalcode` VALUES (76, 'GN', '几内亚');
INSERT INTO `yh_admin_globalcode` VALUES (77, 'GP', '瓜德罗普');
INSERT INTO `yh_admin_globalcode` VALUES (78, 'GQ', '赤道几内亚');
INSERT INTO `yh_admin_globalcode` VALUES (79, 'GR', '希腊');
INSERT INTO `yh_admin_globalcode` VALUES (80, 'GS', '南乔治亚岛和南桑威奇群岛');
INSERT INTO `yh_admin_globalcode` VALUES (81, 'GT', '危地马拉');
INSERT INTO `yh_admin_globalcode` VALUES (82, 'GU', '关岛');
INSERT INTO `yh_admin_globalcode` VALUES (83, 'GW', '几内亚比绍');
INSERT INTO `yh_admin_globalcode` VALUES (84, 'GY', '圭亚那');
INSERT INTO `yh_admin_globalcode` VALUES (85, 'HK', '香港');
INSERT INTO `yh_admin_globalcode` VALUES (86, 'HM', '赫德岛和麦克唐纳群岛');
INSERT INTO `yh_admin_globalcode` VALUES (87, 'HN', '洪都拉斯');
INSERT INTO `yh_admin_globalcode` VALUES (88, 'HR', '克罗地亚');
INSERT INTO `yh_admin_globalcode` VALUES (89, 'HT', '海地');
INSERT INTO `yh_admin_globalcode` VALUES (90, 'HU', '匈牙利');
INSERT INTO `yh_admin_globalcode` VALUES (91, 'ID', '印尼');
INSERT INTO `yh_admin_globalcode` VALUES (92, 'IE', '爱尔兰');
INSERT INTO `yh_admin_globalcode` VALUES (93, 'IL', '以色列');
INSERT INTO `yh_admin_globalcode` VALUES (94, 'IM', '马恩岛');
INSERT INTO `yh_admin_globalcode` VALUES (95, 'IN', '印度');
INSERT INTO `yh_admin_globalcode` VALUES (96, 'IO', '英属印度洋领地');
INSERT INTO `yh_admin_globalcode` VALUES (97, 'IQ', '伊拉克');
INSERT INTO `yh_admin_globalcode` VALUES (98, 'IR', '伊朗');
INSERT INTO `yh_admin_globalcode` VALUES (99, 'IS', '冰岛');
INSERT INTO `yh_admin_globalcode` VALUES (100, 'IT', '意大利');
INSERT INTO `yh_admin_globalcode` VALUES (101, 'JE', '泽西岛');
INSERT INTO `yh_admin_globalcode` VALUES (102, 'JM', '牙买加');
INSERT INTO `yh_admin_globalcode` VALUES (103, 'JO', '约旦');
INSERT INTO `yh_admin_globalcode` VALUES (104, 'JP', '日本');
INSERT INTO `yh_admin_globalcode` VALUES (105, 'KH', '柬埔寨');
INSERT INTO `yh_admin_globalcode` VALUES (106, 'KI', '基里巴斯');
INSERT INTO `yh_admin_globalcode` VALUES (107, 'KM', '科摩罗');
INSERT INTO `yh_admin_globalcode` VALUES (108, 'KW', '科威特');
INSERT INTO `yh_admin_globalcode` VALUES (109, 'KY', '开曼群岛');
INSERT INTO `yh_admin_globalcode` VALUES (110, 'LB', '黎巴嫩');
INSERT INTO `yh_admin_globalcode` VALUES (111, 'LI', '列支敦士登');
INSERT INTO `yh_admin_globalcode` VALUES (112, 'LK', '斯里兰卡');
INSERT INTO `yh_admin_globalcode` VALUES (113, 'LR', '利比里亚');
INSERT INTO `yh_admin_globalcode` VALUES (114, 'LS', '莱索托');
INSERT INTO `yh_admin_globalcode` VALUES (115, 'LT', '立陶宛');
INSERT INTO `yh_admin_globalcode` VALUES (116, 'LU', '卢森堡');
INSERT INTO `yh_admin_globalcode` VALUES (117, 'LV', '拉脱维亚');
INSERT INTO `yh_admin_globalcode` VALUES (118, 'LY', '利比亚');
INSERT INTO `yh_admin_globalcode` VALUES (119, 'MA', '摩洛哥');
INSERT INTO `yh_admin_globalcode` VALUES (120, 'MC', '摩纳哥');
INSERT INTO `yh_admin_globalcode` VALUES (121, 'MD', '摩尔多瓦');
INSERT INTO `yh_admin_globalcode` VALUES (122, 'ME', '黑山');
INSERT INTO `yh_admin_globalcode` VALUES (123, 'MF', '法属圣马丁');
INSERT INTO `yh_admin_globalcode` VALUES (124, 'MG', '马达加斯加');
INSERT INTO `yh_admin_globalcode` VALUES (125, 'MH', '马绍尔群岛');
INSERT INTO `yh_admin_globalcode` VALUES (126, 'MK', '马其顿');
INSERT INTO `yh_admin_globalcode` VALUES (127, 'ML', '马里');
INSERT INTO `yh_admin_globalcode` VALUES (128, 'MM', '缅甸');
INSERT INTO `yh_admin_globalcode` VALUES (129, 'MO', '澳门');
INSERT INTO `yh_admin_globalcode` VALUES (130, 'MQ', '马提尼克');
INSERT INTO `yh_admin_globalcode` VALUES (131, 'MR', '毛里塔尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (132, 'MS', '蒙塞拉特岛');
INSERT INTO `yh_admin_globalcode` VALUES (133, 'MT', '马耳他');
INSERT INTO `yh_admin_globalcode` VALUES (134, 'MV', '马尔代夫');
INSERT INTO `yh_admin_globalcode` VALUES (135, 'MW', '马拉维');
INSERT INTO `yh_admin_globalcode` VALUES (136, 'MX', '墨西哥');
INSERT INTO `yh_admin_globalcode` VALUES (137, 'MY', '马来西亚');
INSERT INTO `yh_admin_globalcode` VALUES (138, 'NA', '纳米比亚');
INSERT INTO `yh_admin_globalcode` VALUES (139, 'NE', '尼日尔');
INSERT INTO `yh_admin_globalcode` VALUES (140, 'NF', '诺福克岛');
INSERT INTO `yh_admin_globalcode` VALUES (141, 'NG', '尼日利亚');
INSERT INTO `yh_admin_globalcode` VALUES (142, 'NI', '尼加拉瓜');
INSERT INTO `yh_admin_globalcode` VALUES (143, 'NL', '荷兰');
INSERT INTO `yh_admin_globalcode` VALUES (144, 'NO', '挪威');
INSERT INTO `yh_admin_globalcode` VALUES (145, 'NP', '尼泊尔');
INSERT INTO `yh_admin_globalcode` VALUES (146, 'NR', '瑙鲁');
INSERT INTO `yh_admin_globalcode` VALUES (147, 'OM', '阿曼');
INSERT INTO `yh_admin_globalcode` VALUES (148, 'PA', '巴拿马');
INSERT INTO `yh_admin_globalcode` VALUES (149, 'PE', '秘鲁');
INSERT INTO `yh_admin_globalcode` VALUES (150, 'PF', '法属波利尼西亚');
INSERT INTO `yh_admin_globalcode` VALUES (151, 'PG', '巴布亚新几内亚');
INSERT INTO `yh_admin_globalcode` VALUES (152, 'PH', '菲律宾');
INSERT INTO `yh_admin_globalcode` VALUES (153, 'PK', '巴基斯坦');
INSERT INTO `yh_admin_globalcode` VALUES (154, 'PL', '波兰');
INSERT INTO `yh_admin_globalcode` VALUES (155, 'PN', '皮特凯恩群岛');
INSERT INTO `yh_admin_globalcode` VALUES (156, 'PR', '波多黎各');
INSERT INTO `yh_admin_globalcode` VALUES (157, 'PS', '巴勒斯坦');
INSERT INTO `yh_admin_globalcode` VALUES (158, 'PW', '帕劳');
INSERT INTO `yh_admin_globalcode` VALUES (159, 'PY', '巴拉圭');
INSERT INTO `yh_admin_globalcode` VALUES (160, 'QA', '卡塔尔');
INSERT INTO `yh_admin_globalcode` VALUES (161, 'RE', '留尼汪');
INSERT INTO `yh_admin_globalcode` VALUES (162, 'RO', '罗马尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (163, 'RS', '塞尔维亚');
INSERT INTO `yh_admin_globalcode` VALUES (164, 'RU', '俄罗斯');
INSERT INTO `yh_admin_globalcode` VALUES (165, 'RW', '卢旺达');
INSERT INTO `yh_admin_globalcode` VALUES (166, 'SB', '所罗门群岛');
INSERT INTO `yh_admin_globalcode` VALUES (167, 'SC', '塞舌尔');
INSERT INTO `yh_admin_globalcode` VALUES (168, 'SD', '苏丹');
INSERT INTO `yh_admin_globalcode` VALUES (169, 'SE', '瑞典');
INSERT INTO `yh_admin_globalcode` VALUES (170, 'SG', '新加坡');
INSERT INTO `yh_admin_globalcode` VALUES (171, 'SI', '斯洛文尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (172, 'SJ', '斯瓦尔巴群岛和 扬马延岛');
INSERT INTO `yh_admin_globalcode` VALUES (173, 'SK', '斯洛伐克');
INSERT INTO `yh_admin_globalcode` VALUES (174, 'SL', '塞拉利昂');
INSERT INTO `yh_admin_globalcode` VALUES (175, 'SM', '圣马力诺');
INSERT INTO `yh_admin_globalcode` VALUES (176, 'SN', '塞内加尔');
INSERT INTO `yh_admin_globalcode` VALUES (177, 'SO', '索马里');
INSERT INTO `yh_admin_globalcode` VALUES (178, 'SR', '苏里南');
INSERT INTO `yh_admin_globalcode` VALUES (179, 'SS', '南苏丹');
INSERT INTO `yh_admin_globalcode` VALUES (180, 'ST', '圣多美和普林西比');
INSERT INTO `yh_admin_globalcode` VALUES (181, 'SV', '萨尔瓦多');
INSERT INTO `yh_admin_globalcode` VALUES (182, 'SY', '叙利亚');
INSERT INTO `yh_admin_globalcode` VALUES (183, 'SZ', '斯威士兰');
INSERT INTO `yh_admin_globalcode` VALUES (184, 'TC', '特克斯和凯科斯群岛');
INSERT INTO `yh_admin_globalcode` VALUES (185, 'TD', '乍得');
INSERT INTO `yh_admin_globalcode` VALUES (186, 'TG', '多哥');
INSERT INTO `yh_admin_globalcode` VALUES (187, 'TH', '泰国');
INSERT INTO `yh_admin_globalcode` VALUES (188, 'TK', '托克劳');
INSERT INTO `yh_admin_globalcode` VALUES (189, 'TL', '东帝汶');
INSERT INTO `yh_admin_globalcode` VALUES (190, 'TN', '突尼斯');
INSERT INTO `yh_admin_globalcode` VALUES (191, 'TO', '汤加');
INSERT INTO `yh_admin_globalcode` VALUES (192, 'TR', '土耳其');
INSERT INTO `yh_admin_globalcode` VALUES (193, 'TV', '图瓦卢');
INSERT INTO `yh_admin_globalcode` VALUES (194, 'TZ', '坦桑尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (195, 'UA', '乌克兰');
INSERT INTO `yh_admin_globalcode` VALUES (196, 'UG', '乌干达');
INSERT INTO `yh_admin_globalcode` VALUES (197, 'US', '美国');
INSERT INTO `yh_admin_globalcode` VALUES (198, 'UY', '乌拉圭');
INSERT INTO `yh_admin_globalcode` VALUES (199, 'VA', '梵蒂冈');
INSERT INTO `yh_admin_globalcode` VALUES (200, 'VE', '委内瑞拉');
INSERT INTO `yh_admin_globalcode` VALUES (201, 'VG', '英属维尔京群岛');
INSERT INTO `yh_admin_globalcode` VALUES (202, 'VI', '美属维尔京群岛');
INSERT INTO `yh_admin_globalcode` VALUES (203, 'VN', '越南');
INSERT INTO `yh_admin_globalcode` VALUES (204, 'WF', '瓦利斯和富图纳');
INSERT INTO `yh_admin_globalcode` VALUES (205, 'WS', '萨摩亚');
INSERT INTO `yh_admin_globalcode` VALUES (206, 'YE', '也门');
INSERT INTO `yh_admin_globalcode` VALUES (207, 'YT', '马约特');
INSERT INTO `yh_admin_globalcode` VALUES (208, 'ZA', '南非');
INSERT INTO `yh_admin_globalcode` VALUES (209, 'ZM', '赞比亚');
INSERT INTO `yh_admin_globalcode` VALUES (210, 'ZW', '津巴布韦');
INSERT INTO `yh_admin_globalcode` VALUES (211, 'CN', '中国');
INSERT INTO `yh_admin_globalcode` VALUES (212, 'CG', '刚果（布）');
INSERT INTO `yh_admin_globalcode` VALUES (213, 'CD', '刚果（金）');
INSERT INTO `yh_admin_globalcode` VALUES (214, 'MZ', '莫桑比克');
INSERT INTO `yh_admin_globalcode` VALUES (215, 'GG', '根西岛');
INSERT INTO `yh_admin_globalcode` VALUES (216, 'GM', '冈比亚');
INSERT INTO `yh_admin_globalcode` VALUES (217, 'MP', '北马里亚纳群岛');
INSERT INTO `yh_admin_globalcode` VALUES (218, 'ET', '埃塞俄比亚');
INSERT INTO `yh_admin_globalcode` VALUES (219, 'NC', '新喀里多尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (220, 'VU', '瓦努阿图');
INSERT INTO `yh_admin_globalcode` VALUES (221, 'TF', '法属南部领地');
INSERT INTO `yh_admin_globalcode` VALUES (222, 'NU', '纽埃');
INSERT INTO `yh_admin_globalcode` VALUES (223, 'UM', '美国本土外小岛屿');
INSERT INTO `yh_admin_globalcode` VALUES (224, 'CK', '库克群岛');
INSERT INTO `yh_admin_globalcode` VALUES (225, 'GB', '英国');
INSERT INTO `yh_admin_globalcode` VALUES (226, 'TT', '特立尼达和多巴哥');
INSERT INTO `yh_admin_globalcode` VALUES (227, 'VC', '圣文森特和格林纳丁斯');
INSERT INTO `yh_admin_globalcode` VALUES (228, 'TW', '台湾地区');
INSERT INTO `yh_admin_globalcode` VALUES (229, NULL, '台湾省');
INSERT INTO `yh_admin_globalcode` VALUES (230, 'NZ', '新西兰');
INSERT INTO `yh_admin_globalcode` VALUES (231, 'SA', '沙特阿拉伯');
INSERT INTO `yh_admin_globalcode` VALUES (232, 'LA', '老挝');
INSERT INTO `yh_admin_globalcode` VALUES (233, 'KP', '朝鲜 北朝鲜');
INSERT INTO `yh_admin_globalcode` VALUES (234, 'KR', '韩国 南朝鲜');
INSERT INTO `yh_admin_globalcode` VALUES (235, 'PT', '葡萄牙');
INSERT INTO `yh_admin_globalcode` VALUES (236, 'KG', '吉尔吉斯斯坦');
INSERT INTO `yh_admin_globalcode` VALUES (237, 'KZ', '哈萨克斯坦');
INSERT INTO `yh_admin_globalcode` VALUES (238, 'TJ', '塔吉克斯坦');
INSERT INTO `yh_admin_globalcode` VALUES (239, 'TM', '土库曼斯坦');
INSERT INTO `yh_admin_globalcode` VALUES (240, 'UZ', '乌兹别克斯坦');
INSERT INTO `yh_admin_globalcode` VALUES (241, 'KN', '圣基茨和尼维斯');
INSERT INTO `yh_admin_globalcode` VALUES (242, 'PM', '圣皮埃尔和密克隆');
INSERT INTO `yh_admin_globalcode` VALUES (243, 'SH', '圣赫勒拿');
INSERT INTO `yh_admin_globalcode` VALUES (244, 'LC', '圣卢西亚');
INSERT INTO `yh_admin_globalcode` VALUES (245, 'MU', '毛里求斯');
INSERT INTO `yh_admin_globalcode` VALUES (246, 'CI', '科特迪瓦');
INSERT INTO `yh_admin_globalcode` VALUES (247, 'KE', '肯尼亚');
INSERT INTO `yh_admin_globalcode` VALUES (248, 'MN', '蒙古国 蒙古');

SET FOREIGN_KEY_CHECKS = 1;
