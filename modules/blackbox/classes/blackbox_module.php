<?php

class Blackbox_Module extends Core_ModuleBase
{

    protected function createModuleInfo()
    {
        return new Core_ModuleInfo("Blackbox", "Ринамика", "Blackbox модуль");
    }

    public function subscribeEvents()
    {
        Phpr::$events->addEvent(Db_Events::onModelDefineColumns, function (Db_ActiveRecord $model) {
            if ($model instanceof Catalog_Category) {
                $model->defineColumn("hot", "Популярная категория");
            }

            if ($model instanceof Catalog_Product) {
                $model->defineColumn("regular_photo", "Обычное фото");
                $model->defineColumn("is_sale", "Добавить в блок Товары со скидкой");
                $model->defineColumn("sales", "Продано");
            }
        });

        Phpr::$events->addEvent(Db_Events::onModelDefineFormFields, function (Db_ActiveRecord $model) {
            if ($model instanceof Catalog_Category) {
                $model->addFormField("hot", "left")->sortOrder(50)->tab(Catalog_Category::$generalTabTitle);
            }

            if ($model instanceof Catalog_Product) {
                $model->addFormField("regular_photo", "left")->sortOrder(60)->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("is_sale", "left")->sortOrder(70)->tab(Catalog_Product::$generalTabTitle);
                $model->addFormField("sales", "left")->sortOrder(50)->tab(Catalog_Product::$generalTabTitle);
            }
        });
    }
}