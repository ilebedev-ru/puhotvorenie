<?php

class yaProduct extends ObjectModel
{
    public $id;
    public $id_product;
    public $export = true;
    
    public static $definition = array(
        'table' => 'yaproduct',
        'primary' => 'id_product',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array
        (
            'export' => array('type' => self::TYPE_BOOL),
        ),
    );
}