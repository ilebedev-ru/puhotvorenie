<?php

class SNConfiguration extends ObjectModel {
    
    public	$id_sn_service;
    public	$sn_service_name;
    public	$sn_service_name_full;
    public	$sn_service_key_id;
    public	$sn_service_key_secret;
    public	$class;
    public	$active;
        
	/**
	 * @see ObjectModel::$definition
	 */
    public static $definition = array(
        'table' => 'sn_service',
	'primary' => 'id_sn_service',
	'multilang' => false,
	'fields' => array(
            'sn_service_name' =>	array('type' => self::TYPE_STRING),
            'sn_service_name_full' =>	array('type' => self::TYPE_STRING),
            'sn_service_key_id' =>	array('type' => self::TYPE_STRING),
            'sn_service_key_secret' =>	array('type' => self::TYPE_STRING),
            'class' =>	array('type' => self::TYPE_STRING),
            'active' =>	array('type' => self::TYPE_BOOL),
            )
	);
    
}


