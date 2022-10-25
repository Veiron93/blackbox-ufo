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
class Catalog extends App_Controller
{

	const productsPerPage = 40;

	public function index()
	{
		$this->setTitle("Каталог");
	}

	public function category($categoryId, $pageIndex)
	{
		try {
			$category = $this->catalog->getCategory($categoryId);

			if (!$category) {
				$this->throw404();
			}

			if (!$pageIndex) {
				$pageIndex = 1;
			}

			$pagination = $this->catalog->pagination("category_id = $categoryId", self::productsPerPage, $pageIndex);

			$category->seo = self::seo_category($category, $pagination);

			$this->viewData['category'] = $category;
			$this->viewData['products'] = $this->catalog->getProducts("cp.category_id = $categoryId", self::productsPerPage, $pageIndex - 1, self::sorting());
			$this->viewData['pagination'] = $pagination;

			$this->setTitle($category->name);
		} catch (Exception $ex) {
			$this->viewData['error'] = $ex->getMessage();
		}
	}

	public function product($productId)
	{
		try {
			$product = $this->catalog->getProduct($productId);

			if (!$product) {
				$this->throw404();
			}

			$category = $this->catalog->getCategory($product->category_id);
			$product->seo = self::seo_product($product);

			$this->viewData['product'] = $product;
			$this->viewData['category'] = $category;
		} catch (Exception $ex) {
			$this->viewData['error'] = $ex->getMessage();
		}
	}


	public function all_products($pageIndex = null)
	{
		if (!$pageIndex) $pageIndex = 1;

		$this->viewData['allProducts'] = $this->catalog::getProducts(null, self::productsPerPage, $pageIndex - 1, "cp.id desc");
		$this->viewData['pagination'] = $this->catalog->pagination(null, self::productsPerPage, $pageIndex);
	}

	//СОРТИРОВКА
	public function sorting()
	{
		$sorting = 'sales desc';
		$partsURI = parse_url($_SERVER['REQUEST_URI']);

		if (isset($partsURI['query'])) {

			parse_str($partsURI['query'], $query);

			if (isset($query['order'])) {
				switch ($query['order']) {
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


	private static function get_seo($id, $class)
	{
		$seo = Db_DbHelper::object("SELECT seo_title, seo_description, seo_keywords, seo_metatags
                FROM seo_record_params
                WHERE parent_object_id = ? AND parent_object_class = ?", [$id, $class]);

		if (!$seo) {
			$seo = new stdClass();
			$seo->seo_title = '';
			$seo->seo_description = '';
			$seo->seo_keywords = '';
			$seo->seo_metatags = '';
		}

		return $seo;
	}

	private static function get_seo_templates()
	{
		$codes = [
			'postfix_seo_title_category',
			'postfix_seo_description_category',
			'postfix_seo_title_product',
			'postfix_seo_description_product'
		];

		$templates = Db_DbHelper::objectArray("SELECT content, code
                FROM admin_text_blocks
                WHERE code in (?)", [$codes]);

		$seo_templates = [];

		foreach ($templates as $template) {
			$seo_templates[$template->code] = $template->content;
		}

		return $seo_templates;
	}


	private static function seo_category($category, $pagination = null)
	{
		$seo = self::get_seo($category->id, 'Catalog_Category');
		$seo_templates = self::get_seo_templates();

		if ($seo->seo_title && $category->seo_title_add_postfix) {
			$seo->seo_title .= ' | ' . $seo_templates['postfix_seo_title_category'];
		}

		if (!$seo->seo_title) {
			$seo->seo_title = $category->name . ' | ' . $seo_templates['postfix_seo_title_category'];
		}

		if ($pagination && $pagination->count_pages > 1) {
			$seo->seo_title .= ' - страница ' . $pagination->number_current_page . ' из ' . $pagination->count_pages;
		}

		// description
		if ($seo->seo_description && $category->seo_description_add_postfix) {
			$seo->seo_description .= ' | ' . $seo_templates['postfix_seo_description_category'];
		}

		if (!$seo->seo_description) {
			$seo->seo_description = "⭐️ Купить " . $category->name . " " . $seo_templates['postfix_seo_description_category'];
		}

		if (!$seo->seo_keywords) {
			$words = [];
			array_push($words, ...['купить ' . $category->name, $category->name, 'в южно-сахалинске']);
			$seo->seo_keywords = implode(', ', $words);
		}

		return $seo;
	}


	private static function seo_product($product)
	{
		$seo = self::get_seo($product->id, 'Catalog_Product');
		$seo_templates = self::get_seo_templates();

		if ($seo->seo_title && $product->seo_title_add_postfix) {
			$seo->seo_title .= ' | ' . $seo_templates['postfix_seo_title_product'] . ' - ' . $product->id;
		}

		if (!$seo->seo_title) {
			$seo->seo_title = $product->name . ' | ' . $seo_templates['postfix_seo_title_product'] . ' - ' . $product->id;
		}

		if ($seo->seo_description && $product->seo_description_add_postfix) {
			$seo->seo_description .= ' | ' . $seo_templates['postfix_seo_description_product'];
		}

		if (!$seo->seo_description) {
			$seo->seo_description = "⭐️ Купить " . $product->name . " " . $seo_templates['postfix_seo_description_product'];
		}

		if (!$seo->seo_keywords) {
			$words = [];
			array_push($words, ...['купить ' . $product->name, $product->name, 'в южно-сахалинске']);
			$seo->seo_keywords = implode(', ', $words);
		}

		return $seo;
	}


	// private function filters($filters)
	// {
	// 	$filtersId = [];
	// 	$where = '';

	// 	foreach ($filters as $key => $filter) {
	// 		array_push($filtersId, $key);

	// 		//traceLog($filter);
	// 		$where = $where . " and cav.value = 1";
	// 	}

	// 	$products = Db_DbHelper::objectArray("SELECT *
	// 		FROM catalog_attribute_values as cav 
	// 		WHERE cav.attribute_id in (?)", [$filtersId]);

	// 	foreach ($products as $product) {

	// 		foreach ($filters as $key => $filter) {
	// 			//array_push($filtersId, $key);
	// 		}
	// 		//$product
	// 	}
	// }


	// private function applyBaseFilter(Catalog_Product $stmt, Catalog_Category $from)
	// {
	// 	$filtersCount = 0;
	// 	$stmtWhere = new Db_Where();
	// 	foreach ($from->filters as $filter) {
	// 		$values = Phpr::$request->getFromArray("prop.{$filter->id}", null);
	// 		if (!is_null($values)) {
	// 			switch ($filter->data_type) {
	// 				case Catalog_Attribute::typeBool:
	// 					if ($values == "yes") {
	// 						$stmtWhere->orWhere("cav.attribute_id = ? and cav.value = 1", $filter->id);
	// 						$filtersCount++;
	// 					} else if ($values == "no") {
	// 						$stmtWhere->orWhere("cav.attribute_id = ? and cav.value is null", $filter->id);
	// 						$filtersCount++;
	// 					}
	// 					break;
	// 				case Catalog_Attribute::typeCheckboxList:
	// 					if (is_array($values) && count($values)) {
	// 						$where = new Db_Where();
	// 						foreach ($values as $value) {
	// 							$value = mysqli_real_escape_string(Db::$connection, $value);
	// 							$where->orWhere("cav.attribute_id = ? and cav.value like '%{$value}%'", $filter->id);
	// 						}
	// 						$stmtWhere->orWhere($where);
	// 						$filtersCount++;
	// 					}
	// 					break;
	// 				case Catalog_Attribute::typeNumber:
	// 				case Catalog_Attribute::typeFloat:
	// 					if (is_array($values)) {
	// 						if (array_key_exists("from", $values) && is_numeric($values["from"])) {
	// 							if (array_key_exists("to", $values) && is_numeric($values["to"])) {
	// 								$stmtWhere->orWhere(
	// 									"cav.attribute_id = ? and cav.value between CAST(? as DECIMAL) and CAST(? as DECIMAL)",
	// 									$filter->id,
	// 									(float) $values["from"],
	// 									(float) $values["to"]
	// 								);
	// 							} else {
	// 								$stmtWhere->orWhere("cav.attribute_id = ? and cav.value >= CAST(? as DECIMAL)", $filter->id, (float) $values["from"]);
	// 							}
	// 							$filtersCount++;
	// 						} elseif (array_key_exists("to", $values) && is_numeric($values["to"])) {
	// 							$stmtWhere->orWhere("cav.attribute_id = ? and cav.value <= CAST(? as DECIMAL)", $filter->id, (float) $values["to"]);
	// 							$filtersCount++;
	// 						}
	// 					}
	// 					break;
	// 			}
	// 		}
	// 	}

	// 	if ($filtersCount > 0) {
	// 		$stmt->join("catalog_attribute_values as cav", "cav.product_id=catalog_products.id", "", "inner");
	// 		$stmt->where($stmtWhere);
	// 		$stmt->group("cav.product_id");
	// 		$stmt->having("count(cav.product_id) = {$filtersCount}");
	// 	}
	// }
}
