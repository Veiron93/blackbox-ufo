begin;
alter table catalog_categories add title_sku varchar(50) default null;
commit;