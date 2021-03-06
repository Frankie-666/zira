<?php
/**
 * Zira project.
 * categorysettings.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Categorysettings extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-option-vertical';
    protected static $_title = 'Records settings';

    protected $_help_url = 'zira/help/category-settings';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_window_form_init(this);'
            )
        );
    }

    public function load() {
        if (empty($this->item)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $category = new Zira\Models\Category($this->item);
        if (!$category->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        $form = new \Dash\Forms\Categorysettings();
        $categoryArr = $category->toArray();

        if ($categoryArr['slider_enabled']===null) $categoryArr['slider_enabled'] = Zira\Config::get('slider_enabled', 1);
        if ($categoryArr['gallery_enabled']===null) $categoryArr['gallery_enabled'] = Zira\Config::get('gallery_enabled', 1);
        if ($categoryArr['comments_enabled']===null) $categoryArr['comments_enabled'] = Zira\Config::get('comments_enabled', 1);
        if ($categoryArr['rating_enabled']===null) $categoryArr['rating_enabled'] = Zira\Config::get('rating_enabled', 0);
        if ($categoryArr['display_author']===null) $categoryArr['display_author'] = Zira\Config::get('display_author', 0);
        if ($categoryArr['display_date']===null) $categoryArr['display_date'] = Zira\Config::get('display_date', 0);
        if ($categoryArr['records_list']===null) $categoryArr['records_list'] = 1;

        $form->setValues($categoryArr);

        $this->setBodyContent($form);

        $this->setTitle(Zira\Locale::t(self::$_title) . ' - ' . Zira\Locale::t($category->title));
    }
}