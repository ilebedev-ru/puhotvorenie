<?php
class AdminStoresController extends AdminStoresControllerCore
{
    protected function _getDefaultFieldsContent()
    {
        $formFields = parent::_getDefaultFieldsContent(); // TODO: Change the autogenerated stub

        $formFields['PS_SHOP_PHONE'] =  array(
            'title' => $this->l('Телефон'),
            'validation' => 'isGenericName',
            'type' => 'text'
        );
        $formFields['PS_SHOP_PHONE2'] = array(
            'title' => $this->l('Второй телефон'),
            'validation' => 'isGenericName',
            'type' => 'text'
        );
        $formFields['PS_SHOP_WORKTIME'] = array(
            'title' => $this->l('Время работы'),
            'hint' => $this->l(''),
            'validation' => 'isGenericName',
            'type' => 'textarea',
            'cols' => 30,
            'rows' => 5
        );
        $formFields['PS_SHOP_VK'] = array(
            'title' => $this->l('Вконтакте'),
            'hint' => $this->l('Ссылка'),
            'validation' => 'isGenericName',
            'type' => 'text'
        );
        $formFields['PS_SHOP_FB'] = array(
            'title' => $this->l('Facebook'),
            'hint' => $this->l('Ссылка'),
            'validation' => 'isGenericName',
            'type' => 'text'
        );
        $formFields['PS_SHOP_INST'] = array(
            'title' => $this->l('Instagramm'),
            'hint' => $this->l('Ссылка'),
            'validation' => 'isGenericName',
            'type' => 'text'
        );
        unset($formFields['PS_SHOP_FAX']);
        return $formFields;
    }

}
