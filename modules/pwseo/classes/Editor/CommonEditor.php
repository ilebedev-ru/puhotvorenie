<?php

require_once(dirname(__FILE__) . '/AbstractEditor.php');

/**
 *
 */
class CommonEditor extends AbstractEditor {

	/**
	 * Конфиг сущностей
	 * @var array
	 */
	protected $configEntities;

//	protected $entity;

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

		if (isset($allConfig[$idEntity])) {
			$idItem = $this->getIdItemByRequest($idEntity, $allConfig[$idEntity]);
			if ($idItem !== false) {
				$this->init($idEntity, $idItem);

				return $this;
			}
		}

		return $this->getNext()->getEditor();
	}

	public function getName() {
		return 'Common';
	}

}
