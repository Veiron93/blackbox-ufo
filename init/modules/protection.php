<?php
Phpr::$router->addPrefix('protection-prefix', 'protection');
Phpr::$router->addRule('$protection-prefix', 'protection_index')->controller('protection')->action('index');
