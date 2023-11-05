<?php

/**
 * UFO CMF
 *
 * PHP application framework
 *
 * @package      UFO CMF
 * @copyright    (c) 2023, Rinamika, http://rinamika.ru
 * @author       Dmitry Yudin
 * @since        1.0
 * @license      http://rinamika.ru/ufo/licence.txt Rinamika Application License
 * @filesource
 */

class Tools_Catalog extends Db_ActiveRecord
{



    /**
     * {@inheritdoc}
     *
     * @var string
     */
    public $table_name = 'tools_catalog';

    public $implement = 'Db_Sortable, Db_AutoFootprints';

    /**
     * {@inheritdoc}
     *
     * @var boolean
     */
    public $disableTypographic = true;

    //<editor-fold desc="Model fields" defaultstate="collapsed">
    /**
     * @var string
     */
    public $title = null;

    /**
     * @var string
     */
    public $title_f3bus = null;

    /**
     * @var string
     */
    public $uuid = null;

    /**
     * @var bool
     */
    public $is_delivery = false;

    /**
     * @var float
     */
    public $delivery_cost = 0;

    /**
     * @var float
     */
    public $free_delivery_cost = 0;

    /**
     * @var string
     */
    public $address = null;

    /**
     * @var string
     */
    public $address_f3bus = null;

    /**
     * @var float
     */
    public $latitude = 0;

    /**
     * @var float
     */
    public $longitude = 0;

    /**
     * @var string
     */
    public $phones = null;

    /**
     * @var string
     */
    public $schedule = null;

    /**
     * @var string
     */
    public $sms_order_is_accepted = null;

    /**
     * @var string
     */
    public $sms_order_is_ready = null;

    /**
     * @var string
     */
    public $sms_order_sent_for_delivery = null;

    /**
     * @var bool
     */
    public $is_activated = false;

    /**
     * @var bool
     */
    public $is_source = false;

    /**
     * @var int
     */
    public $source_id = 0;

    /**
     * @var Phpr_DateTime
     */
    public $created_at = null;

    /**
     * @var Phpr_DateTime
     */
    public $updated_at = null;

    //</editor-fold>

    //<editor-fold desc="Db_AutoFootprints" defaultstate="collapsed">
    /**
     * @var boolean Делает поля видимыми в списках. Это поле может быть объявлено в классе модели.
     */
    public $auto_footprints_visible = true;

    /**
     * @var boolean Делает поля видимыми в списках по умолчанию. Это поле может быть объявлено в классе модели.
     */
    public $auto_footprints_default_invisible = false;

    /**
     * @var string Заголовок для поля "Дата создания". Это поле может быть объявлено в классе модели.
     */
    public $auto_footprints_created_at_name = 'Дата создания';

    /**
     * @var string Заголовок для поля "Создал". Это поле может быть объявлено в классе модели.
     */
    public $auto_footprints_created_user_name = 'Создал';

    /**
     * @var string Заголовок для поля "Дата изменения". Это поле может быть объявлено в классе модели.
     */
    public $auto_footprints_updated_at_name = 'Дата обновления';

    /**
     * @var string Заголовок для поля "Изменил". Это поле может быть объявлено в классе модели.
     */
    public $auto_footprints_updated_user_name = 'Изменил';

    /**
     * @var string
     */
    public $created_user_name = null;

    /**
     * @var string
     */
    public $updated_user_name = null;

    //</editor-fold>

    // public $belongs_to = [
    //     'city' => [
    //         'class_name' => 'Company_City',
    //         'foreign_key' => 'city_id'
    //     ]
    // ];

    // public $has_many = [
    //     'photos' => [
    //         'class_name' => 'Db_File',
    //         'foreign_key' => 'master_object_id',
    //         'conditions' => "master_object_class='Company_Department' and field='photos'",
    //         'order' => 'sort_order',
    //         'delete' => true
    //     ]
    // ];

    /**
     * @var string
     */
    public static $generalTabTitle = 'Основные параметры';

    /**
     * @var string
     */
    public static $photoTabTitle = 'Фотография';

    /**
     * @var string
     */
    public static $addressTabTitle = 'Адрес';

    /**
     * @var string
     */
    public static $smsTabTitle = 'SMS';

    /**
     * @var string
     */
    public static $smsOrderIsAcceptedTitle = 'Текст SMS о том, что заказ принят на сайте';

    /**
     * @var string
     */
    public static $smsOrderIsReadyTitle = 'Текст SMS о том, что заказ готов к самовывозу';

    /**
     * @var string
     */
    public static $smsOrderSentForDeliveryTitle = 'Текст SMS о том, что заказ передан на доставку';


    /**
     * Get an instanceof of the Tools_Catalog model
     *
     * @return Tools_Catalog
     */
    public static function create(): Tools_Catalog
    {
        traceLog(333);
        return new self();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $context
     * @throws Phpr_SystemException
     */
    public function define_columns($context = null)
    {
        // $this->defineColumn('title', 'Название')->validation()->fn('trim')->required('Пожалуйста, укажите название аптеки');
        // $this->defineColumn('title_f3bus', 'Название от F3bus')->invisible()->validation()->fn('trim')->required('Пожалуйста, укажите название аптеки от F3bus');
        // $this->defineColumn('uuid', 'UUID')->invisible()->validation()->fn('trim')->required('Пожалуйста, укажите UUID аптеки');
        // $this->defineColumn('source_id', 'Source ID')->invisible()->validation()->fn('trim')->required('Пожалуйста, укажите Source ID аптеки');
        // $this->defineColumn('is_delivery', 'Есть ли доставка?');
        // $this->defineColumn('delivery_cost', 'Стоимость доставки при повышении порога')->invisible();
        // $this->defineColumn('free_delivery_cost', 'Порог бесплатной доставки')->invisible();
        // $this->defineColumn('is_source', 'ЦО?');
        // $this->defineColumn('is_activated', 'Действующая аптека?');
        // $this->defineMultiRelationColumn('city', 'city', 'Город', "@title")->validation()->required('Пожалуйста, укажите город аптеки');
        // $this->defineColumn('address', 'Адрес')->validation()->fn('trim')->required('Пожалуйста, укажите адрес аптеки');
        // $this->defineColumn('address_f3bus', 'Адрес от F3Bus')->invisible()->validation()->fn('trim')->required('Пожалуйста, укажите адрес аптеки');
        // $this->defineColumn('latitude', 'Широта')->invisible();
        // $this->defineColumn('longitude', 'Долгота')->invisible();
        // $this->defineColumn('phones', 'Телефоны')->invisible();
        // $this->defineColumn('schedule', 'График работы')->invisible();
        // $this->defineColumn('sms_order_is_accepted', self::$smsOrderIsAcceptedTitle)->invisible();
        // $this->defineColumn('sms_order_is_ready', self::$smsOrderIsReadyTitle)->invisible();
        // $this->defineColumn('sms_order_sent_for_delivery', self::$smsOrderSentForDeliveryTitle)->invisible();
        // $this->defineMultiRelationColumn('photos', 'photos', 'Фотографии', "@name")->invisible();
    }

    public function define_form_fields($context = null)
    {
        // $this->addFormField('title')->tab(self::$generalTabTitle);
        // $this->addFormField('title_f3bus')->disabled()->tab(self::$generalTabTitle);
        // $this->addFormField('uuid', 'left')->disabled()->tab(self::$generalTabTitle);
        // $this->addFormField('source_id', 'right')->disabled()->tab(self::$generalTabTitle);
        // $this->addFormField('is_source', 'left')->tab(self::$generalTabTitle);
        // $this->addFormField('is_delivery', 'right')->tab(self::$generalTabTitle);
        // $this->addFormField('delivery_cost', 'left')->tab(self::$generalTabTitle);
        // $this->addFormField('free_delivery_cost', 'right')->tab(self::$generalTabTitle);
        // $this->addFormField('schedule')->renderAs(frm_textarea)->tab(self::$generalTabTitle);
        // $this->addFormPartial("map")->tab(self::$addressTabTitle);
        // $this->addFormField('address_f3bus')->disabled()->tab(self::$addressTabTitle);
        // $this->addFormField('city')->renderAs(frm_dropdown)->referenceSort('sort_order')->emptyOption('--- выберите город ---')->tab(self::$addressTabTitle);
        // $this->addFormField('address')->tab(self::$addressTabTitle);
        // $this->addFormField('latitude', 'left')->tab(self::$addressTabTitle);
        // $this->addFormField('longitude', 'right')->tab(self::$addressTabTitle);
        // $this->addFormField('phones')->tab(self::$addressTabTitle);
        // $field = $this->addFormField('photos')
        //     ->renderAs(frm_image_attachment)
        //     ->noLabel()
        //     ->noAttachmentsLabel('Фотография не загружена')
        //     ->tab(self::$photoTabTitle);
        // $field->custom_options['sortable'] = true;
        // $field->custom_options['main'] = true;
        // $this->addFormField('sms_order_is_accepted')->comment('Используйте %s для вставки номера заказа')->tab(self::$smsTabTitle);
        // $this->addFormField('sms_order_is_ready')->comment('Используйте %s для вставки номера заказа')->tab(self::$smsTabTitle);
        // $this->addFormField('sms_order_sent_for_delivery')->comment('Используйте %s для вставки номера заказа')->tab(self::$smsTabTitle);
    }

    public function after_save()
    {
        if ($this->is_source) {
            // Db_DbHelper::query('update company_departments set is_source = null where id <> ?', [$this->id]);
        }
    }
}
