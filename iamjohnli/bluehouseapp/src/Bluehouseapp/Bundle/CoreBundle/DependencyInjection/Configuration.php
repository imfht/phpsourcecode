<?php

namespace Bluehouseapp\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bluehouseapp');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $this->addClassesSection($rootNode);
        $this->addSettingsSection($rootNode);
        $this->addTemplatesSection($rootNode);
        return $treeBuilder;
    }

    private function addTemplatesSection(ArrayNodeDefinition $node)
    {

        $node
            ->children()
            ->arrayNode('templates')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('audit')->defaultValue('BluehouseappWebBundle:Backend/Admin/Audit')->end()
            ->scalarNode('banedIPs')->defaultValue('BluehouseappWebBundle:Backend/Admin/BanedIPs')->end()
#
            ->scalarNode('node')->defaultValue('BluehouseappWebBundle:Backend/Admin/Node')->end()
            ->scalarNode('category')->defaultValue('BluehouseappWebBundle:Backend/Admin/Category')->end()
            ->scalarNode('member')->defaultValue('BluehouseappWebBundle:Backend/Admin/Member')->end()
            ->end()
            ->end()
            ->end()
            ->end();

    }
    /**
     * Adds `classes` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('classes')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('audit')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\Audit')->end()
            ->scalarNode('controller')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Controller\AuditController')->end()
            ->scalarNode('repository')->cannotBeEmpty()->end()
//            ->scalarNode('form')->defaultValue('')->end()
            ->end()
            ->end()
            ->arrayNode('banedIPs')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\BanedIPs')->end()
            ->scalarNode('controller')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Controller\BanedIPsController')->end()
            ->scalarNode('repository')->cannotBeEmpty()->end()
            ->scalarNode('form')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Form\BanedIPsType')->end()
            ->end()
            ->end()
            ->arrayNode('category')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\Category')->end()
            ->scalarNode('controller')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Controller\CategoryController')->end()
            ->scalarNode('repository')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\CategoryRepository')->end()
            ->scalarNode('form')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Form\CategoryType')->end()
            ->end()
            ->end()
            ->arrayNode('member')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\Member')->end()
            ->scalarNode('controller')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Controller\MemberController')->end()
            ->scalarNode('repository')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\MemberRepository')->end()
            ->scalarNode('form')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Form\MemberType')->end()
            ->end()
            ->end()
            ->arrayNode('node')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\Node')->end()
            ->scalarNode('controller')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Controller\NodeController')->end()
            ->scalarNode('repository')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\NodeRepository')->end()
            ->scalarNode('form')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Form\NodeType')->end()
            ->end()
            ->end()
            ->arrayNode('post')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\Post')->end()
            ->scalarNode('controller')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Controller\PostController')->end()
            ->scalarNode('repository')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\PostRepository')->end()
            ->scalarNode('form')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Form\PostType')->end()
            ->end()
            ->end()
            ->arrayNode('postComment')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\PostComment')->end()
            ->scalarNode('controller')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Controller\PostCommentController')->end()
            ->scalarNode('repository')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\PostCommentRepository')->end()
            ->scalarNode('form')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Form\PostCommentType')->end()
            ->end()
            ->end()
            ->arrayNode('userBehavior')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('model')->defaultValue('Bluehouseapp\Bundle\CoreBundle\Entity\BanedIPs')->end()
//            ->scalarNode('controller')->defaultValue('')->end()
//            ->scalarNode('repository')->cannotBeEmpty()->end()
//            ->scalarNode('form')->defaultValue('')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }



    private function addSettingsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('settings')
            ->addDefaultsIfNotSet()
            ->children()
            ->variableNode('paginate')->defaultNull()->end()
            ->variableNode('limit')->defaultNull()->end()
            ->arrayNode('allowed_paginate')
            ->prototype('integer')->end()
            ->defaultValue(array(20, 30, 50))
            ->end()
            ->integerNode('default_page_size')->defaultValue(20)->end()
            ->booleanNode('sortable')->defaultFalse()->end()
            ->variableNode('sorting')->defaultNull()->end()
            ->booleanNode('filterable')->defaultFalse()->end()
            ->variableNode('criteria')->defaultNull()->end()
            ->end()
            ->end()
            ->end()
        ;
    }

}
