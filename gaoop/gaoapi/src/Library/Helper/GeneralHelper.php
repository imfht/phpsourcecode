<?php


namespace App\Library\Helper;


use App\Entity\AdminModule;
use App\Entity\AdminUserGroup;
use App\Entity\Info;
use App\Entity\Tags;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class GeneralHelper extends BaseHelper
{
    public $info = null;

    public $info_id = 0;

    public function __construct()
    {
        parent::__construct();

        $info = $this->getCurrentInfo();
        if (is_object($info)) {
            $this->info = $info;
            $this->info_id = $info->getId();
        }
    }

    /**
     * User: Gao
     * Date: 2020/2/27
     * Description: 获取当前编辑元数据
     * @return mixed
     */
    public function getCurrentInfo()
    {
        return $this->entity_manager->getRepository(Info::class)->getCurrentInfo();
    }

    /**
     * User: gao
     * Date: 2019/11/27
     * Description: 获取当前编辑元数据ID
     * @return int
     */
    public function getCurrentInfoId()
    {
        return $this->info_id;
    }

    /**
     * User: gao
     * Date: 2019/11/29
     * Description: ~
     * @param string $type
     * @param string $message
     */
    public function addFlash(string $type, string $message)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
        }

        $this->container->get('session')->getFlashBag()->add($type, $message);
    }

    /**
     * User: gao
     * Date: 2019/12/4
     * Description: 生成当前info的api配置文件
     * @throws \Exception
     */
    public function buildCurrentInfoApiConfig()
    {
        if ($this->info->getIsAutoUpdate()) {
            $kernel = GetterHelper::getContainer()->get('kernel');
            $application = new Application($kernel);
            $application->setAutoExit(true);
            $input = new ArrayInput([
                'command' => 'build:openapi-config-data'
            ]);
            $output = new BufferedOutput();
            $exitCode = $application->run($input, $output);
        }
    }

    /**
     * User: gao
     * Date: 2019/12/4
     * Description: ~
     * @param $id
     * @param $name
     * @return bool
     */
    public function hasTag($id, $name): bool
    {
        $qb = $this->entity_manager->createQueryBuilder();

        $qb->select('t')
            ->from('App\Entity\Tags', 't')
            ->where('t.name = :name')
            ->andWhere('t.infoId = :infoId')
            ->setParameter('name', $name)
            ->setParameter('infoId', $this->info_id);

        if (!is_null($id)) {
            $qb->andWhere('t.id != :id')
                ->setParameter('id', $id);
        }
        $result = $qb->getQuery()->getResult();

        return count($result) > 0 ? true : false;
    }

    public function getTagNameById($id)
    {
        $tag = $this->entity_manager->getRepository(Tags::class)->find($id);
        return is_object($tag) ? $tag->getName() : '-';
    }

    /**
     * User: gao
     * Date: 2019/12/26
     * Description: 获取下API发布版本
     * @param $version
     * @return string
     */
    public function getNextVersion(): string
    {
        $result = '1.0.0';

        $version_arr = explode('.', $this->info->getVersion());
        if (count($version_arr) == 3 && is_numeric($version_arr[0]) && is_numeric($version_arr[1]) && is_numeric($version_arr[2])) {
            if ($version_arr[2] < 99) {
                $version_arr[2] += 1;
            } else {
                $version_arr[2] = 0;
                if ($version_arr[1] < 99) {
                    $version_arr[1] += 1;
                } else {
                    $version_arr[1] = 0;
                    $version_arr[0] += 1;
                }
            }
            $result = implode('.', $version_arr);
        }

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/3/8
     * Description: 获取文档redis前缀键名
     * @param $info_id
     * @param string $type
     * @return string|null
     */
    public function getDocumentRedisPrefixKey($info_id, $type = 'other')
    {
        $result = null;

        if (!is_null($this->info)) {
            $result = 'gaoapi:info:' . $info_id . ':document:' . $type;
        }

        return $result;
    }

    /**
     * User: Gao
     * Date: 2019-12-27
     * Description: 实现each方法
     * @param $array
     * @return array|bool
     */
    public static function myEach(&$array)
    {
        $res = array();
        $key = key($array);
        if ($key !== null) {
            next($array);
            $res[1] = $res['value'] = $array[$key];
            $res[0] = $res['key'] = $key;
        } else {
            $res = false;
        }
        return $res;
    }

    /**
     * User: gao
     * Date: 2020/3/20
     * Description: 获取管理员用户组名称
     * @param $id
     * @return string
     */
    public function getAdminUserGroupNameById($id)
    {
        $result = '-';

        $obj = $this->entity_manager->getRepository(AdminUserGroup::class)->find($id);
        if (is_object($obj)) {
            $result = $obj->getName();
        }

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 获取模块sonata_admin集合
     * @param $admin_user_group_id
     * @return mixed
     */
    public function getModuleSonataAdminsByAdminUserGroupId($admin_user_group_id)
    {
        return $this->entity_manager->getRepository(AdminUserGroup::class)->getSonataAdminById($admin_user_group_id);
    }
}