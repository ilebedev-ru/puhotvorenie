<?php

require_once(dirname(__FILE__) . '/AbstractEditor.php');

/**
 *
 */
class CmsCategoryEditor extends AbstractEditor {

	/**
	 * @inheritdoc
	 */
	public function getEditor() {
		//определение - подходит ли данный класс для редактирования сущности

		$idEntity = $this->getIdEntityByContext();

		$allConfig = $this->getCommonConfig();

		if ($idEntity === 'cms') {
			if ((int)Tools::getValue('id_cms_category')) {
				$idEntity = 'cmscategory';
			}
			else {
				$idEntity = 'cmspage';
			}

			$this->init($idEntity, $this->getIdItemByRequest($idEntity, $allConfig[$idEntity]));

			return $this;
		}

		return $this->getNext()->getEditor();
	}

	public function getName() {
		return 'CmsCategory';
	}

}