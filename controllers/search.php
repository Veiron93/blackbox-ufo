<?php

class Search extends App_Controller
{
    const resultsPerPage = 40;

    public function __construct()
    {
        parent::__construct();
    }

    public function widget()
    {
        $query = "Защитные стёкла";

        $categories = Db_DbHelper::objectArray("SELECT c.id, c.name, pc.name as parent_name
                FROM catalog_categories as c
                left join 
                    catalog_categories pc on pc.id = c.parent_id
                where
                    lower(c.name) like '%{$query}%' and (c.hidden is null or c.hidden <> 1)
                group by c.id") ?: [];












        $products = Db_DbHelper::objectArray("
                select p.id, p.name, p.price, df.id as image_id, df.disk_name as image_path 
                from 
                    catalog_products as p
                left join 
                    db_files df on df.master_object_id = p.id and df.master_object_class = 'Catalog_Product' and df.field = 'photos'
                where
                    (p.hidden is null or p.hidden <> 1) and (lower(p.name) like '%{$query}%')                
                group by p.id
                ") ?: [];


        $this->ajaxResponse(["categories" => $categories, "products" => $products]);
    }


    public function index()
    {
        // try {

        //     if ($page < 1) {
        //         $page = 1;
        //     }

        //     $query = Phpr::$request->getField("q");
        //     $query = mysqli_real_escape_string(Db::$connection, strip_tags($query));
        //     $query = mb_strtolower($query);

        //     if (!$query || mb_strlen($query) < 3) {
        //         throw new Phpr_ApplicationException("Слишком короткий поисковой запрос.");
        //     }

        //     $pagination = new Phpr_Pagination(self::resultsPerPage);
        //     $limit = self::resultsPerPage;
        //     $offset = ($page - 1) * $limit;

        //     $count = Db_DbHelper::scalar("
		// 		select count(distinct p.id)
		// 		from catalog_products as p
		// 		where 
        //             lower(p.name) like '%{$query}%' and (p.hidden is null or p.hidden <> 1)") ?: 0;
            
            
        //     $this->viewData['products'] = Db_DbHelper::objectArray("
        //         select p.id, p.name, p.price, p.price_title, df.id as image_id, df.disk_name as image_path 
        //         from 
        //             catalog_products as p
        //         left join 
        //             db_files df on df.master_object_id = p.id and df.master_object_class = 'Catalog_Product' and df.field = 'photos'
        //         where
        //             (p.hidden is null or p.hidden <> 1) and (lower(p.name) like '%{$query}%' || p.code_product like '%{$query}%')                
        //         group by p.id
        //         limit {$offset}, {$limit}") ?: [];

        //     $this->viewData['categories'] = Db_DbHelper::objectArray("
        //         select c.id, c.name, df.id as image_id, df.disk_name as image_path 
        //         from 
        //             catalog_categories as c
        //         left join 
        //             db_files df on df.master_object_id = c.id and df.master_object_class = 'Catalog_Category' and df.field = 'image'
        //         where
        //             lower(c.name) like '%{$query}%' and (c.hidden is null or c.hidden <> 1)
        //         group by c.id") ?: [];
      
        //     $pagination->setRowCount($count);
        //     $pagination->setCurrentPageIndex($page - 1);
        //     $this->viewData['pagination'] = $pagination;
        //     $this->viewData['page'] = $page;
        //     $this->viewData["query"] = "?q=" . h(urlencode(Phpr::$request->getField("q")));
      
        // } catch (Exception $ex) {
        //     $this->viewData['error'] = $ex->getMessage();
        //     Phpr::$errorLog->logException($ex);
        // }
    }
}