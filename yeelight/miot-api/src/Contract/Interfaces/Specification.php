<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-7
 * Time: 下午5:53.
 */

namespace MiotApi\Contract\Interfaces;

interface Specification
{
    public function __construct($urn);

    public function getUrn();

    public function getType();

    public function getDescription();

    public function toContext();

    public function toCollection();

    public function toArray();

    public function toJson();
}
