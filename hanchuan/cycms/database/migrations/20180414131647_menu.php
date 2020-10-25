<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Menu extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $table = $this->table('menu', array('engine'=>'InnoDB'));
        $table->addColumn('pid', 'integer', array('signed'=>false,'limit' => 11,'default'=>0,'comment'=>'父级ID'))
            ->addColumn('url', 'string', array('limit' => 255,'default'=>'','comment'=>'链接'))
            ->addColumn('title', 'string', array('limit' => 255,'default'=>'','comment'=>'名称'))
            ->addColumn('icon', 'string', array('limit' => 255,'default'=>'','comment'=>'标图'))
            ->addColumn('tips', 'string', array('limit' => 255,'default'=>'','comment'=>'提示语'))
            ->addColumn('status', 'boolean', array('limit' => 1,'default'=>1,'comment'=>'0隐藏，1显示'))
            ->addColumn('o', 'integer', array('limit' => 11,'default'=>0,'comment'=>'排序，越小越靠前'))
            ->addIndex(array('pid','o'))
            ->create();

        $rows = array(
                        array(
                            'id'=>1,
                            'pid'=>0,
                            'url'=>'index/index',
                            'title'=>'控制台',
                            'icon'=>'menu-icon fa fa-tachometer',
                            'tips'=>'经常查看操作日志，发现异常以便及时追查原因。',
                            'status'=>1,
                            'o'=>1,
                        ),
                        array(
                            'id'=>2,
                            'pid'=>0,
                            'url'=>'#',
                            'title'=>'开发选项',
                            'icon'=>'menu-icon fa fa-cogs',
                            'tips'=>'',
                            'status'=>1,
                            'o'=>2,
                        ),
                        array(
                            'id'=>3,
                            'pid'=>2,
                            'url'=>'menu/index',
                            'title'=>'后台菜单',
                            'icon'=>'menu-icon fa  fa-folder-o',
                            'tips'=>'开发新功能，新增、修改、删除后台菜单。',
                            'status'=>1,
                            'o'=>3,
                        ),
                        array(
                            'id'=>4,
                            'pid'=>2,
                            'url'=>'variable/index',
                            'title'=>'自定义变量',
                            'icon'=>'menu-icon fa  fa-circle-o',
                            'tips'=>'可新增、修改、删除后台自定义变量，方便后台模板直接调用。',
                            'status'=>1,
                            'o'=>4,
                        ),
                        array(
                            'id'=>5,
                            'pid'=>0,
                            'url'=>'#',
                            'title'=>'系统设置',
                            'icon'=>'menu-icon fa fa-cog',
                            'tips'=>'',
                            'status'=>1,
                            'o'=>5,
                        ),
                        array(
                            'id'=>6,
                            'pid'=>5,
                            'url'=>'setting/index',
                            'title'=>'网站设置',
                            'icon'=>'menu-icon fa  fa-info-circle',
                            'tips'=>'网站设置，含自定义变量值的设置。',
                            'status'=>1,
                            'o'=>6,
                        ),
                        array(
                            'id'=>7,
                            'pid'=>5,
                            'url'=>'database/backup',
                            'title'=>'数据库备份',
                            'icon'=>'menu-icon fa fa-floppy-o',
                            'tips'=>'建议定期备份网站数据库，以便网站故障时能及时恢复数据。',
                            'status'=>1,
                            'o'=>7,
                        ),
                        array(
                            'id'=>8,
                            'pid'=>5,
                            'url'=>'database/recovery',
                            'title'=>'数据还原',
                            'icon'=>'menu-icon fa fa-undo',
                            'tips'=>'将备份在数据库文件还原致数据库。',
                            'status'=>1,
                            'o'=>8,
                        ),
                        array(
                            'id'=>9,
                            'pid'=>5,
                            'url'=>'database/optimize',
                            'title'=>'数据优化',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>9,
                        ),
                        array(
                            'id'=>10,
                            'pid'=>5,
                            'url'=>'search/index',
                            'title'=>'功能搜索',
                            'icon'=>'',
                            'tips'=>'找不到后台功能搜索一下就能找到。',
                            'status'=>0,
                            'o'=>10,
                        ),
                        array(
                            'id'=>11,
                            'pid'=>5,
                            'url'=>'database/repair',
                            'title'=>'数据修复',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>11,
                        ),
                        array(
                            'id'=>12,
                            'pid'=>0,
                            'url'=>'#',
                            'title'=>'用户权限',
                            'icon'=>'menu-icon fa fa-users',
                            'tips'=>'用户管理，用户组管理。',
                            'status'=>1,
                            'o'=>12,
                        ),
                        array(
                            'id'=>13,
                            'pid'=>12,
                            'url'=>'user/index',
                            'title'=>'用户管理',
                            'icon'=>'menu-icon fa fa-user',
                            'tips'=>'用户列表、新增、编辑、删除管理。',
                            'status'=>1,
                            'o'=>12,
                        ),
                        array(
                            'id'=>14,
                            'pid'=>12,
                            'url'=>'group/index',
                            'title'=>'分组权限',
                            'icon'=>'menu-icon fa fa-lock',
                            'tips'=>'权限组列表、新增、编辑、删除管理。',
                            'status'=>1,
                            'o'=>14,
                        ),
                        array(
                            'id'=>15,
                            'pid'=>0,
                            'url'=>'#',
                            'title'=>'网站管理',
                            'icon'=>'menu-icon fa fa-desktop',
                            'tips'=>'网站内空管理',
                            'status'=>1,
                            'o'=>15,
                        ),
                        array(
                            'id'=>16,
                            'pid'=>15,
                            'url'=>'article/index',
                            'title'=>'文章管理',
                            'icon'=>'',
                            'tips'=>'网站文章管理，对网站文章进行新增、修改、删除操作。',
                            'status'=>1,
                            'o'=>16,
                        ),
                        array(
                            'id'=>17,
                            'pid'=>15,
                            'url'=>'article/add',
                            'title'=>'新增文章',
                            'icon'=>'',
                            'tips'=>'新增网站文章。',
                            'status'=>1,
                            'o'=>17,
                        ),
                        array(
                            'id'=>18,
                            'pid'=>15,
                            'url'=>'article/edit',
                            'title'=>'编辑文章',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>18,
                        ),
                        array(
                            'id'=>19,
                            'pid'=>15,
                            'url'=>'article/save',
                            'title'=>'文章保存',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>19,
                        ),
                        array(
                            'id'=>20,
                            'pid'=>15,
                            'url'=>'category/index',
                            'title'=>'分类管理',
                            'icon'=>'',
                            'tips'=>'文章分类管理，对分类进行新增、修改、删除操作。',
                            'status'=>1,
                            'o'=>20,
                        ),
                        array(
                            'id'=>21,
                            'pid'=>15,
                            'url'=>'category/add',
                            'title'=>'新增分类',
                            'icon'=>'',
                            'tips'=>'新增文章分类',
                            'status'=>1,
                            'o'=>21,
                        ),
                        array(
                            'id'=>22,
                            'pid'=>15,
                            'url'=>'category/edit',
                            'title'=>'编辑文章',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>22,
                        ),
                        array(
                            'id'=>23,
                            'pid'=>15,
                            'url'=>'category/save',
                            'title'=>'保存分类',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>23,
                        ),
                        array(
                            'id'=>24,
                            'pid'=>15,
                            'url'=>'banner/index',
                            'title'=>'横幅管理',
                            'icon'=>'',
                            'tips'=>'横幅管理，可对网站横幅广告进行新增、修改、删除操作。',
                            'status'=>1,
                            'o'=>24,
                        ),
                        array(
                            'id'=>25,
                            'pid'=>15,
                            'url'=>'banner/add',
                            'title'=>'新增横幅',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>1,
                            'o'=>25,
                        ),
                        array(
                            'id'=>26,
                            'pid'=>15,
                            'url'=>'banner/edit',
                            'title'=>'编辑横幅',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>26,
                        ),
                        array(
                            'id'=>27,
                            'pid'=>15,
                            'url'=>'banner/save',
                            'title'=>'保存横幅',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>27,
                        ),
                        array(
                            'id'=>28,
                            'pid'=>15,
                            'url'=>'link/index',
                            'title'=>'友情链接',
                            'icon'=>'',
                            'tips'=>'友情链接管理，对网站友情链接进行新增、修改、删除操作。',
                            'status'=>1,
                            'o'=>28,
                        ),
                        array(
                            'id'=>29,
                            'pid'=>15,
                            'url'=>'link/add',
                            'title'=>'新增链接',
                            'icon'=>'',
                            'tips'=>'新增网站友情连接。',
                            'status'=>1,
                            'o'=>29,
                        ),
                        array(
                            'id'=>30,
                            'pid'=>15,
                            'url'=>'link/edit',
                            'title'=>'编辑链接',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>30,
                        ),
                        array(
                            'id'=>31,
                            'pid'=>15,
                            'url'=>'link/save',
                            'title'=>'保存链接',
                            'icon'=>'',
                            'tips'=>'',
                            'status'=>0,
                            'o'=>31,
                        ),
                        array(
                            'id'=>32,
                            'pid'=>0,
                            'url'=>'#',
                            'title'=>'个人中心',
                            'icon'=>'menu-icon fa fa-user',
                            'tips'=>'',
                            'status'=>1,
                            'o'=>32,
                        ),
                        array(
                            'id'=>33,
                            'pid'=>32,
                            'url'=>'profile/index',
                            'title'=>'个人资料',
                            'icon'=>'menu-icon fa fa-user',
                            'tips'=>'用户个人资料修改。',
                            'status'=>1,
                            'o'=>33,
                        ),
                        array(
                            'id'=>34,
                            'pid'=>32,
                            'url'=>'logout/index',
                            'title'=>'退出登录',
                            'icon'=>'menu-icon fa fa-power-off',
                            'tips'=>'',
                            'status'=>1,
                            'o'=>34,
                        ),
                    );

        $this->insert('menu', $rows);
    }

    public function down()
    {
        $this->dropTable('menu');
    }
}
