<?php
/**
 * 测试实例
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/4/4
 * Time: 17:04
 */

namespace app\Models\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="articles")
 **/
class Article
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="integer") */
    private $user_id;

    /** @ORM\Column(type="string") */
    private $title;
}