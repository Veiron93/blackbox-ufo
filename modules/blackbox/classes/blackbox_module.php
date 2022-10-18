<?php

class Blackbox_Module extends Core_ModuleBase
{

    protected function createModuleInfo()
    {
        return new Core_ModuleInfo("Blackbox", "Ринамика", "Blackbox модуль");
    }

    const tabs = ['usedProducts' => 'Товар Б/У'];

    public function subscribeEvents()
    {
        Phpr::$events->addEvent(Db_Events::onModelDefineColumns, function (Db_ActiveRecord $model) {
            if ($model instanceof Catalog_Category) {
                $model->defineColumn("hot", "Популярная категория");
                $model->defineColumn("title_sku", "Заголовок у артикулов");
            }

            if ($model instanceof Catalog_Product) {
                $model->defineColumn("regular_photo", "Обычное фото");
                $model->defineColumn("sales", "Продано");
                $model->defineColumn("title_sku", "Заголовок у артикулов")->invisible();
                $model->defineColumn("is_sale", "Добавить в блок - Товары со скидкой")->invisible();

                // расширение для Б/У товаров
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
            }
        });

        Phpr::$events->addEvent(Db_Events::onModelDefineFormFields, function (Db_ActiveRecord $model) {

            if ($model instanceof Catalog_Category) {
                $model->addFormField("hot", "left")->sortOrder(50)->tab(Catalog_Category::$generalTabTitle);
                $model->addFormField("title_sku", "left")->sortOrder(20)->tab("Дополнительно");
            }

            if ($model instanceof Catalog_Product) {
                $model->addFormField("sales", "left")->sortOrder(50)->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("regular_photo", "left")->sortOrder(70)->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("is_sale", "left")->sortOrder(80)->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("title_sku", "left")->sortOrder(90)->tab("Дополнительно");


                // расширение для Б/У товаров
                $tab = self::tabs['usedProducts'];

                $model->addFormField("is_useded_device", "left")->sortOrder(110)->tab($tab);
                $model->addFormField("show_block_useded_device", "left")->sortOrder(120)->tab($tab);
                $model->addFormField("state_device_useded_device", "left")->sortOrder(130)->tab($tab);
                $model->addFormField("state_battery_useded_device", "left")->sortOrder(140)->tab($tab);
                $model->addFormField("guarantee_useded_devicet", "left")->sortOrder(150)->tab($tab);
                $model->addFormField("defect_screen_useded_device", "left")->sortOrder(160)->tab($tab);
                $model->addFormField("defect_body_useded_device", "left")->sortOrder(170)->tab($tab);
                $model->addFormField("complect_useded_device", "left")->sortOrder(180)->tab($tab);
                $model->addFormField("complect_non_elements_useded_device", "left")->sortOrder(190)->tab($tab);
                $model->addFormField("added_acsessuares_useded_device", "left")->sortOrder(200)->tab($tab);
            }

            if ($model instanceof Shop_Order) {
                $model->addFormPartial("modules/blackbox/controllers/partials/_list_goods.htm")->tab('Список товаров');
            }
        });
    }
}
