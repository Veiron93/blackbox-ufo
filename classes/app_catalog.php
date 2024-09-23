<?
class App_Catalog
{

	private static $categories;

	public function __construct()
	{
		self::$categories = self::getCategories();
	}

	/**
	 * Категории 
	 *
	 * @return array
	 */

	public static function getCategories($where = null)
	{

		$categories_arr_assoc = [];

		$categories = Db_DbHelper::objectArray("SELECT 
				cc.id, cc.name, cc.hot, cc.title_sku, cc.level, cc.parent_id, cc.path, cc.title_sku,
				cc.seo_title_add_postfix, cc.seo_description_add_postfix, cc.parent_title,
				f.id as image_id, f.disk_name as image_path
                FROM catalog_categories cc
				LEFT JOIN db_files f ON f.master_object_id = cc.id and f.master_object_class ='Catalog_Category'
                WHERE 
					cc.deleted is null 
					AND cc.hidden is null $where 
				ORDER BY cc.sort_order");

		foreach ($categories as $category) {
			$categories_arr_assoc[$category->id] = $category;
		}

		return $categories_arr_assoc;
	}

	/**
	 * Категория
	 *
	 * @return object
	 */

	public static function getCategory($id)
	{
		$category = self::$categories[$id];
		return $category;
	}

	public static function getRootCategories()
	{

		$rootCategories = [];

		foreach (self::$categories as $category) {
			if ($category->level == 1) {
				$hotCategories = self::getHotCategories($category->id);
				$category->hot_categories = $hotCategories;

				array_push($rootCategories, $category);
			}
		}

		return $rootCategories;
	}


	public static function getChildCategories($parentCategory)
	{

		$categories = [];

		foreach (self::$categories as $category) {
			if ($category->parent_id == $parentCategory->id) {
				array_push($categories, $category);
			}
		}

		return $categories;
	}

	public static function getParentCategories($path)
	{
		$categories = [];
		$idCategories = explode(".", $path);

		foreach ($idCategories as $id) {
			if ($id) {
				array_push($categories, self::getCategory($id));
			}
		}

		return $categories;
	}


	private static function getHotCategories($idCategory, $limit = 10)
	{
		return Db_DbHelper::objectArray("SELECT id, name
				FROM catalog_categories
				WHERE hidden is null AND deleted is null AND hot is not null AND path LIKE '%$idCategory%'
				ORDER BY sort_order asc 
				LIMIT $limit");
	}


	public static function getProducts($where = null, $limit = null, $offset = null, $order = null)
	{
		if ($where) $where = "AND " . $where;
		if ($limit) $limit = "LIMIT " . ($offset ? $limit * $offset . ',' : NULL) . $limit;

		if (!$order) $order = "cp.id asc";

		$products = Db_DbHelper::objectArray("SELECT 
				cp.id, cp.name, cp.regular_photo, cp.old_price, cp.price, cp.is_useded_device, cp.state_device_useded_device, cp.best_seller,

				(SELECT GROUP_CONCAT(CONCAT_WS('---', cs.id, cs.name, cs.leftover, cs.price) SEPARATOR '----') 
					FROM catalog_skus cs 
					WHERE cs.product_id = cp.id
					AND cs.leftover > 0) as skus,
				
				(SELECT GROUP_CONCAT(CONCAT_WS(',', id, disk_name, is_main) ORDER BY sort_order asc SEPARATOR '----')
					FROM db_files 
					WHERE master_object_id = cp.id 
					AND master_object_class = 'Catalog_Product') AS photos

				FROM catalog_products as cp
				WHERE cp.hidden is null 
				AND cp.deleted is null
				AND cp.leftover > 0
				$where
				ORDER BY $order
				$limit");

		foreach ($products as $product) {
			self::imagesProduct($product);
			self::skusProduct($product);
		}

		return $products;
	}

	public static function getProduct($productId)
	{
		$product = Db_DbHelper::object("SELECT 
				cp.id, cp.name, cp.regular_photo, cp.old_price, cp.price, cp.description, 
				cp.short_description, cp.sales, cp.title_sku, cp.category_id, cp.leftover, 
				cp.is_useded_device, cp.state_device_useded_device, cp.state_battery_useded_device,
				cp.guarantee_useded_devicet, cp.defect_screen_useded_device, cp.defect_body_useded_device,
				cp.complect_useded_device, cp.complect_non_elements_useded_device, cp.added_acsessuares_useded_device,
				cp.seo_title_add_postfix, cp.seo_description_add_postfix, cp.service_install,

				(SELECT GROUP_CONCAT(CONCAT_WS('---', cs.id, cs.name, IF(cs.leftover is null, 0, cs.leftover), cs.price) ORDER BY cs.sort_order desc SEPARATOR '----') 
					FROM catalog_skus cs 
					WHERE cs.product_id = cp.id) as skus,		

				(SELECT GROUP_CONCAT(CONCAT_WS(',', id, disk_name, is_main) ORDER BY sort_order asc SEPARATOR '----')
					FROM db_files 
					WHERE master_object_id = cp.id 
					AND master_object_class = 'Catalog_Product') AS photos

				FROM catalog_products cp
				WHERE 
					cp.id = ?
					AND cp.hidden is null 
					AND cp.deleted is null
					AND cp.leftover > 0", [$productId]);

		self::imagesProduct($product);
		self::skusProduct($product);

		return $product;
	}

	/**
	 * фото продукта
	 *
	 * @param object
	 */

	private static function imagesProduct($product)
	{
		$product->images = null;

		if (strlen($product->photos)) {

			$photos = explode("----", $product->photos);
			$index_photo = 0;

			foreach ($photos as $photo) {
				$values = explode(",", $photo);

				if (isset($values[0]) && isset($values[1])) {

					$img = (new LWImageManipulator($values[0], $values[1]));

					$product->images[isset($values[2]) && $values[2] ? 'main_photo' : 'photo_' . $index_photo] = $img;

					if (!isset($values[2]) || isset($values[2]) && !$values[2]) {
						$index_photo++;
					}
				}
			}
		}

		unset($product->photos);
	}


	/**
	 * артикулы продукта
	 *
	 * @param object
	 */

	private static function skusProduct($product)
	{
		if ($product->skus) {
			$skus = explode("----", $product->skus);
			$product->skus = [];

			foreach ($skus as $sku) {

				$values = explode("---", $sku);
				$sku = new stdClass();

				$sku->id = $values[0] ?? null;
				$sku->name = $values[1] ?? null;
				$sku->leftover = $values[2] ?? null;
				$sku->price = (!isset($values[3]) || (isset($values[3]) && $values[3] == 0)) ? $product->price : $values[3];

				array_push($product->skus, $sku);
			}
		}
	}


	public static function imageProduct($images)
	{

		$image = '/resources/images/icons/no-image.svg';

		if (isset($images) && count($images)) {

			if (isset($images["main_photo"])) {
				$imgSrc = $images["main_photo"];
			} elseif (isset($images["photo_0"])) {
				$imgSrc = $images["photo_0"];
			}

			if (isset($imgSrc)) {
				$image = $imgSrc->getThumb(450, 'auto');
			}
		}

		return $image;
	}


	public static function pagination($where, $count_products_page, $number_current_page)
	{

		if ($where) $where = "AND " . $where;

		$count_products = Db_DbHelper::scalar("SELECT COUNT(id) FROM catalog_products
				WHERE hidden is null AND deleted is null AND leftover > 0 $where");

		$count_pages = ceil($count_products / $count_products_page);

		$pagination = new stdClass();

		$pagination->count_products = $count_products;
		$pagination->count_pages = $count_pages;
		$pagination->number_current_page = $number_current_page;

		//traceLog($pagination);

		return $pagination;
	}

	public static function colorStateProduct($state)
	{

		if ($state < 4) {
			$bg = "#c31830";
		} elseif ($state >= 4 && $state <= 6) {
			$bg = "#dd6c0c";
		} elseif ($state >= 7 && $state <= 8) {
			$bg = "#75b100";
		} else {
			$bg = "#49b100";
		}

		return $bg;
	}
}
