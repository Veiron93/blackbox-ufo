<?php

class Protection extends App_Controller
{
    public static $calculatorCategories = null;
    public static $types = null;
    public static $defaultActiveCategory = null;


    public function __construct()
    {
        $this->viewData['defaultActiveCategory'] = self::$defaultActiveCategory = 'phone';

        // категории устройств  
        self::$calculatorCategories = [
            'phone' => (object)['name' => 'Смартфоны', 'hidden' => false],
            'tablet' => (object)['name' => 'Планшеты', 'hidden' => false],
            'watch' => (object)['name' => 'Смарт-часы', 'hidden' => false]
        ];

        $this->viewData['calculatorCategories'] = self::$calculatorCategories;

        // сегменты
        self::$types =  [
            'premium' => (object)[
                'name' => 'Премиум',
                'icon' => '💎'
            ],
            'standart' => (object)[
                'name' => 'Стандарт',
                'icon' => '⭐'
            ],
            'base' => (object)[
                'name' => 'Базовый',
                'icon' => '🟢'
            ]
        ];

        // данные
        $this->viewData['calculatorSections'] = $this->getCalculatorData();

        parent::__construct();
    }

    public function index()
    {
        $this->layout = "__protection_layout";
    }

    public static function prefixService($type)
    {
        return self::$types[$type]->icon . ' ' . self::$types[$type]->name . ' - ';
    }


    private static function getCalculatorData()
    {
        // стоимость услуг для смартфонов
        $phone = (object)[
            'id' => 'phone',
            'services' => [
                (object)[
                    'name' => 'Плёнка на экран',
                    'code' => 'film-glass-phone',
                    'hidden' => false,
                    'segments' => [
                        'premium' => (object)[
                            'hidden' => false,
                            'prices' => [
                                (object)['name' => 'глянцевая', 'price' => 2700],
                                (object)['name' => 'матовая', 'price' => 2700],
                                (object)['name' => 'антишпион', 'price' => 3700],
                            ]
                        ],
                        'standart' => (object)[
                            'hidden' => false,
                            'prices' => [
                                (object)['name' => 'глянцевое покрытие', 'price' => 1500],
                                (object)['name' => 'матовое покрытие', 'price' => 1500],
                            ]

                        ],
                        'base' => (object)[
                            'hidden' => false,
                            'prices' => [
                                (object)['name' => 'глянцевое покрытие', 'price' => 650],
                                (object)['name' => 'матовое покрытие', 'price' => 650],
                            ]
                        ],
                    ],
                ],

                (object)[
                    'name' => 'Плёнка на заднюю панель',
                    'code' => 'film-back-phone',
                    'hidden' => false,
                    'segments' => [
                        'premium' => (object)[
                            'hidden' => false,
                            'prices' => [
                                (object)['name' => 'глянцевая', 'price' => 2700],
                                (object)['name' => 'матовая', 'price' => 2700],
                                (object)['name' => 'антишпион', 'price' => 3700],
                            ]
                        ],
                        'standart' => (object)[
                            'hidden' => false,
                            'prices' => [
                                (object)['name' => 'глянцевое покрытие', 'price' => 1500],
                                (object)['name' => 'матовое покрытие', 'price' => 1500],
                            ]

                        ],
                        'base' => (object)[
                            'hidden' => false,
                            'prices' => [
                                (object)['name' => 'глянцевое покрытие', 'price' => 650],
                                (object)['name' => 'матовое покрытие', 'price' => 650],
                            ]
                        ],
                    ],
                ],
            ],


            // 'Плёнка на заднюю панель' => [
            //     (object)['name' => '💎 Премиум - глянцевое покрытие', 'price' => 1200, 'code' => ''],
            //     (object)['name' => '💎 Премиум - матовое покрытие', 'price' => 1330, 'code' => ''],

            //     (object)['name' => '⭐ Стандарт - глянцевое покрытие', 'price' => 1530, 'code' => ''],
            //     (object)['name' => '⭐ Стандарт - матовое покрытие', 'price' => 1330, 'code' => ''],

            //     (object)['name' => '🟢 Базовый - глянцевое покрытие', 'price' => 1530, 'code' => ''],
            //     (object)['name' => '🟢 Базовый - матовое покрытие', 'price' => 1330, 'code' => ''],
            // ],
            // 'Плёнка на торцы' => [
            //     (object)['name' => '💎 Премиум - глянцевое покрытие', 'price' => 1200, 'code' => ''],
            //     (object)['name' => '💎 Премиум - матовое покрытие', 'price' => 1330, 'code' => ''],

            //     (object)['name' => '⭐ Стандарт - глянцевое покрытие', 'price' => 1530, 'code' => ''],
            //     (object)['name' => '⭐ Стандарт - матовое покрытие', 'price' => 1330, 'code' => ''],

            //     (object)['name' => 'Базовый - глянцевое покрытие', 'price' => 1530, 'code' => ''],
            //     (object)['name' => 'Базовый - матовое покрытие', 'price' => 1330, 'code' => ''],
            // ],
            // 'Стекло на экран' => [
            //     (object)['name' => '💎 Премиум - глянцевое', 'price' => 1200, 'code' => ''],
            //     (object)['name' => '💎 Премиум - матовое', 'price' => 1330, 'code' => ''],
            //     (object)['name' => '💎 Премиум - антишпион', 'price' => 1330, 'code' => ''],

            //     (object)['name' => '⭐ Стандарт - глянцевое', 'price' => 1200, 'code' => ''],
            //     (object)['name' => '⭐ Стандарт - матовое', 'price' => 1330, 'code' => ''],
            //     (object)['name' => '⭐ Стандарт - антишпион', 'price' => 1330, 'code' => ''],

            //     (object)['name' => 'Базовый - глянцевое', 'price' => 1200, 'code' => ''],
            //     (object)['name' => 'Базовый - матовое', 'price' => 1330, 'code' => ''],
            //     (object)['name' => 'Базовый - антишпион', 'price' => 1330, 'code' => ''],
            // ],
            // 'Стекло на камеру' => [
            //     (object)['name' => 'Rimax - глянцевое', 'price' => 1230, 'code' => 'краткое описание'],
            //     (object)['name' => 'Rimax 1', 'price' => 1330, 'code' => ''],
            //     (object)['name' => 'Rimax 2', 'price' => 1530, 'code' => ''],
            //     (object)['name' => 'Rimax 3', 'price' => 1730, 'code' => ''],
            // ],
        ];


        return [$phone];
    }
}
