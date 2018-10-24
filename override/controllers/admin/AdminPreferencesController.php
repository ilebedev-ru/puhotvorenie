<?php

class AdminPreferencesController extends AdminPreferencesControllerCore
{
    public function __construct()
    {
        parent::__construct();
        $this->fields_options['general']['fields']['PS_SSL_ENABLED']['type'] = 'bool';
        unset($this->fields_options['general']['fields']['PS_SSL_ENABLED']['disabled']);
    }
}