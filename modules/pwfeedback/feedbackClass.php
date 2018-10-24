<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class feedbackClass extends ObjectModel
{
	public $id;
	public $id_feedback;
	public $name;
	public $email;
	public $feedback;
	public $answer;
	public $status;
    public $rating;
	public $fb;
	public $twitter;
	public $vk;
	public $odk;
	public $youtube;
	public $date_add;
    const RATING_COUNT = 5; //Count of available rating


    /**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'pwfeedback',
		'primary' => 'id_feedback',
		'multilang' => false,
		'fields' => array(
			//'id_shop' =>				array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
			'name' =>				array('type' => self::TYPE_STRING,'required' => true, 'validate' => 'isGenericName'),
			'feedback' =>			array('type' => self::TYPE_STRING,'required' => true, 'feedback' => 'isString'),
			'answer' =>				array('type' => self::TYPE_HTML, 'validate' => 'isString'),
			'email' =>				array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
			'status' =>				array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'rating' =>				array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'fb' =>					array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'twitter' =>			array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'vk' =>					array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'odk' =>				array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'youtube' =>			array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'date_add' =>			array('type' => self::TYPE_DATE)
		)
	);

	public function copyFromPost()
	{
		/* Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		/* Multilingual fields */
		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
	}
	
	public static function displayFieldName($field, $class = __CLASS__, $htmlentities = true, Context $context = null)
	{
		global $_FIELDS_MODULE;
			
		if ($_FIELDS_MODULE === null && file_exists(dirname(__FILE__).'/translations/fields_'.Context::getContext()->language->iso_code.'.php'))
			include_once(dirname(__FILE__).'/translations/fields_'.Context::getContext()->language->iso_code.'.php');	

		$key = $field;
		return ((is_array($_FIELDS_MODULE) && array_key_exists($key, $_FIELDS_MODULE)) ? ($htmlentities ? htmlentities($_FIELDS_MODULE[$key], ENT_QUOTES, 'utf-8') : $_FIELDS_MODULE[$key]) : $field);
	}
	
	public function validateController($htmlentities = true)
	{
		$errors = array();
		$required_fields_database = (isset(self::$fieldsRequiredDatabase[get_class($this)])) ? self::$fieldsRequiredDatabase[get_class($this)] : array();
		foreach ($this->def['fields'] as $field => $data)
		{
			$value = Tools::getValue($field, $this->{$field});		
			// Check if field is required by user
			if (in_array($field, $required_fields_database))
				$data['required'] = true;
			
			// Checking for required fields
			if (isset($data['required']) && $data['required'] && empty($value) && $value !== '0')
				if (!$this->id || $field != 'passwd')
					$errors[$field] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).'</b> '.Tools::displayError('is required.');

			// Checking for maximum fields sizes
			if (isset($data['size']) && !empty($value) && Tools::strlen($value) > $data['size'])
				$errors[$field] = sprintf(
					Tools::displayError('%1$s is too long. Maximum length: %2$d'),
					self::displayFieldName($field, get_class($this), $htmlentities),
					$data['size']
				);

			// Checking for fields validity
			// Hack for postcode required for country which does not have postcodes
			if (!empty($value) || $value === '0' || ($field == 'postcode' && $value == '0'))
			{
				if (isset($data['validate']) && !Validate::$data['validate']($value) && (!empty($value) || $data['required']))
					$errors[$field] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).'</b> '.Tools::displayError('is invalid.');
				else
				{
					if (isset($data['copy_post']) && !$data['copy_post'])
						continue;
					if ($field == 'passwd')
					{
						if ($value = Tools::getValue($field))
							$this->{$field} = Tools::encrypt($value);
					}
					else
						$this->{$field} = $value;
				}
			}
		}
		return $errors;
	}

    /**
     * For control count of raitings
     */
    public static function getRatings()
    {
        $result = Array();
        for($i=1;$i<=self::RATING_COUNT;$i++){
            $result[] = Array(
                'id' => 'rating_'.$i,
                'value' => $i,
                'label' => $i
            );
        }
        return $result;
    }

    /**
     * @param int $count
     * @param bool $onlyModerated
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getFeedbacks($count = 4, $onlyModerated = true){
        $feedbacks = Db::getInstance()->ExecuteS('
			 SELECT * FROM `'._DB_PREFIX_.'pwfeedback`
			 WHERE 1 '.($onlyModerated ? ' AND status = 1 ' : '').'
			 ORDER BY `date_add` DESC
			 LIMIT '.(int)$count);
        foreach($feedbacks as &$feedback){
            if(file_exists(_PS_MODULE_DIR_.'pwfeedback/photo/feedback-'.$feedback['id_feedback'].'.jpg')) $feedback['image'] = '/modules/pwfeedback/photo/feedback-'.$feedback['id_feedback'].'.jpg';
            else $feedback['image'] = '/modules/pwfeedback/photo/default.jpg';
        }
        return $feedbacks;
    }
}
