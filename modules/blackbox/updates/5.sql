begin;
alter table catalog_products add title_sku varchar(50) default null;
commit;