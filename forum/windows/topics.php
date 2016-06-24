<?php
/**
 * Zira project.
 * threads.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Topics extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-comment';
    protected static $_title = 'Forum threads';

    public $item;

    public $page = 0;
    public $pages = 0;
    public $order = 'desc';

    protected  $limit = 50;
    protected $total = 0;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(false);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);
        $this->setSidebarEnabled(false);

        $this->setOnCreateItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_forum_thread_create, this);'
            )
        );
        $this->setOnEditItemJSCallback(
            $this->createJSCallback(
                'desk_call(dash_forum_thread_edit, this);'
            )
        );

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Topic messages', 'forum'), 'glyphicon glyphicon-comment', 'desk_call(dash_forum_messages, this);', 'edit', true, array('typo'=>'messages'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Open thread page', 'forum'), 'glyphicon glyphicon-new-window', 'desk_call(dash_forum_page, this);', 'edit', true, array('typo'=>'page'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Topic messages', 'forum'), 'glyphicon glyphicon-comment', 'desk_call(dash_forum_messages, this);', 'edit', true, array('typo'=>'messages'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Open thread page', 'forum'), 'glyphicon glyphicon-new-window', 'desk_call(dash_forum_page, this);', 'edit', true, array('typo'=>'page'))
        );

        $this->includeJS('forum/dash');

        $this->setData(array(
            'items' => array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order,
            'category_id'=>0
        ));
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error'=>Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $forum = new \Forum\Models\Forum($this->item);
        if (!$forum->loaded()) return array('error'=>Zira\Locale::t('An error occurred'));

        $this->total = \Forum\Models\Topic::getCollection()
                                    ->count()
                                    ->where('category_id','=',$forum->category_id)
                                    ->and_where('forum_id','=',$forum->id)
                                    ->get('co');

        $this->pages = ceil($this->total / $this->limit);
        if ($this->page > $this->pages) $this->page = $this->pages;
        if ($this->page < 1) $this->page = 1;

        $threads = \Forum\Models\Topic::getCollection()
                                    ->where('category_id','=',$forum->category_id)
                                    ->and_where('forum_id','=',$forum->id)
                                    ->order_by('id', $this->order)
                                    ->limit($this->limit, ($this->page - 1) * $this->limit)
                                    ->get();

        $items = array();
        foreach($threads as $thread) {
            $title = Zira\Helper::html($thread->title);
            $description = Zira\Helper::html($thread->description);
            $items[]=$this->createBodyFileItem($title, $description, $thread->id, null, false, array('type'=>'txt', 'page'=>\Forum\Models\Topic::generateUrl($thread)));
        }
        $this->setBodyItems($items);

        $this->setTitle(Zira\Locale::t(self::$_title).' - '.$forum->title);

        $this->setData(array(
            'items' => array($this->item),
            'page'=>$this->page,
            'pages'=>$this->pages,
            'order'=>$this->order,
            'category_id'=>$forum->category_id
        ));
    }
}