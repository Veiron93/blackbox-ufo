<?
	class App_Controller extends Core_FrontendController{
		public $menuItems;
		public $catalog;
		public static $statusUpdateCart = false;
	    
		public function __construct()
		{
			$this->menuItems = new PKMenu_Items();

			// Cart
			self::$statusUpdateCart = checkActualProductsCart();

			$this->viewData['idsProductsCart'] = getProductsAddedCart();

			$this->catalog = new App_Catalog();
			$this->viewData['catalogRootCategories'] = $this->catalog::$catalogRootCategories;

			parent::__construct();
		}
	}
?>