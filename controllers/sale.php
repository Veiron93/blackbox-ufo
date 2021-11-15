<?php

/**
 * UFO CMF
 *
 * PHP application framework
 *
 * @package		UFO CMF
 * @copyright		(c) 2013, Rinamika, http://rinamika.ru
 * @author			Viktor Suprun
 * @since			1.0
 * @license		http://rinamika.ru/ufo/licence.txt Rinamika Application License
 * @filesource
 */

/**
 * Class Catalog
 */
class Sale extends App_Controller {

	public function index() {

		$sorting = 'p.sort_order';
		$partsURI = parse_url($_SERVER['REQUEST_URI']);

		if(isset($partsURI['query'])){
			parse_str($partsURI['query'], $query);

			if(isset($query['order'])){
				switch($query['order']){
					case 'new':
						$sorting = 'p.id desc';
					break;

					case '-price':
						$sorting = 'p.price desc';
					break;

					case 'price':
						$sorting = 'p.price asc';
					break;
				}
			}
		}

		// get products
		$products = Db_DbHelper::objectArray("SELECT p.id, p.name, p.description, p.price, p.old_price, p.regular_photo, p.category_id
			FROM catalog_products as p
			WHERE p.is_sale is not null and p.hidden is null 
			ORDER BY $sorting");

		// ids products
		$productsIds = [];

		foreach($products as $product) array_push($productsIds, $product->id);

		// get photos to products
		$photos = Db_DbHelper::objectArray("SELECT id, disk_name, master_object_id 
			FROM db_files
			WHERE master_object_class ='Catalog_Product' and master_object_id in (".implode(',', $productsIds).") order by sort_order");

		// add photo in product
		foreach($products as $product){
			$product->photos = [];

			foreach($photos as $photo){
				if($product->id == $photo->master_object_id) array_push($product->photos, $photo);
			}
		}

		$this->viewData['products'] = $products;
	}
}
