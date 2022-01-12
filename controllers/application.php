<?php

class Application extends App_Controller  {

	public function index() {
		// $this->layout = 'index';
		$this->viewData['sale'] = Catalog_Product::create()->where('is_sale = ? and leftover > 0', [1])->order('id desc')->limit(5)->find_all();
		$this->viewData['bestsellers'] = Catalog_Product::create()->where('best_seller = ? and leftover > 0', [1])->order('id desc')->limit(5)->find_all();
		$this->viewData['newProducts'] = Catalog_Product::create()->where('is_new = ? and leftover > 0', [1])->order('id desc')->limit(12)->find_all();
	}
}
