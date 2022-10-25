<?php

class Blackbox_SEO extends Admin_Controller
{

    public $implement = 'Db_ListBehavior, Db_FormBehavior';
    public $list_model_class = 'Blackbox_SEO';
    public $list_record_url = null;
    public $list_no_data_message = 'Не найдено ни одного прайс';
    public $list_custom_body_cells = null;
    public $list_custom_head_cells = null;
    public $form_preview_title = 'Прайс';
    public $form_create_title = 'Добавление';
    public $form_edit_title = 'Просмотр';
    public $form_model_class = 'Blackbox_SEO';
    public $form_not_found_message = 'Прайс не найден';
    public $form_redirect = null;
    public $form_edit_save_flash = 'Прайс был сохранен';
    public $form_create_save_flash = 'Прайс был добавлен';
    public $form_edit_delete_flash = 'Прайс был удален';
    public $form_edit_save_auto_timestamp = true;
    public $app_tab = "catalog";
    public $app_sub_tab = "seo";

    public function __construct()
    {
        traceLog(3333);

        $this->list_record_url = url('catalog/seo/edit/');
        $this->form_redirect = url('catalog/seo/');

        parent::__construct();
    }

    public function index()
    {
        $this->app_page_title = "Настройки SEO";
    }

    protected function getPriceUrl($filename)
    {
        //$path = Pharmacy_Enum::PRICES_DIR_PATH . $filename;
        return ($filename && file_exists(PATH_APP . $path)) ? $path : false;
    }
}
