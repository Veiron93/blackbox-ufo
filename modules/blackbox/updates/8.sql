begin;
alter table catalog_products add is_useded_device tinyint(1);
commit;