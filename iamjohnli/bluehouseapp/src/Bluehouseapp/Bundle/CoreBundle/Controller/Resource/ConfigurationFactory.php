<?php



namespace Bluehouseapp\Bundle\CoreBundle\Controller\Resource;

/**
 * Resource controller configuration factory.
 *
 */
class ConfigurationFactory
{
    /**
     * @var ParametersParser
     */
    protected $parametersParser;

    /**
     * Default Settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Constructor.
     *
     * @param ParametersParser $parametersParser
     * @param array            $settings
     */
    public function __construct(ParametersParser $parametersParser, array $settings)
    {
        $this->settings = $settings;
        $this->parametersParser = $parametersParser;
    }

    /**
     * Create configuration for given parameters.
     *
     * @param string $bundlePrefix
     * @param string $resourceName
     * @param string $templateNamespace
     * @param string $templatingEngine
     *
     * @return Configuration
     */
    public function createConfiguration($bundlePrefix, $resourceName, $templateNamespace, $templatingEngine = 'twig')
    {
        return new Configuration(
            $this->parametersParser,
            $bundlePrefix,
            $resourceName,
            $templateNamespace,
            $templatingEngine,
            $this->settings
        );
    }
}
