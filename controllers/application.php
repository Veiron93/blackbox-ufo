<?php

class Application extends App_Controller  {

	public function index() {
		// $this->layout = 'index';
		
		$where = "catalog_products.leftover > 0 && catalog_products.deleted is null && catalog_products.hidden is null";

		// Товары со скидкой
		$this->viewData['sale'] = Catalog_Product::create()
			->where($where . '&& is_sale is not null')
			->order('id desc')
			->limit(5)
			->find_all();

		// Хиты продаж
		$this->viewData['bestsellers'] = Catalog_Product::create()
			->where($where . '&& best_seller is not null')
			->order('id desc')
			->limit(5)
			->find_all();

		// Новинки
		$this->viewData['newProducts'] = Catalog_Product::create()
			->where($where . '&& is_new is not null')
			->order('id desc')
			->limit(12)
			->find_all();
	}
}
