<?
class App_Controller extends Core_FrontendController
{
	public $menuItems;
	public static $statusUpdateCart = false;

	// CATALOG
	public $catalog;

	public function __construct()
	{
		$this->menuItems = new PKMenu_Items();

		// CART
		self::$statusUpdateCart = checkActualProductsCart();
		$this->viewData['idsProductsCart'] = getProductsAddedCart();

		// CATALOG
		$this->catalog = new App_Catalog();
		$this->viewData['catalogRootCategories'] = $this->catalog::getRootCategories();

		parent::__construct();
	}
}
