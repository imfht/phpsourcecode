<?php


namespace App\Command;


use App\Entity\Info;
use App\Entity\Methods;
use App\Entity\Parameters;
use App\Entity\Paths;
use App\Entity\Servers;
use App\Entity\Tags;
use App\Library\Helper\GeneralHelper;
use App\Message\BuildApiUpdateLog;
use App\Message\BuildDocumentRedisData;
use App\Message\Order;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Yaml\Yaml;

class BuildOpenApiConfigCommand extends Command
{
    protected static $defaultName = 'build:openapi-config-data';

    private $container;

    private $em;

    private $open_api_config_path;

    private $config_file;

    private $info;

    private $message_bus;

    public function __construct(ContainerInterface $container, MessageBusInterface $bus, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->open_api_config_path = $this->container->getParameter('app.swagger.openapi.path');
        $this->info = GeneralHelper::getOneInstance()->getCurrentInfo();
        $this->message_bus = $bus;
    }

    protected function configure()
    {
        $this->setDescription('生成 Swagger-UI openapi.yaml 配置数据.')
            ->setHelp('该命令用于生成Swagger-UI需要的openapi.yaml数据');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!is_object($this->info)) {
            $this->setCode('404');
            exit();
        }

        if (!is_dir($this->open_api_config_path)) {
            mkdir($this->open_api_config_path);
        }
        $info_file = $this->open_api_config_path . $this->info->getTag() . '_openapi.yaml';
        $info_bak_file = $this->open_api_config_path . $this->info->getTag() . '_openapi_bak.yaml';

        // 先备份之前的配置文件用于生成日志对比
        if (is_file($info_file) && file_exists($info_file)) {
            copy($info_file, $info_bak_file);
        } else {
            $bak_info_file = fopen($info_file, "w");
            fclose($bak_info_file);
        }

        // 开始写入最新的配置文件
        $this->config_file = fopen($info_file, "w");
        fwrite($this->config_file, "openapi: 3.0.1\n");

        // 写入元数据
        $this->doInfo();
        // 写入服务
        $this->doServer();
        // 写入标签
        $this->doTag();
        // 写入接口
        $this->doPath();
        // 写入组件
        $this->doComponents();
        // 关闭文件流
        fclose($this->config_file);

        // 生成API更新日志
        $this->message_bus->dispatch(new BuildApiUpdateLog($this->info->getId()));
        // 生成接口文档缓存数据
        $this->message_bus->dispatch(new BuildDocumentRedisData($this->info->getId(), false));
    }

    /**
     * User: gao
     * Date: 2019/11/8
     * Description: 写入元数据
     */
    private function doInfo()
    {
        $version = GeneralHelper::getOneInstance()->getNextVersion();
        $data = [
            'info' => [
                'title' => $this->info->getTitle(),
                'description' => $this->info->getDescription(),
                'version' => $version,
            ]
        ];

        // 同步更新元信息版本号
        $this->em->getRepository(Info::class)->updateCurrentInfoVersion($this->info->getId(), $version);

        fwrite($this->config_file, Yaml::dump($data, 2, 2));
    }

    /**
     * User: gao
     * Date: 2019/11/8
     * Description: 写入请求列表
     */
    private function doServer()
    {
        $server_repository = $this->em->getRepository(Servers::class);
        $servers = $server_repository->findBy(['infoId' => $this->info->getId()]);

        $items = [];
        foreach ($servers as $server) {
            $items[] = [
                'url' => $server->getUrl(),
                'description' => $server->getDescription(),
            ];
        }
        $data = [
            'servers' => $items
        ];

        fwrite($this->config_file, Yaml::dump($data, 2, 2));
    }

    /**
     * User: gao
     * Date: 2019/11/8
     * Description: 写入标签数据
     */
    private function doTag()
    {
        $tag_repository = $this->em->getRepository(Tags::class);
        $tags = $tag_repository->findBy(['infoId' => $this->info->getId()]);

        $items = [];
        foreach ($tags as $tag) {
            $items[] = [
                'name' => $tag->getName(),
                'description' => $tag->getDescription(),
                'externalDocs' => [
                    'url' => $tag->getDocUrl(),
                    'description' => $tag->getDocDescription(),
                ],
            ];
        }
        $data = [
            'tags' => $items
        ];

        fwrite($this->config_file, Yaml::dump($data, 2, 2));
    }

    /**
     * User: gao
     * Date: 2019/11/18
     * Description: 写入接口
     */
    private function doPath()
    {
        $paths_repository = $this->em->getRepository(Paths::class);
        $paths = $paths_repository->findBy([
            'status' => 1,
            'infoId' => $this->info->getId()
        ]);

        $methods = $this->em->getRepository(Methods::class)->getAllDataByArray();
        $tags = $this->em->getRepository(Tags::class)->getAllDataByArray($this->info->getId());

        $responses = [
            '200' => [
                'description' => '请求成功'
            ]
        ];

        $items = [];
        foreach ($paths as $path) {
            $items[$path->getUrl()] = $this->buildParameter($path, $methods, $tags, $responses);
        }

        $data = [
            'paths' => $items
        ];

        fwrite($this->config_file, Yaml::dump($data, 2, 2));
    }

    /**
     * User: Gao
     * Date: 2019-11-23
     * Description: 按参数生成接口
     * @param Paths $path
     * @param $methods
     * @param $tags
     * @param $responses
     * @return array
     */
    private function buildParameter(Paths $path, $methods, $tags, $responses): array
    {
        $result = [];

        $parameters = $this->em->getRepository(Parameters::class)->findBy([
            'pathsId' => $path->getId(),
            'status' => 1
        ]);

        // 参数整理
        $params = [];
        $required = [];
        $properties = [];
        $schema = [
            'type' => 'object',
            'required' => &$required,
            'properties' => &$properties
        ];
        foreach ($parameters as $parameter) {
            if ($parameter->getCategory() == Parameters::IN_BODY) {
                $properties[$parameter->getName()] = [
                    'type' => Parameters::$format_json[$parameter->getFormat()] ?? 'string',
                    'description' => $parameter->getDescription()
                ];
                if ($parameter->getRequired()) {
                    array_push($required, $parameter->getName());
                }
            } else {
                $params[] = [
                    'name' => $parameter->getName(),
                    'in' => Parameters::$categories[$parameter->getCategory()] ?? 'query',
                    'description' => $parameter->getDescription(),
                    'required' => $parameter->getRequired(),
                    'schema' => $this->getSchema($parameter),
                ];
            }
        }

        // 是否开启安全校验
        $security = [];
        if ($path->getIsSecurity() == 1) {
            $security[] = [
                'api_key' => []
            ];
        }

        $result = [
            $methods[$path->getMethodId()] => [
                'tags' => [$tags[$path->getTagId()]],
                'summary' => $path->getSummary(),
                'description' => $path->getDescription(),
                'operationId' => $path->getOperationId(),
                'parameters' => $params,
                'successCode' => $path->getSuccessCode(),
                'remark' => $path->getRemark(),
                'create_admin_id' => $path->getCreateAdminId(),
                'update_admin_id' => $path->getUpdateAdminId(),
                'created_at' => $path->getCreatedAt(),
                'updated_at' => $path->getUpdatedAt(),
                'responses' => $responses,
                'security' => $security,
            ]
        ];

        // 是否包含requestBody参数
        if (count($properties) > 0) {
            $result[$methods[$path->getMethodId()]]['requestBody'] = [
                'content' => [
                    'application/json' => [
                        'schema' => $schema
                    ]
                ],
                'required' => true
            ];
        }

        return $result;
    }

    /**
     * User: gao
     * Date: 2019/11/18
     * Description: 生成参数结构体
     * @param Parameters $parameter
     * @return array
     */
    private function getSchema(Parameters $parameter): array
    {
        $schema = [
            'type' => 'string',
        ];
        switch ($parameter->getFormat()) {
            case Parameters::FORMAT_STRING:
                $schema['type'] = 'string';
                break;
            case Parameters::FORMAT_PASSWORD:
                $schema['type'] = 'string';
                $schema['format'] = 'password';
                break;
            case Parameters::FORMAT_INTEGER:
                $schema['type'] = 'integer';
                $schema['format'] = 'int32';
                break;
            case Parameters::FORMAT_BOOLEAN:
                $schema['type'] = 'boolean';
                break;
            case Parameters::FORMAT_DATE:
                $schema['type'] = 'string';
                $schema['format'] = 'date';
                break;
            case Parameters::FORMAT_DATETIME:
                $schema['type'] = 'string';
                $schema['format'] = 'date-time';
                break;
            case Parameters::FORMAT_BINARY:
                $schema['type'] = 'string';
                $schema['format'] = 'binary';
                break;
            case Parameters::FORMAT_BYTE:
                $schema['type'] = 'string';
                $schema['format'] = 'byte';
                break;
        }

        return $schema;
    }

    /**
     * User: gao
     * Date: 2019/11/18
     * Description: 写入组件
     */
    private function doComponents()
    {
        $data = [
            'components' => [
                'securitySchemes' => [
                    'api_key' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                        'description' => '用于需要进行权限校验的接口验证',
                    ]
                ]
            ]
        ];

        fwrite($this->config_file, Yaml::dump($data, 2, 2));
    }

}