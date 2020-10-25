<?php
$settings = array(
    'node_title' => array(
        'from' => 'node',
        'conditionsType' => 'match',
        'label' => 'value',
        'value' => 'id',
        'mergeQuery' => array(
            'leftJoin' => array(
                array(
                    'id' => 'node_field_title',
                    'conditions' => 'node.id = node_field_title.eid'
                )
            )
        ),
        'query' => 'MATCH(node_field_title.full_text) AGAINST(:word:)',
    ),
);