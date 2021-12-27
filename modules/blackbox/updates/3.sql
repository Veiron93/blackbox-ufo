begin;
alter table catalog_products add is_sale tinyint(1);
commit;