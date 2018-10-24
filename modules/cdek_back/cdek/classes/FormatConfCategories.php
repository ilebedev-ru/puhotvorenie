<?php

class FormatConfCategories extends FormatConfSK
{
	public static function prepareSettingCategories($post_data)
	{
		$input_array = Category::getSimpleCategories(Context::getContext()->language->id);
		$properties = array(
			'width' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_FLOAT),
			'height' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_FLOAT),
			'length' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_FLOAT),
			'weight' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_FLOAT));
		return self::prepareSetting($post_data, $input_array, 'id_category', $properties);
	}

	public static function getSettings()
	{
		return self::prepareSettingCategories(ConfSK::getConf('categories_setting', ConfSK::TYPE_ARRAY));
	}
}