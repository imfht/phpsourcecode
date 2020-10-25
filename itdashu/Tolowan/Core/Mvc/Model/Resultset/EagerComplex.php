<?php

namespace Phalcon\Mvc\Model\Resultset;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Row;

class EagerComplex implements ResultsetInterface, \Iterator, \SeekableIterator, \Countable, \ArrayAccess, \Serializable
{
    protected $Complex;
    protected $baseAlias;

    public function __construct(Complex $Complex)
    {
        $this->Complex = $Complex;
    }

    public function setBaseAlias($sqlalias)
    {
        $this->baseAlias = $sqlalias;

        return $this;
    }

    public function current()
    {
        $row = $this->Complex->current();

        if ($row instanceof Row) {

            foreach ($this->getRowModels($row) as $model) {

                foreach ($this->getModelEagerRelations($model) as $relation) {

                    $sqlalias = $relation->getOption('sqlalias');

                    if (!empty($row->{$sqlalias})) {

                        $referenceModel = $relation->getReferencedModel();

                        if ($row->{$sqlalias} instanceof $referenceModel) {

                            $model->{$relation->getOption('alias')} = $row->{$sqlalias};
                        }
                    }
                }
            }

            if ($this->baseAlias) {
                $row = $row->{$this->baseAlias};
            }
        }

        return $row;
    }

    /**
     * Query all the relationships defined on a model where set sqlalias option
     *
     * @param \Phalcon\Mvc\Model $model
     * @return \Phalcon\Mvc\Model\RelationInterface[]
     */
    protected function getModelEagerRelations(Model $model)
    {
        $relations = $model->getModelsManager()->getRelations(get_class($model));

        return array_filter($relations, function ($relation) {

            if ($relation->getOption('sqlalias')) {

                if (!in_array($relation->getType(), [Relation::BELONGS_TO, Relation::HAS_ONE])) {
                    throw new \LogicException('The sqlalias can be used only with BelongsTo or HasOne relations.');
                }

                return true;
            }

            return false;
        });
    }

    /**
     * @param \Phalcon\Mvc\Model\Row $row
     * @return array
     */
    protected function getRowModels(Row $row)
    {
        $result = [];

        foreach (get_object_vars($row) as $k => $v) {

            if ($v instanceof Model) {
                $result[$k] = $v;
            }
        }

        return $result;
    }

    public function offsetExists($offset)
    {
        return $this->Complex->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->Complex->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->Complex->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->Complex->offsetUnset($offset);
    }

    public function key()
    {
        return $this->Complex->key();
    }

    public function next()
    {
        return $this->Complex->next();
    }

    public function rewind()
    {
        return $this->Complex->rewind();
    }

    public function valid()
    {
        return $this->Complex->valid();
    }

    public function seek($position)
    {
        return $this->Complex->seek($position);
    }

    public function count()
    {
        return $this->Complex->count();
    }

    public function getType()
    {
        return $this->Complex->getType();
    }

    public function getFirst()
    {
        return $this->Complex->getFirst();
    }

    public function getLast()
    {
        return $this->Complex->getLast();
    }

    public function setIsFresh($isFresh)
    {
        return $this->Complex->setIsFresh($isFresh);
    }

    public function isFresh()
    {
        return $this->Complex->isFresh();
    }

    public function getCache()
    {
        return $this->Complex->getCache();
    }

    public function toArray()
    {
        return $this->Complex->toArray();
    }

    public function serialize()
    {
        return $this->Complex->serialize();
    }

    public function unserialize($data)
    {
        return $this->Complex->unserialize($data);
    }
}