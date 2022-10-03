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
                $model->defineColumn("title_sku", "Заголовок у артикулов");
                $model->defineColumn("is_sale", "Добавить в блок - Товары со скидкой");

                // расширение для Б/У товаров
                $model->defineColumn("is_useded_device", "Товар Б/У");
                $model->defineColumn("state_device_useded_device", "Состояние устройства");
                $model->defineColumn("state_battery_useded_device", "Состояние аккумулятора");
                $model->defineColumn("guarantee_useded_devicet", "Гарантия");
                $model->defineColumn("defect_screen_useded_device", "Царапины на экране");
                $model->defineColumn("defect_body_useded_device", "Царапины на корпусе");
                $model->defineColumn("complect_useded_device", "Полный комплект");
                $model->defineColumn("complect_non_elements_useded_device", "Отсутствующие предметы");
                $model->defineColumn("added_acsessuares_useded_device", "Дополнительные аксессуары");
            }
        });

        Phpr::$events->addEvent(Db_Events::onModelDefineFormFields, function (Db_ActiveRecord $model) {
            if ($model instanceof Catalog_Category) {
                $model->addFormField("hot", "left")->sortOrder(50)->tab(Catalog_Category::$generalTabTitle);
                $model->addFormField("title_sku", "left")->sortOrder(20)->tab("Дополнительно");
            }

            if ($model instanceof Catalog_Product) {
                $model->addFormField("regular_photo", "left")->sortOrder(60)->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("sales", "left")->sortOrder(50)->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("title_sku", "left")->sortOrder(10)->tab("Дополнительно");
                $model->addFormField("is_sale", "left")->sortOrder(90)->tab(Catalog_Product::$generalTabTitle);

                // расширение для Б/У товаров
                $tab = self::tabs['usedProducts'];

                $model->addFormField("is_useded_device", "left")->sortOrder(10)->tab($tab);
                $model->addFormField("state_device_useded_device", "left")->sortOrder(20)->tab($tab);
                $model->addFormField("state_battery_useded_device", "left")->sortOrder(30)->tab($tab);
                $model->addFormField("guarantee_useded_devicet", "left")->sortOrder(40)->tab($tab);
                $model->addFormField("defect_screen_useded_device", "left")->sortOrder(50)->tab($tab);
                $model->addFormField("defect_body_useded_device", "left")->sortOrder(60)->tab($tab);
                $model->addFormField("complect_useded_device", "left")->sortOrder(70)->tab($tab);
                $model->addFormField("complect_non_elements_useded_device", "left")->sortOrder(80)->tab($tab);
                $model->addFormField("added_acsessuares_useded_device", "left")->sortOrder(90)->tab($tab);
            }
        });
    }
}
