/**
 * @author zjh
 */
+function ($) {

	"use strict";
	
	/**
	 * 表格组件
	 * @class Grid
	 * @constructor
	 * @example
	 */
	var Grid = function(element, options){
		this.$element = $(element);
		this.options = options;
		this.pageSize = options.pageSize;
		this.pageNo = options.pageNo;
		this.showPage = options.showPage;
		this.searchCondition = {};
        if(typeof options.searchCondition === 'object'){
            this.searchCondition = options.searchCondition;
        }
        this.sortName = null;
        this.sortOrder = null;
        if(options.sortName){
            this.sortName = options.sortName;
        }
        if(options.sortOrder){
            this.sortOrder = options.sortOrder;
        }
        this.itemsMap = {};
		this._initLayout();
		this._initButtons();
		this._initHead();
		this._initOptions();
		this._initEvents();
		if(this.options.autoLoad){
			var self = this;
			setTimeout(function(){
				self._loadData();
			},0);
		}
	};
	Grid.DEFAULTS = {
		/**
		 * 数据载入时的提示文字
		 * @property loadingText
		 * @type String
		 * @default '正在载入...'
		 */
		loadingText: '正在载入...',
		
		/**
		 * 没有数据时的提示文字
		 * @property noDataText
		 * @type String
		 * @default '没有数据'
		 */
		noDataText: '没有数据',    
		
		/**
		 * 是否显示索引列
		 * @property isShowIndexCol
		 * @type Boolean
		 * @default true
		 */
		isShowIndexCol: true, //
		
		/**
		 * 是否显示功能按钮
		 * @property isShowButtons
		 * @type Boolean
		 * @default true
		 */
		isShowButtons: true,
		
		/**
		 * 是否表格准备好时加载数据
		 * @property autoLoad
		 * @type Boolean
		 * @default true
		 */
		autoLoad: true, 
		
		/**
		 * 是否显示分页
		 * @property isShowPages
		 * @type Boolean
		 * @default true
		 */
		isShowPages: true, 
		
		/**
		 * 是否使用本地数据源
		 * @property isUserLocalData
		 * @type Boolean
		 * @default false
		 */
        isUserLocalData: false,
        
        /**
		 * 请求数据方式
		 * @property method
		 * @type String
		 * @default 'POST'
		 */
		method: 'POST', 
		
		/**
		 * 主键字段
		 * @property identity
		 * @type String
		 * @default 'id'
		 */
		identity: 'id', 
		
		/**
		 * 是否锁定列宽度
		 * @property lockWidth
		 * @type Boolean
		 * @default false
		 */
		lockWidth: false,
		
		/**
		 * 每页显示多少条数据，采用物理分页方式
		 * @property pageSize
		 * @type Integer
		 * @default 10
		 */
		pageSize: 10,
		
		/**
		 * 当前页，从1开始
		 * @property pageNo
		 * @type Integer
		 * @default 1
		 */
		pageNo: 1,
		
		/**
		 * 分页时显示多少页
		 * @property showPage
		 * @type Integer
		 * @default 4
		 */
		showPage: 4
	};
	Grid.prototype = {
		Constructor: Grid,
		_initLayout: function(){
			this.table = $(Grid.DEFAULTS.TEMPLATE).appendTo(this.$element);
			this.buttons = this.$element.find('.buttons');
			this.searchContainer = this.$element.find('.search');
			this.condition = this.searchContainer.find('[data-role="condition"]');
			this.totalRecordHtml = this.$element.find('[data-role="total-record"]');
			this.startRecord = this.$element.find('[data-role="start-record"]');
			this.endRecord = this.$element.find('[data-role="end-record"]');
			this.pages = this.$element.find('.pages');
			this.grid = this.$element.find('.grid');
			this.gridBody = this.$element.find('.grid-body').css('width', this.$element.width());
			this.gridTableHead = this.$element.find('.grid-table-head');
			this.gridTableHeadTable = this.gridTableHead.find('table');
			this.gridTableBody = this.$element.find('.grid-table-body').css('width', this.$element.width());
			this.gridTableBodyTable = this.gridTableBody.find('table');
			this.pageSizeSelect = this.$element.find('[data-role="pageSizeSelect"]');
			!this.options.isShowButtons && this.buttons.hide();
			!this.options.isShowPages && this.grid.find('tfoot').hide();
			this.colResizePointer = this.table.find('.colResizePointer');
		},
		_initButtons: function(){
			var self = this;
			var buttons = self.options.buttons;
			if(buttons && buttons.length > 0){
				 for(var i= 0,j=buttons.length; i<j; i++){
					 var action = buttons[i].action;
					  $(buttons[i].content).appendTo(self.buttons).on('click', {action: action}, function(e){
						  e.stopPropagation();
						  e.preventDefault();
						  self.$element.trigger(e.data.action, {data:self.selectedRowsIndex(), item: self.selectedRows()});
					  });
				 }
			}else{
				self.options.isShowButtons = false;
			}
		},
		_initHead: function(){
			var self = this;
			var columns = this.options.columns;
			if(!columns || columns.length == 0){
//				$('body').message({
//					type: 'warning',
//					content: '没有列数据'
//				});
				return;
			}
			var totalColumnWidth = 0;
			var widthRgExp= /^[1-9]\d*\.?\d*(px){0,1}$/;
			var titleHtml = new Array();
			titleHtml.push('<tr>');
			if(this.options.isShowIndexCol){
				titleHtml.push('<th width="50px;"><div class="checker"><span data-role="selectAll"></span></div></th>');
			}else{
				titleHtml.push('<th width="50px;" style="display:none"><div class="checker"><span data-role="selectAll"></span></div></th>');
			}
			for(var i= 0, j=columns.length; i<j; i++){
				var column = columns[i];
				var width = column.width + '';
				titleHtml.push('<th index="'+i+'" width="');
				if(width.match(widthRgExp)){
					width = width.replace('px', '');
					totalColumnWidth +=  parseInt(width);
					titleHtml.push(width +'px"');
				}else{
					titleHtml.push(column.width+'"');
				}
				if(column.sortable && column.sortName){
					titleHtml.push(' class="sort" sortName="'+column.sortName+'" title="点击排序"');
				}
				titleHtml.push('>');
				titleHtml.push(column.title);
				if(!this.options.lockWidth){
					titleHtml.push('<div class="colResize"></div>');
				}
				titleHtml.push('</th>');
			}
			this.gridTableHeadTable.html(titleHtml.join(''));
			if(totalColumnWidth > this.$element.width()){
				this.gridTableHeadTable.css('width', totalColumnWidth);
				this.gridTableBodyTable.css('width', totalColumnWidth);
			}else{
				this.gridTableHead.css('width', this.$element.width());
				this.gridTableHeadTable.find('th:last').css('width', 'auto');
				this.options.columns[this.options.columns.length-1].width = 'auto';
			}
			this.gridTableHeadTable.find('[data-role="selectAll"]').on('click',function(e) {
				e.stopPropagation();
				var $this = $(this);
				if($this.hasClass('checked')){
					self.gridTableBodyTable.find('[data-role="indexCheckbox"]').each(function(){
						$(this).removeClass('checked').closest('tr').removeClass('success');
					});
				}else{
					self.gridTableBodyTable.find('[data-role="indexCheckbox"]').each(function(){
						$(this).addClass('checked').closest('tr').addClass('success');						
					});
				}
				$this.toggleClass('checked');
			});
			var sorts = this.gridTableHeadTable.find('.sort');
			sorts.on('click', function(e){
				e.stopPropagation();
				var $this = $(this);
				self.sortName = $this.attr('sortName');
				if($this.hasClass('sorting-asc')){
					sorts.removeClass('sorting-asc').removeClass('sorting-desc').find('span').remove();
					$this.removeClass('sorting-asc').addClass('sorting-desc');
					$this.find('span').remove().end().append($('<span class="glyphicon glyphicon-arrow-down"></span>'));
					self.sortOrder = 'desc';
				}else{
					sorts.removeClass('sorting-asc').removeClass('sorting-desc').find('span').remove();
					$this.removeClass('sorting-desc').addClass('sorting-asc');
					$this.find('span').remove().end().append($('<span class="glyphicon glyphicon-arrow-up"></span>'));
					self.sortOrder = 'asc';
				}
				self._loadData();
			});
			this.gridTableHeadTable.find('.colResize').on('mousedown', function(e){
				e.stopPropagation();
				var $this = $(this);
				var start = e.pageX;
				var left = self.gridTableHead.offset().left;
				self.colResizePointer.css({'height': self.gridBody.height(), 'left': e.pageX - self.gridTableBody.scrollLeft() - left}).show();
				self.grid.css({'-moz-user-select': 'none', 'cursor': 'move'}).on({
					'selectstart': function (){
						return false;
					},
					'mousemove': function(e){
						self.colResizePointer.css({'left': e.pageX - self.gridTableBody.scrollLeft() - left}).show();
					},
					'mouseup': function(e){
						var end = e.pageX;
						var $th = $this.parent();
						var width = parseFloat($th.attr('width')) + end - start;
						$th.attr('width', width);
						var index = $th.attr('index');
						self.gridTableBodyTable.find('td[index="'+index+'"]').attr('width', width);
						$(this).css({'-moz-user-select': '-moz-all', 'cursor': 'default'}).off('selectstart').off('mouseup').off('mousemove');
						self.colResizePointer.hide();
						self.options.columns[index].width = width;
					}
				});
			});
		},
		_initOptions: function(){
			var self = this;
			//每页记录数
			this.pageSizeSelect.select({
				contents: [
					{value: '5', title: '5'},
					{value: '10', title: '10'},
					{value: '20', title: '20'},
					{value: '50', title: '50'},
					{value: '100', title: '100'}
				]
			});
  			this.pageSizeSelect.setValue(this.options.pageSize).on('change', function(){
  				self.pageSize = $(this).getValue();
  				self.pageNo = Grid.DEFAULTS.pageNo;
  				self._loadData();
  			});
			if(self.options.querys && self.options.querys.length>0){
				this.condition.select({
					title: '选择条件',
					contents: self.options.querys
				});
			}else{
				this.searchContainer.hide();
				!this.options.isShowButtons && this.searchContainer.parent().hide();
			}
		},
		_initEvents: function(){
			var self = this;
			this.gridTableBody.on('scroll', function(){
				self.gridTableHead.css('left', -$(this).scrollLeft());
			});
			this.searchContainer.find('button[data-role="searchBtn"]').on('click', function(){
				for(var i=0,j=self.options.querys.length; i<j; i++){
					delete self.searchCondition[self.options.querys[i].value];
				}
				var condition = self.condition.getValue();
				if(!condition){
					$('body').message({
						type: 'warning',
						content: '请选择查询条件'
					});
					return;
				}
				var value =  self.searchContainer.find('input[data-role="searchValue"]').val().replace(/(^\s*)|(\s*$)/g, "");
				self.searchCondition[condition] =  value;
				self._loadData();
			});
		},
		/*
		 *加载数据
		 */
		_loadData: function(){
			var self = this;
			var params = {};
			params.pagesize = self.pageSize;
			params.page = self.pageNo;
			for(var prop in self.searchCondition){
				params[prop] = self.searchCondition[prop];
			}
			if(self.sortName && self.sortOrder){
				params.sortname = self.sortName;
				params.sortorder = self.sortOrder;
			}
            if(self.options.isUserLocalData){
            	var start = self.pageSize * (self.pageNo-1);
            	var end =  self.pageSize * self.pageNo-1;
            	self.totalRecord = self.options.localData.length;
            	self.startRecord.text(start + 1);
				self.endRecord.text(end+1);
				self.totalRecordHtml.text(self.totalRecord);
				self.items = self.getItemsFromLocalData(start, end);
				self._initPageNo(self.totalRecord)
                if(!self.options.localData || self.options.localData.length == 0){
                    self.gridTableBodyTable.empty();
                    self.gridTableBody.find('[data-role="noData"]').remove();
                    self.gridTableBody.append($('<div data-role="noData" style="font-size:16px ; padding: 20px; width:'+self.gridTableBodyTable.width()+'px;">'+self.options.noDataText+'</div>'));
                }else{
                    self.gridTableBody.find('[data-role="noData"]').remove();
                    self.renderDatas();
                }
                return;
            }else{
            	$.ajax({
    				type: this.options.method,
    				url: this.options.url,
    				data: params,
    				dataType: 'json'
    			}).done(function(result){
    				self._handleResult(self, result);
    			}).fail(function(result){

    			});
            }
			
		},
		/**
		 * 根据开始结束记录数从本地数据获取数据
		 * @method getItemsFromLocalData
		 * @return Array
		 */
		getItemsFromLocalData: function(start, end){
			var items = [];
			if(end > (this.totalRecord - 1)){
				end = this.totalRecord - 1;
			}
			for(var i=start; i<=end; i++){
				items.push(this.options.localData[i]);
			}
			return items;
		},
		/**
		 * 初始化分页
		 * @method _initPageNo
		 */
		_initPageNo: function(){
			var self = this;
			var pageSize = self.pageSizeSelect.getValue();
			this.totalPage = Math.floor(this.totalRecord / pageSize);
			if(this.totalRecord % pageSize != 0){
				this.totalPage ++;
			}
			if(this.totalPage == 0){
				this.pages.hide();
				return;
			}
			var pagination = self.pages.find('ul.pagination');
			var pageHtml = new Array();
			pageHtml.push('<li data-role="firstPage"><a href="#">&laquo;</a></li>');
			pageHtml.push('<li data-role="prev"><a href="#">&lsaquo;</a></li>');
			if((self.pageNo-1) % self.showPage == 0){
				self.pageNo != 1 && pageHtml.push('<li><a href="#">...</a></li>');
				for(var i=self.pageNo; i<=self.totalPage && i<(self.pageNo+self.showPage); i++){
					pageHtml.push('<li data-value="'+i+'" data-role="pageNo"><a href="#">'+i+'</a></li>');
				}
				(self.pageNo + self.showPage) < self.totalPage && pageHtml.push('<li><a href="#">...</a></li>');
			}else{
				var start = Math.floor((self.pageNo-1)/self.showPage)*self.showPage+1;
				start != 1 && pageHtml.push('<li><a href="#">...</a></li>');
				for(var i=start; i<=self.totalPage && i<(start+self.showPage); i++){
					pageHtml.push('<li data-value="'+i+'" data-role="pageNo"><a href="#">'+i+'</a></li>');
				}
				(start + self.showPage) < self.totalPage && pageHtml.push('<li><a href="#">...</a></li>');
			}
			pageHtml.push('<li data-role="next"><a href="#">&rsaquo;</a></li>');
			pageHtml.push('<li data-role="lastPage" ><a href="#">&raquo;</a></li>');
			pagination.html(pageHtml.join('')).find('li[data-role="pageNo"]').on('click', function(){
				self.pageNo = $(this).data('value');
				self._loadData();
			}).end().find('li[data-value="'+self.pageNo+'"]').addClass('active');
			var prevBtn =  pagination.find('li[data-role="prev"]').on('click', function(){
				if($(this).hasClass('disabled')){
					return;
				}
				self.pageNo-- ;
				self.pageOperateStatus = 'prev';
				self._loadData();
			});
			var nextBtn =  pagination.find('li[data-role="next"]').on('click', function(){
				if($(this).hasClass('disabled')){
					return;
				}
				self.pageNo++ ;
				self.pageOperateStatus = 'next';
				self._loadData();
			});
			var firstPageBtn =  pagination.find('li[data-role="firstPage"]').on('click', function(){
				if($(this).hasClass('disabled')){
					return;
				}
				self.pageNo = 1;
				self._loadData();
			});
			var lastPageBtn =  pagination.find('li[data-role="lastPage"]').on('click', function(){
				if($(this).hasClass('disabled')){
					return;
				}
				self.pageNo = self.totalPage;
				self._loadData();
			});
			self.pageNo == 1 && prevBtn.addClass('disabled') && firstPageBtn.addClass('disabled');
			self.pageNo == self.totalPage && nextBtn.addClass('disabled') && lastPageBtn.addClass('disabled');
		},
		/**
		 * 渲染数据
		 * @method renderDatas
		 */
		renderDatas: function(){
			var self = this;
			self.renderRows();
			self.initSelectRowEvent();
			self.options.isShowPages && self._initPageNo();
		},
        initSelectRowEvent: function(){
            var self = this;
            var selectAll = self.gridTableHeadTable.find('[data-role="selectAll"]');
            var indexCheckboxs = this.gridTableBodyTable.find('[data-role="indexCheckbox"]');
            indexCheckboxs.off('click').on('click',function(e) {
                e.stopPropagation();
                var $this = $(this);
                if($this.hasClass('checked')){
                    $this.removeClass('checked').closest('tr').removeClass('success');
                }else{
                    $this.addClass('checked').closest('tr').addClass('success');
                }
                if(self.selectedRowsIndex().length == indexCheckboxs.length){
                    selectAll.addClass('checked');
                }else{
                    selectAll.removeClass('checked');
                }
                self.$element.trigger('selectedRow', {checked: $this.hasClass('checked'), item:self.items[$this.attr('indexValue')]});
            });
            this.gridTableBodyTable.find('tr').off('click').on('click', function(){
                var $this = $(this);
                if($this.hasClass('success')){
                    $this.removeClass('success').find('[data-role="indexCheckbox"]').removeClass('checked');
                }else{
                    $this.addClass('success').find('[data-role="indexCheckbox"]').addClass('checked');
                }
                self.$element.trigger('selectedRow', {checked: !$this.hasClass('success'), item:self.items[$this.attr('indexValue')]});
                if(self.selectedRowsIndex().length == indexCheckboxs.length){
                    selectAll.addClass('checked');
                }else{
                    selectAll.removeClass('checked');
                }
            });
        },
		/**
		 * 渲染表格数据
		 * @method renderRows
		 */
		renderRows: function(){
			var self = this;
			if(self.options.tree && self.options.tree.column){
				self.items = self.initTreeItems(new Array(), self.items);
			}
			var items = self.items;
			var trHtmls = new Array();
			for(var i= 0,j=items.length; i<j; i++){
				var item = items[i];
                self.itemsMap[item.id] = item;
				var trHtml = new Array();
				if(self.options.tree && self.options.tree.column){
					trHtml.push('<tr data-level='+item.level+' data-children='+self.getChildrenCount(0, item.children)+'>');
				}else{
					trHtml.push('<tr>');	
				}
				if(this.options.isShowIndexCol){
					trHtml.push('<td width="50px;"><div class="checker"><span indexValue="'+i+'" data-role="indexCheckbox" data-value="'+item[this.options.identity]+'"></span></div></td>');
				}else{
					trHtml.push('<td width="50px;" style="display:none"><div class="checker"><span indexValue="'+i+'" data-role="indexCheckbox" data-value="'+item[this.options.identity]+'"></span></div></td>');					
				}
				for(var k=0,h=this.options.columns.length; k<h; k++){
					var column = this.options.columns[k];
					trHtml.push('<td index="'+k+'" width="'+column.width+'"');
					if(column.align){
						trHtml.push(' align="'+column.align+'"');
					}
					trHtml.push('>');
					if(self.options.tree && self.options.tree.column 
						&& self.options.tree.column == column.name){
						trHtml.push('<div class="grid-tree-space" style="padding-left:'+(parseInt(item.level)-1)*10+'px;"><span data-role="grid-tree-icon" class="glyphicon glyphicon-folder-open open"></span></div>&nbsp;&nbsp;');
					}
					if(column.render){
						trHtml.push(column.render(item,column.name,i,k));
					}else{
						trHtml.push(item[column.name]);
					}
					trHtml.push('</td>');
				}
				trHtml.push('</tr>');
				trHtmls.push(trHtml.join(''));
			}
			this.gridTableBodyTable.html(trHtmls.join(''));
			if(self.options.tree && self.options.tree.column){
                self.gridTableBodyTable.find('[data-role="grid-tree-icon"]').on('click', function(e){
					e.stopPropagation();
					e.preventDefault();
					var $this = $(this);
					var $tr = $this.closest('tr');
					var level = parseInt($tr.attr('data-level'));
					var next = $tr.next();
					while(next.length > 0){
						if(level < parseInt(next.attr('data-level'))){
							if($this.hasClass('open')){
								next.hide();
								next.find('[data-role="grid-tree-icon"]').removeClass('glyphicon-folder-open').addClass('glyphicon-folder-close');
							}else{
								next.show();
								next.find('[data-role="grid-tree-icon"]').addClass('glyphicon-folder-open').removeClass('glyphicon-folder-close');
							}
							next = next.next();
						}else{
							break;
						}
					}
					if($this.hasClass('open')){
						$this.removeClass('open').removeClass('glyphicon-folder-open').addClass('glyphicon-folder-close');
					}else{
						$this.addClass('open').addClass('glyphicon-folder-open').removeClass('glyphicon-folder-close');
					}
				});
			}
		},
        /**
         * 树形表格获取子节点下的所有数量
         * @method getChildrenCount
         */
        getChildrenCount: function(count, items){
            var self = this;
            count += items.length;
            $.each(items, function(){
                if(this.children){
                    count = self.getChildrenCount(count , this.children);
                }
            });
            return count;
        },
		/**
		 * 初始化树形数据
		 * @method initTreeItems
		 */
		initTreeItems: function(newItems, items){
			var self = this;
			for(var i=0,j=items.length; i<j; i++){
				var item = items[i];
				newItems.push(item);
				if(item.children){
					newItems = self.initTreeItems(newItems, item.children);
				}
			}
			return newItems;
		},
		/**
		 * 返回选择行数据的数组。
		 * @method selectedRows
		 * @return Array
		 */
		selectedRows: function(){
			var self = this;
			var selectItems = new Array();
			this.gridTableBodyTable.find('.checked[data-role="indexCheckbox"]').each(function(){
				selectItems.push(self.items[$(this).attr('indexvalue')]);
			});
			return  selectItems;
		},
		/**
		 * 返回选择行的序号列表
		 * @method selectedRowsNo
		 * @return Array
		 */
		selectedRowsNo: function(){
			var selectIndexs = new Array();
			this.gridTableBodyTable.find('.checked[data-role="indexCheckbox"]').each(function(){
				selectIndexs.push($(this).attr('indexvalue'));
			});
			return  selectIndexs;
		},
		/**
		 * 返回所有行数据。
		 * @method getAllItems
		 * @return Array
		 */
        getAllItems: function(){
			return this.items;
		},
		/**
		 * 返回选择行索引的数组。
		 * @method selectedRowsIndex
		 * @return Array
		 */
		selectedRowsIndex: function(){
			var selectIndexs = new Array();
			this.gridTableBodyTable.find('.checked[data-role="indexCheckbox"]').each(function(){
				selectIndexs.push($(this).attr('data-value'));
			});
			return  selectIndexs;
		},
		/**
		 * 新增一行或多行记录
		 * @method insertRows
		 * @param items(Array)
		 */
		 insertRows: function(items){
		    var self = this;
		    if(!self.items){
		        self.items = new Array();
		        self.gridTableBody.find('[data-role="noData"]').remove();
		    }
		    if(items.length){
		        $.each(items, function(){
		            self.items.push(this);
		            self.itemsMap[this[self.options.identity], this];
		        });
		    }else{
		        self.items.push(items);
		        self.itemsMap[items[this.options.identity]] = items;
		    }
		    self.gridTableBodyTable.empty();
		    self.renderDatas();
			return this.$element;
		 },
		 /**
		  * 移除多行
		  * @method removeRows
		  * @param indexs(Array)
		  */
		 removeRows: function(indexs){
		     var self = this;
		     $.each(indexs, function(){
		          var index = self.getIndexByIdentityValue(this);
		          self.items.splice(index, 1);
		          delete self.itemsMap[this];
		     });
		     self.gridTableBodyTable.empty();
		     self.renderDatas();
		 },
		/**
		 * 更新多行数据
		 * @method updateRows
		 * @param currentKeyId
		 * @param item
		 */
		updateRows: function(currentKeyId, item){
			var self = this;
			var index = self.getIndexByIdentityValue(currentKeyId);
			self.items[index] = item;
			self.itemsMap[item[self.options.identity]] = item;
			self.gridTableBodyTable.empty();
			self.renderDatas();
		},
		/**
		 * 根据索引值返回索引列表
		 * @method getIndexByIdentityValue
		 * @param value
		 * @returns Array
		 */
		getIndexByIdentityValue: function(value){
		   return this.gridTableBodyTable.find('[data-value="'+value+'"]').closest('tr').index();
		},
		/**
		 * 根据索引返回一行
		 * @method getRowByIndex
		 * @param 索引值
		 * @returns jQuery对象
		 */
		getRowByIndex: function(index){
			return this.gridTableBodyTable.find('tr').eq(index);
		},
		
		/**
		 * 根据索引返回一行数据
		 * @method getItemByIndex
		 * @param index
		 * @returns json对象
		 */
        getItemByIndex: function(index){
            return this.items[index];
        },
		/**		 
		 * 刷新表格
		 * @method refresh
		 */
		refresh: function(){
			this.pageNo = Grid.DEFAULTS.pageNo;
			this.gridTableHeadTable.find('[data-role="selectAll"]').removeClass('checked');
			this._loadData();
		},
        /**
         * 销毁表格
         * @method destory
         */
        destory: function(){
           this.$element.off().data('koala.grid', null).empty();
        },
		/**
		 * 外部查询
		 * @method search
		 * @param 查询条件json对象
		 */
		search: function(conditions){
			for(var prop in conditions){
				this.searchCondition[prop] = conditions[prop];
			}
			this._loadData();
		},
		/**
		 * 上移
		 * @method up
		 * @param 索引
		 */
		up: function(index){
            var self = this;
            if(index == 0){
                return;
            }
            var currentRow = self.getRowByIndex(index);
            var prevRow = currentRow.prev('tr');
            var prevItem = self.items[parseInt(index)-1];
            var currentItem = self.items[index];
            if(self.options.tree && self.options.tree.column){
                if(parseInt(currentItem.level) > parseInt(prevItem.level)){
                    return false;
                }else{
                    var tempItem = currentRow.prevAll('[data-level='+currentItem.level+']:first');
                    if(tempItem.length > 0){
                        var tempIndex = tempItem.index();
                        var upLevel = currentRow.prevAll('[data-level='+parseInt(currentItem.level-1)+']:first');
                        if(upLevel.length > 0){
                            if(tempIndex < upLevel.index()){
                                return false;
                            }
                        }
                        prevRow = tempItem;
                    }
                }
            }
            var childrenCount = parseInt(currentRow.attr('data-children'));
            var tempCurrentRow = currentRow.next();
            currentRow.insertBefore(prevRow);
            if(childrenCount > 0){
                for(var i= 0; i < childrenCount; i++){
                    prevRow = currentRow;
                    currentRow = tempCurrentRow;
                    tempCurrentRow = currentRow.next();
                    currentRow.insertAfter(prevRow);
                }
            }
            self.items = new Array();
            self.gridTableBodyTable.find('tr').each(function(){
                var $this = $(this);
                var indexCheckbox = $this.find('[data-role="indexCheckbox"]');
                indexCheckbox.attr('indexvalue', $this.index());
                self.items.push(self.itemsMap[indexCheckbox.attr('data-value')]);
            });
            return true;
		},
		/**
		 * 下移
		 * @method down
		 * @param 索引
		 */
		down: function(index){
			var self = this;
		    if(index == self.items.length){
				return;
			}
            var currentRow = self.getRowByIndex(index);
            var nextRow = currentRow.next('tr');
            var nextItem = self.items[parseInt(index)+1];
			var currentItem = self.items[index];
			if(self.options.tree && self.options.tree.column){
				if(parseInt(currentItem.level) > parseInt(nextItem.level)){
                    return false;
				}else{
                    var tempItem = currentRow.nextAll('[data-level='+currentItem.level+']:first');
                    if(tempItem.length > 0){
                        var tempIndex = tempItem.index();
                        var upLevel = currentRow.nextAll('[data-level='+parseInt(currentItem.level-1)+']:first');
                        if(upLevel.length > 0){
                            if(tempIndex > upLevel.index()){
                                return false;
                            }
                        }
                        nextRow = tempItem;
                        var childrenCount = parseInt(tempItem.attr('data-children'));
                        for(var i= 0; i<childrenCount; i++){
                            nextRow = nextRow.next();
                        }
                    }
                }
			}
            var childrenCount = parseInt(currentRow.attr('data-children'));
            var tempCurrentRow = currentRow.next();
            currentRow.insertAfter(nextRow);
            if(childrenCount > 0){
                for(var i= 0; i < childrenCount; i++){
                    nextRow = currentRow;
                    currentRow = tempCurrentRow;
                    tempCurrentRow = currentRow.next();
                    currentRow.insertAfter(nextRow);
                }
            }
            self.items = new Array();
            self.gridTableBodyTable.find('tr').each(function(){
                var $this = $(this);
                var indexCheckbox = $this.find('[data-role="indexCheckbox"]');
                indexCheckbox.attr('indexvalue', $this.index());
                self.items.push(self.itemsMap[indexCheckbox.attr('data-value')]);
            });
            return true;
		}
		
		,_handleResult:function(self,result){
			if(!result.Rows){
				$('body').message({
					type: 'error',
					content: '查询失败'
				});
				return;
			}
			self.startRecord.text(result.start);
			self.endRecord.text(result.start + result.limit);
			self.totalRecordHtml.text(result.Total);
			self._initPageNo(result.Total)
			self.items = result.Rows;
			self.totalRecord = result.Total;
			if(result.Rows.length == 0){
				self.gridTableBodyTable.empty();
				self.gridTableBody.find('[data-role="noData"]').remove();
				self.gridTableBody.append($('<div data-role="noData" style="font-size:16px ; padding: 20px; width:'+self.gridTableBodyTable.width()+'px;">'+self.options.noDataText+'</div>'));
			}else{
				self.gridTableBody.find('[data-role="noData"]').remove();
				self.renderDatas();
			}
            self.$element.trigger('complate', result);
		}
	};
	$.fn.getGrid = function(){
		return $(this).data('koala.grid');
	};
	Grid.DEFAULTS.TEMPLATE = '<div class="table-responsive"><table class="table table-responsive table-bordered grid"><thead><tr><th><div class="btn-group buttons"></div><div class="search"><div class="btn-group select " data-role="condition"></div><div class="input-group" style="width:180px;"><input type="text" class="input-medium form-control" placeholder="Search" data-role="searchValue"><div class="input-group-btn"><button type="button" class="btn btn-default" data-role="searchBtn"><span class="glyphicon glyphicon-search"></span></button></div></div></div></th></tr></thead><tbody><tr><td><div class="colResizePointer"></div><div class="grid-body"><div class="grid-table-head"><table class="table table-bordered"></table></div><div class="grid-table-body"><table class="table table-responsive table-bordered table-hover table-striped"></table></div></div></td></tr></tbody><tfoot><tr><td><div class="records">显示:<span data-role="start-record">1</span>-<span data-role="end-record">10</span>, 共<span data-role="total-record">0</span>条记录。&nbsp;每页显示:<div class="btn-group select " data-role="pageSizeSelect"></div>条</div><div><div class="btn-group pages"><ul class="pagination"></ul></div></div></td></tr></tfoot></table></div>';
	var old = $.fn.grid;
	$.fn.grid = function(option){
		return this.each(function(){
			var $this = $(this);
			var data = $this.data('koala.grid');
			var options = $.extend({}, Grid.DEFAULTS, $this.data(),typeof option == 'object' && option);
			//这了为了适应API_Builder的调用，修改一下
//			if(!data){
				$this.data('koala.grid',(data = new Grid(this, options)));
//			}
			if(typeof option == 'string'){
				data[option]();
			}
		});
	};
	$.fn.grid.Constructor = Grid;
	$.fn.grid.noConflict = function(){
		$.fn.grid = old;
		return this;
	};
}(window.jQuery);