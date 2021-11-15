<?php
    Phpr::$router->addPrefix('sale-prefix', 'sale');
    Phpr::$router->addRule('$sale-prefix')->controller('sale')->action('index');
