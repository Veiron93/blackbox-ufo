<?php

class Blackbox_Module extends Core_ModuleBase
{

    protected function createModuleInfo()
    {
        return new Core_ModuleInfo('Blackbox', "Ринамика", "Blackbox модуль");
    }



    const tabs = [
        'usedProducts' => 'Товар Б/У',
        'seo' => 'SEO'
    ];


    public function subscribeEvents()
    {

        // Phpr::$events->addEvent(Catalog_Events::onExtendTabs, function (Admin_Tab $tab) {
        //     $tab->contentTab('seo', 'Настройки SEO', url("catalog/seo"));
        // });


        Phpr::$events->addEvent(Db_Events::onModelDefineColumns, function (Db_ActiveRecord $model) {
            if ($model instanceof Catalog_Category) {
                $model->defineColumn("hot", "Популярная категория");
                $model->defineColumn("title_sku", "Заголовок у артикулов");

                // SEO
                $model->defineColumn("seo_title_add_postfix", "Добавить постфикс к Title")->invisible();
                $model->defineColumn("seo_description_add_postfix", "Добавить постфикс к Description")->invisible();
            }

            if ($model instanceof Catalog_Product) {
                $model->defineColumn("regular_photo", "Обычное фото");
                $model->defineColumn("sales", "Продано");
                $model->defineColumn("individual_price", "Индивидуальная цена");
                $model->defineColumn("title_sku", "Заголовок у артикулов")->invisible();
                $model->defineColumn("is_sale", "Добавить в блок - Товары со скидкой")->invisible();

                // Б/У товаровы
                $model->defineColumn("is_useded_device", "Товар Б/У")->invisible();
                $model->defineColumn("show_block_useded_device", "Добавить в блок на главной")->invisible();
                $model->defineColumn("state_device_useded_device", "Состояние устройства")->invisible();
                $model->defineColumn("state_battery_useded_device", "Состояние аккумулятора")->invisible();
                $model->defineColumn("guarantee_useded_devicet", "Гарантия")->invisible();
                $model->defineColumn("defect_screen_useded_device", "Царапины на экране")->invisible();
                $model->defineColumn("defect_body_useded_device", "Царапины на корпусе")->invisible();
                $model->defineColumn("complect_useded_device", "Полный комплект")->invisible();
                $model->defineColumn("complect_non_elements_useded_device", "Отсутствующие предметы")->invisible();
                $model->defineColumn("added_acsessuares_useded_device", "Дополнительные аксессуары")->invisible();

                // SEO
                $model->defineColumn("seo_title_add_postfix", "Добавить постфикс к Title")->invisible();
                $model->defineColumn("seo_description_add_postfix", "Добавить постфикс к Description")->invisible();
            }
        });

        Phpr::$events->addEvent(Db_Events::onModelDefineFormFields, function (Db_ActiveRecord $model) {

            if ($model instanceof Catalog_Category) {
                $model->addFormField("hot", "left")->sortOrder(50)->tab(Catalog_Category::$generalTabTitle);
                $model->addFormField("title_sku", "left")->sortOrder(20)->tab("Дополнительно");

                $tabSEO = self::tabs['seo'];
                $model->addFormField("seo_title_add_postfix", "left")->sortOrder(80)->tab($tabSEO);
                $model->addFormField("seo_description_add_postfix", "left")->sortOrder(90)->tab($tabSEO);
            }

            if ($model instanceof Catalog_Product) {
                $model->addFormField("regular_photo", "left")->sortOrder(10)->tab('Фотографии');
                $model->addFormField("title_sku", "left")->sortOrder(90)->tab("Дополнительно");

                $model->addFormField("individual_price", "left")->sortOrder(50)->comment('На стоимость товара не повлияют другие факторы. Например: общая скидка, красивая цена', 'above')->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("is_sale", "left")->sortOrder(70)->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("sales")->sortOrder(110)->tab(Catalog_Product::$generalTabTitle);


                // Б/У товары
                $tabUsedDevice = self::tabs['usedProducts'];

                $model->addFormField("is_useded_device", "left")->sortOrder(110)->tab($tabUsedDevice);
                $model->addFormField("show_block_useded_device", "left")->sortOrder(120)->tab($tabUsedDevice);
                $model->addFormField("state_device_useded_device", "left")->sortOrder(130)->tab($tabUsedDevice);
                $model->addFormField("state_battery_useded_device", "left")->sortOrder(140)->tab($tabUsedDevice);
                $model->addFormField("guarantee_useded_devicet", "left")->sortOrder(150)->tab($tabUsedDevice);
                $model->addFormField("defect_screen_useded_device", "left")->sortOrder(160)->tab($tabUsedDevice);
                $model->addFormField("defect_body_useded_device", "left")->sortOrder(170)->tab($tabUsedDevice);
                $model->addFormField("complect_useded_device", "left")->sortOrder(180)->tab($tabUsedDevice);
                $model->addFormField("complect_non_elements_useded_device", "left")->sortOrder(190)->tab($tabUsedDevice);
                $model->addFormField("added_acsessuares_useded_device", "left")->sortOrder(200)->tab($tabUsedDevice);

                $tabSEO = self::tabs['seo'];
                $model->addFormField("seo_title_add_postfix", "left")->tab($tabSEO);
                $model->addFormField("seo_description_add_postfix", "left")->tab($tabSEO);
            }

            if ($model instanceof Shop_Order) {
                $model->addFormPartial("modules/blackbox/controllers/partials/_list_goods.htm")->tab('Список товаров');
            }
        });
    }
}
