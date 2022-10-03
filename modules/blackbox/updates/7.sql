begin;
alter table catalog_products 
    add state_device_useded_device varchar(2) default null,
    add state_battery_useded_device varchar(2) default null,
    add guarantee_useded_devicet varchar(100) default null,
    add defect_screen_useded_device tinyint(1),
    add defect_body_useded_device tinyint(1),
    add complect_useded_device tinyint(1),
    add complect_non_elements_useded_device text default null,
    add added_acsessuares_useded_device text default null;
commit;