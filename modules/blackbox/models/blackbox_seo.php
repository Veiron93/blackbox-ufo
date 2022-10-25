<?php

/**
 * Class Pharmacy_Price
 *
 * @property
 */
class Blackbox_SEO extends Db_ActiveRecord
{



    /**
     * {@inheritdoc}
     *
     * @var string
     */
    public $table_name = 'blackbox_seo';

    //<editor-fold desc="Model fields" defaultstate="collapsed">
    //</editor-fold>

    /**
     * {@inheritdoc}
     *
     * @var boolean
     */
    public $disableTypographic = true;

    /**
     * Get an instanceof of the Blackbox_SEO model
     *
     * @return Blackbox_SEO
     */
    public static function create()
    {
        return new self();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $context
     */
    public function define_columns($context = null)
    {
        $this->defineColumn('guid', 'GUID')->validation()->fn('trim')->required();
        $this->defineColumn('description', 'Краткое описание прайса')->validation()->fn('trim')->required();
        $this->defineColumn('filename', 'Имя файла');
        $this->defineColumn('created_at', 'Дата создания');
        $this->defineColumn('updated_at', 'Дата обновления');
    }

    /**
     * {@inheritdoc}
     *
     * @param string $context
     */
    public function define_form_fields($context = null)
    {
        $this->addFormField('created_at', 'left')->renderAs(frm_datetime)->disabled();
        $this->addFormField('updated_at', 'right')->renderAs(frm_datetime)->disabled();
        $this->addFormField('guid')->renderAs(frm_text);
        $this->addFormField('description')->renderAs(frm_text);
    }
}
