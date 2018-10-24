<?php

class PwBackFormAjaxModuleFrontController extends ModuleFrontController
{

    /**
     * @var array
     */
    public $errors = array();

    /**
     * @var array
     */
    public $response = array();

    /**
     * @var array
     */
    public $fields = array();

    /**
     * @throws PrestaShopException
     */
    public function init() {
        parent::init();
    }

    /**
     *
     */
    public function initContent()
    {
        parent::initContent();

        $this->fields = array(
            'name'      => $this->module->l('Имя: '),
            'email'     => $this->module->l('Почта: '),
            'message'   => $this->module->l('Сообщение: '),
        );

        $response = $this->sendRequestData();

        $this->ajaxDie(Tools::jsonEncode($response));
    }

    /**
     *
     */
    public function sendRequestData()
    {
        $data = Tools::getAllValues();

        /*if ( empty($data['email'])
            || !Validate::isEmail($data['email']))
        {
            $this->addError('email', $this->module->l('Не корректный E-mail адрес'));
        }*/

        if ( empty($data['phone']) ||
            !Validate::isPhoneNumber($data['phone']))
        {
            $this->addError('phone', $this->module->l('Не корректный номер телефона'));
        }

        if ( empty($data['message']) ||
            strlen($data['message']) < 10)
        {
            $this->addError('message', $this->module->l('Минимальная длина сообщения 10 символов'));
        }
        
        if ($this->issetErrors()) {

            $this->response = array(
                'status' => 'error',
                'errors' => $this->getErrors()
            );

            $this->ajaxDie(Tools::jsonEncode($this->response));
        }

        $subject    = Configuration::get('PW_BF_CAPTION');
        $emails     = $this->prepareEmails(Configuration::get('PW_BF_EMAILS'));
        $mailParams = $this->prepareMailParams($data);

        foreach ($emails as $email) {

            if ( !Validate::isEmail($email)) {
                continue;
            }

            if ( !Mail::Send(
                (int)$this->context->cookie->id_lang,
                $this->module->name,
                $subject,
                $mailParams,
                $email,
                'Магазин',
                null,  null, null, null,
                $this->module->getLocalPath() . 'mails/', true)
            ) {
                $this->addError('internal', $this->module->l('Не удалось зарегистрировать запрос'));
            }
        }

        if ($this->issetErrors()) {
            $this->response = array(
                'status' => 'error',
                'errors' => $this->getErrors()
            );
        } else {
            $this->response = array(
                'status'    => 'success',
                'message'   => Configuration::get('PW_BF_MESSAGE')
            );
        }

        $this->ajaxDie(Tools::jsonEncode($this->response));
    }

    /**
     * @param $emails
     * @return array
     */
    public function prepareEmails($emails)
    {
        $result = array();
        $emails = preg_split('/\\r\\n?|\\n/', $emails);

        foreach ($emails as $email) {
            if ($email !== '' && Validate::isEmail($email)) {
                $result[] = trim($email);
            }
        }
        return $result;
    }

    /**
     * @param array $params
     * @return array
     */
    public function prepareMailParams(array $params)
    {
        $result = array();
        foreach ($params as $key => $value) {
            $result['{'.$key.'}'] = $value;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function issetErrors()
    {
        return !empty($this->errors) ? true : false;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $field
     * @param $error
     */
    public function addError($field, $error)
    {
        $this->errors[$field] = $error;
    }
}
