var dash_menu_open = function() {
    this.nested_level = 0;
    this.previous = [];
    desk_call(dash_menu_drag, this);
};

var dash_menu_load = function() {
    var sidebarItem;
    var menu = this.options.data.menu;
    if (menu == dash_menu_primary_id || menu == dash_menu_secondary_id) {
        sidebarItem = this.findSidebarItemByProperty('typo','topmenu');
    } else if (menu == dash_menu_footer_id) {
        sidebarItem = this.findSidebarItemByProperty('typo','bottommenu');
    }
    if (sidebarItem) {
        $(sidebarItem.element).addClass('active');
    }
    if (this.options.data.parent) {
        this.enableItemsByProperty('action','level');
    } else {
        this.disableItemsByProperty('action','level');
    }
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_menu_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo','childitems');
    this.disableItemsByProperty('typo','secondary');
    if (selected && selected.length==1 && !this.nested_level) {
        this.enableItemsByProperty('typo','childitems');
    }
    if (selected && selected.length==1 && this.options.data.menu == dash_menu_primary_id) {
        this.enableItemsByProperty('typo','secondary');
    }
    this.disableItemsByProperty('typo','preview');
    this.disableItemsByProperty('typo','newtab');
    if (selected && selected.length==1 && selected[0].url.indexOf('#')!=0 && selected[0].url!='javascript:void(0)') {
        this.enableItemsByProperty('typo','newtab');
        if (selected[0].url.indexOf('http')!=0) this.enableItemsByProperty('typo','preview');
    }
};

var dash_menu_new_item = function() {
    var data = {
        'data': {
            'menu':this.options.data.menu,
            'parent':this.options.data.parent},
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
    };
    desk_call(dash_menu_menuitem_wnd, null, data);
};

var dash_menu_top = function() {
    if (this.options.data.menu == dash_menu_primary_id || this.options.data.menu == dash_menu_secondary_id) return;
    var topSidebarItem = this.findSidebarItemByProperty('typo','topmenu');
    var bottomSidebarItem = this.findSidebarItemByProperty('typo','bottommenu');
    if (topSidebarItem) $(topSidebarItem.element).addClass('active');
    if (bottomSidebarItem) $(bottomSidebarItem.element).removeClass('active');
    this.options.data.menu = dash_menu_primary_id;
    this.options.data.parent = null;
    this.nested_level = 0;
    this.previous = [];
    desk_window_reload(this);
};

var dash_menu_bottom = function() {
    if (this.options.data.menu == dash_menu_footer_id) return;
    var topSidebarItem = this.findSidebarItemByProperty('typo','topmenu');
    var bottomSidebarItem = this.findSidebarItemByProperty('typo','bottommenu');
    if (topSidebarItem) $(topSidebarItem.element).removeClass('active');
    if (bottomSidebarItem) $(bottomSidebarItem.element).addClass('active');
    this.options.data.menu = dash_menu_footer_id;
    this.options.data.parent = null;
    this.nested_level = 0;
    this.previous = [];
    desk_window_reload(this);
};

var dash_menu_language = function(element) {
    var language = this.options.data.language;
    var id = $(element).attr('id');
    var item = this.findMenuItemByProperty('id',id);
    if (item && typeof(item.language)!="undefined") {
        if (item.language!=language) {
            this.options.data.language=item.language;
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
            $(element).find('.glyphicon').removeClass('glyphicon-filter').addClass('glyphicon-ok');
        } else {
            this.options.data.language='';
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
        }
    }
};

var dash_menu_drag = function() {
    this.isContentDragging = false;
    this.dragStartY = null;
    this.dragStartItem = null;
    this.dragOverItem = null;
    this.dragReplaced = false;
    this.dragImage = new Image(); this.dragImage.src=dash_menu_blank_src;
    $(this.content).bind('dragstart',this.bind(this,function(e){
        if (this.isDisabled()) return;
        if (typeof(e.originalEvent.target)=="undefined") return;
        this.isContentDragging = true;
        this.dragStartY = e.originalEvent.pageY;
        this.dragStartItem = $(e.originalEvent.target).parents('li').children('a').attr('id');
        e.originalEvent.dataTransfer.setDragImage(this.dragImage,-10,0);
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',.5);
        for (var i=0; i<this.options.bodyItems.length; i++) {
            this.options.bodyItems[i].is_dragged = false;
        }
    }));
    $(this.content).bind('dragover',this.bind(this,function(e){
        if (this.isDisabled()) return;
        if (typeof(e.originalEvent.target)=="undefined" || !this.isContentDragging) return;
        var item = $(e.originalEvent.target).parents('li').children('a');
        if ($(item).length==0 || $(item).parents('#'+this.getId()).length==0) return;
        if ($(item).parent('li').hasClass('tmp-drag-widget-item')) return;
        if (this.dragReplaced && $(item).attr('id') == this.dragStartItem) {
            var startItem = this.findBodyItemByProperty('id',this.dragStartItem);
            var endItem = this.findBodyItemByProperty('id',this.dragOverItem);
            if (startItem && endItem && typeof(startItem.sort_order)!="undefined" && typeof(endItem.sort_order)!="undefined") {
                var start_order = startItem.sort_order;
                var end_order = endItem.sort_order;
                startItem.sort_order = end_order;
                endItem.sort_order = start_order;
                startItem.is_dragged = true;
                endItem.is_dragged = true;
            }
            this.dragOverItem = null;
            this.dragStartY = e.originalEvent.pageY;
            this.dragReplaced = false;
        }
        if (this.dragStartItem!=$(item).attr('id') && this.dragOverItem!=$(item).attr('id')) {
            this.dragOverItem=$(item).attr('id');
            var tmp = '<li class="tmp-drag-widget-item"></li>';
            if (e.originalEvent.pageY > this.dragStartY) {
                $(this.content).find('#'+this.dragOverItem).parent('li').after(tmp);
            } else {
                $(this.content).find('#'+this.dragOverItem).parent('li').before(tmp);
            }
            $(this.content).find('li.tmp-drag-widget-item').replaceWith($(this.content).find('#'+this.dragStartItem).parent('li'));
            this.dragReplaced = true;
        }
    }));
    $(this.element).bind('drop',this.bind(this,function(e){
        if (this.isDisabled()) return;
        var dragged = [];
        var orders = [];
        for (var i=0; i<this.options.bodyItems.length; i++) {
            if (typeof(this.options.bodyItems[i].sort_order)!="undefined" && typeof(this.options.bodyItems[i].is_dragged)!="undefined" && this.options.bodyItems[i].is_dragged) {
                dragged.push(this.options.bodyItems[i].data);
                orders.push(this.options.bodyItems[i].sort_order);
            }
        }
        if (dragged.length>1 && orders.length>1) {
            desk_window_request(this, url('dash/menu/drag'),{'items':dragged,'orders':orders});
        }
    }));
    $(this.content).bind('dragend',this.bind(this,function(e){
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',1);
        this.isContentDragging = false;
        this.dragStartY = null;
        this.dragStartItem = null;
        this.dragOverItem = null;
        this.dragReplaced = false;
        $(this.content).find('li.tmp-drag-widget-item').remove();
    }));
};

var dash_menu_drop = function(element) {
    if (!(element instanceof FileList) && typeof(element.parent)!="undefined" && (element.parent=='record' || element.parent=='category')) {
        var data = {'type':element.parent, 'item':element.data, 'menu':this.options.data.menu, 'parent':this.options.data.parent};
        desk_window_request(this, url('dash/menu/drop'),data);
    }
};

var dash_menu_child = function() {
    if (this.nested_level) return;
        var selected = this.getSelectedContentItems();
        if (selected && selected.length==1) {
        this.previous.push(this.options.data.parent);
        this.options.data.parent = selected[0].data;
        desk_window_reload(this);
        this.nested_level++;
    }
};

var dash_menu_secondary = function() {
    if (this.options.data.menu != dash_menu_primary_id) return;
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        this.options.data.menu = dash_menu_secondary_id;
        this.previous.push(this.options.data.parent);
        this.options.data.parent = selected[0].data;
        this.nested_level = 0;
        desk_window_reload(this);
    }
};

var dash_menu_up = function() {
    if (this.previous.length==0) return;
    if (this.options.data.menu == dash_menu_secondary_id && !this.nested_level) {
        this.options.data.menu=dash_menu_primary_id;
    } else {
        this.nested_level--;
    }
    this.options.data.parent=this.previous.pop();
    if (this.options.data.menu == dash_menu_primary_id && this.options.data.parent) {
        this.nested_level++;
    }
    desk_window_reload(this);
};

var dash_menu_page = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].url)!="undefined" && selected[0].url.indexOf('#')!=0 && selected[0].url!='javascript:void(0)') {
        window.location.href=selected[0].url;
    }
};

var dash_menu_view = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].url)!="undefined" && selected[0].url.indexOf('#')!=0 && selected[0].url.indexOf('http')!=0 && selected[0].url!='javascript:void(0)') {
        var data = {'url':[selected[0].url]};
        desk_call(dash_menu_web_wnd, null, {'data':data})
    }
};