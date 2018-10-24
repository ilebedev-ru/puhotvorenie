<?php

class CDEKCity extends ObjectModelCore
{
    public $id_cdek;
    public $city_name;
    public $country_id;
    public $country_iso;
    public $country_name;
    public $name;
    public $post_code_array;
    public $region_id;
    public $region_name;

    public static $definition = array(
        'table' => 'cdek_city',
        'primary' => 'id_cdek_city',
        'fields' => array(
            'id_cdek' => array(
                'type' => self::TYPE_INT,
                'validate' => ValidateTypeSK::IS_INT
            ),
            'city_name' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'country_id' => array(
                'type' => self::TYPE_INT,
                'validate' => ValidateTypeSK::IS_INT
            ),
            'country_iso' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'country_name' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'post_code_array' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'region_id' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
            'region_name' => array(
                'type' => self::TYPE_STRING,
                'validate' => ValidateTypeSK::IS_STRING
            ),
        )
    );

    public static function getCityByName($name)
    {
        $id = (int)Db::getInstance()->getValue(
            'SELECT `id_cdek_city`
            FROM '._DB_PREFIX_.'cdek_city WHERE `city_name` = "'.$name . '"'
        );

        $object = new self($id);

        if (empty($object->id)) {
            $object->city_name = $name;
            //return $object->createCity($object); @pw
        }

        $object->name = $name;
        return $object;
    }

    protected static function createCity($city)
    {
        $requestUrl = 'http://api.cdek.ru/city/getListByTerm/jsonp.php?q='.$city->city_name.'&name_startsWith='.$city->city_name.'&_='.time();
        if(isset($_COOKIE['pastila'])){
         var_dump($requestUrl);
        }
        $responsedCity = false;
        $responsedData = json_decode(file_get_contents($requestUrl));

        foreach ($responsedData->geonames as $data) {
            if (mb_strtolower($data->cityName) != mb_strtolower($city->city_name)) {
                continue;
            }

            $responsedCity = $data;
        }

        if ($responsedCity) {
            $city->id_cdek = $responsedCity->id;
            $city->city_name = $responsedCity->cityName;
            $city->country_id = $responsedCity->countryId;
            $city->country_iso = $responsedCity->countryIso;
            $city->country_name = $responsedCity->countryName;
            $city->name = $responsedCity->name;
            $city->post_code_array = json_encode((array)$responsedCity->postCodeArray);
            $city->region_id = $responsedCity->regionId;
            $city->region_name = $responsedCity->regionName;

            $city->save();
        }

        return $city;
    }
}