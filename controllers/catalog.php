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
class Catalog extends App_Controller {

	const productsPerPage = 40;

	public function index() {
		$categories = Catalog_Category::create()->list_root_children();
		$arrCategories = [];

		foreach($categories as $category){
			if(!$category->hidden){
				array_push($arrCategories, $category);
			}
		}

		$this->viewData['categories'] = $arrCategories;
		$this->setTitle("Каталог");
	}

	public function category($categoryId, $pageIndex = 1) {
		try {
			/* @var $category Catalog_Category */
			$this->viewData['category'] = $category = Catalog_Category::create()->find($categoryId);
			
			if (!$category) {
				$this->throw404();
			}

			$pagination = new Phpr_Pagination(self::productsPerPage);
			
			$this->viewData['category'] = $category;
			$this->viewData['products'] = $category->list_products(true, $pagination, $pageIndex - 1, self::sorting());
			$this->viewData['pagination'] = $pagination;

			$this->setTitle($category->name);
			Admin_SeoPlugin::apply($category);

		} catch (Exception $ex) {
			$this->viewData['error'] = $ex->getMessage();
		}
	}


	public function product($productId) {
		try {
			/* @var $product Catalog_Product */
			$product = Catalog_Product::create()->find($productId);
			
			if (!$product) {
				$this->throw404();
			}

			if(count($product->skus)){
				foreach($product->skus as $sku){
					$product_hidden = true;

					if($sku->leftover){
						$product_hidden = false;
						break;
					}
				}
			}
			
			if(isset($product_hidden) && !$product_hidden || !count($product->skus) && $product->leftover){
				$this->viewData['product'] = $product;
				$this->viewData['category'] = $product->category;
				$this->setTitle($product->name);
				Admin_SeoPlugin::apply($product);
			}else{
				$this->throw404();
			}

		} catch (Exception $ex) {
			$this->viewData['error'] = $ex->getMessage();
		}
	}


	public function all_products(){
		$this->viewData['allProducts'] = Catalog_Product::create()->where('catalog_products.leftover > 0 && catalog_products.deleted is null && catalog_products.hidden is null')->order('id desc')->find_all();
	}

	//СОРТИРОВКА
	public function sorting(){
		$sorting = 'sales desc';
		$partsURI = parse_url($_SERVER['REQUEST_URI']);

		if(isset($partsURI['query'])){

			parse_str($partsURI['query'], $query);

			if(isset($query['order'])){
				switch($query['order']){
					case 'name':
						$sorting = 'name';
					break;

					case 'new':
						$sorting = 'id desc';
					break;

					case '-price':
						$sorting = 'price desc';
					break;

					case 'price':
						$sorting = 'price asc';
					break;
				}
			}
		}

		return $sorting;
	}


	private function filters($filters){
		$filtersId = [];
		$where = '';

		foreach($filters as $key => $filter){
			array_push($filtersId, $key);

			//traceLog($filter);
			$where = $where . " and cav.value = 1";
		}

		$products = Db_DbHelper::objectArray("SELECT *
			FROM catalog_attribute_values as cav 
			WHERE cav.attribute_id in (?)", [$filtersId]);	

		foreach($products as $product){

			foreach($filters as $key => $filter){
				//array_push($filtersId, $key);
			}
			//$product
		}
	}









	private function applyBaseFilter(Catalog_Product $stmt, Catalog_Category $from) {
		$filtersCount = 0;
		$stmtWhere = new Db_Where();
		foreach ($from->filters as $filter) {
			$values = Phpr::$request->getFromArray("prop.{$filter->id}", null);
			if (!is_null($values)) {
				switch($filter->data_type) {
					case Catalog_Attribute::typeBool:
						if ($values == "yes") {
							$stmtWhere->orWhere("cav.attribute_id = ? and cav.value = 1", $filter->id);
							$filtersCount++;
						} else if ($values == "no") {
							$stmtWhere->orWhere("cav.attribute_id = ? and cav.value is null", $filter->id);
							$filtersCount++;
						}
						break;
					case Catalog_Attribute::typeCheckboxList:
						if (is_array($values) && count($values)) {
							$where = new Db_Where();
							foreach ($values as $value) {
								$value = mysqli_real_escape_string(Db::$connection, $value);
								$where->orWhere("cav.attribute_id = ? and cav.value like '%{$value}%'", $filter->id);
							}
							$stmtWhere->orWhere($where);
							$filtersCount++;
						}
						break;
					case Catalog_Attribute::typeNumber:
					case Catalog_Attribute::typeFloat:
						if (is_array($values)) {
							if (array_key_exists("from", $values) && is_numeric($values["from"])) {
								if (array_key_exists("to", $values) && is_numeric($values["to"])) {
									$stmtWhere->orWhere("cav.attribute_id = ? and cav.value between CAST(? as DECIMAL) and CAST(? as DECIMAL)",
										$filter->id, (float) $values["from"], (float) $values["to"]);
								} else {
									$stmtWhere->orWhere("cav.attribute_id = ? and cav.value >= CAST(? as DECIMAL)", $filter->id, (float) $values["from"]);
								}
								$filtersCount++;
							} elseif (array_key_exists("to", $values) && is_numeric($values["to"])) {
								$stmtWhere->orWhere("cav.attribute_id = ? and cav.value <= CAST(? as DECIMAL)", $filter->id, (float) $values["to"]);
								$filtersCount++;
							}
						}
						break;
				}
			}
		}

		if ($filtersCount > 0) {
			$stmt->join("catalog_attribute_values as cav", "cav.product_id=catalog_products.id", "", "inner");
			$stmt->where($stmtWhere);
			$stmt->group("cav.product_id");
			$stmt->having("count(cav.product_id) = {$filtersCount}");
		}
	}
}
