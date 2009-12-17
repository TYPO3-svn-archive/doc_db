/*
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.SearchForm.js 112 2009-11-29 17:12:44Z lcherpit $
 */
Ext.ns('DocDb');


/*
 * Types multiselectList
 * Preconfigured class 
 */
DocDb.SearchForm = Ext.extend(Ext.form.FormPanel, {
  
  border:false
  
    ,initComponent:function() {
    
    var config = {
        title             : this.lang.form.title || 'form title not defined'
        ,height           : this.height
        ,deferHeight      : true
        ,border           : false
        ,collapsible      : true
        ,hideCollapseTool : true
        
      ,items : [{
        layout       : 'column'
        ,id           : 'mSelect'
        ,border      : false
        ,bodyStyle : 'padding-bottom:5px;'
        ,defaults    : {
          hideLabel    : true
          ,border       : false
          ,height       : this.columnHeight
          ,allowBlank   : false
          ,displayField : 1
        },
        items : [{
          bodyStyle : 'padding:'+this.mSelPadding.top+'px '+this.mSelPadding.inner+'px 0 '+this.mSelPadding.outer+'px;'
          ,columnWidth : 0.55
          ,items : [{
            xtype   : 'ownerslist'
            ,legend      : this.lang.owner.legend
            ,loadingText : this.lang.owner.loading
            ,labelAll    : this.lang.owner.all
            ,width: '100%'
            ,height : this.columnHeight-this.mSelPadding.top
          }]
        },{
          bodyStyle     : 'padding:'+this.mSelPadding.top+'px '+this.mSelPadding.inner+'px 0 0;'
          ,columnWidth : 0.28
          ,items : [{
            xtype   : 'typeslist'
            ,legend      : this.lang.type.legend
            ,loadingText : this.lang.type.loading
            ,labelAll    : this.lang.type.all
            ,width: '100%'
            ,height : this.columnHeight-this.mSelPadding.top
          }]
        },{
          bodyStyle : 'padding:'+this.mSelPadding.top+'px '+this.mSelPadding.outer+'px 0 0;'
          ,columnWidth : 0.165
          ,items : [{
            xtype        : 'statuslist'
            ,legend      : this.lang.status.legend
            ,loadingText : this.lang.status.loading
            ,labelAll    : this.lang.status.all
            ,width       : '100%'
            ,height      : this.columnHeight-this.mSelPadding.top
          }]
        }]
      },{
        xtype          : 'descriptorstree'
        ,id            : 'dsrcTree'
        ,title         : this.lang.dscrtree.legend
        ,lang          : this.lang.dscrtree
        ,treeHeight    : this.treeHeight
        ,width         : this.width
        ,treeNodes     : this.treeNodes
      }] // end items of advSearch form
    }; // eo config object
    

    // apply config
    Ext.apply( this, Ext.apply( this.initialConfig, config ) );

    DocDb.SearchForm.superclass.initComponent.apply( this, arguments );
  } // eo function initComponent
 
//  ,afterRender:function() {
//    this.store.load();
// 
//    DocDb.SearchForm.superclass.afterRender.apply(this, arguments);
//  } // eo function afterRender
  
}); // eo extend
 
Ext.reg( 'searchform', DocDb.SearchForm );
