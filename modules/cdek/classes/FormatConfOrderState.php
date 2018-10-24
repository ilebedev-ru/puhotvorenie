<?php

class FormatConfOrderState extends FormatConfSK
{
    public static function prepareSettingOrderState($post_data)
    {
        $input_array = OrderState::getOrderStates(Context::getContext()->language->id);
        $properties = array(
            'cancel_order' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_BOOL),
            'send_order' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_BOOL),
            'free_shipping' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_BOOL),
            'paid_full' => array('default_value' => 0, 'validate' => ObjectModel::TYPE_BOOL)
        );
        return self::prepareSetting($post_data, $input_array, 'id_order_state', $properties);
    }

    public static function getSettings()
    {
        return self::prepareSettingOrderState(ConfSK::getConf('order_state_settings', ConfSK::TYPE_ARRAY));
    }

    /**
     * @return array
     */
    public static function getSendOrderStatuses()
    {
        $settings = self::getSettings();
        $statuses = array();
        foreach ($settings as $id_order_status => $setting) {
            if ($setting['send_order']) {
                $statuses[] = $id_order_status;
            }
        }
        return $statuses;
    }

    /**
     * @return array
     */
    public static function getCancelOrderStatuses()
    {
        $settings = self::getSettings();
        $statuses = array();
        foreach ($settings as $id_order_status => $setting) {
            if ($setting['cancel_order']) {
                $statuses[] = $id_order_status;
            }
        }
        return $statuses;
    }
}
