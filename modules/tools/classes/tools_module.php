<?php

class Tools_Module extends Core_ModuleBase
{
    protected function createModuleInfo()
    {
        return new Core_ModuleInfo('Tools', "Ринамика", "Tools модуль");
    }


    /**
     * @param Admin_TabCollection $tabCollection
     * @return void
     */
    public function listTabs($tabCollection)
    {
        $user = Phpr::$security->getUser();

        if ($user && $user->get_permission('company', 'manage')) {
            $tab = $tabCollection->tab('tools', "Инструменты", 'catalog', 19, 'icon-tools');
            $tab->contentTab("tools_catalog", "Каталог", url('tools/catalog'));
        }
    }


    public function subscribeEvents()
    {

        // Phpr::$events->addEvent(Catalog_Events::onExtendTabs, function (Admin_Tab $tab) {
        //     $tab->contentTab('seo', 'Настройки SEO', url("catalog/seo"));
        // });


        // Phpr::$events->addEvent(Db_Events::onModelDefineColumns, function (Db_ActiveRecord $model) {
        // });

        // Phpr::$events->addEvent(Db_Events::onModelDefineFormFields, function (Db_ActiveRecord $model) {
        // });
    }
}
