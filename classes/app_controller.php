<?

	class App_Controller extends Core_FrontendController{
		public $menuItems;
	    
		public function __construct()
		{
			$this->menuItems = new PKMenu_Items();
			parent::__construct();
		}
	}

?>