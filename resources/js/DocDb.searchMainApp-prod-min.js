/** @author  : Laurent Cherpit */
Ext.ns("DocDb");DocDb.SearchForm=Ext.extend(Ext.form.FormPanel,{border:false,initComponent:function(){var a={title:this.lang.form.title||"form title not defined",height:this.height,deferHeight:true,border:false,collapsible:true,hideCollapseTool:true,items:[{layout:"column",id:"mSelect",border:false,bodyStyle:"padding-bottom:5px;",defaults:{hideLabel:true,border:false,height:this.columnHeight,allowBlank:false,displayField:1},items:[{bodyStyle:"padding:"+this.mSelPadding.top+"px "+this.mSelPadding.inner+"px 0 "+this.mSelPadding.outer+"px;",columnWidth:0.55,items:[{xtype:"ownerslist",legend:this.lang.owner.legend,loadingText:this.lang.owner.loading,labelAll:this.lang.owner.all,width:"100%",height:this.columnHeight-this.mSelPadding.top}]},{bodyStyle:"padding:"+this.mSelPadding.top+"px "+this.mSelPadding.inner+"px 0 0;",columnWidth:0.28,items:[{xtype:"typeslist",legend:this.lang.type.legend,loadingText:this.lang.type.loading,labelAll:this.lang.type.all,width:"100%",height:this.columnHeight-this.mSelPadding.top}]},{bodyStyle:"padding:"+this.mSelPadding.top+"px "+this.mSelPadding.outer+"px 0 0;",columnWidth:0.165,items:[{xtype:"statuslist",legend:this.lang.status.legend,loadingText:this.lang.status.loading,labelAll:this.lang.status.all,width:"100%",height:this.columnHeight-this.mSelPadding.top}]}]},{xtype:"descriptorstree",id:"dsrcTree",title:this.lang.dscrtree.legend,lang:this.lang.dscrtree,treeHeight:this.treeHeight,width:this.width,treeNodes:this.treeNodes}]};Ext.apply(this,Ext.apply(this.initialConfig,a));DocDb.SearchForm.superclass.initComponent.apply(this,arguments)}});Ext.reg("searchform",DocDb.SearchForm);/** @author  : Laurent Cherpit */
Ext.namespace("DocDb");DocDb.mainPanel=Ext.extend(Ext.Panel,{id:"mainPanel",layout:"vbox",layoutConfig:{align:"left",pack:"start"},border:true,initComponent:function(){var a={renderTo:this.statvar.RENDER_TO,width:this.statvar.mainPWidth,height:this.statvar.mSelHeight+this.statvar.treeHeight.min,formHeight:this.statvar.formHeight,gridHeight:this.statvar.gridHeight,resPHeight:this.statvar.resPHeight,items:[{xtype:"docgridresultdetail",id:"resultsPanel",standaloneGrid:false,pageSize:this.statvar.PAGESIZE,lang:this.lang.grid,width:this.statvar.mainPWidth,height:0,docDetail:this.statvar.docDetail,docDetailLL:this.lang.docDetail},{xtype:"searchform",id:"advSearch",lang:this.lang,width:this.statvar.mainPWidth,height:this.statvar.formHeight,columnHeight:this.statvar.mSelHeight,mSelPadding:{inner:8,outer:5,top:5},treeHeight:this.statvar.treeHeight,treeNodes:this.statvar.nodes}]};Ext.apply(this,Ext.apply(this.initialConfig,a));DocDb.mainPanel.superclass.initComponent.apply(this,arguments);this.btnSubmit=Ext.getCmp("btnFormSubmit");this.btnSubmit.on("click",this.getFormAllVal);this.btnBackToForm=Ext.getCmp("btnMakeNewSearch");this.btnBackToForm.on("click",function(){this.toggleGrid(false);var b=Ext.getCmp("gridResults").getSelectionModel();b.clearSelections(true);Ext.getCmp("detailPanel").restoreInitText()},this)},getFormAllVal:function(d,i){var a=Ext.getCmp("advSearch");var k=Ext.getCmp("dsrcTree");var f=Ext.getCmp("gridResults");var c=Ext.getCmp("mainPanel");if(a.getForm().isValid()){var g=c.statvar.gridParams;var b=k.getChecked(),h="",j={};Ext.copyTo(g,a.getForm().getValues(),"owner,type,selType,status");g.selNodes="";Ext.each(b,function(e){if(h.length>0){h+=","}h+=e.id});if(h.length){g.selNodes=h}Ext.iterate(g,function(l,e){f.store.setBaseParam(l,e)},this);j.callback=function(){c.toggleGrid(true);c.body.unmask();c.body.fadeIn({endOpacity:0,easing:"easeOut",duration:0.7})};f.store.load(j);if(k.body.isMasked()){k.chkAll.setValue(false)}c.body.mask(c.lang.form.searchRun,"x-mask-loading");delete b,h,g,j}},toggleGrid:function(g){var i=Ext.getCmp("resultsPanel");var a=Ext.getCmp("gridResults");var d=Ext.getCmp("g-p-bbar");var b=Ext.getCmp("mainPanel");var e=Ext.getCmp("advSearch");var c=e.getPosition();var h=Math.ceil(c[0]);var f=Math.ceil(c[1]);if(g){i.setHeight(b.resPHeight);a.setHeight(b.gridHeight);b.setHeight(b.resPHeight);a.show();d.show();b.doLayout();i.doLayout();e.collapse()}else{d.hide();a.hide();i.setHeight(0);e.setPagePosition(h,(f-b.resPHeight));a.setHeight(0);e.body.setStyle("opacity",1);e.expand();e.body.fadeIn({endOpacity:0,easing:"easeOut",duration:0.7});(function(){Ext.getCmp("dsrcTree").resizeTreePanel()}.defer(10))}}});Ext.reg("mainpanel",DocDb.mainPanel);Ext.onReady(function(){Ext.QuickTips.init({showDelay:100,dismissDelay:0,shadow:true});Ext.app.REMOTING_API.namespace=DocDb;Ext.app.REMOTING_API.enableBuffer=60;Ext.app.REMOTING_API.id="docdb-direct";Ext.Direct.addProvider(Ext.app.REMOTING_API);var a=new DocDb.mainPanel();a.show();var b=Ext.get("loading-mask");(function(){b.setHeight(a.getHeight())}.defer(10));setTimeout(function(){Ext.fly("loading").remove();b.fadeOut({duration:1,remove:true})},250)});