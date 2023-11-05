<?php

/** @noinspection PhpUnused */

/**
 * UFO CMF
 *
 * PHP application framework
 *
 * @package     UFO CMF
 * @copyright   (c) 2023, Rinamika, http://rinamika.ru
 * @author      Dmitry Yudin
 * @since       1.0
 * @license     http://rinamika.ru/ufo/licence.txt Rinamika Application License
 * @filesource
 */

/**
 * Class Tools_Catalog
 */
class Tools_Catalog extends Admin_Controller
{

    protected $globalHandlers = [
        "onResetNew",
        "onResetBestSellers",
        "onResetDiscountIndex",
        "onResetDiscounts",
        "onAddDiscounts",
        "onBeautifulPrice",
        "onResetIndividualPrices",
        "onResetBeautifulPrice"
    ];

    public $tools_catalog_redirect = null;

    public $reset_new_flash = 'Все Новинки сброшены';
    public $reset_best_sellers_flash = 'Все Хит продаж сброшены';
    public $reset_discounts_flash = 'Все Скидки сброшены';
    public $add_discounts_flash = 'Скидка применена';
    public $beautiful_price_flash = 'Красивая цена установлена';
    public $reset_beautiful_price_flash = 'Красивая цена сброшена';

    protected $required_permissions = ['tools:manage'];

    /**
     * @throws Phpr_SystemException
     */
    public function __construct()
    {
        $this->tools_catalog_redirect = url('tools/catalog');
        $this->app_tab = 'tools';
        $this->app_sub_tab = 'tools_catalog';
        $this->addCss('/modules/tools/resources/css/tools_catalog.css');
        $this->addJavaScript('/modules/tools/resources/js/tools_catalog.js');
        parent::__construct();
    }

    public function index()
    {
        $this->app_page_title = 'Каталог - Инструменты';
    }


    protected function onResetNew()
    {
        try {
            Db_DbHelper::query("UPDATE catalog_products SET is_new = null WHERE is_new = 1");

            Phpr::$session->flash['success'] = $this->reset_new_flash;
            Phpr::$response->redirect($this->tools_catalog_redirect);
        } catch (Exception $exception) {
            Phpr::$response->ajaxReportException($exception, true, true);
        }
    }


    protected function onResetBestSellers()
    {
        try {
            Db_DbHelper::query("UPDATE catalog_products SET best_seller = null WHERE best_seller = 1");

            Phpr::$session->flash['success'] = $this->reset_best_sellers_flash;
            Phpr::$response->redirect($this->tools_catalog_redirect);
        } catch (Exception $exception) {
            Phpr::$response->ajaxReportException($exception, true, true);
        }
    }

    protected function onResetDiscountIndex()
    {
        try {
            Db_DbHelper::query("UPDATE catalog_products SET is_sale = null WHERE is_sale = 1");

            Phpr::$session->flash['success'] = $this->reset_best_sellers_flash;
            Phpr::$response->redirect($this->tools_catalog_redirect);
        } catch (Exception $exception) {
            Phpr::$response->ajaxReportException($exception, true, true);
        }
    }


    protected function onResetDiscounts()
    {
        try {
            Db_DbHelper::query("UPDATE catalog_products as cp SET price = (SELECT old_price FROM catalog_products WHERE id = cp.id), old_price = null WHERE old_price is not null AND individual_price is null");

            Phpr::$session->flash['success'] = $this->reset_discounts_flash;
            Phpr::$response->redirect($this->tools_catalog_redirect);
        } catch (Exception $exception) {
            Phpr::$response->ajaxReportException($exception, true, true);
        }
    }

    protected function onResetIndividualPrices()
    {
        try {
            Db_DbHelper::query("UPDATE catalog_products as cp SET price = (SELECT old_price FROM catalog_products WHERE id = cp.id), old_price = null, individual_price = null WHERE old_price is not null AND individual_price is not null");


            Phpr::$session->flash['success'] = $this->reset_discounts_flash;
            Phpr::$response->redirect($this->tools_catalog_redirect);
        } catch (Exception $exception) {
            Phpr::$response->ajaxReportException($exception, true, true);
        }
    }


    protected function onAddDiscounts()
    {
        try {
            $discount = $_POST['discount'];

            if (!$discount) {
                Phpr::$session->flash['error'] = "Введите скидку";
                Phpr::$response->redirect($this->tools_catalog_redirect);

                return;
            }

            Db_DbHelper::query("UPDATE catalog_products AS cp
                SET cp.old_price = cp.price, cp.price = (cp.price - (cp.price / 100 * $discount))
                WHERE 
                    cp.deleted is null AND
                    cp.hidden is null AND
                    cp.individual_price is null");

            Phpr::$session->flash['success'] = $this->add_discounts_flash;
            Phpr::$response->redirect($this->tools_catalog_redirect);
        } catch (Exception $exception) {
            Phpr::$response->ajaxReportException($exception, true, true);
        }
    }


    protected function onBeautifulPrice()
    {
        try {
            Db_DbHelper::query("UPDATE catalog_products SET price = CEILING(price) - 1 WHERE deleted is null AND hidden is null AND individual_price is null");

            Phpr::$session->flash['success'] = $this->beautiful_price_flash;
            Phpr::$response->redirect($this->tools_catalog_redirect);
        } catch (Exception $exception) {
            Phpr::$response->ajaxReportException($exception, true, true);
        }
    }


    protected function onResetBeautifulPrice()
    {
        try {
            Db_DbHelper::query("UPDATE catalog_products SET price = (price + 1) WHERE deleted is null AND hidden is null AND individual_price is null");

            Phpr::$session->flash['success'] = $this->reset_beautiful_price_flash;
            Phpr::$response->redirect($this->tools_catalog_redirect);
        } catch (Exception $exception) {
            Phpr::$response->ajaxReportException($exception, true, true);
        }
    }
}
