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
                $model->defineColumn("state_device", "Состояние устройства");
                $model->defineColumn("state_battery", "Состояние аккумулятора");
                $model->defineColumn("garant", "Гарантия");
                $model->defineColumn("defect_screen", "Царапины на экране");
                $model->defineColumn("defect_body", "Царапины на корпусе");
                $model->defineColumn("complect", "Полный комплект");
                $model->defineColumn("complect_non_elements", "Отсутствующие предметы");
                $model->defineColumn("added_acsessuares", "Дополнительные аксессуары");
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
                $model->addFormField("title_sku", "left")->sortOrder(70)->tab("Дополнительно");
                $model->addFormField("is_sale", "left")->sortOrder(70)->tab("Дополнительно");

                // расширение для Б/У товаров
                $tab = self::tabs['usedProducts'];

                // $model->addFormField("state_device", "left")->sortOrder(10)->tab($tab);
                // $model->addFormField("state_battery", "right")->sortOrder(20)->tab($tab);
                // $model->addFormField("garant", "left")->sortOrder(30)->tab($tab);
                // $model->addFormField("defect_screen", "left")->sortOrder(40)->tab($tab);
                // $model->addFormField("defect_body", "right")->sortOrder(50)->tab($tab);
                // $model->addFormField("complect", "left")->sortOrder(60)->tab($tab);
                // $model->addFormField("complect_non_elements", "left")->sortOrder(70)->tab($tab);
                // $model->addFormField("added_acsessuares", "left")->sortOrder(80)->tab($tab);
            }
        });
    }
}
