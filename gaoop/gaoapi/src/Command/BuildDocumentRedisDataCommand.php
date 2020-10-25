<?php


namespace App\Command;


use App\Entity\Info;
use App\Library\Helper\GeneralHelper;
use App\Service\Redis;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class BuildDocumentRedisDataCommand extends Command
{
    protected static $defaultName = 'build:document-redis-data';

    private $container;

    private $em;

    private $logger;

    private $redis;

    private $open_api_config_path;

    private $menu;

    private $option_show;

    private $info;

    public function __construct(ContainerInterface $container, LoggerInterface $logger, Redis $redis, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->open_api_config_path = $this->container->getParameter('app.swagger.openapi.path');
        $this->logger = $logger;
        $this->redis = $redis;
    }

    protected function configure()
    {
        $this->addArgument('info_id', InputArgument::REQUIRED, '需要被执行的info数据id值')
            ->addOption('show', null, InputOption::VALUE_NONE, '是否显示进度条')
            ->setDescription('生成接口文档redis缓存数据')
            ->setHelp('根据指定info生成对应接口文档查询数据');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->menu = [];

        $info_id = $input->getArgument('info_id');
        $this->info = $this->em->getRepository(Info::class)->find($info_id);
        $this->option_show = $input->getOption('show');

        if (!is_object($this->info)) {
            $this->logger->error('info_id：' . $info_id . ' 数据无效');
            return 404;
        }

        $info_file = $this->open_api_config_path . $this->info->getTag() . '_openapi.yaml';
        $data = Yaml::parseFile($info_file);
        $tags = $data['tags'] ?? [];
        $paths = $data['paths'] ?? [];

        // 生成api参数内容
        $this->buildParameter($paths, $output);
        // 生成文档菜单数据
        $this->buildMenu($tags, $output);
    }

    /**
     * User: Gao
     * Date: 2020/3/5
     * Description: 生成文档菜单数据
     * @param $tags
     * @param $output
     */
    private function buildMenu($tags, $output)
    {
        $redis_prefix = GeneralHelper::getOneInstance()->getDocumentRedisPrefixKey($this->info->getId(), Info::REDIS_DOCUMENT_MENU_PREFIX_KEY);

        if ($this->option_show) {
            $section = $output->section();
            $progressBar = new ProgressBar($section, count($tags));
            $progressBar->setFormat(PHP_EOL . '任务：%message% ' . PHP_EOL . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %memory:6s%');
            $progressBar->setMessage('生成接口菜单缓存数据');
            $progressBar->start();
            foreach ($tags as &$tag) {
                $tag['child'] = [];
                if (isset($this->menu[$tag['name']])) {
                    $tag['child'] = $this->menu[$tag['name']];
                }
                $progressBar->advance();
            }
            $progressBar->finish();
        } else {
            foreach ($tags as &$tag) {
                $tag['child'] = [];
                if (isset($this->menu[$tag['name']])) {
                    $tag['child'] = $this->menu[$tag['name']];
                }
            }
        }

        $this->redis->set($redis_prefix, json_encode($tags, JSON_UNESCAPED_UNICODE));
    }

    /**
     * User: Gao
     * Date: 2020/3/5
     * Description: 生成接口内容缓存数据
     * @param $paths
     * @param $output
     */
    private function buildParameter($paths, $output)
    {
        $redis_prefix = GeneralHelper::getOneInstance()->getDocumentRedisPrefixKey($this->info->getId(), Info::REDIS_DOCUMENT_PATH_PREFIX_KEY);
        if ($this->option_show) {
            $section = $output->section();
            $progressBar = new ProgressBar($section, count($paths));
            $progressBar->setFormat('任务：%message% ' . PHP_EOL . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %memory:6s%');
            $progressBar->setMessage('生成接口内容缓存数据');
            $progressBar->start();
            foreach ($paths as $url => $path) {
                list($method, $item) = GeneralHelper::myEach($path);
                $operationId = $item['operationId'];
                // 获取参数
                $parameters = $item['parameters'] ?? [];
                // body类型参数特殊处理
                $body_parameter_data = $item['requestBody']['content']['application/json']['schema'] ?? [];
                if (count($body_parameter_data) > 0) {
                    $body_required = $body_parameter_data['required'];
                    foreach ($body_parameter_data['properties'] as $key => $value) {
                        $body_parameter = [
                            'name' => $key,
                            'in' => 'body',
                            'description' => $value['description'],
                            'required' => in_array($key, $body_required),
                            'schema' => [
                                'type' => $value['type']
                            ]
                        ];
                        array_push($parameters, $body_parameter);
                    }
                }

                $gather = [
                    'url' => $url,
                    'method' => strtoupper($method),
                    'summary' => $item['summary'] ?? '',
                    'description' => $item['description'] ?? '',
                    'successCode' => $item['successCode'] ?? '',
                    'remark' => $item['remark'] ?? '',
                    'parameters' => $parameters
                ];

                $this->redis->set($redis_prefix . ':' . $operationId, json_encode($gather, JSON_UNESCAPED_UNICODE));

                // 记录数据为menu准备
                $tag = $item['tags'][0] ?? '';
                $this->menu[$tag][] = [
                    'summary' => $item['summary'],
                    'operationId' => $operationId
                ];

                $progressBar->advance();
            }
            $progressBar->finish();
        } else {
            foreach ($paths as $url => $path) {
                list($method, $item) = GeneralHelper::myEach($path);
                $operationId = $item['operationId'];
                // 获取参数
                $parameters = $item['parameters'] ?? [];
                // body类型参数特殊处理
                $body_parameter_data = $item['requestBody']['content']['application/json']['schema'] ?? [];
                if (count($body_parameter_data) > 0) {
                    $body_required = $body_parameter_data['required'];
                    foreach ($body_parameter_data['properties'] as $key => $value) {
                        $body_parameter = [
                            'name' => $key,
                            'in' => 'body',
                            'description' => $value['description'],
                            'required' => in_array($key, $body_required),
                            'schema' => [
                                'type' => $value['type']
                            ]
                        ];
                        array_push($parameters, $body_parameter);
                    }
                }

                $gather = [
                    'url' => $url,
                    'method' => strtoupper($method),
                    'summary' => $item['summary'] ?? '',
                    'description' => $item['description'] ?? '',
                    'successCode' => $item['successCode'] ?? '',
                    'remark' => $item['remark'] ?? '',
                    'parameters' => $parameters
                ];

                $this->redis->set($redis_prefix . ':' . $operationId, json_encode($gather, JSON_UNESCAPED_UNICODE));

                // 记录数据为menu准备
                $tag = $item['tags'][0] ?? '';
                $this->menu[$tag][] = [
                    'summary' => $item['summary'],
                    'operationId' => $operationId
                ];

            }
        }
    }

}