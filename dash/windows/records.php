<?php
/**
 * Zira project.
 * categories.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Dash\Dash;
use Zira;
use Zira\Permission;

class Records extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-book';
    protected static $_title = 'Records';

    protected $_help_url = 'zira/help/records';

    public $search;
    public $page = 0;
    public $pages = 0;
    public $order = 'desc';

    protected  $limit = 50;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(true);
        $this->setReloadButtonEnabled(false);
        $this->setBodyViewListVertical(true);

        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('New record'), 'glyphicon glyphicon-file', 'desk_call(dash_records_create_record, this);', 'create')
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarSeparator()
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Slider'), 'glyphicon glyphicon-film', 'desk_call(dash_records_record_slider, this);', 'edit', true, array('typo'=>'slider'))
        );
        $this->addDefaultSidebarItem(
            $this->createSidebarItem(Zira\Locale::t('Gallery'), 'glyphicon glyphicon-th', 'desk_call(dash_records_record_gallery, this);', 'edit', true, array('typo'=>'gallery'))
        );

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('New category'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_records_create_category, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('New record'), 'glyphicon glyphicon-file', 'desk_call(dash_records_create_record, this);', 'create')
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t($this->_edit_action_text), 'glyphicon glyphicon-pencil', 'desk_window_edit_item(this)', 'edit', true)
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('New category'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_records_create_category, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('New record'), 'glyphicon glyphicon-file', 'desk_call(dash_records_create_record, this);', 'create')
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t($this->_edit_action_text), 'glyphicon glyphicon-pencil', 'desk_window_edit_item(this)', 'edit', true)
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::t('Up'), 'glyphicon glyphicon-level-up', 'desk_call(dash_records_up, this);', 'level', true)
        );
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(null, Zira\Locale::t('Reload'), 'glyphicon glyphicon-repeat', 'desk_window_reload(this);', 'reload')
        );

        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Copy'), 'glyphicon glyphicon-duplicate', 'desk_call(dash_records_copy, this);', 'edit', true, array('typo'=>'editor'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Move'), 'glyphicon glyphicon-share', 'desk_call(dash_records_move, this);', 'edit', true, array('typo'=>'editor'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Edit description'), 'glyphicon glyphicon-font', 'desk_call(dash_records_desc, this);', 'edit', true, array('typo'=>'description'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('SEO tags'), 'glyphicon glyphicon-search', 'desk_call(dash_records_seo, this);', 'edit', true, array('typo'=>'seo'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Attach picture'), 'glyphicon glyphicon-picture', 'desk_call(dash_records_record_image, this);', 'edit', true, array('typo'=>'editor'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Publish'), 'glyphicon glyphicon-ok', 'desk_call(dash_records_record_publish, this);', 'edit', true, array('typo'=>'publish'))
        );

        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_records_edit, this);'
            )
        );

        $this->setOnDeleteItemsJSCallback(
            $this->createJSCallback(
                'desk_call(dash_records_delete, this);'
            )
        );

        $this->addDefaultOnLoadScript(
            'desk_call(dash_records_load, this);'
        );

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_records_select, this);'
            )
        );

        $this->setOnDropJSCallback(
            $this->createJSCallback(
                'desk_call(dash_records_drop, this, element);'
            )
        );

        $this->setSidebarContent('<div class="record-infobar" style="white-space:nowrap;text-overflow: ellipsis;width:100%;overflow:hidden"></div>');

        $this->setData(array(
            'page'=>1,
            'pages'=>1,
            'order'=>$this->order,
            'root' => '',
            'language' => '',
            'slider_enabled'=>0,
            'gallery_enabled'=>0
        ));

        $this->addStrings(array(
            'Information',
            'Enter category',
            'Enter description'
        ));

        $this->addVariables(array(
            'record_status_published_id' => Zira\Models\Record::STATUS_PUBLISHED,
            'record_status_not_published_id' => Zira\Models\Record::STATUS_NOT_PUBLISHED,
            'record_status_front_page_id' => Zira\Models\Record::STATUS_FRONT_PAGE,
            'record_status_not_front_page_id' => Zira\Models\Record::STATUS_NOT_FRONT_PAGE,
            'dash_records_wnd' => Dash::getInstance()->getWindowJSName(Records::getClass()),
            'dash_records_category_wnd' => Dash::getInstance()->getWindowJSName(Category::getClass()),
            'dash_records_record_wnd' => Dash::getInstance()->getWindowJSName(Record::getClass()),
            'dash_records_category_settings_wnd' => Dash::getInstance()->getWindowJSName(Categorysettings::getClass()),
            'dash_records_record_text_wnd' => Dash::getInstance()->getWindowJSName(Recordtext::getClass()),
            'dash_records_record_html_wnd' => Dash::getInstance()->getWindowJSName(Recordhtml::getClass()),
            'dash_records_category_meta_wnd' => Dash::getInstance()->getWindowJSName(Categorymeta::getClass()),
            'dash_records_record_meta_wnd' => Dash::getInstance()->getWindowJSName(Recordmeta::getClass()),
            'dash_records_web_wnd' => Dash::getInstance()->getWindowJSName(Web::getClass()),
            'dash_records_record_images_wnd' => Dash::getInstance()->getWindowJSName(Recordimages::getClass()),
            'dash_records_record_slides_wnd' => Dash::getInstance()->getWindowJSName(Recordslides::getClass())
        ));

        $this->includeJS('dash/records');
    }

    public function load() {
        if (!Permission::check(Permission::TO_VIEW_RECORDS)) {
            $this->setData(array(
                'page'=>1,
                'pages'=>1,
                'order'=>$this->order,
                'root'=>'',
                'language'=>'',
                'slider_enabled'=>0,
                'gallery_enabled'=>0
            ));
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $root = (string)Zira\Request::post('root');
        $language= (string)Zira\Request::post('language');
        if (!empty($language) && !in_array($language, Zira\Config::get('languages'))) {
            $language = '';
        }

        // getting category id and titles chain
        $category_id = Zira\Category::ROOT_CATEGORY_ID;

        $slider_enabled = Zira\Config::get('slider_enabled', 1);
        $gallery_enabled = Zira\Config::get('gallery_enabled', 1);

        $categories = array();
        if (!empty($root)) {
            $_root = trim($root, '/');
            $p = strpos($_root, '/');
            if ($p!==false) $_root = substr($_root, 0, $p);
            $rows = Zira\Models\Category::getCollection()
                ->where('name', '=', $_root)
                ->or_where('name', 'like', $_root . '/%')
                ->order_by('name', 'asc')
                ->get();
            foreach ($rows as $row) {
                $categories[$row->name] = $row->title;
                if (!empty($root) && $row->name == trim($root, '/')) {
                    $category_id = $row->id;
                    if ($row->slider_enabled !== null) $slider_enabled = $row->slider_enabled;
                    if ($row->gallery_enabled !== null) $gallery_enabled = $row->gallery_enabled;
                }
            }
        }

        // categories count
        $query = Zira\Models\Category::getCollection();
        $query->count();
        $query->where('parent_id','=',$category_id);
        if (!empty($this->search)) {
            $query->and_where();
            $query->open_where();
            $query->where('name','like','%'.$this->search.'%');
            $query->or_where('title','like','%'.$this->search.'%');
            $query->close_where();
        }
        $categories_total = $query->get('co');
        $category_page = $this->page;
        $category_pages = ceil($categories_total / $this->limit);
        if ($category_page > $category_pages) $category_page = $category_pages;
        if ($category_page < 1) $category_page = 1;

        // records count
        $query = Zira\Models\Record::getCollection();
        $query->count();
        $query->where('category_id', '=', $category_id);
        if (!empty($language)) {
            $query->and_where('language', '=', $language);
        }
        if (!empty($this->search)) {
            $query->and_where();
            $query->open_where();
            $query->where('name','like','%'.$this->search.'%');
            $query->or_where('title','like','%'.$this->search.'%');
            $query->close_where();
        }
        $records_total = $query->get('co');
        $records_pages = ceil($records_total / $this->limit);
        $record_page = $this->page;
        if ($record_page > $records_pages) $record_page = $records_pages;
        if ($record_page < 1) $record_page = 1;

        // max count
        $this->total = max($categories_total, $records_total);
        $this->pages = max($category_pages, $records_pages);
        $this->page = max($category_page, $record_page);

        // categories
        $items = array();
        if ($categories_total>0) {
            $query = Zira\Models\Category::getCollection();
            $query->where('parent_id','=',$category_id);
            if (!empty($this->search)) {
                $query->and_where();
                $query->open_where();
                $query->where('name','like','%'.$this->search.'%');
                $query->or_where('title','like','%'.$this->search.'%');
                $query->close_where();
            }
            $query->order_by('id', $this->order);
            $query->limit($this->limit, ($this->page - 1) * $this->limit);
            $rows = $query->get();
        } else {
            $rows = array();
        }

        foreach ($rows as $row) {
            $name = $row->name;
            $_root = trim($root, '/');
            if (!empty($_root)) {
                $name = substr($row->name, strlen($_root) + 1);
            }
            $items[] = $this->createBodyFolderItem($name, Zira\Locale::t($row->title), $row->id, 'desk_call(dash_records_open_category, this);', false, array('parent'=>'category', 'typo' => 'category', 'category' => $name, 'description'=>$row->description));
        }

        // records
        if ($records_total>0) {
            $query = Zira\Models\Record::getCollection();
            $query->where('category_id', '=', $category_id);
            if (!empty($language)) {
                $query->and_where('language', '=', $language);
            }
            if (!empty($this->search)) {
                $query->and_where();
                $query->open_where();
                $query->where('name','like','%'.$this->search.'%');
                $query->or_where('title','like','%'.$this->search.'%');
                $query->close_where();
            }
            $query->order_by('id', $this->order);
            $query->limit($this->limit, ($this->page - 1) * $this->limit);
            $rows = $query->get();
        } else {
            $rows = array();
        }

        foreach($rows as $row) {
            if ($row->thumb) {
                $items[]=$this->createBodyItem($row->name, $row->title, Zira\Helper::baseUrl($row->thumb), $row->id, 'desk_call(dash_records_record_html, this);', false, array('type'=>'html','parent'=>'record','typo'=>'record','activated'=>$row->published,'front_page'=>$row->front_page,'page'=>ltrim(trim($root,'/').'/'.$row->name,'/'),'description'=>$row->description,'language'=>count(Zira\Config::get('languages')) > 1 ? $row->language : null));
            } else {
                $items[]=$this->createBodyFileItem($row->name, $row->title, $row->id, 'desk_call(dash_records_record_html, this);', false, array('type'=>'html','parent'=>'record','typo'=>'record','activated'=>$row->published,'front_page'=>$row->front_page,'page'=>ltrim(trim($root,'/').'/'.$row->name,'/'),'description'=>$row->description,'language'=>count(Zira\Config::get('languages')) > 1 ? $row->language : null));
            }
        }

        $this->setBodyItems($items);

        // window title
        if (empty($root)) {
            $this->setTitle(Zira\Locale::t(self::$_title));
        } else {
            $cats = explode('/',trim($root,'/'));
            $_cat = '';
            $_cats = array();
            foreach($cats as $cat) {
                if (!empty($_cat)) $_cat .= '/';
                $_cat .= $cat;
                if (!array_key_exists($_cat, $categories)) continue;
                $_cats []= Zira\Locale::t($categories[$_cat]);
            }
            $this->setTitle(Zira\Locale::t(self::$_title).': '.implode(' / ',$_cats));
        }

        // menu
        $categoryMenu = array(
            $this->createMenuDropdownItem(Zira\Locale::t('New category'), 'glyphicon glyphicon-folder-close', 'desk_call(dash_records_create_category, this);', 'create'),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Edit description'), 'glyphicon glyphicon-font', 'desk_call(dash_records_desc, this);', 'edit', true, array('typo'=>'description')),
            $this->createMenuDropdownItem(Zira\Locale::t('SEO tags'), 'glyphicon glyphicon-search', 'desk_call(dash_records_seo, this);', 'edit', true, array('typo'=>'seo')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Records settings'), 'glyphicon glyphicon-option-vertical', 'desk_call(dash_records_category_settings, this);', 'edit', true, array('typo'=>'settings')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Create widget'), 'glyphicon glyphicon-modal-window', 'desk_call(dash_records_category_widget, this);', 'edit', true, array('typo'=>'widget'))
        );

        $recordMenu = array(
            $this->createMenuDropdownItem(Zira\Locale::t('New record'), 'glyphicon glyphicon-file', 'desk_call(dash_records_create_record, this);', 'create'),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Open editor'), 'glyphicon glyphicon-text-size', 'desk_call(dash_records_record_html, this);', 'edit', true, array('typo'=>'editor')),
            $this->createMenuDropdownItem(Zira\Locale::t('Edit code'), 'glyphicon glyphicon-list-alt', 'desk_call(dash_records_record_text, this);', 'edit', true, array('typo'=>'editor')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Edit description'), 'glyphicon glyphicon-font', 'desk_call(dash_records_desc, this);', 'edit', true, array('typo'=>'description')),
            $this->createMenuDropdownItem(Zira\Locale::t('SEO tags'), 'glyphicon glyphicon-search', 'desk_call(dash_records_seo, this);', 'edit', true, array('typo'=>'seo')),
            $this->createMenuDropdownItem(Zira\Locale::t('Attach picture'), 'glyphicon glyphicon-picture', 'desk_call(dash_records_record_image, this);', 'edit', true, array('typo'=>'editor')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('View page'), 'glyphicon glyphicon-eye-open', 'desk_call(dash_records_record_view, this);', 'edit', true, array('typo'=>'preview')),
            $this->createMenuDropdownItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_records_record_page, this);', 'edit', true, array('typo'=>'record')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Publish'), 'glyphicon glyphicon-ok', 'desk_call(dash_records_record_publish, this);', 'edit', true, array('typo'=>'publish')),
            $this->createMenuDropdownItem(Zira\Locale::t('Show on front page'), 'glyphicon glyphicon-home', 'desk_call(dash_records_record_front, this);', 'edit', true, array('typo'=>'front_page')),
            $this->createMenuDropdownSeparator(),
            $this->createMenuDropdownItem(Zira\Locale::t('Slider'), 'glyphicon glyphicon-film', 'desk_call(dash_records_record_slider, this);', 'edit', true, array('typo'=>'slider')),
            $this->createMenuDropdownItem(Zira\Locale::t('Gallery'), 'glyphicon glyphicon-th', 'desk_call(dash_records_record_gallery, this);', 'edit', true, array('typo'=>'gallery'))
        );

        $menu = array(
            $this->createMenuItem($this->getDefaultMenuTitle(), $this->getDefaultMenuDropdown()),
            $this->createMenuItem(Zira\Locale::t('Category'), $categoryMenu),
            $this->createMenuItem(Zira\Locale::t('Record'), $recordMenu)
        );

        if (count(Zira\Config::get('languages'))>1) {
            $langMenu = array();
            foreach(Zira\Locale::getLanguagesArray() as $lang_key=>$lang_name) {
                if (!empty($language) && $language==$lang_key) $icon = 'glyphicon glyphicon-ok';
                else $icon = 'glyphicon glyphicon-filter';
                $langMenu []= $this->createMenuDropdownItem($lang_name, $icon, 'desk_call(dash_records_language, this, element);', 'language', false, array('language'=>$lang_key));
            }
            $menu []= $this->createMenuItem(Zira\Locale::t('Languages'), $langMenu);
        }

        $this->setMenuItems($menu);

        $this->setData(array(
            'search'=>$this->search,
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order,
            'root' => $root,
            'language' => $language,
            'slider_enabled'=>$slider_enabled,
            'gallery_enabled'=>$gallery_enabled
        ));
    }
}