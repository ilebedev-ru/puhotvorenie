<?php
include_once('PWModuleFrontController.php');
class PwdeveloperGeneratorModuleFrontController extends PWModuleFrontController {
	
	public $errors;
	
    public function initContent() 
	{
        parent::initContent();
		$this->setTemplate('generator.tpl');
		$this->context->smarty->assign(array(
			'errors' => $this->errors,
		));
    }
	
	public function postProcess()
	{
		if(Tools::isSubmit('addCustomer')){
			$this->addTestCustomer();
		}
		if(Tools::isSubmit('addProduct')){
			$this->addProduct();
		}
	}
	
	protected function addProduct()
	{
		$testProducts = unserialize(Configuration::get('PW_TEST_PRODUCTS'));
		if(!is_array($testProducts))
			$testProducts = array();
		$product = new Product();
		$name = 'Тестовый продукт '.count($product);
		$product->name = PWTools::createMultiLangField($name);
		$product->quantity = 100;
		$product->link_rewrite = PWTools::createMultiLangField(Tools::link_rewrite($name));
		$product->price = rand(100, 10000);
		$homecat = Category::getRootCategory();
		$product->id_category_default = $homecat->id;

		$product->description = PWTools::createMultiLangField('Lorem Ipsum - это текст-"рыба", часто используемый в печати и вэб-дизайне. Lorem Ipsum является стандартной "рыбой" для текстов на латинице с начала XVI века. В то время некий безымянный печатник создал большую коллекцию размеров и форм шрифтов, используя Lorem Ipsum для распечатки образцов. Lorem Ipsum не только успешно пережил без заметных изменений пять веков, но и перешагнул в электронный дизайн. Его популяризации в новое время послужили публикация листов Letraset с образцами Lorem Ipsum в 60-х годах и, в более недавнее время, программы электронной вёрстки типа Aldus PageMaker, в шаблонах которых используется Lorem Ipsum.');
		$product->add();
		$product->addToCategories(array($homecat));
		$testProducts[] = $product->id;
		Configuration::updateValue('PW_TEST_PRODUCTS', serialize($testProducts));
		// if($_FILES['image']['name']) addProductImage($product, 'auto', 'image');
		// if($_FILES['image2']['name']) addProductImage($product, 'auto', 'image2');
		// if($_FILES['image3']['name']) addProductImage($product, 'auto', 'image3');
		// if($_FILES['image4']['name']) addProductImage($product, 'auto', 'image4');
		$this->errors[] = 'Добавлен тестовый продукт';
	}
	
	protected function addTestCustomer()
	{
		if($cust = Customer::getByEmail('test@prestaweb.ru')){
			$this->errors[] = 'Пользователь test@prestaweb.ru был создан ранее';
			$this->authTest();
			return false;
		}
		$customer = new Customer();
		$customer->firstname = 'Test';
		$customer->lastname = 'Prestaweb';
		$customer->email = 'test@prestaweb.ru';
		$customer->passwd = Tools::encrypt('12345678');
		if($customer->validateFields(true)){
			$customer->save();
			$this->addTestAddress($customer);
			$this->errors[] = 'Пользователь test@prestaweb.ru создан. Пароль: 12345678';
		}
		$this->authTest();
	}
	
	protected function addTestAddress($customer)
	{
		$address = new Address();
		$address->alias = "тестовый адрес";
		$address->phone_mobile = '79058024377';
		$address->lastname = $customer->lastname;
		$address->firstname = $customer->firstname;
		$address->phone = '79058024377';
		$address->id_customer = intval($customer->id);
		$address->city = 'Москва';
		$address->id_country = 177;
		$address->id_state = 743;
		$address->other = 'Тестовый адрес Prestaweb.ru';
		$address->postcode = null;
		$address->company = 'AltoPromo';
		$address->address1 = 'Ленина 57А корпус 7';
		$address->address2 = '';
		$this->errors = array_unique(array_merge($this->errors, $address->validateFieldsRequiredDatabase()));
		if (!$address->add()) {
			$this->errors[] = Tools::displayError('Возникла ошибка при создании адреса...');
			return false;
		}
		$this->errors[] = 'Тестовый адрес успешно создан';
	}
	
	protected function authTest()
	{
		$customer = new Customer();
		$customer->getByEmail('test@prestaweb.ru');
		if(Validate::isLoadedObject($customer)){
			$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
			$this->context->cookie->id_customer = (int)($customer->id);
			$this->context->cookie->customer_lastname = $customer->lastname;
			$this->context->cookie->customer_firstname = $customer->firstname;
			$this->context->cookie->logged = 1;
			$customer->logged = 1;
			$this->context->cookie->is_guest = $customer->isGuest();
			$this->context->cookie->passwd = $customer->passwd;
			$this->context->cookie->email = $customer->email;
			$this->context->customer = $customer;
			if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id))
				$this->context->cart = new Cart($id_cart);
			else
			{
				$this->context->cart->id_carrier = 0;
				$this->context->cart->setDeliveryOption(null);
				$this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
				$this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));
			}
			$this->context->cart->id_customer = (int)$customer->id;
			$this->context->cart->secure_key = $customer->secure_key;
			$this->context->cart->save();
			$this->context->cookie->id_cart = (int)$this->context->cart->id;
			$this->context->cookie->write();
			$this->context->cart->autosetProductAddress();
		}
	}
 
}