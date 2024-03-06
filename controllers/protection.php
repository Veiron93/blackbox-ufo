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

        $this->viewData['brands'] = ['Sunshine', 'Hoco', 'Mietubl'];
    }


    protected function onOrderProtection()
    {
        try {
            // $keys = array('phone', 'name', 'date');
            // $values = array_intersect_key($_POST, array_fill_keys($keys, 1));

            $inputJSON = file_get_contents('php://input');

            $data = json_decode($inputJSON, TRUE);

            $data_order = $data['data'];
            $cart = $data['cart'];

            $values['phone'] = $data_order['phone'];
            $values['author_name'] = $data_order['name'];
            $values['author_email'] = "noreplay@bb65.ru";

            $values['comment'] = self::initListServices($cart);


            // traceLog($values['comment']);

            if (!$this->validation->validate($values)) {
                $this->validation->throwException();
            }

            $message = GlobalComments_Comment::create($values);
            $message->save($values);

            $response['success'] = "Спасибо за обращение!";

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

                    $segmentService = self::$types[$segment]->name . ' - ' . $nameSegment . ' - ' . $price . 'руб.';

                    $deviceServices .= $key . '. ' . $nameService . PHP_EOL . $segmentService . PHP_EOL . PHP_EOL;
                }
            }

            $list .= ">>> " . $device . " <<<" . PHP_EOL . $deviceServices . PHP_EOL;
        }

        return $list . "Итоговая стоимость: " . $totalPrice;
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
        $pricePhoneFilmHDPremium = (object)['name' => 'глянцевая', 'price' => 2000];
        $pricePhoneFilmMattePremium = (object)['name' => 'матовая', 'price' => 2000];
        $pricePhoneFilmPrivacyHDPremium = (object)['name' => 'антишпион-глянцевая', 'price' => 2900];
        $pricePhoneFilmPrivacyMattePremium = (object)['name' => 'антишпион-матовая', 'price' => 2900];

        // standart
        $pricePhoneFilmHDStandart = (object)['name' => 'глянцевая', 'price' => 1200];
        $pricePhoneFilmMatteStandart = (object)['name' => 'матовая', 'price' => 1200];
        $pricePhoneFilmPrivacyHDStandart = (object)['name' => 'антишпион-глянцевая', 'price' => 1900];
        $pricePhoneFilmPrivacyMatteStandart = (object)['name' => 'антишпион-матовая', 'price' => 1900];

        // base
        $pricePhoneFilmHDBase = (object)['name' => 'глянцевая', 'price' => 650];
        $pricePhoneFilmMatteBase = (object)['name' => 'матовая', 'price' => 650];
        $pricePhoneFilmPrivacyHDBase = (object)['name' => 'антишпион-глянцевая', 'price' => 1200];
        $pricePhoneFilmPrivacyMatteBase = (object)['name' => 'антишпион-матовая', 'price' => 1200];


        // >>> СТЁКЛА ЭКРАН
        // premium
        $pricePhoneGlassHDPremium = (object)['name' => 'глянцевое', 'price' => 1500];
        $pricePhoneGlassMattePremium = (object)['name' => 'матовое', 'price' => 1500];
        $pricePhoneGlassPrivacyHDPremium = (object)['name' => 'антишпион-глянцевое', 'price' => 2000];
        $pricePhoneGlassPrivacyMattePremium = (object)['name' => 'антишпион-матовое', 'price' => 2000];

        // standart
        $pricePhoneGlassHDStandart = (object)['name' => 'глянцевое', 'price' => 700];
        $pricePhoneGlassMatteStandart = (object)['name' => 'матовое', 'price' => 700];
        $pricePhoneGlassPrivacyHDStandart = (object)['name' => 'антишпион-глянцевое', 'price' => 2900];
        $pricePhoneGlassPrivacyMatteStandart = (object)['name' => 'антишпион-матовое', 'price' => 2900];

        // base
        $pricePhoneGlassHDBase = (object)['name' => 'глянцевая', 'price' => 450];
        $pricePhoneGlassMatteBase = (object)['name' => 'матовая', 'price' => 450];
        $pricePhoneGlassPrivacyHDBase = (object)['name' => 'антишпион-глянцевая', 'price' => 1200];
        $pricePhoneGlassPrivacyMatteBase = (object)['name' => 'антишпион-матовая', 'price' => 1200];


        // >>> СТЁКЛА НА КАМЕРУ
        // premium
        $pricePhoneGlassCameraFullPremium = (object)['name' => 'Полная защита', 'price' => 1000];
        $pricePhoneGlassCameraLensesPremium = (object)['name' => 'Защита линз', 'price' => 1000];

        // standart
        $pricePhoneGlassCameraFullStandart = (object)['name' => 'Полная защита', 'price' => 450];
        $pricePhoneGlassCameraLensesStandart = (object)['name' => 'Защита линз', 'price' => 450];

        // base
        $pricePhoneGlassCameraFullBase = (object)['name' => 'Полная защита', 'price' => 300];
        $pricePhoneGlassCameraLensesBase = (object)['name' => 'Защита линз', 'price' => 300];


        ////////////////////////// ПЛАНШЕТ
        // >>> ПЛЁНКИ
        // premium
        $priceTabletFilmHDPremium = (object)['name' => 'глянцевая', 'price' => 2000];
        $priceTabletFilmMattePremium = (object)['name' => 'матовая', 'price' => 2000];
        $priceTabletFilmPrivacyHDPremium = (object)['name' => 'антишпион-глянцевая', 'price' => 2900];
        $priceTabletFilmPrivacyMattePremium = (object)['name' => 'антишпион-матовая', 'price' => 2900];

        // standart
        $priceTabletFilmHDStandart = (object)['name' => 'глянцевая', 'price' => 1200];
        $priceTabletFilmMatteStandart = (object)['name' => 'матовая', 'price' => 1200];
        $priceTabletFilmPrivacyHDStandart = (object)['name' => 'антишпион-глянцевая', 'price' => 1900];
        $priceTabletFilmPrivacyMatteStandart = (object)['name' => 'антишпион-матовая', 'price' => 1900];

        // base
        $priceTabletFilmHDBase = (object)['name' => 'глянцевая', 'price' => 650];
        $priceTabletFilmMatteBase = (object)['name' => 'матовая', 'price' => 650];
        $priceTabletFilmPrivacyHDBase = (object)['name' => 'антишпион-глянцевая', 'price' => 1200];
        $priceTabletFilmPrivacyMatteBase = (object)['name' => 'антишпион-матовая', 'price' => 1200];


        // СЕГМЕНТЫ - СМАРТФОН
        $phoneFilmSegments = [
            'premium' => (object)[
                'hidden' => false,
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
                'hidden' => false,
                'prices' => [$priceTabletFilmHDPremium, $priceTabletFilmMattePremium, $priceTabletFilmPrivacyHDPremium, $priceTabletFilmPrivacyMattePremium]
            ],
            // 'standart' => (object)[
            //     'hidden' => false,
            //     'prices' => [$pricePhoneFilmHDStandart, $pricePhoneFilmMatteStandart, $pricePhoneFilmPrivacyHDStandart, $pricePhoneFilmPrivacyMatteStandart]

            // ],
            // 'base' => (object)[
            //     'hidden' => false,
            //     'prices' => [$pricePhoneFilmHDBase, $pricePhoneFilmMatteBase, $pricePhoneFilmPrivacyHDBase, $pricePhoneFilmPrivacyMatteBase]
            // ],
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

        return [$phone, $tablet];
    }
}
