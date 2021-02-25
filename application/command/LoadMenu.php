<?php
/**
 * 加载菜单表
 */
namespace app\command;

use app\admin\model\AdminMenuModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Session;

class LoadMenu extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('loadmenu')
            ->setDescription('Load menu');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        $menu = $this->menu();
        // 截断菜单表
        Db::query("truncate table yh_admin_menu");
        $output->writeln("菜单表已清空");
        // 开始循环写入
        $admin_menu = new AdminMenuModel();
        $data = [];
        foreach ($menu as $key => $val){
            if($key != "subclass"){
                $data[$key] = $val;
            }
        }
        $admin_menu->isUpdate(false)->save($data);
        $output->writeln("一级菜单添加成功");
        $admin_menus = new AdminMenuModel();
        $datas = [];
        foreach ($menu['subclass'] as $key => $val){
            foreach ($val as $k => $v){
                if($k != "subclass"){
                    $datas[$val['menu_id']][$k] = $v;
                } else {
                    foreach ($val['subclass'] as $value){
                        foreach ($value as $kk => $vv){
                            if($kk != 'subclass'){
                                $datas[$value['menu_id']][$kk] = $vv;
                            } else {
                                foreach ($vv as $vvv){
                                    foreach ($vvv as $kkkk => $vvvv){
                                        $datas[$vvv['menu_id']][$kkkk] = $vvvv;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $admin_menus->isUpdate(false)->saveAll($datas);
        $output->writeln("二级菜单添加成功");
        $output->writeln("三级菜单添加成功");
    }

    //后台导航配置
    public function menu()
    {
        $menu = [
            'menu_id' => 10000,   // 菜单ID
            'name' => '首页',      // 菜单名称
            'parent_id' => 0,     // 菜单父级ID
            'iconfont' => '',     // 图标  // 图标http://www.h-ui.net/Hui-3.7-Hui-iconfont.shtml
            'is_show' => 0,       // 是否显示:0不显示,1显示
            'level' => 1,         // 等级:1:一级菜单,2:二级菜单,3:三级菜单
            'sort' => 0,          // 排序
            'route' => '/admin',        // 路由地址
            'subclass' => [
                $this->user_menu(),
                $this->webuser_menu(),
                $this->college_menu(),
                $this->smartcloud_menu(),
                $this->system_menu(), // 系统设置放在最后
            ],
        ];
        return $menu;
    }
    // 管理员管理  11000
    public function user_menu()
    {
        $user = [
            'menu_id' => 11000,
            'name' => '管理员管理',
            'parent_id' => 0,
            'iconfont' => '&#xe62d;',
            'is_show' => 1,
            'level' => 1,
            'sort' => 1,
            'route' => '',
            'subclass' => [
                [
                    'menu_id' => 11100,
                    'name' => '管理员列表',
                    'parent_id' => 11000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 1,
                    'route' => '/admin/user',
                    'subclass' => [
                        [
                            'menu_id' => 11101,
                            'name' => '管理员添加',
                            'parent_id' => 11100,
                            'iconfont' => '',
                            'is_show' => 0,
                            'level' => 3,
                            'sort' => 1,
                            'route' => '/admin/user/create',
                        ],
                        [
                            'menu_id' => 11102,
                            'name' => '管理员修改',
                            'parent_id' => 11100,
                            'iconfont' => '',
                            'is_show' => 0,
                            'level' => 3,
                            'sort' => 2,
                            'route' => '/admin/user/edit',
                        ],
                    ]
                ],
                [
                    'menu_id' => 11200,
                    'name' => '角色管理',
                    'parent_id' => 11000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 2,
                    'route' => '/admin/group',
                ],
            ],
        ];
        return $user;
    }
    // 系统设置  12000
    public function system_menu()
    {
        $system = [
            'menu_id' => 12000,
            'name' => '系统设置',
            'parent_id' => 0,
            'iconfont' => '&#xe62e;',
            'is_show' => 1,
            'level' => 1,
            'sort' => 2,
            'route' => '',
            'subclass' => [
                [
                    'menu_id' => 12100,
                    'name' => '登录日志',
                    'parent_id' => 12000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 1,
                    'route' => '/admin/system/loginlog',
                    'subclass' => [

                    ]
                ],
            ],
        ];
        return $system;
    }
    // 前台用户  13000
    public function webuser_menu()
    {
        $system = [
            'menu_id' => 13000,
            'name' => '前台用户管理',
            'parent_id' => 0,
            'iconfont' => '&#xe62b;',
            'is_show' => 1,
            'level' => 1,
            'sort' => 3,
            'route' => '',
            'subclass' => [
                [
                    'menu_id' => 13100,
                    'name' => '用户列表',
                    'parent_id' => 13000,
                    'iconfont' => '',
                    'is_show' => 0,
                    'level' => 2,
                    'sort' => 1,
                    'route' => '/admin/webuser',
                    'subclass' => [
                        [
                            'menu_id' => 13101,
                            'name' => '用户添加',
                            'parent_id' => 13100,
                            'iconfont' => '',
                            'is_show' => 0,
                            'level' => 3,
                            'sort' => 1,
                            'route' => '/admin/webuser/create',
                        ],
                        [
                            'menu_id' => 13102,
                            'name' => '用户修改',
                            'parent_id' => 13100,
                            'iconfont' => '',
                            'is_show' => 0,
                            'level' => 3,
                            'sort' => 2,
                            'route' => '/admin/webuser/edit',
                        ],
                    ]
                ],
                [
                    'menu_id' => 13200,
                    'name' => '角色列表',
                    'parent_id' => 13000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 2,
                    'route' => '/admin/webrole',
                    'subclass' => [
                    ]
                ],
            ],
        ];
        return $system;
    }
    // 学院模块  14000
    public function college_menu()
    {
        $college = [
            'menu_id' => 14000,
            'name' => '学院管理',
            'parent_id' => 0,
            'iconfont' => '&#xe616;',
            'is_show' => 1,
            'level' => 1,
            'sort' => 4,
            'route' => '',
            'subclass' => [
                [
                    'menu_id' => 14100,
                    'name' => '课程列表',
                    'parent_id' => 14000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 1,
                    'route' => '/admin/college',
                    'subclass' => [

                    ]
                ],
                [
                    'menu_id' => 14200,
                    'name' => '栏目管理',
                    'parent_id' => 14000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 1,
                    'route' => '/admin/college/category',
                    'subclass' => [
                        [
                            'menu_id' => 14201,
                            'name' => '栏目添加',
                            'parent_id' => 14200,
                            'iconfont' => '',
                            'is_show' => 0,
                            'level' => 3,
                            'sort' => 1,
                            'route' => '/admin/college/category_create',
                        ],
                        [
                            'menu_id' => 14202,
                            'name' => '栏目修改',
                            'parent_id' => 14200,
                            'iconfont' => '',
                            'is_show' => 0,
                            'level' => 3,
                            'sort' => 2,
                            'route' => '/admin/college/category_edit',
                        ],
                    ]
                ],
                [
                    'menu_id' => 14300,
                    'name' => '轮播图列表',
                    'parent_id' => 14000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 3,
                    'route' => '/admin/college/rotation',
                    'subclass' => [

                    ]
                ],
            ]
        ];
        return $college;
    }
    // 智能云模块  15000
    public function smartcloud_menu()
    {
        $college = [
            'menu_id' => 15000,
            'name' => '智能云管理',
            'parent_id' => 0,
            'iconfont' => '&#xe694;',
            'is_show' => 1,
            'level' => 1,
            'sort' => 5,
            'route' => '',
            'subclass' => [
                [
                    'menu_id' => 15100,
                    'name' => '微信列表',
                    'parent_id' => 15000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 1,
                    'route' => '/admin/smartcloud',
                    'subclass' => [

                    ]
                ],
                [
                    'menu_id' => 15200,
                    'name' => '联系人列表',
                    'parent_id' => 15000,
                    'iconfont' => '',
                    'is_show' => 1,
                    'level' => 2,
                    'sort' => 2,
                    'route' => '/admin/smartcloud/friendlist',
                    'subclass' => [
                        [
                            'menu_id' => 15201,
                            'name' => '聊天记录',
                            'parent_id' => 15200,
                            'iconfont' => '',
                            'is_show' => 0,
                            'level' => 3,
                            'sort' => 1,
                            'route' => '/admin/smartcloud/friendchatrecord',
                            'subclass' => [

                            ]
                        ],
                    ]
                ],
            ]
        ];
        return $college;
    }
}
