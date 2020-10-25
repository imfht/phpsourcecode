<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Methods;
use App\Entity\Parameters;
use App\Entity\Paths;
use App\Entity\Tags;
use App\Form\Type\ParamsFieldType;
use App\Library\Admin\MyAdmin;
use App\Library\Helper\GeneralHelper;
use App\Library\Helper\GetterHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Security;

final class PathsAdmin extends MyAdmin
{
    protected $accessMapping = ['import' => 'IMPORT'];

    protected $security;

    public function __construct($code, $class, $baseControllerName, Security $security)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->security = $security;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('url')
            ->add('methodId', 'doctrine_orm_choice', ['label' => 'Method'], ChoiceType::class, [
                'choices' => $this->getMethodsData(true)
            ])
            ->add('tagId', 'doctrine_orm_choice', ['label' => 'Tag'], ChoiceType::class, [
                'choices' => $this->getTagsData(true)
            ])
            ->add('operationId')
            ->add('isSecurity')
            ->add('status');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('url')
            ->add('methodId', 'choice', [
                'label' => 'Method',
                'choices' => $this->getMethodsData()
            ])
            ->add('tagId', 'choice', [
                'label' => 'Tag',
                'choices' => $this->getTagsData()
            ])
            ->add('summary')
            ->add('isSecurity')
            ->add('status')
            ->add('createdAt', null, [
                'format' => 'Y-m-d H:i:s',
                'timezone' => 'PRC'
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $paths_id = is_null($this->getSubject()) ? null : $this->getSubject()->getId();

        $formMapper
            ->with('Field', ['class' => 'col-md-8'])
            ->add('params', ParamsFieldType::class, [
                'paths_id' => $paths_id
            ])
            ->end()
            ->with('Other', ['class' => 'col-md-8'])
            ->add('success_code', null, [
                'attr' => [
                    'style' => 'resize: vertical',
                    'placeholder' => 'Enter ...',
                    'rows' => 13
                ],
            ])
            ->add('remark', null, [
                'attr' => [
                    'style' => 'resize: vertical',
                    'placeholder' => 'Enter ...',
                    'rows' => 13
                ]
            ])
            ->end()
            ->with('Basic', ['class' => 'col-md-4 top-col'])
            ->add('url')
            ->add('methodId', ChoiceType::class, [
                'label' => 'Method',
                'choices' => $this->getMethodsData(true),
            ])
            ->add('tagId', ChoiceType::class, [
                'label' => 'Tag',
                'choices' => $this->getTagsData(true),
            ])
            ->add('summary', null, ['help' => '摘要尽量精简，将用于文档接口说明使用'])
            ->add('description', TextareaType::class, [
                'attr' => ['style' => 'height: 50px; resize:none;'],
                'required' => false,
            ])
            ->add('isSecurity', null, [])
            ->add('status')
            ->end();
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('url')
            ->add('methodId')
            ->add('tagId')
            ->add('summary')
            ->add('operationId')
            ->add('description')
            ->add('isSecurity')
            ->add('status')
            ->add('createdAt')
            ->add('updatedAt');
    }

    private function getMethodsData($is_turn = false)
    {
        $result = [];

        $methods = $this->modelManager->findBy(Methods::class);
        foreach ($methods as $method) {
            if ($is_turn) {
                $result[$method->getValue()] = $method->getId();
            } else {
                $result[$method->getId()] = $method->getValue();
            }
        }

        return $result;
    }

    private function getTagsData($is_turn = false)
    {
        $result = [];

        $tags = $this->modelManager->findBy(Tags::class, ['infoId' => GeneralHelper::getInstance()->info_id]);
        foreach ($tags as $tag) {
            if ($is_turn) {
                $result[$tag->getName()] = $tag->getId();
            } else {
                $result[$tag->getId()] = $tag->getName();
            }
        }

        return $result;
    }

    public function prePersist($object)
    {
        parent::prePersist($object); // TODO: Change the autogenerated stub

        $object->setOperationId(uniqid());
        $object->setInfoId(GeneralHelper::getInstance()->info_id);
        $object->setCreateAdminId($this->security->getUser()->getId());
        $object->setCreatedAt(new \DateTime());

    }

    public function postPersist($object)
    {
        parent::postPersist($object); // TODO: Change the autogenerated stub

        // 保存参数
        $this->saveParameters($object);

        // 是否自动更新文档
        GeneralHelper::getOneInstance()->buildCurrentInfoApiConfig();
    }

    public function postUpdate($object)
    {
        parent::postUpdate($object); // TODO: Change the autogenerated stub

        // 保存参数
        $this->saveParameters($object);

        // 是否自动更新文档
        GeneralHelper::getOneInstance()->buildCurrentInfoApiConfig();
    }

    public function preUpdate($object)
    {
        parent::preUpdate($object); // TODO: Change the autogenerated stub
        $object->setUpdateAdminId($this->security->getUser()->getId());
    }

    public function postRemove($object)
    {
        parent::postRemove($object); // TODO: Change the autogenerated stub

        // 是否自动更新文档
        GeneralHelper::getOneInstance()->buildCurrentInfoApiConfig();
    }

    private function saveParameters($object)
    {
        $params_data = $this->getRequest()->get('params_data', []);

        // 先删除之前的参数
        $sql = GetterHelper::getEntityManager()->createQuery('update App\Entity\Parameters p set p.status = -1 where p.pathsId = ' . $object->getId() . ' and p.status = 1');
        $numUpdated = $sql->execute();

        GetterHelper::getEntityManager()->getRepository(Parameters::class)->batchInsert($object->getId(), $params_data);
    }

    public function preBatchAction($actionName, ProxyQueryInterface $query, array &$idx, $allElements)
    {
        switch ($actionName) {
            case 'delete':
                $this->verifyInfo(Paths::class, $idx, $allElements);
                break;
        }

        parent::preBatchAction($actionName, $query, $idx, $allElements); // TODO: Change the autogenerated stub
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context); // TODO: Change the autogenerated stub

        $query->andWhere(
            $query->expr()->eq($query->getRootAliases()[0] . '.infoId', ':infoId')
        );
        $query->setParameter('infoId', GeneralHelper::getInstance()->info_id);

        return $query;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        parent::validate($errorElement, $object); // TODO: Change the autogenerated stub

        $errorElement
            ->with('tagId')
            ->assertNotNull(array('message' => '标签不能为空'))
            ->end();
    }

    public function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection); // TODO: Change the autogenerated stub

        $collection->add('import', 'import');
        $collection->remove('export');
        $collection->remove('show');

    }

    public function configureActionButtons($action, $object = null)
    {
        $list = [];

        if (\in_array($action, ['tree', 'show', 'edit', 'delete', 'list', 'batch', 'import'], true)
            && $this->hasAccess('create')
            && $this->hasRoute('create')
        ) {
            $list['create'] = [
                // NEXT_MAJOR: Remove this line and use commented line below it instead
                'template' => $this->getTemplate('button_create'),
                // 'template' => $this->getTemplateRegistry()->getTemplate('button_create'),
            ];
        }

        if (\in_array($action, ['show', 'delete', 'acl', 'history'], true)
            && $this->canAccessObject('edit', $object)
            && $this->hasRoute('edit')
        ) {
            $list['edit'] = [
                // NEXT_MAJOR: Remove this line and use commented line below it instead
                'template' => $this->getTemplate('button_edit'),
                //'template' => $this->getTemplateRegistry()->getTemplate('button_edit'),
            ];
        }

        if (\in_array($action, ['show', 'edit', 'acl'], true)
            && $this->canAccessObject('history', $object)
            && $this->hasRoute('history')
        ) {
            $list['history'] = [
                // NEXT_MAJOR: Remove this line and use commented line below it instead
                'template' => $this->getTemplate('button_history'),
                // 'template' => $this->getTemplateRegistry()->getTemplate('button_history'),
            ];
        }

        if (\in_array($action, ['edit', 'history'], true)
            && $this->isAclEnabled()
            && $this->canAccessObject('acl', $object)
            && $this->hasRoute('acl')
        ) {
            $list['acl'] = [
                // NEXT_MAJOR: Remove this line and use commented line below it instead
                'template' => $this->getTemplate('button_acl'),
                // 'template' => $this->getTemplateRegistry()->getTemplate('button_acl'),
            ];
        }

        if (\in_array($action, ['edit', 'history', 'acl'], true)
            && $this->canAccessObject('show', $object)
            && \count($this->getShow()) > 0
            && $this->hasRoute('show')
        ) {
            $list['show'] = [
                // NEXT_MAJOR: Remove this line and use commented line below it instead
                'template' => $this->getTemplate('button_show'),
                // 'template' => $this->getTemplateRegistry()->getTemplate('button_show'),
            ];
        }

        if (\in_array($action, ['list', 'edit', 'show'], true)
            && $this->hasAccess('import')
            && $this->hasRoute('import')
        ) {
            $list['import'] = [
                'template' => 'Backend/paths/import_button.html.twig',
            ];
        }

        if (\in_array($action, ['show', 'edit', 'delete', 'acl', 'batch', 'import'], true)
            && $this->hasAccess('list')
            && $this->hasRoute('list')
        ) {
            $list['list'] = [
                // NEXT_MAJOR: Remove this line and use commented line below it instead
                'template' => $this->getTemplate('button_list'),
                // 'template' => $this->getTemplateRegistry()->getTemplate('button_list'),
            ];
        }

        return $list;
    }
}
