<?php

Phpr::$router->addPrefix("search-prefix", "search");
Phpr::$router->addRule('$search-prefix', "search_index")->controller('search')->action('index');


Phpr::$router->addRule('$search-prefix/widget/', "search_widget")->controller('search')->action('widget');