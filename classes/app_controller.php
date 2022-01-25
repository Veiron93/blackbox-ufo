<?
	class App_Controller extends Core_FrontendController{
		public $menuItems;
		public static $statusUpdateCart = false;
	    
		public function __construct()
		{
			$this->menuItems = new PKMenu_Items();

			// Cart
			self::$statusUpdateCart = checkActualProductsCart();
			$this->viewData['idsProductsCart'] = getProductsAddedCart();

			parent::__construct();
		}
	}
?>