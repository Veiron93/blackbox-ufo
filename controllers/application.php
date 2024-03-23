<?php
class Application extends App_Controller
{
	protected $globalHandlers = [
		"onGetRandomProducts"
	];

	public function index()
	{

		// БЛОКИ С ТОВАРАМИ

		// Б/У товары
		$usededProducts = $this->catalog::getProducts("cp.is_useded_device is not null && cp.show_block_useded_device", 6);

		// Товары со скидкой
		$productSale = $this->catalog::getProducts("cp.is_sale is not null", 6);

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
		$productBestsellers = $this->catalog::getProducts("cp.best_seller is not null", 6);

		// Новинки
		$productNew = $this->catalog::getProducts("cp.is_new is not null", 12, null, "cp.id desc");

		// бесконечный список товаров
		$infinitiListProducts = App_Catalog::getProducts(null, 30, null, 'RAND()');


		$this->viewData['blocks'] = [
			'newProducts' => [
				'show' => true,
				'type' => 'product_slider',
				'products' => $productNew ?? [],
				'params' => [
					'title' => 'Новинки',
					'view_type' => 'list'
				]
			],
			'usededProducts' => [
				'show' => true,
				'type' => 'product_grid',
				'products' => $usededProducts ?? [],
				'params' => [
					'title' => 'Б/У товары',
					'view_type' => 'list'
				]
			],
			'saleProducts' => [
				'show' => true,
				'type' => 'product_grid',
				'products' => $productSale ?? [],
				'params' => [
					'title' => 'Скидки',
					'link' => '/sale/'
				]
			],
			'bestsellersProducts' => [
				'show' => true,
				'type' => 'product_grid',
				'products' => $productBestsellers ?? [],
				'params' => [
					'title' => 'Топ продаж 🔥',
					'view_type' => 'list'
				]
			],

			'productsInfinity' => [
				'show' => true,
				'type' => 'infinity_products_list',
				'products' => $infinitiListProducts ?? [],
				'params' => [
					'title' => 'И ещё немного товаров',
					'view_type' => 'list'
				]
			],
		];


		$this->viewData['pathBlocks'] = $this->getViewsDir() . "/catalog/blocks/";
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
