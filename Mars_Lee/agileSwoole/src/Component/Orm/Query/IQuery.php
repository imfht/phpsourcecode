<?php


namespace Component\Orm\Query;


interface IQuery
{
        public function insert(array $data, string $table) : IQuery;
        public function delete(array $condition = []) : IQuery;
        public function select(string $fields = '*') : IQuery ;
        public function update(array $data) : IQuery;
        public function where(string $where, array $conditions) : IQuery;
        public function group(string $fields) : IQuery;
        public function limit(int $limit, int $offset) : IQuery;
        public function order(string $field, string $sort = 'desc') : IQuery;
        public function having(string $condition, array $bind=null) : IQuery;
        public function from(string $table, string $database = '') : IQuery;
        public function execute() : string ;
        public function fetch(bool $object = false) : array;
        public function fetchAll(bool $object = false): array;
}