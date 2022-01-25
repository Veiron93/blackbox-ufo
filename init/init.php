<?php

$APP_CONF = array();

/*
 * Override the Security object
 */
Phpr::$security = new Core_Security();

/*
 * Begin the session
 */
Phpr::$session->start();


/*
 * Admin routes
 */
$backendRoot = isset($CONFIG['BACKEND_URL']) ? $CONFIG['BACKEND_URL'] : '/admin';
if (substr($backendRoot, 0, 1) == '/')
	$backendRoot = substr($backendRoot, 1);

$route = Phpr::$router->addRule("sitemap.xml");
$route->folder('modules/admin/controllers');
$route->controller('admin_sitemap');
$route->action('output');

$route = Phpr::$router->addRule($backendRoot . "/:module/:controller/:param1/:param2");
$route->folder('modules/:module/controllers');
$route->def('controller', 'index');
$route->action('index');
$route->check('param1', '/^[0-9]+$/');
$route->check('param2', '/^[0-9]+$/');
$route->convert('controller', '/^.*$/', ':module_$0');

$route = Phpr::$router->addRule($backendRoot . "/:module/:controller/:action/:param1/:param2/:param3/:param4");
$route->folder('modules/:module/controllers');
$route->def('module', 'admin');
$route->def('controller', 'index');
$route->def('action', 'index');
$route->def('param1', null);
$route->def('param2', null);
$route->def('param3', null);
$route->def('param4', null);
$route->convert('controller', '/^.*$/', ':module_$0');

$route = Phpr::$router->addRule('site_get_file/:param1/:param2')
	->controller('application')
	->action('site_get_file')
	->check('param1', '/^[0-9]+$/')
	->def('param2', null);

/*
 *
 * Изменять маршруты можно начиная с этого места.
 *
 */

phrp_configure_modules();

// Common routes

$route = Phpr::$router->addRule("");
$route->controller('application');
$route->action('index');

Phpr::$router->addRule(":action")->controller('application');

$route = Phpr::$router->addRule(":controller/:param1");
$route->action('index');
$route->check('param1', '/^[0-9]+$/');

$route = Phpr::$router->addRule(':controller/:action/:param1/:param2/:param3/:param4');
$route->def('param1', null);
$route->def('param2', null);
$route->def('param3', null);
$route->def('param4', null);
$route->def('action', 'index');

/*
 * Send the no-cache headers
 */
header("Pragma: public");
header("Expires: 0");
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0, max-age=0', false);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

header("Content-type: text/html; charset=utf-8");

/*
 * Init multibyte strings encoding
 */
mb_internal_encoding('UTF-8');

$APP_CONF['WEBSITE_NAME'] = 'Шаблон сайта';
$APP_CONF['ENABLE_TYPOGRAPHIC'] = false;

$APP_CONF['STATICPAGE_LAYOUT'] = 'default';
$APP_CONF['404_LAYOUT'] = 'not_found';
$APP_CONF['GA_WEB_PROPERTY_ID'] = '';
$APP_CONF['ENABLE_MODULE_INIT_FILES'] = true;
$APP_CONF['AUTO_CREATE_MODULE_FILES'] = true;
$APP_CONF['DISABLE_AUTOMATIC_MENU_HIDING'] = true;

$APP_CONF['PHPR_USE_PROXY_MODELS'] = true;

$APP_CONF['PJCCOMP_CACHE'] = 'temp';

$APP_CONF['HANGING_PUNCTUATION'] = false;

$APP_CONF['ADMIN_PAGES_IMAGE'] = true;
//$APP_CONF['ADMIN_PAGES_DESCRIPTION'] = false;
$APP_CONF['TINYMCE_HTML_CONTENT_STYLES'] = "/resources/css/tinymce.css";

/*$APP_CONF['STATIC_TEMPLATES_NAME_ASSOCIATIONS'] = [
    'static_page' => 'По умолчанию'
];*/
$APP_CONF['DEFAULT_FILE_CATEGORY_NAME'] = 'Документы';
$APP_CONF['THUMBNAIL_SAVE_ORIGINAL_PATH'] = true;

$APP_CONF['PAGINATION_GO_AWAY_ONE'] = true; // убирать номер страницы из url для первой страницы в пагинаторе


$APP_CONF['DELIVERY'] = [
    ['name' => 'Самовывоз', 'price' => '0', 'code' => 'pickup', 'checked'=>true, 'description' => '<p>Адрес: <a href="https://go.2gis.com/hy97i" target="_blank">Карла Маркса 51, офис 108</a></p><p>Ежедневно с 10:00 до 20:00</p>'],
    ['name' => 'по Южно-Сахалинску', 'code' => 'ys','price' => '200'],
    ['name' => 'Дальнее, Хомутово', 'code' => 'sector-1','price' => '250'],
    ['name' => 'Троицкое, Ново-Троицкое, Луговое', 'code' => 'sector-2','price' => '300'],
    ['name' => 'Ново-Александровск', 'code' => 'sector-3','price' => '350']
];

$APP_CONF['TEXT_WHATSAPP_QUESTION_PRODUCT'] = "https://api.whatsapp.com/send?phone=79624192078&text=%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5%2C%20%D0%BF%D0%BE%D0%B4%D1%81%D0%BA%D0%B0%D0%B6%D0%B8%D1%82%D0%B5%20%D0%BF%D0%BE%D0%B6%D0%B0%D0%BB%D1%83%D0%B9%D1%81%D1%82%D0%B0%20%D0%BF%D0%BE%20%D0%B4%D0%B0%D0%BD%D0%BD%D0%BE%D0%BC%D1%83%20%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D1%83%20";