<?php

//не трогать
$APP_CONF['ERROR_IGNORE'][] = 'Phpr_NotAcceptableException';

Phpr::$router->addPrefix("catalog-prefix", "catalog");

$route = Phpr::$router->addRule('$catalog-prefix/product/:product_id', "catalog_product")->controller('catalog')->action('product');
$route = Phpr::$router->addRule('$catalog-prefix', "catalog_index")->controller('catalog')->action('index');

$route = Phpr::$router->addRule('$catalog-prefix/:category_id/:page_index', "catalog_category");
$route->check('page_index', '/^[0-9]+$/');
$route->def('page_index', null);
$route->controller('catalog');
$route->action('category');


$APP_CONF['CATALOG_CATEGORIES_PANEL_NAME'] = 'Категории';
$APP_CONF['CATALOG_PRODUCTS_PANEL_NAME'] = 'Продукты';
$APP_CONF['CATALOG_MANUAL_PRODUCT_SORTING'] = false;
$APP_CONF['CATALOG_ENABLE_PDF_EXPORT_BUTTON'] = false;
$APP_CONF['CATALOG_ENABLE_PRODUCT_SHORT_DESCRIPTION'] = false; // Включить краткое описание
$APP_CONF['CATALOG_MAX_CATEGORY_NESTING_LEVEL'] = 3; // Максимальный уровень вложенности категорий, null означает неограниченную вложенность
$APP_CONF['CATALOG_ENABLE_CATEGORY_CODES'] = false; // Включает коды категорий
$APP_CONF['CATALOG_ENABLE_IS_NEW_FLAG'] = true; //отображать чекбокс "Новинка"
$APP_CONF['CATALOG_ENABLE_BESTSELLER_FLAG'] = true; //отображать чекбокс "Топ продаж"
$APP_CONF['CATALOG_HAS_QUANTITY_COLLECT'] = true; //Считать остатки
$APP_CONF['CATALOG_HAS_QUANTITATIVE_ATTRIBUTES'] = false; //Поддержка количественных характеристик, не работает без CATALOG_HAS_QUANTITY_COLLECT
$APP_CONF['CATALOG_ENABLE_ATTRGROUPS_COPYING'] = false; //Включить возможность копирования групп характеристик
$APP_CONF['CATALOG_ENABLE_FILTERS'] = true; //Включить поддержку фильтров
$APP_CONF['CATALOG_ENABLE_ATTRIBUTES'] = true; //Включить поддержку характеристик и типов продуктов
$APP_CONF['CATALOG_ENABLE_SKUS'] = true; //Включить поддержку артикулов
$APP_CONF['CATALOG_ENABLE_SKUS_PAGINATION'] = false; //Включить пагинацию в артикулах
$APP_CONF['CATALOG_ENABLE_OLD_PRICE'] = true; //Включить поле "старая цена"
$APP_CONF['CATALOG_ENABLE_DISCOUNT'] = false; //Включить поле "скидка"
$APP_CONF['CATALOG_PRODUCT_COPY_ENABLE'] = false; // Включить возможность копирования продукта (создание нового продукта на основе текущего)
$APP_CONF['CATALOG_PRODUCT_COPY_FIELDS'] = [ // Список полей для копирования
    'category_id',
    'path',
    'type_id',
    'name',
    'description',
    'price',
    'discount',
    'best_seller',
    'is_new',
    'hidden',
    'leftover',
    'short_description',
    'old_price',
    'start_discount_date',
    'deleted',
    'photos',
    'skus'];
// Включить возможность импорта данных, указанных ниже полей, из выбранного продукта в текущий продукт
$APP_CONF['CATALOG_PRODUCT_IMPORT_ENABLE'] = false;
$APP_CONF['CATALOG_PRODUCT_IMPORT_FIELDS'] = [ // Список полей для импорта
    'description',
    'photos'];
//template to sitemap.xml urls
//available placeholders:
// :id - id of the product
// :created_at - product create date !!!MAYBE NULL!!!
// :updated_at - product update date !!!MAYBE NULL!!!
// :category_id - id of the parent category. !!!NULL IF CATEGORIES ISN'T USED!!!
$APP_CONF['CATALOG_SITEMAP_URL_TEMPLATE'] = Phpr::$router->getPrefixedUrl("catalog-prefix", "product/:id");
// Это для SEO. Если указано название поля модели, то добавлять его значение в конец ссылки
$APP_CONF['CATALOG_SITEMAP_ADD_FIELD_NAME'] = '';

// ключать категории в sitemap
$APP_CONF['CATALOG_SITEMAP_CATEGORY_LIST'] = false;
// шабло URL для категории каталога
$APP_CONF['CATALOG_SITEMAP_CATEGORY_URL_TEMPLATE'] = Phpr::$router->getPrefixedUrl("catalog-prefix", ":id");
// если нужно использовать дополнительные поля из категории для формирования шаблона url, то нужно указать их
$APP_CONF['CATALOG_SITEMAP_CATEGORY_ADD_FIELD_NAME'] = '';