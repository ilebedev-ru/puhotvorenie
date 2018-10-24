<?php

class blocktestClass extends ObjectModel
{
    /** @var string Name */
    public $id_blocktest;
    public $name;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'blocktest',
        'primary' => 'id_blocktest',
//      'multilang' => true,
        'fields' => array(
            'name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        ),
    );

    public static function getList()
    {
        return Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.''.self::$definition['table'].'` ');
    }



}