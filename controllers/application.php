<?php
class Application extends App_Controller
{
	protected $globalHandlers = [
		"onGetRandomProducts"
	];

	public function index()
	{
		// Б/У товары
		$this->viewData['usededProducts'] = $this->catalog::getProducts("cp.is_useded_device is not null && cp.show_block_useded_device", 6);

		// Товары со скидкой
		$this->viewData['productSale'] = $this->catalog::getProducts("cp.is_sale is not null", 6);

		$this->viewData['countProductsDiscount'] = Db_DbHelper::scalar(
			"SELECT COUNT(id)
				FROM catalog_products
				WHERE 
					old_price is not null
					AND hidden is null 
					AND deleted is null
					AND leftover > 0"
		);

		// Хиты продаж
		$this->viewData['productBestsellers'] = $this->catalog::getProducts("cp.best_seller is not null", 6);

		// Новинки
		$this->viewData['productNew'] = $this->catalog::getProducts("cp.is_new is not null", 12, null, "cp.id desc");

		// бесконечный список товаров
		$this->viewData['infinitiListProducts'] = App_Catalog::getProducts(null, 30, null, 'RAND()');
	}


	public function onGetRandomProducts()
	{
		$inputJSON = file_get_contents('php://input');
		$ids = json_decode($inputJSON, TRUE)['ids'];
		$ids = implode(',', $ids);

		$new_products = $this->catalog::getProducts("cp.id NOT IN ($ids)", 30, null, 'RAND()');
		$new_products_ids = [];

		foreach ($new_products as $new_product) {
			array_push($new_products_ids, $new_product->id);
		}

		$block = "__temporary_block";

		Phpr_View::beginBlock($block);
		$this->renderPartial($this->getViewsDir() . "catalog/_render_products.htm", ['products' => $new_products]);
		Phpr_View::endBlock();

		$products_partial = Phpr_View::block($block);

		$this->ajaxResponse(['newProducts' => $products_partial, 'newProductsIds' => $new_products_ids]);
	}
}
