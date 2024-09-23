begin;
alter table catalog_categories add parent_title tinyint(1);
commit;