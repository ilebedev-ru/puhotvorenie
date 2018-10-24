<?php

require_once(dirname(__FILE__) . '/../../exceptions/EditorNextException.php');
require_once(dirname(__FILE__) . '/../../exceptions/EditorException.php');
require_once(dirname(__FILE__) . '/EditorInterface.php');

/**
 * Абстрактный класс редактора
 */
abstract class AbstractEditor implements EditorInterface {

	/**
	 * Следующий элемент
	 * @var AbstractEditor
	 */
	protected $next;

	protected $id_lang;

	/**
	 * Модель
	 * @var ObjectModelCore
	 */
	protected $model;

	protected $idEntity;

	protected $id;

	protected $entityConfig;

	public function __construct() {
		// $this->id_lang = Configuration::get('PS_LANG_DEFAULT');
		$this->id_lang = Context::getContext()->language->id;
	}

	public function init($idEntity, $idItem) {
		$allConfig = $this->getCommonConfig();
		$this->entityConfig = $allConfig[$idEntity];
		$this->idEntity = $idEntity;
		$this->id = $idItem;
	}

	/**
	 * @inheritdoc
	 */
	public function getIdEntity() {
		return $this->idEntity;
	}

	/**
	 * @inheritdoc
	 */
	public function getIdItem() {
		return $this->id;
	}

	public function setNext(AbstractEditor $editor) {
		$this->next = $editor;

		return $editor;
	}

	protected function getNext() {
		if ($this->next === null) {
			throw new EditorNextException('Редактор не найден');
		}

		return $this->next;
	}

	/**
	 * Получение редактора
	 * Анализирует текущую сущность и загружает нужный конфиг
	 * @return AbstractEditor
	 *
	 * @throws EditorNextException исключение в случае невозможности получить редактор
	 */
	abstract public function getEditor();

	/**
	 * Загрузка данных
	 * @throws EditorException
	 * @return bool
	 */
	public function load() {
		$classname = $this->entityConfig['class'];
		if ($this->id === null) {
			throw new EditorException('Id записи некорректный');
		}
		//сама загрузка

		//загружаем модель
        if(isset($this->entityConfig['require']) && file_exists($this->entityConfig['require'])){
            require_once $this->entityConfig['require'];
        }
		if (!class_exists($classname)) {
			throw new EditorException('Отсутствует класс модели ' . $classname);
		}

		$reflection = new ReflectionClass($classname);

		if ($reflection->isSubclassOf('ObjectModelCore') === false) {
			throw new EditorException('Класс модели ' . $classname . ' не является типом ObjectModel');
		}

		//2. создаём инстанс
		$model = $reflection->newInstance($this->id);

		//3. проверяем загруженность
		if (Validate::isLoadedObject($model) === false) {
			throw new EditorException('Модель для сущности (класс  ' . $classname . ') не загружена');
		}

		$this->model = $model;

		if($this->model->id) return true;
        return false;
	}

	/**
	 * Сохранение данных
	 * @throws EditorException
	 * @return bool
	 */
	public function save() {
		if ($this->id === null) {
			throw new EditorException('Id записи некорректный');
		}
		//сохранение

		$validateResult = $this->model->validateFields(false, true);

		if ($validateResult !== true) {
			throw new EditorException('Ошибка при сохранении модели: ' . $validateResult);
		}
		return $this->model->save();
	}

	public function getFields() {
		return $this->entityConfig['fields'];
	}

	public function getFieldValue($fieldId) {
		if (property_exists($this->model, $fieldId) === false) {
			//если поле отсутствует у модели
			if (!empty($this->entityConfig['fields'][$fieldId]['alias_for'])) {
				//но для него есть алиас, то подставляем алиас как имя поля
				$fieldId = $this->entityConfig['fields'][$fieldId]['alias_for'];
			}
			else {
                return false;
				// throw new EditorException('Поле ' . $fieldId . ' у модели отсутствует');
			}
		}

		if(isset($this->entityConfig['fields'][$fieldId]['lang']) && $this->entityConfig['fields'][$fieldId]['lang']) {
			if (isset($this->model->{$fieldId}[$this->id_lang])) {
				return $this->model->{$fieldId}[$this->id_lang];
			}
			else {
				return '';
			}
		}

		return $this->model->$fieldId;
	}
	
	public function setFieldValue($fieldId, $fieldValue) {
        if(empty($this->model)) $this->load();

		if (property_exists($this->model, $fieldId) === false) {
			//если поле отсутствует у модели
			if ($this->entityConfig['fields'][$fieldId]['alias_for']) {
				//но для него есть алиас, то подставляем алиас как имя поля
				$fieldId = $this->entityConfig['fields'][$fieldId]['alias_for'];
			}
			else {
				throw new EditorException('Поле ' . $fieldId . ' у модели отсутствует');
			}
		}


		if(isset($this->entityConfig['fields'][$fieldId]['lang']) && $this->entityConfig['fields'][$fieldId]['lang']) {
			$this->model->{$fieldId}[$this->id_lang] = $fieldValue;
		}
		else {
			$this->model->{$fieldId} = $fieldValue;
		}

		return true;
	}

	public function getStatus($withName = true){
		if(isset($this->model->active)) {
			return ($this->model->active ? ($withName ? 'Скрыть' : 1) : ($withName ? 'Включить' : 0));
		}
		return ($withName ? 'не определено' : 0);
	}

	/**
	 * Получить имя сущности по id файла точки входа (для PS 1.4)
	 * @return string
	 */
	protected function getIdEntityByContext() {
		if (class_exists('Dispatcher')) {
			$controllerName = get_class(Context::getContext()->controller);
			if(preg_match('/^([0-9a-z]*)Controller$/i', $controllerName, $res)) {
				return ltrim(strtolower(preg_replace('/[A-Z]/', '-$0', $res[1])), '-');
//				return mb_strtolower($res[1]);
			}
		}
		else {
			return preg_replace('/\.php$/', '', basename($_SERVER['PHP_SELF']));
		}
	}

	protected function getIdItemByRequest($idEntity, $entityConfig) {
		if (isset($entityConfig['id'])) {
			return Tools::getValue('id_' . $entityConfig['id']);
		}
		else {
			return Tools::getValue('id_' . $idEntity);
		}
	}

	/**
	 * Получить конфиг сущностей
	 * @return array
	 */
	protected function getCommonConfig() {
		return require(dirname(__FILE__) . '/../../entities_config.php');
	}

	abstract public function getName();

	/**
	 * @param string $name
	 * @return static
	 */
	public static function getEditorByName($name) {
		$className = $name . 'Editor';

		if (class_exists($className)) {
			return new $className();
		}
	}

}