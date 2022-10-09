<?php

class Search extends App_Controller
{
    const productsPerPage = 40;

    public function __construct()
    {
        parent::__construct();
    }

    public function widget()
    {
        try {
            $inputJSON = file_get_contents('php://input');
            $query = json_decode($inputJSON, TRUE);

            $query = mysqli_real_escape_string(Db::$connection, strip_tags($query["query"]));
            $query = mb_strtolower($query);
            $query = trim($query);

            if (!$query || mb_strlen($query) < 2) {
                return false;
            }

            // CATEGORIES

            $categories = Db_DbHelper::objectArray("SELECT 
                c.id, c.name, c.level,
                pc.name AS parent_name, pc.level AS parent_level, 
                COUNT(cp.id) AS products_count
                FROM 
                    catalog_categories AS c
                LEFT JOIN 
                    catalog_categories pc ON pc.id = c.parent_id
                LEFT JOIN  
                    catalog_products cp ON cp.category_id = c.id
                WHERE
                    lower(c.name) LIKE '%{$query}%' AND c.hidden IS NULL AND c.deleted IS NULL AND cp.hidden IS NULL AND cp.deleted IS NULL
                GROUP BY c.id") ?: [];


            // PRODUCTS

            $productsAll = Db_DbHelper::objectArray("SELECT p.id, p.name, p.price, df.id AS image_id, df.disk_name AS image_path 
                FROM 
                    catalog_products as p
                LEFT JOIN 
                    db_files df on
                    df.master_object_id = p.id and 
                    df.sort_order = (SELECT MIN(sort_order) FROM db_files WHERE master_object_id = p.id and df.field = 'photos' and master_object_class = 'Catalog_Product')
                WHERE
                    lower(p.name) LIKE '%{$query}%' AND p.hidden IS NULL AND p.deleted IS NULL      
                GROUP BY p.id") ?: [];

            $products = [];

            if (count($productsAll)) {
                for ($i = 0; $i <= ((count($productsAll) - 1) >= 4 ? 4 : count($productsAll) - 1); $i++) {
                    $image = (new LWImageManipulator($productsAll[$i]->image_id, $productsAll[$i]->image_path))->getThumb(40, 40, Phpr_ImageManipulator::fitAndCrop);

                    $productsAll[$i]->image = $image;

                    unset($productsAll[$i]->disk_name);
                    unset($productsAll[$i]->image_path);

                    array_push($products, $productsAll[$i]);
                }
            }

            $this->ajaxResponse([
                "categories" => array_slice($categories, 0, 5),
                "products" => $products,
                "count_categories" => count($categories),
                "count_products" => count($productsAll)
            ]);
        } catch (Exception $exception) {
            Phpr::$response->setHttpStatus(Phpr_Response::httpInternalServerError);
            Phpr::$errorLog->logException($exception);
        }
    }


    public function index($pageIndex = null)
    {
        try {
            if (!$pageIndex) $pageIndex = 1;

            $query = Phpr::$request->getField("q");
            $query = mysqli_real_escape_string(Db::$connection, strip_tags($query));
            $query = mb_strtolower($query);

            if (!$query || mb_strlen($query) < 2) {
                throw new Phpr_ApplicationException("Слишком короткий поисковой запрос.");
            }

            // CATEGORIES

            $this->viewData['categories'] = $categories = Db_DbHelper::objectArray("SELECT c.id, c.name, pc.name AS parent_name, COUNT(cp.id) AS products_count
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
            $whereProducts = "lower(cp.name) LIKE '%{$query}%' AND cp.hidden IS NULL AND cp.deleted IS NULL";

            $this->viewData['products'] = $products = $this->catalog->getProducts($whereProducts, self::productsPerPage, $pageIndex - 1);

            $this->viewData["query"] = "?q=" . h(urlencode(Phpr::$request->getField("q")));
        } catch (Exception $ex) {
            $this->viewData['error'] = $ex->getMessage();
            Phpr::$errorLog->logException($ex);
        }
    }
}
