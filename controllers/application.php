<?php

class Application extends App_Controller  {

	public function index() {
		// $this->layout = 'index';

		$this->viewData['bestsellers'] = Catalog_Product::create()->where('best_seller = ?', [1])->order('id desc')->limit(4)->find_all();

		$this->viewData['newProducts'] = Catalog_Product::create()->where('is_new = ?', [1])->order('id desc')->limit(6)->find_all();
	}
}
