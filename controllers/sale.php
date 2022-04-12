<?php

class Sale extends App_Controller {

	public function index() {

		$sorting = 'catalog_products.sort_order';
		$partsURI = parse_url($_SERVER['REQUEST_URI']);

		if(isset($partsURI['query'])){
			parse_str($partsURI['query'], $query);

			if(isset($query['order'])){
				switch($query['order']){
					case 'new':
						$sorting = 'catalog_products.id desc';
						break;

					case '-price':
						$sorting = 'catalog_products.price desc';
						break;

					case 'price':
						$sorting = 'catalog_products.price asc';
						break;
				}
			}
		}

		$where = "catalog_products.leftover > 0 && catalog_products.old_price is not null && catalog_products.deleted is null && catalog_products.hidden is null";

		$this->viewData['products'] = Catalog_Product::create()->where($where)->order($sorting)->find_all();
	}
}
