<?php
class App_Helper{

	 /**
     * Добавляет поле image
     *
     * @param array $rows
     * @return void
     */
	 private static function initImages(&$rows){
	 	foreach($rows as $row){
	 		if($row->image_id){
	 			$row->image = new LWImageManipulator($row->image_id, $row->image_path);
	 		}
	 		else{
	 			$row->image = null;
	 		}
	 	}
	 }

	public static function getBanners($category){
		$result = Db_DbHelper::objectArray("SELECT b.id, b.name, b.description, b.link, b.banner_text, banner_type, bg_color,
			image_width, image_height,
			f.id as image_id, f.disk_name as image_path, b.open_in_new_window
		FROM banners as b
		LEFT JOIN banners_categories as cat on cat.id = b.category_id
		LEFT JOIN db_files as f on f.master_object_id = b.id and f.master_object_class ='Banners_Banner'
		WHERE b.enabled = 1 AND cat.code = '{$category}'
		ORDER BY b.sort_order desc");

		self::initImages($result);
		return $result;
	}

	public static function getHotCatalogCategories($id){
		return Db_DbHelper::objectArray("SELECT c.id, c.name
		FROM catalog_categories as c
		WHERE c.hidden is null and c.deleted is null and c.hot is not null and c.parent_id = $id
		ORDER BY c.sort_order asc");
	}


	public static function getDiscountPercentage($oldPrice, $price){
		return round(100 - ($price*100/$oldPrice));
	}
}