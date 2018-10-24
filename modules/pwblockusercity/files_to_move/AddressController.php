<?php

class AddressController extends AddressControllerCore{
	public function postProcess()
	{
		if (Tools::isSubmit('submitAddress'))
			$this->processSubmitAddress();
		else if (!Validate::isLoadedObject($this->_address) && Validate::isLoadedObject($this->context->customer))
		{
			$_POST['firstname'] = $this->context->customer->firstname;
			$_POST['lastname'] = $this->context->customer->lastname;
			$_POST['company'] = $this->context->customer->company;
			if(isset($this->context->cookie->city) && !empty($this->context->cookie->city)){
				$_POST['city'] = $this->context->cookie->city;
			}
		}
	}
}