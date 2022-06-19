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
	$productCartItems = $cart->getItems();
	$idsProductsCart = [];
	$statusUpdateCart = false;

	foreach ($productCartItems as $productCart) {
		array_push($idsProductsCart, $productCart->productId);
	}

	if (count($idsProductsCart)) {
		// проверка товара на количество в наличии и актуальные цены
		$actualProducts = Db_DbHelper::objectArray("SELECT id, leftover, price FROM catalog_products
		WHERE id IN (" . implode(',', $idsProductsCart) . ")");

		foreach ($productCartItems as $productCart) {

			foreach ($actualProducts as $actualProduct) {

				if ($productCart->productId == $actualProduct->id) {

					// если актульная стоимость отличается от стоимости товара добавленно ранее в корзину, то меняем на актуальную
					if ($productCart->price != $actualProduct->price) {
						$productCart->setPrice($actualProduct->price);
						$cart->notifyCartUpdated();

						if (!$statusUpdateCart) $statusUpdateCart = true;
					}

					// если товара в наличии меньше чем добавлено в корзине, то устанавливается значение из остатка
					if ($productCart->quantity > $actualProduct->leftover) {
						$productCart->setQuantity($actualProduct->leftover);
						$cart->notifyCartUpdated();

						if (!$statusUpdateCart) $statusUpdateCart = true;
					}

					break;
				}
			}
		}
	}

	return $statusUpdateCart;
}

function getProductsAddedCart()
{
	$cart = Shop_Cart::getCart();
	$productCartItems = $cart->getItems();
	$idsProductsCart = [];

	foreach ($productCartItems as $productCart) {
		array_push($idsProductsCart, $productCart->productId);
	}

	return $idsProductsCart;
}


function skus($productSkus)
{
	$skus = [];

	foreach ($productSkus as $e) {

		if($e->leftover){
			
			$sku["id"] = $e->id;
			$sku["product_id"] = $e->product_id;
			$sku["name"] = $e->name;
			$sku["price"] = $e->price;
			$sku["leftover"] = $e->leftover;

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

	$price = isset($sku) ? $sku->price : $product->price;

	if($total_price) $price = $product->quantity * $price;
	
	return $price;
}

// стоимость товаров добавленных в корзину 
function cartTotalPrice($products){

	$total_price = null;
	
	foreach($products as $product){

		if($product->skuId) $sku = $product->getSku();

		$total_price += $product->quantity * (isset($sku) ? $sku->price : $product->price);
	}

	return $total_price;
}
