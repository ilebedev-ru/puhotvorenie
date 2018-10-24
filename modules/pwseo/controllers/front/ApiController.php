<?php

require_once(dirname(__FILE__) . '/../../classes/Editor/EditorChainBuilder.php');

class ApiController {

	const EDIT_COMMAND = 'entity_edit';

	protected $secureKey;

	protected $response = [
		'code'    => null,
		'data'    => [],
		'message' => '',
	];

	public function __construct() {
		$this->secureKey = Configuration::get('apiSecretKey');
	}

	public function run() {
		$command = Tools::getValue('command');
		//data вытаскиваем напрямую из $_POST, т.к. престовская функция Tools::getValue почему-то ломает UTF-8 в json-е
		$data = isset($_POST['data']) ? json_decode($_POST['data'], true) : null;
		$hash = Tools::getValue('hash');


		if (!$this->checkHash($hash, $data)) {
			$this->response['code']    = 500;
			$this->response['message'] = 'Не совпадает хэш-подпись запроса';
		}
		else {

			switch ($command) {
				case static::EDIT_COMMAND:

					$this->actionEdit($data);

					break;

				default:

					$this->response['code']    = 1;
					$this->response['message'] = 'Неизвестная команда';

					break;
			}
		}

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($this->response, JSON_UNESCAPED_UNICODE);
	}

	protected function actionEdit($data) {
		$this->response['code'] = 0;
		//		$this->response['data'] = ['qwe' => 'asd'];
		//		$this->response['message'] = 'ok';

		$_SERVER['REQUEST_URI'] = $data['sitePageUrl'];

		$dispatcher = Dispatcher::getInstance();
		//		$dispatcher->dispatch();
		$controller_ = mb_strtolower($dispatcher->getController());

		$controllers = Dispatcher::getControllers(array(_PS_FRONT_CONTROLLER_DIR_, _PS_OVERRIDE_DIR_.'controllers/front/'));
		$controllerClass = $controllers[$controller_];

		if (class_exists($controllerClass) === false) {
			$this->response['code']    = 1;
			$this->response['message'] = 'Неизвестная страница';

			return;
		}

		$controllerInst = new $controllerClass();

		try {
			$editor = EditorChainBuilder::buildChain()->getEditor();
		}
		catch (EditorNextException $e) {
			$this->response['data']['ex'] = $e->getMessage();

			return false;
		}

		try {
			$editor->load();

			$editor->setFieldValue($data['fieldId'], $data['value']);

			$res = $editor->save();

			$this->response['code'] = ($res ? 0 : 1);
		}
		catch (EditorException $e) {
			$this->response['code']    = 1;
			$this->response['message'] = $e->getMessage();
		}
	}

	protected function checkHash($hash, $data) {
		return $hash === sha1(sha1(serialize($data) . $this->secureKey));
	}

}