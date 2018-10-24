<?php

require_once(dirname(__FILE__) . '/AbstractEditor.php');

/**
 * вообще изначально писался только под index и editorial, но сейчас по сути грейженая версия commoneditor-а
 */
class IndexEditor extends AbstractEditor {

	/**
	 * Конфиг сущностей
	 * @var array
	 */
	protected $configEntities;

//	protected $entity;

    protected $entitys = array();
    protected $ids = array();
    protected $models = array();
    
    public $multiclass = true;

	public function __construct() {
		parent::__construct();

		$this->configEntities = require(dirname(__FILE__) . '/../../entities_config.php');
	}

	/**
	 * @inheritdoc
	 */
	public function getEditor() {
		//определение - подходит ли данный класс для редактирования сущности

		$idEntity = $this->getIdEntityByContext();

		$allConfig = $this->getCommonConfig();
		if (isset($allConfig[$idEntity]) && !isset($allConfig[$idEntity]['class'])) {
            foreach($allConfig[$idEntity] as $key => $config){
                if(isset($config['default_id'])){
                    $idItem = $config['default_id'];
                }elseif(isset($config['id']) && $config['id'] == 'meta'){
                    $meta = Meta::getMetaByPage($idEntity, $this->id_lang);
                    $idItem = $meta['id_meta'];
                }else{
                    $idItem = $this->getIdItemByRequest($idEntity, $config);
                }
                if ($idItem !== false) {
                    $this->ids[$key] = $idItem;
                }
            }
			if (count($this->ids)) {
                $this->entityConfig = $allConfig[$idEntity];
                $this->idEntity = $idEntity;
				return $this;
			}
		}

		return $this->getNext()->getEditor();
	}

	public function getName() {
		return 'Index';
	}
    
    public function getIdItem() {
        if (isset($this->models[$this->getIdEntity()]->id)) {
            return $this->models[$this->getIdEntity()]->id;
        }
		return $this->id;
	}
    
    public function setFieldValue($fieldId, $fieldValue) {
        if(empty($this->model)) $this->load();

        $model = $this->models[$this->getIdEntity()];

		if (property_exists($model, $fieldId) === false) {
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
			$model->{$fieldId}[$this->id_lang] = $fieldValue;
		}
		else {
			$model->{$fieldId} = $fieldValue;
		}
        $model->update();
		return true;
	}
    
    public function load() {
        if (empty($this->ids)) {
            $this->ids[$this->getIdEntity()] = $this->id;
        }
        foreach($this->ids as $key => $id){
            $classname = $this->entityConfig[$key]['class'];
            if(isset($this->entityConfig[$key]['require']) && file_exists($this->entityConfig[$key]['require'])){
                require_once $this->entityConfig[$key]['require'];
            }
            if (!class_exists($classname)) {
                continue;
                //throw new EditorException('Отсутствует класс модели ' . $classname);
            }
            $reflection = new ReflectionClass($classname);
            if ($reflection->isSubclassOf('ObjectModelCore') === false) {
                continue;
                // throw new EditorException('Класс модели ' . $classname . ' не является типом ObjectModel');
            }
            
            $model = $reflection->newInstance($id);
            if (Validate::isLoadedObject($model) === false) {
                continue;
                // throw new EditorException('Модель для сущности (класс  ' . $classname . ') не загружена');
            }
            if($model->id)
                $this->models[$key] = $model;
        }
        return (bool)count($this->models);
	}
    
    public function getFields() {
        $fields = array();
        foreach($this->ids as $key => $id){
            $field = $this->entityConfig[$key];
            $field['id'] = $id;
            $fields[$key] = $field;
        }
        return $fields;
	}
    
    public function getFieldValue($fieldId) {
        $value = '';
        foreach($this->models as $key => $model){
            if (property_exists($model, $fieldId) === false) {
                //если поле отсутствует у модели
                if (isset($this->entityConfig[$key]['fields'][$fieldId]['alias_for'])) {
                    //но для него есть алиас, то подставляем алиас как имя поля
                    $fieldId = $this->entityConfig[$key]['fields'][$fieldId]['alias_for'];
                }
                else {
                    continue;
                }
            }

            if(isset($this->entityConfig[$key]['fields'][$fieldId]['lang']) && $this->entityConfig[$key]['fields'][$fieldId]['lang']) {
                if (isset($model->{$fieldId}[$this->id_lang])) {
                    return $model->{$fieldId}[$this->id_lang];
                }
                else {
                    return '';
                }
            }else{
                return $model->$fieldId;
            }
        }
		return '';
	}
    
    public function save() {
        $ids = Tools::getValue('entityId');
        foreach(Tools::getValue('entity') as $i => $entity){
            $this->ids[$entity] = $ids[$i];
        }
        $this->load();
        foreach($this->getFields() as $key => $config) {
            $model = $this->models[$key];
            
            foreach($config['fields'] as $fieldId => $fieldParams){
                if(Tools::isSubmit($fieldId)){
                    if (property_exists($model, $fieldId) === false) {
                        //если поле отсутствует у модели
                        if ($this->entityConfig[$key]['fields'][$fieldId]['alias_for']) {
                            //но для него есть алиас, то подставляем алиас как имя поля
                            $fieldId = $this->entityConfig[$key]['fields'][$fieldId]['alias_for'];
                        }
                        else {
                            throw new EditorException('Поле ' . $fieldId . ' у модели отсутствует');
                        }
                    }


                    if(!empty($this->entityConfig[$key]['fields'][$fieldId]['lang'])) {
                        $model->{$fieldId}[$this->id_lang] = Tools::getValue($fieldId);
                    }
                    else {
                        $model->{$fieldId} = Tools::getValue($fieldId);
                    }
                }
            }
        }
        foreach($this->models as $key => $model){
            if($model->validateFields(false, true) !== true){
                continue;
            }
            $model->save();
        }
        return true;
    }
    
    public function getStatus($withName = true){
        if(isset($this->models[$this->getIdEntity()]->active)) {
            return ($this->models[$this->getIdEntity()]->active ? ($withName ? 'Скрыть' : 1) : ($withName ? 'Включить' : 0));
        }
		return ($withName ? 'не определено' : 0);
	}

}
