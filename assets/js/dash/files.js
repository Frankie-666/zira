var dash_files_load = function() {
    $(this.element).find('.filemanager-infobar').html('');
    var item = this.findToolbarItemByProperty('action','level');
    var root = this.options.data.root.split(desk_ds).slice(0,-1);
    if (root.length>0) {
        this.enableToolbarItem(item);
    } else {
        this.disableToolbarItem(item);
    }
};

var dash_files_open = function() {
    if (typeof(this.options.data)!="undefined" && typeof(this.options.data.max_upload_size)!="undefined") this.max_upload_size=this.options.data.max_upload_size; else this.max_upload_size=null;
    $(this.element).find('.dashwindow-upload-form input[type=file]').change(this.bind(this, function(){
        var root = this.options.data.root;
        desk_upload(token(),url('dash/files/xhrupload'), root, $(this.element).find('.dashwindow-upload-form input[type=file]').get(0).files, null, this.max_upload_size, this.className);
    }));
    this.disableItemsByProperty('action','call');
};

var dash_files_drop = function(element) {
    if (element instanceof FileList) {
        var root = this.options.data.root; desk_upload(token(),url('dash/files/xhrupload'), root, element, null, this.max_upload_size, this.className);
    } else if (typeof(element.parent)!="undefined" && element.parent=='files') {
        desk_window_request(this, url('dash/files/copy'),{'path':this.options.data.root, 'file':element.data});
    }
};

var dash_files_select = function() {
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1) this.disableItemsByProperty('action','call');
    else this.enableItemsByProperty('action','call');
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type=='folder') {
        this.disableItemsByProperty('typo','download');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type!='archive') {
        this.disableItemsByProperty('typo','archive');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || (selected[0].type!='image' && selected[0].type!='txt' && selected[0].type!='html')) {
        this.disableItemsByProperty('action','edit');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type=='folder' || selected[0].type=='image'|| selected[0].type=='archive') {
        this.disableItemsByProperty('typo','notepad');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type!='image') {
        this.disableItemsByProperty('typo','show_image');
    }
    if (selected && selected.length && selected.length==1 && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.filemanager-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
    $(this.element).find('.filemanager-infobar').html('');
    try { window.clearTimeout(this.timer); } catch(err) {};
    this.timer = window.setTimeout(this.bind(this,function(){
        $(this.element).find('.filemanager-infobar').html('');
        var selected = this.getSelectedContentItems();
        if (!selected || !selected.length || selected.length!=1) return;
        desk_post(url('dash/files/info'),{'dirroot':this.options.data.root,'file':selected[0].data, 'token':token()}, this.bind(this, function(response){
            if (response && response.length>0) {
                $(this.element).find('.filemanager-infobar').append('<div style="cursor:default;padding:0px;margin:10px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                $(this.element).find('.filemanager-infobar').append('<div style="border-top:1px solid #B3B6D1;border-bottom:1px solid #EDEDF6;height:1px;padding:0px;margin:10px 0px"></div>');
                for (var i=0; i<response.length; i++) {
                    $(this.element).find('.filemanager-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                }
            }
        }));
    }),1000);
    }
};

var dash_files_up = function() {
    var root = this.options.data.root.split(desk_ds).slice(0,-1);
    if (root.length>0) {
        this.options.data.root=root.join(desk_ds);
        this.options.data.page=1;
        desk_window_reload(this);
    }
};

var dash_files_mkdir = function() {
    desk_prompt(t('Enter name'), this.bind(this, function(name){
        var root = this.options.data.root;
        desk_window_request(this, url('dash/files/mkdir'),{'name':name, 'root':root});
    }));
};

var dash_files_new_text_file = function() {
    desk_prompt(t('Enter name'), this.bind(this, function(name){
        var root = this.options.data.root;
        desk_window_request(this, url('dash/files/textfile'),{'name':name, 'root':root});
    }));
};

var dash_files_new_html_file = function() {
    desk_prompt(t('Enter name'), this.bind(this, function(name){
        var root = this.options.data.root;
        desk_window_request(this, url('dash/files/htmlfile'),{'name':name, 'root':root});
    }));
};

var dash_files_rename = function() {
    var selected = desk_window_selected(this);
    if (typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_prompt(t('Enter name'), this.bind(this, function(name){
            desk_window_request(this, url('dash/files/rename'),{'name':name, 'file':selected.items[0]});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected.items[0].split(desk_ds).slice(-1)[0]);
    }
};
    
var dash_files_upload_url = function() {
    desk_prompt(t('Enter URL address'),this.bind(this, function(address) {
        var root = this.options.data.root;
        if (address.length>0) {
            desk_window_request(this, url('dash/files/xhruploadsrc'), {'dirroot': root, 'url': address});
        }
    }));
};    
    
var dash_files_download = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].type)!="undefined" && selected[0].type!='folder') {
        var a=document.createElement('a');
        var regexp = new RegExp('\\'+desk_ds, 'g');
        a.href=baseUrl(selected[0].data.replace(regexp, '/'));
        a.download=selected[0].data.split(desk_ds).slice(-1)[0];
        document.body.appendChild(a);
        HTMLElementClick.call(a);
        document.body.removeChild(a);
    }
};
    
var dash_files_copy = function() {
    var selected = desk_window_selected(this);
    if (typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_prompt(t('Enter folder path'), this.bind(this, function(path){
            desk_window_request(this, url('dash/files/copy'),{'path':path, 'file':selected.items[0]});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected.items[0].split(desk_ds).slice(0,-1).join(desk_ds));
    }
};

var dash_files_move = function() {
    var selected = desk_window_selected(this);
    if (typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_prompt(t('Enter folder path'), this.bind(this, function(path){
            desk_window_request(this, url('dash/files/move'),{'path':path, 'file':selected.items[0]});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected.items[0].split(desk_ds).slice(0,-1).join(desk_ds));
    }
};
    
var dash_files_pack = function() {
    var selected = desk_window_selected(this);
    if (typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_prompt(t('Enter archive name'), this.bind(this, function(name){
            var root = this.options.data.root;
            desk_window_request(this, url('dash/files/pack'),{'name':name, 'files':selected.items, 'dirroot': root});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected.items[0].split(desk_ds).slice(-1)[0].split('.')[0]);
    }
};    
    
var dash_files_unpack = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].type)!="undefined" && selected[0].type=='archive') {
        desk_prompt(t('Enter folder path'), this.bind(this, function(path){
            desk_window_request(this, url('dash/files/unpack'),{'dirroot':path, 'file':selected[0].data});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(this.options.data.root);
    }
};    
    
var dash_files_notepad = function() {
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1) return;
    var data=selected[0].data;
    desk_text_editor(data);
};    
    
var dash_files_show_image = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].image_url)!="undefined" && selected[0].image_url.length>0) {
        $('body').append('<a href="'+selected[0].image_url+'" data-lightbox="filemanager_zoomer" id="dashwindow-filemanager-zoomer-lightbox"></a>');
        $('#dashwindow-filemanager-zoomer-lightbox').trigger('click');
        $('#dashwindow-filemanager-zoomer-lightbox').remove();
    }
};    

var dash_files_edit = function() {
    try { window.clearTimeout(this.timer); } catch(err) {};
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1) return;
    var data=selected[0].data;
    if (typeof(selected[0].type)!="undefined" && selected[0].type=='folder') {
        this.options.data.root=data;
        this.options.data.page=1;
        desk_window_reload(this);
    } else if (typeof(selected[0].type)!="undefined" && selected[0].type=='archive') {
        desk_call(dash_files_unpack, this);
    } else if (typeof(selected[0].type)!="undefined" && selected[0].type=='txt') {
        desk_text_editor(data);
    } else if (typeof(selected[0].type)!="undefined" && selected[0].type=='html') {
        desk_html_editor(data);
    } else if (typeof(selected[0].type)!="undefined" && selected[0].type=='image') {
        desk_image_editor(data);
    }
};