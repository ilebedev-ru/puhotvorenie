<?php

require_once(dirname(__FILE__) . '/AbstractEditor.php');

/**
 *
 */
class OtherEditor extends AbstractEditor {

	/**
	 * @inheritdoc
	 */
	public function getEditor() {
		//определение - подходит ли данный класс для редактирования сущности

		$idEntity = $this->getIdEntityByContext();

		$meta = Meta::getMetaByPage($idEntity, $this->id_lang);

		$allConfig = $this->getCommonConfig();

		if (!empty($meta)) {
			$idEntity = 'other';

			$this->init($idEntity, $meta['id_meta']);

			return $this;
		}

		return $this->getNext()->getEditor();
	}

	public function getName() {
		return 'Other';
	}

}