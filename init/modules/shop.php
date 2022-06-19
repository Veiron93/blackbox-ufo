<?php

$APP_CONF['SHOP_ENABLE_LEFTOVERS'] = false; // Включить пересчет остатков
$APP_CONF['SHOP_HIDDEN_SKU_WITH_ZERO_LEFTOVERS'] = false; // Не показывать артикулы с нулевыми остатками
$APP_CONF['CUSTOM_ORDER_SOUND'] = true; //использовать для нотификации о заказах звук /resources/sound/shop.ogg
$APP_CONF['PAYMENT_PAGE_SUPPORT'] = false; // поддержка страницы оплаты (отсылается письмо клиенту со ссылкой на страницу оплаты)
$APP_CONF['SHOW_IP_ADDRESS'] = false; // отображать в панели управления с какого ip адреса сделан заказа
$APP_CONF['SHOP_REMOVE_ITEMS_WITH_ZERO_QUANTITY'] = true; // автоматически удалять из корзины товары с нулевым количеством
// Учитывать настройки рабочего времени.
// Используйте функцию Shop_Helper::isTheShopOpen() для определения работы магазина.
$APP_CONF['SHOP_ENABLE_WORK_TIME'] = false;
//Не удалять продукты и категории из БД, но не отображать (флаг deleted).
//Включать при подключении магазина, чтобы не было ошибок при удалении из-за связей с заказами
$APP_CONF['CATALOG_DISABLE_DELETE'] = true;

$APP_CONF['PAYMENT_SHOW_TABS'] = true;
// Поддержка режима витрины
$APP_CONF['SHOWCASE_MODE_SUPPORT'] = false;

Phpr::$router->addPrefix('shop-prefix', 'shop');
Phpr::$router->addRule('$shop-prefix')->controller('shop')->action('index');
Phpr::$router->addRule('$shop-prefix/cart', 'shop_cart')->controller('shop')->action('cart');
Phpr::$router->addRule('$shop-prefix/checkout', 'shop_checkout')->controller('shop')->action('checkout');
Phpr::$router->addRule('$shop-prefix/cart/print', 'cart_print')->controller('shop')->action('cart_print');
