
var telescope = new function(){
    this.init = function(){
        var grid = {width: 5, height:  4, top: 6, left: 3, hidden: true};
        if(proofLogin())
            grid = {width: 8, height:  8, top: 1, left: 2, hidden: true};
        this.applicationVar = new application('telescope');
        this.applicationVar.create('Telescope', 'html', "<div id='telescopeFrame'></div>", grid);
        
        
	this.tabs = new tabs('#telescopeFrame');
        this.tabs.init();
        
        
        var html;
        
        html = this.generateHeader('');
        
        var results = {'folders':[], 'collections':[], 'files':[], 'youtube':[],'wikipedia':[]};
        
        html += '<div class="searchFrame">';
        
        html += this.generateNav(results);
        
        html += this.generateFrame(results);
        
        html += '</div>';
        
        
	this.tabs.addTab('Home', '', html);
        telescope.initHandlers();
    };
    //returns type for li.type_
    this.getHandlerType = function(handlertype){
        var types = [];
        types['youtube'] = ['video', 'link'];
        types['wikipedia'] = ['link'];
    };
    this.getFilters = function($li){
        var results = [];
        var $base = $li.parentsUntil('#telescope');
        $base.find('.leftNav').find('li.active').each(function(){
            results.push($(this).attr('data-type'));
        });
        
        $base.find('#filterList').find('li.active').each(function(){
            results.push($(this).attr('data-type'));
        });
        
        return results;
    };
    this.applyFilterCategories = function($li){
        
    };
    this.applyFilters = function($li){
        var filters = telescope.getFilters($li);
        
        var $base = $li.parentsUntil('#telescope').find('.telescopeList');
        
        //if class is active -> show lists with type
        //if empty filters, filters = [undefined, undefined, undefined], so filters.length is 3, thats why if(filters.length === 0) can't be use. instead var i is counted up and checked afterwards. if i === 0 -> show all list items.
        var i = 0;
        if($li.hasClass('active') === true){
            $base.find('li').hide();
                $.each(filters, function(index, value){
                    $base.find('.type_'+value).show();
                    i++;
                });
        }else{
            //if class is not active -> show lists with 
            $base.find('li.type_'+$li.attr('data-type')).hide();
        }
                if(i === 0){
                    $base.find('li').show();
                }
    };
    
    this.initHandlers = function(){
        $('#telescope .leftNav .categoryTitle').unbind('click');
        $('#telescope .leftNav .categoryTitle').bind('click', function(){
            
            
           $(this).toggleClass('active');
           $('#telescope .leftNav .categoryTitle').not(this).removeClass('active');
           var $searchFrame = $(this).parent().parent().parent();
           $(this).parent().find('li').not('.categoryTitle').hide();
           if($(this).hasClass('active') === true){
                $searchFrame.addClass('navOpen');
                $(this).nextUntil('.categoryTitle').slideDown();
           }else{
                $searchFrame.removeClass('navOpen');
                $(this).nextUntil('.categoryTitle').slideUp();
           }
        });
        $('#telescope .leftNav li').not('.categoryTitle').unbind('click');
        
        $('#telescope #filterList li').unbind('click');
        $('#telescope #filterList li').bind('click', function(){
            
            $(this).toggleClass('active');
            telescope.applyFilters($(this));
            
        });
        
        $('#telescope .leftNav li').not('.categoryTitle').bind('click', function(){
            var type = $(this).attr('data-type');
            
            $(this).toggleClass('active');
            
            telescope.applyFilters($(this));
        });
        $('#telescope #telescopeInput').bind('keydown', function(){
            var query = $(this).val();
            var $this = $(this);
            delay(function(){
                $this.parent().next().html(telescope.updateResult(query));
                $this.parent().next().children('.leftNav').children().children('li').show();
            
            }, 500);
        });
        
        
        //init handlers on list
        $('.telescopeList li').children().not('.settings').bind('click', function(e){
            eval('handlers.'+$(this).parent().attr('data-type')+'.handler(\''+$(this).parent().attr('data-selector')+'\')');
        });
        
        //init preventDefault for itemSettingsbutton
        $('.telescopeList .itemSettingsButton').bind('click', function(e){
        });
        
        //init toggle view buttons
        $('#telescope .frameRight .headerbuttons .icon').click(function(){
            console.log('wubba');
            var classes = $(this).attr('class');
            
            
            $(this).toggleClass('active');
            
            //toggle blue icon class
            if($(this).hasClass('active')){
                $(this).attr('class',classes.replace('icon-', 'blue-'));
            }else{
                $(this).attr('class',classes.replace('blue-', 'icon-'));
            }
            
            //remove active class from all other icons inside the icon bar
            $(this).parent().find('.icon').not(this).removeClass('active');
            
            //remove blue icon class from all other icons inside the icon bar
            $(this).parent().find('.icon').not(this).each(function(){
                
                var classes = $(this).attr('class');
                $(this).attr('class',classes.replace('blue-', 'icon-'));
                
            });
            
            var $telescopeList = $(this).parent().parent().parent().parent().parent().find('.telescopeList');
            
            $telescopeList.removeClass('table thumbSmall thumbBig');
            
            switch($(this).attr('data-type')){
                case 'table':
                    $telescopeList.addClass('table');
                    break;
                case 'thumbSmall':
                    $telescopeList.addClass('thumbSmall');
                    break;
                case 'thumbBig':
                    $telescopeList.addClass('thumbBig');
                    break;
            }
            
        });
    };
    
    this.foldLeftNav = function(){
        
    };
    
    //apply filters
    
    //load results from api
    this.loadResults = function(query){
        var results = {};
        //universe
        
            //folders
            results.folders = handlers.folders.query(query, 0, 50);
            //elements
            results.collections = handlers.collections.query(query, 0, 50);
            //files
            results.files = handlers.files.query(query, 0, 50);
        
        
        //youtube
        results.youtube = handlers.youtube.query(query, 0, 50);
        
        //wiki
        results.wikipedia = handlers.wikipedia.query(query, 0, 50);
        
        return results;
    };
    
    //updates result in opened telescope tab
    this.updateResult = function(query){
        var results = this.loadResults(query);
        return this.generateNav(results)+this.generateFrame(results);
    };
    this.showResultLength = function(length){
        if(length >= 50)
            return '&nbsp;<span class="label label-info">'+50+'+</span>';
        else{
            return '&nbsp;<span class="label label-info">'+length+'</span>';
        }
    };
    
    this.parseResults = function(results){
        var html;
        html = telescope.parseResult('folders', results.folders);
        html += telescope.parseResult('collections', results.collections);
        html += telescope.parseResult('files', results.files);
        html += telescope.parseResult('youtube', results.youtube);
        html += telescope.parseResult('wikipedia', results.wikipedia);
        return html;
    };
    
    
    //builds result thumb
    this.buildThumb = function(type, selector){

        if(selector !== null){
            var html;
                html = '<div>'+handlers[type].getThumbnail(selector)+'<h2>'+gui.shorten(handlers[type].getTitle(selector), 28)+'</h2></div>';
                html += '<div>'+nl2br(gui.shorten(handlers[type].getDescription(selector), 50))+'</div>';
                html += '<div></div>';
                var itemType;
                switch(type){

                    default:
                        itemType = 'link';
                        break;
                    case 'file'||'element'||'folder'||'user':
                        itemType = type;
                        break;
                }
                html += '<div class="settings">'+item.showItemSettings(itemType,selector)+'</div>';
            return html;
        }else{
            return '';
        }
    };
    
    //parses results to li
    this.parseResult = function(type, results){
        var html = '';
        $.each(results, function(index, value){
            html += '<li class="type_'+type+'" data-type="'+type+'" data-selector="'+value+'">'+telescope.buildThumb(type,value)+'</li>';
        });
        return html;
    };
    
    this.generateHeader = function(query){
        
        var html;
        
        html = '<header>';
            html += '<ul id="filterList">';
                html += '<li data-type="link"><span class="icon icon-link brightIcon"></span><span class="icon blue-link blueIcon"></span></li>';
                html += '<li data-type="image"><span class="icon icon-image brightIcon"></span><span class="icon blue-image blueIcon"></span></li>';
                html += '<li data-type="video"><span class="icon icon-youtube brightIcon"></span><span class="icon blue-youtube blueIcon"></span></li>';
                html += '<li data-type="file"><span class="icon icon-file brightIcon"></span><span class="icon blue-file blueIcon"></span></li>';
                html += '<li data-type="user"><span class="icon icon-user brightIcon"></span><span class="icon blue-user blueIcon"></span></li>';
            html += '</ul>';
            html += '<input type="text" id="telescopeInput" placeholder="search" value="'+query+'">';
            html += '<span class="icon icon-search"></span>';
        html += '</header>';
        return html;
    }
    
    this.generateNav = function(results){
        var html;
        html = "<div class='leftNav dark' style='background: #37474f;border-top: 1px solid #dcdcdc;'>";
        html +=    '<ul>';
        html +=       '<li class="categoryTitle active" style="margin-left:1px;"><span class="icon blue-reader"></span>Everything</li>';
        html +=       '<li style="margin-left:130px;" class="categoryTitle"><span class="icon blue-gear"></span>universeOS</li>';
        html +=       '<li data-type="folders"><span class="icon blue-folder"></span>Folders'+this.showResultLength(results.folders.length)+'</li>';
        html +=       '<li data-type="collections"><span class="icon blue-archive"></span>Collections'+this.showResultLength(results.collections.length)+'</li>';
        html +=       '<li data-type="files"><span class="icon blue-file"></span>Files'+this.showResultLength(results.files.length)+'</li>';
//        html +=       '<li class="spacer"></li>';
//        html +=       '<li data-type="folder"><span class="icon blue-eye"></span>Public</li>';
//        html +=       '<li data-type="folder"><span class="icon blue-eye"></span>Hidden</li>';
        html +=       '<li style="margin-left:270px;" class="categoryTitle"><span class="icon blue-rss"></span>Web</li>';
        html +=       '<li data-type="youtube"><span class="icon blue-youtube"></span>Youtube'+this.showResultLength(results.youtube.length)+'</li>';
        html +=       '<li data-type="wikipedia"><span class="icon blue-wikipedia"></span>Wikipedia'+this.showResultLength(results.wikipedia.length)+'</li>';
        html +=       '<li class="categoryTitle spacer"></li>';//empty title row at the end of navigation, otherwise the toggle function doesnt work
        html +=         '<span class="icon white-chevron-right toggleLeftNav"></span>';
        html +=   '</ul>';
        html += '</div>';
        return html;
    };
    
    this.generateFrame = function(results){
        var html,header;
        html = "<div class='frameRight' style='border-top: 1px solid #dcdcdc;' id='telescopeFrame'>";
        
            header = '<header>';
                header += '<div style="width: 300px;">';
                    header += 'Name';
                header += '</div>';
                header += '<div>';
                    header += 'Date';
                header += '</div>';
                header += '<div>';
                    header += 'Size';
                header += '</div>';
                header += '<div style="min-width: 110px;">';
                    header += '<span class="headerbuttons"><span class="icon blue-list" data-type="table"></span><span class="icon icon-small-symbols" data-type="thumbSmall"></span><span class="icon icon-large-symbols" data-type="thumbBig"></span>';
                header += '</div>';
            header += '</header>';

        html += '<ul class="telescopeList table">'+header+telescope.parseResults(results)+'</ul>';
        html += '</div>';
        return html;
    };
    
    this.appendResults = function(){
        
    };
    
    this.query = function(query){
        this.applicationVar.show();
        var results = this.loadResults(query);
        var html;
        
        html = this.generateHeader(query);
        
        html += '<div class="searchFrame">';
        
        html += this.generateNav(results);
        
        html += this.generateFrame(results);
        
        html += '</div>';
        
        this.tabs.addTab(query, '', html);
        this.initHandlers();
    };
        
};