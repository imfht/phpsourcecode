<?php

namespace PHPDish\Bundle\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\GridBuilder;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Source\Source;
use PHPDish\Bundle\CoreBundle\Model\Post;
use PHPDish\Bundle\CoreBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @Route("/posts", name="admin_post_index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $source = new Entity(User::class);
        $builder = $this->createGridBuilder($source, [
            'persistence'  => true,
            'route'        => 'admin_post_index',
            'filterable'   => false,
            'sortable'     => false,
            'max_per_page' => 20,
        ]);

        $grid = $builder
            ->add('id', 'number', [
                'title'   => '#',
                'primary' => 'true',
            ])
            ->add('username', 'text')
            ->add('createdAt', 'datetime', [
                'field' => 'createdAt',
            ])
            ->add('profile', 'text')
            ->getGrid();

        return $grid->getGridResponse('PHPDishAdminBundle:Post:index.html.twig');
    }

    /**
     * @return GridBuilder
     */
    public function createGridBuilder(Source $source = null, array $options = [])
    {
        return $this->container->get('apy_grid.factory')->createBuilder('grid', $source, $options);
    }
}
