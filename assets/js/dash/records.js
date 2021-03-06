var dash_records_load = function() {
    $(this.element).find('.record-infobar').html('');
    var item = this.findToolbarItemByProperty('action','level');
    var root = this.options.data.root.split('/').slice(0,-1);
    if (root.length>0) {
        this.enableToolbarItem(item);
    } else {
        this.disableToolbarItem(item);
    }
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].activated)!="undefined" && this.options.bodyItems[i].activated==record_status_not_published_id) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
    if (typeof(this.options.data.search)!="undefined") {
        var search = this.findToolbarItemByProperty('action','search');
        if (search) {
            $(search.element).val(this.options.data.search);
        }
    }
};

var dash_records_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo','editor');
    this.disableItemsByProperty('typo','record');
    this.disableItemsByProperty('typo','publish');
    this.disableItemsByProperty('typo','front_page');
    this.disableItemsByProperty('typo','settings');
    this.disableItemsByProperty('typo','preview');
    this.disableItemsByProperty('typo','widget');
    this.disableItemsByProperty('typo','slider');
    this.disableItemsByProperty('typo','gallery');
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo == 'record') {
        this.enableItemsByProperty('typo','editor');
        this.enableItemsByProperty('typo','preview');
        if (typeof(selected[0].activated)!="undefined" && selected[0].activated==record_status_published_id) {
            this.enableItemsByProperty('typo','record');
        } else if (typeof(selected[0].activated)!="undefined" && selected[0].activated==record_status_not_published_id) {
            this.enableItemsByProperty('typo','publish');
        }
        if (typeof(selected[0].front_page)!="undefined" && selected[0].front_page==record_status_not_front_page_id) {
            this.enableItemsByProperty('typo','front_page');
        }
        if (this.options.data.slider_enabled != 0) {
            this.enableItemsByProperty('typo','slider');
        }
        if (this.options.data.gallery_enabled != 0) {
            this.enableItemsByProperty('typo','gallery');
        }
    } else if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo == 'category') {
        this.enableItemsByProperty('typo','settings');
        this.enableItemsByProperty('typo','widget');
    }
    if (selected && selected.length && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record' && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.record-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
        $(this.element).find('.record-infobar').html('');
        try { window.clearTimeout(this.timer); } catch(err) {};
        this.timer = window.setTimeout(this.bind(this,function(){
            $(this.element).find('.record-infobar').html('');
            var selected = this.getSelectedContentItems();
            if (!selected || !selected.length || selected.length!=1) return;
            desk_post(url('dash/records/info'),{'item':selected[0].data, 'token':token()}, this.bind(this, function(response){
                if (response && response.length>0) {
                    $(this.element).find('.record-infobar').append('<div style="cursor:default;padding:0px;margin:10px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                    $(this.element).find('.record-infobar').append('<div style="border-top:1px solid #B3B6D1;border-bottom:1px solid #EDEDF6;height:1px;padding:0px;margin:10px 0px"></div>');
                    for (var i=0; i<response.length; i++) {
                        $(this.element).find('.record-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                    }
                }
            }));
        }),1000);
    }
};

var dash_records_drop = function(element) {
    if (element instanceof FileList) return;
    if (typeof(element.parent)!="undefined" && element.parent=='record') {
        desk_window_request(this, url('dash/records/copy'),{'root':this.options.data.root, 'item':element.data});
    }
};

var dash_records_up = function() {
    var root = this.options.data.root.split('/').slice(0,-1);
    if (root.length>0) {
        this.options.data.root=root.join('/');
        this.options.data.search='';
        desk_window_reload(this);
        this.options.data.page=1;
    }
};

var dash_records_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined") {
        if (selected[0].typo == 'category') {
            var data = {
                'data':desk_window_selected(this,1),
                'reload': this.className,
                'onClose':function(){
                    desk_window_reload_all(this.options.reload);
                }
            };
            data.data.root = this.options.data.root;
            desk_call(dash_records_category_wnd, null, data);
        } else if (selected[0].typo == 'record') {
            desk_call(dash_records_open_record, this);
        }
    }
};

var dash_records_delete = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length>0) {
        var data = {'items':[],'types':[]};
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].data)=="undefined" || typeof(selected[i].typo)=="undefined") continue;
            data.items.push(selected[i].data);
            data.types.push(selected[i].typo);
        }
    }
    desk_window_request(this, url('dash/index/delete'), data);
};

var dash_records_copy = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        desk_prompt(t('Enter category'), this.bind(this, function(name){
            desk_window_request(this, url('dash/records/copy'),{'root':name, 'item':selected[0].data});
        }));
        var root = this.options.data.root;
        if (root.substr(0,1)=='/') root = root.substr(1);
        $('#zira-prompt-dialog input[name=modal-input]').val(root);
    }
};

var dash_records_move = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        desk_prompt(t('Enter category'), this.bind(this, function(name){
            if (name.length>0 && name.substr(0,1)!='/') name = '/'+name;
            if (name == this.options.data.root) return;
            desk_window_request(this, url('dash/records/move'),{'root':name, 'item':selected[0].data});
        }));
        var root = this.options.data.root;
        if (root.substr(0,1)=='/') root = root.substr(1);
        $('#zira-prompt-dialog input[name=modal-input]').val(root);
    }
};

var dash_records_create_record = function() {
    var data = {
        data:{},
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    data.data.root = this.options.data.root;
    data.data.language = this.options.data.language;
    desk_call(dash_records_record_wnd, null, data);
};

var dash_records_create_category = function() {
    var data = {
        data:{},
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    data.data.root = this.options.data.root;
    desk_call(dash_records_category_wnd, null, data);
};

var dash_records_open_category = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].category)!="undefined") {
        this.options.data.root = this.options.data.root+'/'+selected[0].category;
        this.options.data.search='';
        this.options.data.page=1;
        this.loadBody();
    }
};

var dash_records_category_settings = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo == 'category') {
        var data = {'data':desk_window_selected(this,1)};
        desk_call(dash_records_category_settings_wnd, null, data);
    }
};

var dash_records_category_widget = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo == 'category') {
        desk_window_request(this, url('dash/records/widget'),{'item':selected[0].data});
    }
};

var dash_records_open_record = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {
            data:{},
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        data.data.root = this.options.data.root;
        data.data.language = this.options.data.language;
        data.data.items = desk_window_selected(this,1);
        desk_call(dash_records_record_wnd, null, data);
    }
};

var dash_records_record_text = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_text_wnd, null, {'data':data});
    }
};

var dash_records_record_html = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_html_wnd, null, {'data':data});
    }
};

var dash_records_desc = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].description)!="undefined" && typeof(selected[0].typo)!="undefined") {
        if (selected[0].typo == 'category') {
            desk_prompt(t('Enter description'), this.bind(this, function(desc){
                desk_window_request(this, url('dash/system/description'),{'type': 'category', 'description':desc, 'item':selected[0].data});
            }));
            $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].description);
        } else if (selected[0].typo == 'record') {
            desk_multi_prompt(t('Enter description'), this.bind(this, function(desc){
                desk_window_request(this, url('dash/records/description'),{'description':desc, 'item':selected[0].data});
            }));
            $('#zira-multi-prompt-dialog textarea[name=modal-input]').val(selected[0].description);
        }
    }
};

var dash_records_record_image = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        desk_file_selector(this.bind(this,function(elements){
            if (!elements || elements.length==0) return;
            var element = elements[0];
            if (element instanceof FileList) return;
            if (typeof(element)!="object" || typeof(element.type)=="undefined" || element.type!='image' || typeof(element.data)=="undefined") return;
            if (typeof(element.parent)=="undefined" || element.parent!='files') return;
            desk_window_request(this, url('dash/records/image'),{'image':element.data, 'item':selected[0].data});
        }));
    }
};

var dash_records_seo = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined") {
        var data = {'items':[selected[0].data]};
        if (selected[0].typo == 'category') {
            desk_call(dash_records_category_meta_wnd, null, {
                'data':data,
                'reload': this.className,
                'onClose':function(){
                    desk_window_reload_all(this.options.reload);
                }
            });
        } else if (selected[0].typo == 'record') {
            desk_call(dash_records_record_meta_wnd, null, {
                'data':data,
                'reload': this.className,
                'onClose':function(){
                    desk_window_reload_all(this.options.reload);
                }
            });
        }
    }
};

var dash_records_record_page = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record' && typeof(selected[0].page)!="undefined") {
        var language = '';
        if (typeof(selected[0].language)!="undefined" && selected[0].language!==null) language = selected[0].language + '/';
        window.location.href=url(language+selected[0].page);
    }
};

var dash_records_record_view = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record' && typeof(selected[0].page)!="undefined") {
        var language = '';
        if (typeof(selected[0].language)!="undefined" && selected[0].language!==null) language = selected[0].language + '/';
        var data = {'url':[language+selected[0].page]};
        desk_call(dash_records_web_wnd, null, {'data':data});
    }
};

var dash_records_record_publish = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'item':selected[0].data};
        desk_window_request(this, url('dash/records/publish'), data);
    }
};

var dash_records_record_front = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'item':selected[0].data};
        desk_window_request(this, url('dash/records/frontpage'), data);
    }
};

var dash_records_record_gallery = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_images_wnd, null, {'data':data});
    }
};

var dash_records_record_slider = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_slides_wnd, null, {'data':data});
    }
};

var dash_records_language = function(element) {
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

var desk_record_category = function(item) {
    var data = {'root':item};
    desk_call(dash_records_wnd, null, {'data':data});
};

var desk_record_editor = function(item) {
    var data = {'items':[item]};
    desk_call(dash_records_record_html_wnd, null, {'data':data});
};