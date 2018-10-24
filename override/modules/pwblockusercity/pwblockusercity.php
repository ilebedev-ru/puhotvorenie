<?php

class PWblockusercityOverride extends PWblockusercity
{
   
    public function hookregionSelect()
    {
        if(!isset($this->context->cookie->city) || empty($this->context->cookie->city)){
            if(empty($this->userInfo->city->name_ru)){
                $city = Configuration::get('PS_PWBLOCKUSERCITY_CITY');
            }else{
                $city = $this->userInfo->city->name_ru;
            }
            $this->context->cookie->city =  $city;
            $this->context->cookie->write();
        }else{
            $city = $this->context->cookie->city;
        }

        $this->smarty->assign($this->getConfig($city));
        return $this->display(__FILE__, '/views/templates/front/pwblockusercity.tpl');
    }
    
    public function hookTop($params)
    {
        return;
    }
}
