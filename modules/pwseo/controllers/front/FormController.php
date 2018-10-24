<?php

require_once(dirname(__FILE__) . '/../../classes/Editor/EditorChainBuilder.php');

class FormController {

	public function run() {
		header('Content-type: application/json');
		$action = Tools::getValue('action');

		$result = array();

		if(method_exists($this, 'action' . $action)) {
			$result = $this->{'action' . $action}();
		}

		return json_encode($result);
	}

	public function actionSend() {

		$result = array(
			'success' => false,
			'message' => '',
		);

		$editorName = Tools::getValue('editor_name');
		$idEntity = Tools::getValue('id_entity');
		$idItem = Tools::getValue('id_item');

		$editor = AbstractEditor::getEditorByName($editorName);


		try {
			//			$editor = EditorChainBuilder::buildChain()->getEditor(Tools::getValue('id_entity'), Tools::getValue('id_item'));
			$editor->init($idEntity, $idItem);
			$editor->load();

		} catch (EditorException $ex) {
			$result['success'] = false;
			$result['message'] = 'Данную страницу невозможно отредактировать.';
		}

		foreach($editor->getFields() as $fieldId => $fieldParams) {
		    if(Tools::isSubmit($fieldId))
			    $editor->setFieldValue($fieldId, Tools::getValue($fieldId));
		}

		try {
			$editor->save();

			$result['success'] = true;

		} catch (EditorException $ex) {
			$result['success'] = false;
			$result['message'] = $ex->getMessage();
		}

		return $result;
	}

	public function actiontoogleStatus(){
        $result = array(
            'success' => false,
            'message' => '',
        );

        $editorName = Tools::getValue('editor_name');
        $idEntity = Tools::getValue('id_entity');
        $idItem = Tools::getValue('id_item');

        $editor = AbstractEditor::getEditorByName($editorName);


        try {
            //			$editor = EditorChainBuilder::buildChain()->getEditor(Tools::getValue('id_entity'), Tools::getValue('id_item'));
            $editor->init($idEntity, $idItem);
            $editor->load();

        } catch (EditorException $ex) {
            $result['success'] = false;
            $result['message'] = 'Данную страницу невозможно отредактировать.';
        }

        $editor->setFieldValue('active',!$editor->getStatus(false));


        try {
            $editor->save();

            $result['success'] = true;

        } catch (EditorException $ex) {
            $result['success'] = false;
            $result['message'] = $ex->getMessage();
        }

        return $result;
    }

}