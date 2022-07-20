<?php

class Application extends App_Controller  {

	public function index() {
		// $this->layout = 'index';

		// Товары со скидкой
		$this->viewData['productSale'] = $this->catalog::getProducts("cp.is_sale is not null", 6);

		// Хиты продаж
		$this->viewData['productBestsellers'] = $this->catalog::getProducts("cp.best_seller is not null", 6);

		// Новинки
		$this->viewData['productNew'] = $this->catalog::getProducts("cp.is_new is not null", 12);
	}


}
