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
 * String helpers
 */

function h($str)
{
	return Phpr_Html::encode($str);
}

function plainText($str)
{
	return Phpr_Html::plainText($str);
}

function l($key)
{
	return Phpr::$lang->app($key);
}

/*
 * Date helpers
 */

/**
 * @param $date
 * @param string $format
 * @return string
 */
function displayDate($date, $format = '%x')
{
	return Phpr_Date::display($date, $format);
}

/**
 * @return Phpr_DateTime
 */
function gmtNow()
{
	return Phpr_DateTime::gmtNow();
}

/*
 * Other helpers
 */

function traceLog($Str, $Listener = 'INFO')
{
	if (Phpr::$traceLog)
		Phpr::$traceLog->write($Str, $Listener);
}

function flash()
{
	return Admin_Html::flash();
}

function post($name, $default = null)
{
	return Phpr::$request->post($name, $default);
}

/*
 * Form helpers
 */

function optionState($currentValue, $selectedValue)
{
	return PHpr_Form::optionState($selectedValue, $currentValue);
}

function checkboxState($value)
{
	return Phpr_Form::checkboxState($value);
}

function radioState($currentValue, $selectedValue)
{
	return Phpr_Form::radioState($currentValue, $selectedValue);
}

function a_link($url, $title)
{
	if ($url != Phpr::$request->getCurrentUri()) {
		return sprintf('<a href="%s">%s</a>', $url, $title);
	} else {
		return sprintf('<span>%s</span>', $title);
	}
}

/**
 * Генерирует ссылку для именованного роутинг-правила
 *
 * <pre>
 * <a href="<?= u('news-post', $post->id) ?>"></a>
 * <a href="<?= u('news-post', array($post->id)) ?>"></a>
 * <a href="<?= u('news-post', array('id' => $post->id)) ?>"></a>
 * <a href="<?= u('news-category', $category->id, $page+1) ?>"></a>
 * <a href="<?= u('news-category', array($category->id, $page+1)) ?>"></a>
 * <a href="<?= u('news-category', array('category_id' => $category->id, 'page' => $page+1)) ?>"></a>
 * </pre>
 *
 * @param string $name Название роута
 * @param string ... Остальные параметры
 * @see Phpr_Router#url()
 * @return string
 */
function u($name)
{
	$args = func_get_args();
	$name = array_shift($args);
	reset($args);
	$passedArgs = null;
	switch (count($args)) {
		case 1:
			if (is_array(current($args))) {
				$passedArgs = current($args);
			} else {
				$passedArgs = $args;
			}
			break;
		case 0:
			$passedArgs = null;
			break;
		default:
			$passedArgs = $args;
	}
	return Phpr::$router->url($name, $passedArgs);
}


function checkActualProductsCart()
{
	$cart = Shop_Cart::getCart();
	$product_cart_items = $cart->getItems();

	$status_update_cart = false;

	$products_id = [];
	$skus_id = [];

	

	foreach ($product_cart_items as $product_cart) {
		array_push($products_id, $product_cart->productId);

		if($product_cart->skuId){
			array_push($skus_id, $product_cart->skuId);
		}
	}

	// изменяет стоимость
	if(!function_exists('setPriceProduct')) {
		function setPriceProduct($product_cart, $actual_price, $cart, $status_update_cart){
			if($product_cart->price != $actual_price){
				$product_cart->setPrice($actual_price);
				$cart->notifyCartUpdated();

				if(!$status_update_cart) $status_update_cart = true;
			}
		}
	}

	// изменяет остаток
	if(!function_exists('setLeftoverProduct')){
		function setLeftoverProduct($product_cart, $actual_leftover, $cart, $status_update_cart){
			if($product_cart->quantity > $actual_leftover){
				$product_cart->setQuantity($actual_leftover);
				$cart->notifyCartUpdated();

				if(!$status_update_cart) $status_update_cart = true;
			}
		}
	}

	

	if (count($products_id)) {

		traceLog(2222);

		if(count($skus_id)){

			$actual_products = Db_DbHelper::objectArray("SELECT p.id, p.leftover, p.price, s.id sku_id, s.price sku_price, s.leftover sku_leftover
				FROM catalog_products as p
				LEFT JOIN catalog_skus as s on s.product_id = p.id AND s.id IN (" . implode(',', $skus_id) . ")
				WHERE p.id IN (" . implode(',', $products_id) . ")");

		}else{
			$actual_products = Db_DbHelper::objectArray("SELECT id, leftover, price
				FROM catalog_products
				WHERE id IN (" . implode(',', $products_id) . ")");
		}	

		foreach ($product_cart_items as $product_cart) {
			foreach ($actual_products as $actual_product){

				if($product_cart->skuId){
					if($actual_product->sku_id == $product_cart->skuId){
						$actual_price = $actual_product->sku_price ? $actual_product->sku_price : $actual_product->price;

						setPriceProduct($product_cart, $actual_price, $cart, $status_update_cart);
						setLeftoverProduct($product_cart, $actual_product->sku_leftover, $cart, $status_update_cart);
						break;
					}
				}else{
					if($actual_product->id == $product_cart->productId){
						setPriceProduct($product_cart, $actual_product->price, $cart, $status_update_cart);
						setLeftoverProduct($product_cart, $actual_product->leftover, $cart, $status_update_cart);
						break;
					}
				}

			}
		}
	}
}


function getProductsAddedCart()
{
	$cart = Shop_Cart::getCart();
	$items = $cart->getItems();
	$products_cart_id = [];

	foreach ($items as $item) {
		array_push($products_cart_id, $item->productId);
	}

	return $products_cart_id;
}


function skus($product_skus)
{
	$skus = [];

	foreach ($product_skus as $s) {

		if($s->leftover){
			
			$sku["id"] = $s->id;
			$sku["product_id"] = $s->product_id;
			$sku["name"] = $s->name;
			$sku["price"] = $s->price;
			$sku["leftover"] = $s->leftover;

			array_push($skus, $sku);
		}
	}

	usort($skus, function ($a, $b) {
		return strcmp($a["name"], $b["name"]);
	});

	return $skus;
}


// стоимость товара в корзине
function cartProductPrice($product, $total_price = false){

	if($product->skuId) $sku = $product->getSku();

	$price = isset($sku) && $sku->price ? $sku->price : $product->price;

	if($total_price) $price = $product->quantity * $price;
	
	return $price;
}

// стоимость товаров добавленных в корзину 
function cartTotalPrice($products){

	$total_price = null;
	
	foreach($products as $product){

		$sku = null;

		if($product->skuId) $sku = $product->getSku();
		$total_price += $product->quantity * (isset($sku) && $sku->price ? $sku->price : $product->price);
	}

	return $total_price;
}
