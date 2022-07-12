<?
	class App_Catalog{

		public static $categories;

		public function __construct()
		{
			self::$categories = self::getCategories();
		}

		public function getCategory($id){
			$category = self::$categories[$id];
			return $category;
		}

		public static function getCategories(){

			$categories_arr_assoc = [];

			$categories = Db_DbHelper::objectArray("SELECT 
				cc.id, cc.name, cc.hot, cc.title_sku, cc.level, cc.parent_id, cc.path,
				f.id as image_id, f.disk_name as image_path
                FROM catalog_categories cc
				LEFT JOIN db_files f ON f.master_object_id = cc.id and f.master_object_class ='Catalog_Category'
                WHERE cc.deleted is null 
				AND cc.hidden is null");

			
			foreach($categories as $category){
				$categories_arr_assoc[$category->id] = $category;
			}

			//traceLog($categories);

			return $categories_arr_assoc;
		}

		public static function getRootCategories(){

			$rootCategories = [];

			foreach(self::$categories as $category){
				if($category->level == 1){
					$hotCategories = self::getHotCategories($category->id);
					$category->hot_categories = $hotCategories;

					array_push($rootCategories, $category);
				}
			}

			return $rootCategories;
		}


		public static function getChildCategories($parentCategory){

			$categories = [];
			
			foreach(self::$categories as $category){
				if($category->parent_id == $parentCategory->id){
					array_push($categories, $category);
				}
			}

			return $categories;
		}


		private static function getHotCategories($idCategory, $limit = 2){
			return Db_DbHelper::objectArray("SELECT id, name
				FROM catalog_categories
				WHERE hidden is null AND deleted is null AND hot is not null AND path LIKE '%$idCategory%'
				ORDER BY sort_order asc 
				LIMIT $limit");
		}
	
    
		public static function getProducts($limit = 10, $where = null, $offset = 0, $order = null){

			if($where) $where = "AND " . $where;

			if($offset){
                $offset = $limit * $offset;
            }

			if(!$order){
				$order = "cp.id asc";
			}
			
			$products = Db_DbHelper::objectArray("SELECT 
				cp.id, cp.name, cp.regular_photo, cp.old_price, cp.price,

				(SELECT GROUP_CONCAT(CONCAT_WS('---', cs.id, cs.name, cs.leftover, cs.price) SEPARATOR '----') 
					FROM catalog_skus cs 
					WHERE cs.product_id = cp.id
					AND cs.leftover > 0) as skus,
				
				(SELECT GROUP_CONCAT(CONCAT_WS(',', id, disk_name, is_main) SEPARATOR '----')
					FROM db_files 
					WHERE master_object_id = cp.id 
					AND master_object_class = 'Catalog_Product') AS photos

				FROM catalog_products cp
				WHERE cp.hidden is null 
				AND cp.deleted is null
				AND cp.leftover > 0
				$where
				ORDER BY $order
				LIMIT $limit
				OFFSET $offset");

			
			foreach($products as $product){

				// IMAGES
				$product->images = null;

				if(strlen($product->photos)){
					$photos = explode("----", $product->photos);
					$index_photo = 0;

					foreach($photos as $photo){
						$values = explode(",", $photo);

						$photo = new stdClass();
						$photo->image_id = $values[0] ?? null;
						$photo->image_path = $values[1] ?? null;

						if(isset($values[2]) && $values[2]){
							$product->images["main_photo"] = $photo;
						}else{
							$product->images["photo_$index_photo"] = $photo;
							$index_photo++;
						}
					}
				}

				unset($product->photos);

				// SKUS
				$skus_arr = [];

				if($product->skus){
					$skus = explode("----", $product->skus);

					foreach($skus as $sku){

						$values = explode("---", $sku);
						$sku = new stdClass();
		
						$sku->id = $values[0] ?? null;
						$sku->name = $values[1] ?? null;
						$sku->leftover = $values[2] ?? null;
						$sku->price = $values[3] ?? null;

						array_push($skus_arr, $sku);
					}

					$product->skus = $skus_arr;
				}
			}

			//traceLog($products);

			return $products;
		}
	}
?>