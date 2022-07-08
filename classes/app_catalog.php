<?
	class App_Catalog{

		private static $categories;
		public static $catalogRootCategories;



		public function __construct()
		{
			self::$categories = $this->getCategories();
			self::$catalogRootCategories = $this->getRootCategories();
		}

		public function getCategories(){

			$categories = Db_DbHelper::objectArray("SELECT 
				cc.id, cc.name, cc.hot, cc.title_sku, cc.level, cc.parent_id, cc.path,
				f.id as image_id, f.disk_name as image_path
                FROM catalog_categories cc
				LEFT JOIN db_files f ON f.master_object_id = cc.id and f.master_object_class ='Catalog_Category'
                WHERE cc.deleted is null 
				AND cc.hidden is null");

			return $categories;
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


		static function getHotCategories($idCategory, $limit = 2){
			return Db_DbHelper::objectArray("SELECT id, name
				FROM catalog_categories
				WHERE hidden is null AND deleted is null AND hot is not null AND path LIKE '%$idCategory%'
				ORDER BY sort_order asc 
				LIMIT $limit");
		}
	
    
		public static function getProducts($limit = 500, $where = null){

			if($where){
				$where = "AND " . $where;
			}

			// (SELECT CONCAT_WS('---', id, disk_name)
			// 		FROM db_files 
			// 		WHERE master_object_id = cp.id 
			// 		AND master_object_class = 'Catalog_Product'
			// 		AND is_main = 1) AS photo_is_main,


			//AND (is_main != 1 || is_main is null)
			
			$products = Db_DbHelper::objectArray("SELECT 
				cp.id, cp.name, cp.regular_photo, cp.old_price, cp.price,

				(SELECT GROUP_CONCAT(CONCAT_WS('---', cs.id, cs.name, cs.leftover, cs.price) SEPARATOR '----') 
					FROM catalog_skus cs 
					WHERE cs.product_id = cp.id
					AND cs.leftover > 0) as skus,	
				
				(SELECT GROUP_CONCAT(CONCAT_WS('---', id, disk_name, is_main) SEPARATOR '----')
					FROM db_files 
					WHERE master_object_id = cp.id 
					AND master_object_class = 'Catalog_Product') AS photos

				FROM catalog_products cp
				WHERE cp.hidden is null 
				AND cp.deleted is null
				AND cp.leftover > 0
				$where
				
				ORDER BY cp.sort_order asc 
				LIMIT $limit");

			// PHOTOS

			foreach($products as $product){
				

				$productPhotos = [];
				$photos = explode("----", $product->photos);

				foreach($photos as $photo){

					if($photo){
						$photoArr = explode("---", $photo);

						if(count($photoArr)){						
							$photoObj["id"] = isset($photoArr[0]) ? $photoArr[0] : null;
							$photoObj["name"] = isset($photoArr[1]) ? $photoArr[1] : null;
							$photoObj["leftover"] = isset($photoArr[2]) ? $photoArr[2] : null;

							array_push($productPhotos, $photoObj);
						}
					}
				}

				$product->photos = $productPhotos;
			}


			
			// SKUS
			// foreach($products as $product){

			// 	$productSkus = [];
			// 	$skus = explode("----", $product->skus);

			// 	foreach($skus as $sku){

			// 		if($sku){
			// 			$skuArr = explode("---", $sku);

			// 			if(count($skuArr)){

			// 				//$skuObj = null;
							
			// 				$skuObj["id"] = isset($skuArr[0]) ? $skuArr[0] : null;
			// 				$skuObj["name"] = isset($skuArr[1]) ? $skuArr[1] : null;
			// 				$skuObj["leftover"] = isset($skuArr[2]) ? $skuArr[2] : null;
			// 				$skuObj["price"] = isset($skuArr[3]) ? $skuArr[3] : null;

			// 				//$skuObj = (object)$skuObj;

			// 				array_push($productSkus, $skuObj);
			// 			}
			// 		}
			// 	}

			// 	$product->skus = $productSkus;
			// }

			traceLog($products);

			return $products;
		}
	}
?>