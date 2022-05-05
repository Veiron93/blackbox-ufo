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
        try {
            $inputJSON = file_get_contents('php://input');
            $query = json_decode( $inputJSON, TRUE ); 

            $query = mysqli_real_escape_string(Db::$connection, strip_tags($query["query"]));
            $query = mb_strtolower($query);
            $query = trim($query);

            if (!$query || mb_strlen($query) < 2) {
                return false;
            }

            //traceLog($query);

            // CATEGORIES

            $categories = Db_DbHelper::objectArray("SELECT c.id, c.name, pc.name AS parent_name, COUNT(cp.id) AS products_count
                FROM 
                    catalog_categories AS c
                LEFT JOIN 
                    catalog_categories pc ON pc.id = c.parent_id
                LEFT JOIN  
                    catalog_products cp ON cp.category_id = c.id
                WHERE
                    lower(c.name) LIKE '%{$query}%' AND c.hidden IS NULL AND c.deleted IS NULL AND cp.hidden IS NULL AND cp.deleted IS NULL
                GROUP BY c.id LIMIT 5") ?: [];


            // PRODUCTS

            $products = Db_DbHelper::objectArray("SELECT p.id, p.name, p.price, df.id AS image_id, df.disk_name AS image_path 
                FROM 
                    catalog_products as p
                LEFT JOIN 
                    db_files df on df.master_object_id = p.id and df.master_object_class = 'Catalog_Product' and df.field = 'photos'
                WHERE
                    lower(p.name) LIKE '%{$query}%' AND p.hidden IS NULL AND p.deleted IS NULL         
                GROUP BY p.id LIMIT 5") ?: [];


            $this->ajaxResponse(["categories" => $categories, "products" => $products]);

        }  catch (Exception $exception) {
            Phpr::$response->setHttpStatus(Phpr_Response::httpInternalServerError);
            Phpr::$errorLog->logException($exception);
        }
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