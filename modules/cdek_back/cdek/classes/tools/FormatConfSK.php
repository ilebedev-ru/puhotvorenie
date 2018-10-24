<?php

class FormatConfSK
{
	public static function prepareSetting($post_data, $input_array, $id_key, $properties)
	{
		$return_data = array();

		if (is_array($input_array) && count($input_array))
			foreach ($input_array as $item)
			{
				if (!array_key_exists($item[$id_key], $return_data))
					$return_data[$item[$id_key]] = array();

				foreach ($properties as $property => $value)
				{
					if (is_array($post_data) && array_key_exists($item[$id_key], $post_data)
						&& array_key_exists($property, $post_data[$item[$id_key]]))
						$return_data[$item[$id_key]][$property] = ToolsModuleSK::formatValue($post_data[$item[$id_key]][$property], $value['validate']);
					else
						$return_data[$item[$id_key]][$property] = $value['default_value'];
				}
			}

		return $return_data;
	}

	public static function prepareSettingOrderStates($post_data)
	{
		$input_array = OrderState::getOrderStates(Context::getContext()->language->id);
		$properties = array(
			'add_bonus' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_INT),
			'cancel_bonus' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_INT));
		return self::prepareSetting($post_data, $input_array, 'id_order_state', $properties);
	}

	public static function prepareSettingGroup($post_data)
	{
		$input_array = Group::getGroups(Context::getContext()->language->id);
		$properties = array(
			'enabled' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_INT),
			'commission' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_FLOAT),
			'allow_count_product' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_INT),
			'allow_count_image' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_INT));
		return self::prepareSetting($post_data, $input_array, 'id_group', $properties);
	}

	public static function getSettingGroup()
	{
		return self::prepareSettingGroup(ConfSK::getConf('SETTING_GROUPS', ConfSK::TYPE_ARRAY));
	}

	public static function getSettingOrderStates()
	{
		return self::prepareSettingOrderStates(ConfSK::getConf('SETTING_ORDER_STATES', ConfSK::TYPE_ARRAY));
	}
}