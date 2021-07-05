<?php

//Использовать реальный сервер Сбербанка для транзакций, если false — используется тестовый
//настраивать в config.php, в этом файле не использовать
//$CONFIG['PAYMENT_SBERBANK_PRODUCTION'] = false;

$APP_CONF['PAYMENT_ENABLE_ORDERS_INTEGRATION'] = true; //включить интеграцию в каталог
$APP_CONF['PAYMENT_ENABLE_SHOP_INTEGRATION'] = true; //включить интеграцию с магазином
$APP_CONF['PAYMENT_SHOW_TABS'] = false; //показывать раздел 'Платежи' в левом меню и 'Платежные системы' в настройках
//Список доступных платежных бэкэндов
//Пустой массив — разрешить все имеющиеся
//array('Payment_CardBackend', 'Payment_CashBackend') — разрешить только оплату наличными и банковской картой курьеру
$APP_CONF['PAYMENT_AVAILABLE_BACKENDS'] = array();

//Скан печати
$APP_CONF['PAYMENT_INVOICE_STAMP'] = "/resources/images/payment/stamp.png";

//Скан подписи главного бухгалтера
$APP_CONF['PAYMENT_INVOICE_CHIEF_SIGNATURE'] = "/resources/images/payment/chief.png";

//Скан подписи руководителя
$APP_CONF['PAYMENT_INVOICE_LEADER_SIGNATURE'] = "/resources/images/payment/leader.png";

$APP_CONF['TRACE_LOG']['SBERBANK'] = PATH_APP . '/logs/sberbank.txt';

Phpr::$router->addPrefix('payment-prefix', 'payment');
Phpr::$router->addRule('$payment-prefix/prepaid/:via/:payment_id/:param1/:param2', 'payment_prepaid')
	->controller('payment')
	->action('prepaid')
	->def('param1', null)
	->def('param2', null)
	->check('via', "/^[a-z]+$/")
	->check('payment_id', "/^[a-f0-9]+$/");
Phpr::$router->addRule('$payment-prefix/success/:via/:payment_id/:param1/:param2', 'payment_success')
	->controller('payment')
	->action('success')
	->def('param1', null)
	->def('param2', null)
	->check('via', "/^[a-z]+$/")
	->check('payment_id', "/^[a-f0-9]+$/");
Phpr::$router->addRule('$payment-prefix/failed/:via/:payment_id/:param1/:param2', 'payment_failed')
	->controller('payment')
	->action('failed')
	->def('param1', null)
	->def('param2', null)
	->check('via', "/^[a-z]+$/")
	->check('payment_id', "/^[a-f0-9]+$/");
Phpr::$router->addRule('$payment-prefix/callback/:via', 'payment_callback')
	->controller('payment')
	->action('callback')
	->check('via', "/^[a-z]+$/");