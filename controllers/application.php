<?php

class Application extends App_Controller  {

	public function index() {
		// $this->layout = 'index';
		$this->viewData['sale'] = Catalog_Product::create()->where('is_sale = ?', [1])->order('id desc')->limit(6)->find_all();
		$this->viewData['bestsellers'] = Catalog_Product::create()->where('best_seller = ?', [1])->order('id desc')->limit(5)->find_all();
		$this->viewData['newProducts'] = Catalog_Product::create()->where('is_new = ?', [1])->order('id desc')->limit(12)->find_all();
	}
}
