<?php

class CDEKLogger extends ObjectModel
{
    /**
     * @var string
     */
    public $method;
    /**
     * @var string
     */
    public $message;
    /**
     * @var string
     */
    public $request;
    /**
     * @var string
     */
    public $response;
    /**
     * @var string
     */
    public $date_add;

    public static $definition = array(
        'table' => 'cdek_logger',
        'primary' => 'id_cdek_logger',
        'fields' => array(
            'method' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'message' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'request' => array(
                'type' => self::TYPE_NOTHING,
                'validate' => 'isAnything'
            ),
            'response' => array(
                'type' => self::TYPE_NOTHING,
                'validate' => 'isAnything'
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            )
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->date_add = date('Y-m-d H:i:s');
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function addMessage($method, $message, $request, $response)
    {
        if (!ConfSK::getConf('write_log')) {
            return false;
        }
        $object = new self();
        $object->method = $method;
        $object->message = $message;
        $object->request = ToolsModuleSK::decodeCode($request);
        $object->response = ToolsModuleSK::decodeCode($response);
        $object->save();
    }

    const LIMIT = 20;
    /**
     * @param int $p
     * @param $get_total
     * @param $search
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getAll($p = 1, $get_total = false, $search = null)
    {
        $limit = self::LIMIT;
        $sql = new DbQuery();
        $sql->from('cdek_logger');
        $sql->orderBy('id_cdek_logger DESC');

        if (is_array($search)) {
            if (isset($search['method']) && $search['method']) {
                $sql->where('method = "'.pSQL($search['method']).'"');
            }
            $date_begin = (isset($search['date_begin']) && $search['date_begin'] ? $search['date_begin'] : false);
            $date_end = (isset($search['date_end']) && $search['date_end'] ? $search['date_end'] : false);
            if ($date_begin || $date_end) {
                if ($date_begin) {
                    $sql->where('date_add >= "'.pSQL($date_begin).' 00:00:00"');
                }
                if ($date_end) {
                    $sql->where('date_add <= "'.pSQL($date_end).' 23:59:59"');
                }
            }
        }

        if (!$get_total) {
            $sql->select('*');
            $sql->limit($limit, (($p - 1) * $limit));
            $result = Db::getInstance()->executeS($sql->build());
        } else {
            $sql->select('COUNT(id_cdek_logger)');
            $result = (int)Db::getInstance()->getValue($sql->build());
        }

        return $result;
    }

    public static function getMethods()
    {
        $sql = new DbQuery();
        $sql->select('method');
        $sql->from('cdek_logger');
        $sql->groupBy('method');
        $result = Db::getInstance()->executeS($sql->build());
        return (is_array($result) ? $result : array());
    }
}