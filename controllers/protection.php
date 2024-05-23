<?php

class Protection extends App_Controller
{
    public static $calculatorCategories = null;
    public static $types = null;
    public static $defaultActiveCategory = null;

    protected $globalHandlers = [
        "onOrderProtection"
    ];


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
                'icon' => '☘️'
            ]


        ];


        // данные
        $this->viewData['calculatorSections'] = $this->getCalculatorData();

        parent::__construct();
    }

    public function index()
    {
        $this->layout = "__protection_layout";

        $this->viewData['brands'] = ['Sunshine', 'Hoco', 'Mietubl', 'Remax', 'Baseus'];
    }


    protected function onOrderProtection()
    {
        try {
            $inputJSON = file_get_contents('php://input');

            $data = json_decode($inputJSON, TRUE);

            $data_order = $data['data'];
            $cart = $data['cart'];

            $values['phone'] = $data_order['phone'];
            $values['author_name'] = $data_order['name'];
            $values['author_email'] = "noreplay@bb65.ru";
            $values['date'] = $data_order['date'];
            $values['comment'] = self::initListServices($cart);

            $dateOrderArr = explode('-', $values['date']);

            if ($dateOrderArr) {
                $date = new Phpr_DateTime();
                $date->setDate(abs($dateOrderArr[0]), abs($dateOrderArr[1]), abs($dateOrderArr[2]));
                $values['comment'] .= PHP_EOL  . "Дата записи: " . $date->format('%e %B %Y');
            }

            if (!$this->validation->validate($values)) {
                $this->validation->throwException();
            }

            $message = GlobalComments_Comment::create($values);
            $message->save($values);

            $response['success'] = "Успех";

            $this->ajaxResponse($response);
        } catch (Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    private static function initListServices($cart)
    {
        $list = "";

        $data = self::getCalculatorData();
        $services = [];
        $totalPrice = 0;

        foreach ($data as $item) {
            foreach ($item->services as $service) {
                $services[$service->code] = $service;
            }
        }

        foreach ($cart as $cartItem) {
            $device = $cartItem['deviceName'];
            $deviceServices = "";

            if (isset($cartItem['services']) && $cartItem['services']) {
                foreach ($cartItem['services'] as $key => $service) {
                    $serviceData = explode('~', $service['code']);

                    $code = $serviceData[0];
                    $segment = $serviceData[1];
                    $index = $serviceData[2];

                    $nameService = $services[$code]->name;
                    $nameSegment = $services[$code]->segments[$segment]->prices[$index]->name;
                    $price = $services[$code]->segments[$segment]->prices[$index]->price;

                    $totalPrice += $price;

                    $segmentService = self::$types[$segment]->name . ' - ' . $nameSegment . ' - ' . $price . ' руб.';

                    $deviceServices .= ($key + 1) . '. ' . $nameService . PHP_EOL . $segmentService . PHP_EOL . PHP_EOL;
                }
            }

            $list .= ">>> " . $device . " <<<" . PHP_EOL . $deviceServices . PHP_EOL;
        }

        return $list . "Итоговая стоимость: " . $totalPrice . ' руб.';
    }

    public static function prefixService($type)
    {
        return self::$types[$type]->icon . ' ' . self::$types[$type]->name . ' - ';
    }

    private static function getCalculatorData()
    {
        ////////////////////////// СМАРТФОН
        // >>> ПЛЁНКИ
        // premium
        $pricePhoneFilmHDPremium = (object)['name' => 'глянцевая', 'price' => 1500];
        $pricePhoneFilmMattePremium = (object)['name' => 'матовая', 'price' => 1500];
        $pricePhoneFilmPrivacyHDPremium = (object)['name' => 'антишпион-глянцевая', 'price' => 2000];
        $pricePhoneFilmPrivacyMattePremium = (object)['name' => 'антишпион-матовая', 'price' => 2000];

        // standart
        $pricePhoneFilmHDStandart = (object)['name' => 'глянцевая', 'price' => 1300];
        $pricePhoneFilmMatteStandart = (object)['name' => 'матовая', 'price' => 1500];
        $pricePhoneFilmPrivacyHDStandart = (object)['name' => 'антишпион-глянцевая', 'price' => 1400, 'hidden' => true];
        $pricePhoneFilmPrivacyMatteStandart = (object)['name' => 'антишпион-матовая', 'price' => 1400, 'hidden' => true];

        // base
        $pricePhoneFilmHDBase = (object)['name' => 'глянцевая', 'price' => 700];
        $pricePhoneFilmMatteBase = (object)['name' => 'матовая', 'price' => 800];
        $pricePhoneFilmPrivacyHDBase = (object)['name' => 'антишпион-глянцевая', 'price' => 900, 'hidden' => true];
        $pricePhoneFilmPrivacyMatteBase = (object)['name' => 'антишпион-матовая', 'price' => 900, 'hidden' => true];


        // >>> СТЁКЛА ЭКРАН
        // premium
        $pricePhoneGlassHDPremium = (object)['name' => 'глянцевое', 'price' => 1300];
        $pricePhoneGlassMattePremium = (object)['name' => 'матовое', 'price' => 1400, 'hidden' => true];
        $pricePhoneGlassPrivacyHDPremium = (object)['name' => 'антишпион-глянцевое', 'price' => 1600];
        $pricePhoneGlassPrivacyMattePremium = (object)['name' => 'антишпион-матовое', 'price' => 1500, 'hidden' => true];

        // standart
        $pricePhoneGlassHDStandart = (object)['name' => 'глянцевое', 'price' => 900];
        $pricePhoneGlassMatteStandart = (object)['name' => 'матовое', 'price' => 900, 'hidden' => true];
        $pricePhoneGlassPrivacyHDStandart = (object)['name' => 'антишпион-глянцевое', 'price' => 1100, 'hidden' => true];
        $pricePhoneGlassPrivacyMatteStandart = (object)['name' => 'антишпион-матовое', 'price' => 1100, 'hidden' => true];

        // base
        $pricePhoneGlassHDBase = (object)['name' => 'глянцевое', 'price' => 550];
        $pricePhoneGlassMatteBase = (object)['name' => 'матовое', 'price' => 550, 'hidden' => true];
        $pricePhoneGlassPrivacyHDBase = (object)['name' => 'антишпион-глянцевое', 'price' => 800, 'hidden' => true];
        $pricePhoneGlassPrivacyMatteBase = (object)['name' => 'антишпион-матовое', 'price' => 800, 'hidden' => true];


        // >>> СТЁКЛА НА КАМЕРУ
        // premium
        $pricePhoneGlassCameraFullPremium = (object)['name' => 'полная защита', 'price' => 1000, 'hidden' => true];
        $pricePhoneGlassCameraLensesPremium = (object)['name' => 'защита линз', 'price' => 1000, 'hidden' => true];

        // standart
        $pricePhoneGlassCameraFullStandart = (object)['name' => 'полная защита', 'price' => 450, 'hidden' => true];
        $pricePhoneGlassCameraLensesStandart = (object)['name' => 'защита линз', 'price' => 450, 'hidden' => true];

        // base
        $pricePhoneGlassCameraFullBase = (object)['name' => 'полная защита', 'price' => 250];
        $pricePhoneGlassCameraLensesBase = (object)['name' => 'защита линз', 'price' => 300];


        ////////////////////////// ПЛАНШЕТ
        // >>> ПЛЁНКИ
        // premium
        $priceTabletFilmHDPremium = (object)['name' => 'глянцевая', 'price' => 2000];
        $priceTabletFilmMattePremium = (object)['name' => 'матовая', 'price' => 2000];
        $priceTabletFilmPrivacyHDPremium = (object)['name' => 'антишпион-глянцевая', 'price' => 2900];
        $priceTabletFilmPrivacyMattePremium = (object)['name' => 'антишпион-матовая', 'price' => 2900];

        // standart
        $priceTabletFilmHDStandart = (object)['name' => 'глянцевая', 'price' => 1100];
        $priceTabletFilmMatteStandart = (object)['name' => 'матовая', 'price' => 1100, 'hidden' => true];
        $priceTabletFilmPrivacyHDStandart = (object)['name' => 'антишпион-глянцевая', 'price' => 1900, 'hidden' => true];
        $priceTabletFilmPrivacyMatteStandart = (object)['name' => 'антишпион-матовая', 'price' => 1900, 'hidden' => true];

        // base
        $priceTabletFilmHDBase = (object)['name' => 'глянцевая', 'price' => 650];
        $priceTabletFilmMatteBase = (object)['name' => 'матовая', 'price' => 650];
        $priceTabletFilmPrivacyHDBase = (object)['name' => 'антишпион-глянцевая', 'price' => 1200];
        $priceTabletFilmPrivacyMatteBase = (object)['name' => 'антишпион-матовая', 'price' => 1200];


        ////////////////////////// СМАРТ-ЧАСЫ
        // >>> ПЛЁНКИ
        // premium
        $priceWatchFilmHDPremium = (object)['name' => 'глянцевая', 'price' => 1300];
        $priceWatchFilmMattePremium = (object)['name' => 'матовая', 'price' => 1300];
        $priceWatchFilmPrivacyHDPremium = (object)['name' => 'антишпион-глянцевая', 'price' => 2900];
        $priceWatchFilmPrivacyMattePremium = (object)['name' => 'антишпион-матовая', 'price' => 2900];

        // standart
        $priceWatchFilmHDStandart = (object)['name' => 'глянцевая', 'price' => 1300];
        $priceWatchFilmMatteStandart = (object)['name' => 'матовая', 'price' => 1500];
        $priceWatchFilmPrivacyHDStandart = (object)['name' => 'антишпион-глянцевая', 'price' => 1900, 'hidden' => true];
        $priceWatchFilmPrivacyMatteStandart = (object)['name' => 'антишпион-матовая', 'price' => 1900, 'hidden' => true];

        // base
        $priceWatchFilmHDBase = (object)['name' => 'глянцевая', 'price' => 700];
        $priceWatchFilmMatteBase = (object)['name' => 'матовая', 'price' => 800, 'hidden' => true];
        $priceWatchFilmPrivacyHDBase = (object)['name' => 'антишпион-глянцевая', 'price' => 1200, 'hidden' => true];
        $priceWatchFilmPrivacyMatteBase = (object)['name' => 'антишпион-матовая', 'price' => 1200, 'hidden' => true];


        // СЕГМЕНТЫ - СМАРТФОН
        $phoneFilmSegments = [
            'premium' => (object)[
                'hidden' => true,
                'prices' => [$pricePhoneFilmHDPremium, $pricePhoneFilmMattePremium, $pricePhoneFilmPrivacyHDPremium, $pricePhoneFilmPrivacyMattePremium]
            ],
            'standart' => (object)[
                'hidden' => false,
                'prices' => [$pricePhoneFilmHDStandart, $pricePhoneFilmMatteStandart, $pricePhoneFilmPrivacyHDStandart, $pricePhoneFilmPrivacyMatteStandart]

            ],
            'base' => (object)[
                'hidden' => false,
                'prices' => [$pricePhoneFilmHDBase, $pricePhoneFilmMatteBase, $pricePhoneFilmPrivacyHDBase, $pricePhoneFilmPrivacyMatteBase]
            ],
        ];

        $phoneGlassSegments = [
            'premium' => (object)[
                'hidden' => false,
                'prices' => [$pricePhoneGlassHDPremium, $pricePhoneGlassMattePremium, $pricePhoneGlassPrivacyHDPremium, $pricePhoneGlassPrivacyMattePremium]
            ],
            'standart' => (object)[
                'hidden' => false,
                'prices' => [$pricePhoneGlassHDStandart, $pricePhoneGlassMatteStandart, $pricePhoneGlassPrivacyHDStandart, $pricePhoneGlassPrivacyMatteStandart]

            ],
            'base' => (object)[
                'hidden' => false,
                'prices' => [$pricePhoneGlassHDBase, $pricePhoneGlassMatteBase, $pricePhoneGlassPrivacyHDBase, $pricePhoneGlassPrivacyMatteBase]
            ],
        ];

        $phoneGlassCameraSegments = [
            'premium' => (object)[
                'hidden' => false,
                'prices' => [$pricePhoneGlassCameraFullPremium, $pricePhoneGlassCameraLensesPremium]
            ],
            'standart' => (object)[
                'hidden' => false,
                'prices' => [$pricePhoneGlassCameraFullStandart, $pricePhoneGlassCameraLensesStandart]

            ],
            'base' => (object)[
                'hidden' => false,
                'prices' => [$pricePhoneGlassCameraFullBase, $pricePhoneGlassCameraLensesBase]
            ],
        ];


        // СЕГМЕНТЫ - ПЛАНШЕТ
        $tabletFilmSegments = [
            'premium' => (object)[
                'hidden' => true,
                'prices' => [$priceTabletFilmHDPremium, $priceTabletFilmMattePremium, $priceTabletFilmPrivacyHDPremium, $priceTabletFilmPrivacyMattePremium]
            ],
            'standart' => (object)[
                'hidden' => false,
                'prices' => [$priceTabletFilmHDStandart, $priceTabletFilmMatteStandart, $priceTabletFilmPrivacyHDStandart, $priceTabletFilmPrivacyMatteStandart]
            ],
            // 'base' => (object)[
            //     'hidden' => false,
            //     'prices' => [$pricePhoneFilmHDBase, $pricePhoneFilmMatteBase, $pricePhoneFilmPrivacyHDBase, $pricePhoneFilmPrivacyMatteBase]
            // ],
        ];

        // СЕГМЕНТЫ - СМАРТ-ЧАСЫ
        $watchFilmSegments = [
            'premium' => (object)[
                'hidden' => true,
                'prices' => [$priceWatchFilmHDPremium, $priceWatchFilmMattePremium, $priceWatchFilmPrivacyHDPremium, $priceWatchFilmPrivacyMattePremium]
            ],
            'standart' => (object)[
                'hidden' => false,
                'prices' => [$priceWatchFilmHDStandart, $priceWatchFilmMatteStandart, $priceWatchFilmPrivacyHDStandart, $priceWatchFilmPrivacyMatteStandart]
            ],
            'base' => (object)[
                'hidden' => false,
                'prices' => [$priceWatchFilmHDBase, $priceWatchFilmMatteBase, $priceWatchFilmPrivacyHDBase, $priceWatchFilmPrivacyMatteBase]
            ],
        ];


        // ДАННЫЕ - СМАРТФОН
        $phone = (object)[
            'id' => 'phone',
            'services' => [
                (object)[
                    'name' => 'Плёнка на экран',
                    'code' => 'film-glass-phone',
                    'hidden' => false,
                    'segments' => $phoneFilmSegments
                ],
                (object)[
                    'name' => 'Плёнка на заднюю панель',
                    'code' => 'film-back-phone',
                    'hidden' => false,
                    'segments' => $phoneFilmSegments
                ],
                (object)[
                    'name' => 'Стекло на экран',
                    'code' => 'glass-phone',
                    'hidden' => false,
                    'segments' => $phoneGlassSegments
                ],
                (object)[
                    'name' => 'Стекло на камеру',
                    'code' => 'glass-camera-phone',
                    'hidden' => false,
                    'segments' => $phoneGlassCameraSegments
                ],
            ],
        ];

        // ДАННЫЕ - ПЛАНШЕТ
        $tablet = (object)[
            'id' => 'tablet',
            'services' => [
                (object)[
                    'name' => 'Плёнка на экран',
                    'code' => 'film-glass-tablet',
                    'hidden' => false,
                    'segments' => $tabletFilmSegments
                ],
                // (object)[
                //     'name' => 'Плёнка на заднюю панель',
                //     'code' => 'film-back-table',
                //     'hidden' => false,
                //     'segments' => $tableFilmSegments
                // ],
                // (object)[
                //     'name' => 'Стекло на экран',
                //     'code' => 'glass-table',
                //     'hidden' => false,
                //     'segments' => $tableGlassSegments
                // ],
                // (object)[
                //     'name' => 'Стекло на камеру',
                //     'code' => 'glass-camera-table',
                //     'hidden' => false,
                //     'segments' => $tableGlassCameraSegments
                // ],
            ],
        ];


        // ДАННЫЕ - СМАРТ-ЧАСЫ
        $watch = (object)[
            'id' => 'watch',
            'services' => [
                (object)[
                    'name' => 'Плёнка на экран',
                    'code' => 'film-glass-watch',
                    'hidden' => false,
                    'segments' => $watchFilmSegments
                ],
                // (object)[
                //     'name' => 'Плёнка на заднюю панель',
                //     'code' => 'film-back-table',
                //     'hidden' => false,
                //     'segments' => $tableFilmSegments
                // ],
                // (object)[
                //     'name' => 'Стекло на экран',
                //     'code' => 'glass-table',
                //     'hidden' => false,
                //     'segments' => $tableGlassSegments
                // ],
                // (object)[
                //     'name' => 'Стекло на камеру',
                //     'code' => 'glass-camera-table',
                //     'hidden' => false,
                //     'segments' => $tableGlassCameraSegments
                // ],
            ],
        ];

        return [$phone, $tablet, $watch];
    }
}
