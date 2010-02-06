/** @author  : Laurent Cherpit */
DocDb.SearchForm=Ext.extend(Ext.form.FormPanel,{border:false,initComponent:function(){var e=this.lang,d=this.mSelPadding,c=this.columnHeight,a=(Ext.isIE?c-15:c-d.top),b={title:e.form.title||"form title not defined",height:this.height,deferHeight:true,border:false,collapsible:true,hideCollapseTool:true,items:[{layout:"column",id:"mSelect",border:false,bodyStyle:"padding-bottom:5px;",defaults:{hideLabel:true,border:false,height:c,allowBlank:false,displayField:1},items:[{bodyStyle:"padding:"+d.top+"px "+d.inner+"px 0 "+d.outer+"px;",columnWidth:0.55,items:[{xtype:"ownerslist",legend:e.owner.legend,loadingText:e.owner.loading,labelAll:e.owner.all,width:"100%",height:a}]},{bodyStyle:"padding:"+d.top+"px "+d.inner+"px 0 0;",columnWidth:0.28,items:[{xtype:"typeslist",legend:e.type.legend,loadingText:e.type.loading,labelAll:e.type.all,width:"100%",height:a}]},{bodyStyle:"padding:"+d.top+"px "+d.outer+"px 0 0;",columnWidth:0.165,items:[{xtype:"statuslist",legend:e.status.legend,loadingText:e.status.loading,labelAll:e.status.all,width:"100%",height:a}]}]},{xtype:"descriptorstree",id:"dsrcTree",title:e.dscrtree.legend,lang:e.dscrtree,treeHeight:this.treeHeight,width:this.width,treeNodes:this.treeNodes}]};Ext.apply(this,Ext.apply(this.initialConfig,b));DocDb.SearchForm.superclass.initComponent.apply(this,arguments)}});Ext.reg("searchform",DocDb.SearchForm);
DocDb.mainPanel=Ext.extend(Ext.Panel,{id:"mainPanel",layout:"vbox",layoutConfig:{align:"left",pack:"start"},border:true,initComponent:function(){var b=this.statvar,c=this.lang,a={renderTo:b.RENDER_TO,width:b.mainPWidth,height:b.mSelHeight+b.treeHeight.min,formHeight:b.formHeight,gridHeight:b.gridHeight,gSortInfo:b.gridParams,items:[{xtype:"gridresults",id:"gridResults",lang:c,docDetail:b.docDetail,dF:b.gridParams.dF,colsW:b.gridParams.colsW,pageSize:parseInt(b.PAGESIZE,10),region:"north",width:b.mainPWidth,height:0,standaloneGrid:false,hidden:true},{xtype:"searchform",id:"advSearch",lang:c,width:b.mainPWidth,height:b.formHeight,columnHeight:b.mSelHeight,mSelPadding:{inner:8,outer:5,top:5},treeHeight:b.treeHeight,treeNodes:b.nodes,collapsed:true}]};Ext.apply(this,Ext.apply(this.initialConfig,a));DocDb.mainPanel.superclass.initComponent.apply(this,arguments);this.btnSubmit=Ext.getCmp("btnFormSubmit");this.btnSubmit.on("click",this.getFormAllVal);this.btnBackToForm=Ext.getCmp("btnMakeNewSearch");this.btnBackToForm.on("click",function(){this.toggleGrid(false)},this)},getFormAllVal:function(f,i){var d=Ext.getCmp("advSearch"),l=Ext.getCmp("dsrcTree"),a=Ext.getCmp("gridResults"),j=Ext.getCmp("mainPanel"),h,c,b,g,k;if(d.getForm().isValid()){h=a.store;c=j.statvar.gridParams;b=l.getChecked();g="";k={};Ext.copyTo(c,d.getForm().getValues(),"owner,type,selType,status");c.selNodes="";Ext.each(b,function(e){if(g.length>0){g+=","}g+=e.id});if(g.length){c.selNodes=g}Ext.iterate(c,function(m,e){if(m!=="colsW"&&m!=="dF"){h.setBaseParam(m,e)}},this);h.setDefaultSort(j.gSortInfo.field,j.gSortInfo.direction);k.callback=function(){j.toggleGrid(true);j.body.unmask();j.el.fadeIn({endOpacity:1,easing:"easeOut",duration:0.6,block:true})};h.load(k);if(l.body.isMasked()){l.chkAll.setValue(false)}j.body.mask(j.lang.form.searchRun,"x-mask-loading");delete b,g,c,k}},toggleGrid:function(f){var a=Ext.getCmp("gridResults"),c=Ext.getCmp("g-p-bbar"),g=Ext.getCmp("mainPanel"),d=Ext.getCmp("advSearch"),i=Ext.getCmp("dsrcTree"),b=d.getPosition(),h=Math.ceil(b[0]),e=Math.ceil(b[1]);if(f){a.setHeight(g.gridHeight);g.setHeight(g.gridHeight);a.show();c.show();g.doLayout();d.collapse()}else{d.el.setStyle({opacity:"0"});a.getSelectionModel().clearSelections(true);this.gSortInfo=a.store.sortInfo;c.hide();a.hide();d.setPagePosition(h,(e-g.gridHeight));a.setHeight(0);d.expand();d.on("expand",function(j){i.resizeTreePanel();(function(){j.el.fadeIn({endOpacity:1,easing:"easeOut",duration:1,stopFx:1})}.defer(10))})}},setBaseParams:function(){var a=Ext.getCmp("gridResults").store,b=Ext.getCmp("mainPanel"),d=Ext.get("loading-mask"),e={},c=this.statvar.gridParams;Ext.iterate(c,function(g,f){a.setBaseParam(g,f)},this);a.setDefaultSort(c.field,c.direction);delete c;e.callback=function(){b.toggleGrid(true);Ext.fly("loading").remove();d.fadeOut({duration:1,remove:true})};a.load(e)}});Ext.reg("mainpanel",DocDb.mainPanel);DocDb.initMain=function(){Ext.QuickTips.init({showDelay:100,dismissDelay:0,shadow:true});Ext.app.REMOTING_API.namespace=DocDb;Ext.app.REMOTING_API.enableBuffer=60;Ext.app.REMOTING_API.id="docdb-direct";Ext.Direct.addProvider(Ext.app.REMOTING_API);var a=new DocDb.mainPanel();(function(){Ext.get("loading-mask").setHeight(a.getHeight())}.defer(1));(function(){a.setBaseParams()}.defer(10))};
