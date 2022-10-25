begin;
alter table catalog_categories 
    add seo_title_add_postfix tinyint(1),
    add seo_description_add_postfix tinyint(1);

alter table catalog_products 
    add seo_title_add_postfix tinyint(1),
    add seo_description_add_postfix tinyint(1);
commit;