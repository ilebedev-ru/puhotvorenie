<?php

class PwBackCallAjaxModuleFrontController extends ModuleFrontController
{
    /**
     * @var array
     */
    public $emails = array();

    /**
     * @var int
     */
    public $formExtended = 0;

    /**
     * @var array
     */
    public $response = array(
        'status' => 1
    );

    /**
     * Шаблон формы
     *
     * @var string
     */
    public $template = 'pwbackcall';

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

        /*
        if (!$this->isXmlHttpRequest()) {
            // добавить 404 ошибку
            $this->ajaxDie('access denied');
        }*/

        $emails = Configuration::get('PW_BACK_EMAILS');
        $this->emails = $this->module->prepareEmails($emails);

        $input = Tools::getAllValues();

        switch ($input['action']) {
            case 'call':
                $this->formExtended = (int)Configuration::get('PW_BACK_CALL_FROM_EXTENDED');
                $this->sendCallNotify($input);
                break;
            case 'question':
                $this->sendQuestNotify($input);
                break;
        }
    }

    /**
     * Отправка формы задать вопрос
     *
     * @param $data
     */
    public function sendQuestNotify($data)
    {
        //TODO
    }

    /**
     * Отправка формы обратного звонка
     *
     * @param $data
     */
    public function sendCallNotify($data)
    {
        $mailParams = array();

        $this->template = 'pwbackcall';

        if ($this->formExtended) {

            $this->template = 'pwbackcall_extended';
        
            /*if ( !isset($data['name']) || $data['name'] == '') {
                $this->addError('name', 'Введите имя');
            }*/

            /*if ( !isset($data['email']) || !Validate::isEmail($data['email']))
            {
                $this->addError('email', 'Не корректный E-mail адрес');
            }*/
        }

        if ( !isset($data['phone']) || $data['phone'] == '' || !Validate::isPhoneNumber($data['phone'])) {
            $this->addError('phone', 'Не корректный номер телефона');
        }
        
        if ($this->isErrors()) {
            $this->ajaxDie(Tools::jsonEncode($this->response));
        }
        
        $mailParams = $this->prepareMailParams($data);

        $subject = Configuration::get('PW_BACK_FORM_CAPTION');
        $subject = !empty($subject) ? $subject : 'Обратный звонок';

        foreach ($this->emails as $email) {

            if ( !Mail::Send(
                (int)$this->context->cookie->id_lang,
                $this->template,
                $subject,
                $mailParams,
                $email,
                'Магазин',
                null,  null, null, null,
                $this->module->getLocalPath() . 'mails/', true)
            ) {
                $this->addError('Не удалось зарегистрировать запрос');
                $this->ajaxDie(Tools::jsonEncode($this->response));
            }
        }

        $this->ajaxDie(Tools::jsonEncode($this->response));
    }

    /**
     * @param $field
     * @param $error
     */
    public function addError($field, $error)
    {
        if ($this->response['status'] == 1) {
            $this->response['status'] = 0;
        }
        $this->response['errors'][$field] = $error;
    }

    /**
     * @return bool
     */
    public function isErrors()
    {
        return !empty($this->response['errors']) ? true : false;
    }

    /**
     * @param $data
     * @return array
     */
    public function prepareMailParams($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $result['{'.$key.'}'] = $value;
        }
        return $result;
    }
}