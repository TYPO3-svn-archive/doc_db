/*
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.SearchForm.js 199 2010-01-18 17:23:39Z lcherpit $
 */
Ext.ns('DocDb');


/*
 * Types multiselectList
 * Preconfigured class 
 */
DocDb.SearchForm = Ext.extend(Ext.form.FormPanel, {
  
  border:false,
  
    initComponent:function() {

        var ll   = this.lang,
        mPad     = this.mSelPadding,
        colH     = this.columnHeight,
        colItemH = (Ext.isIE ? colH-15 : colH-mPad.top),

        config = {
            title            : ll.form.title || 'form title not defined',
            height           : this.height,
            deferHeight      : true,
            border           : false,
            collapsible      : true,
            hideCollapseTool : true,


            items : [{
                layout      : 'column',
                id          : 'mSelect',
                border      : false,
                bodyStyle : 'padding-bottom:5px;',
                defaults    : {
                  hideLabel    : true,
                  border       : false,
                  height       : colH,
                  allowBlank   : false,
                  displayField : 1
                },
                items : [{
                  bodyStyle : 'padding:'+mPad.top+'px '+mPad.inner+'px 0 '+mPad.outer+'px;',
                  columnWidth : 0.55,
                  items : [{
                    xtype   : 'ownerslist',
                    legend      : ll.owner.legend,
                    loadingText : ll.owner.loading,
                    labelAll    : ll.owner.all,
                    width: '100%',
                    height : colItemH
                  }]
                },{
                  bodyStyle     : 'padding:'+mPad.top+'px '+mPad.inner+'px 0 0;',
                  columnWidth : 0.28,
                  items : [{
                    xtype   : 'typeslist',
                    legend      : ll.type.legend,
                    loadingText : ll.type.loading,
                    labelAll    : ll.type.all,
                    width: '100%',
                    height : colItemH
                  }]
                },{
                  bodyStyle : 'padding:'+mPad.top+'px '+mPad.outer+'px 0 0;',
                  columnWidth : 0.165,
                  items : [{
                    xtype        : 'statuslist',
                    legend      : ll.status.legend,
                    loadingText : ll.status.loading,
                    labelAll    : ll.status.all,
                    width       : '100%',
                    height      : colItemH
                  }]
                }]
                },{
                xtype          : 'descriptorstree',
                id            : 'dsrcTree',
                title         : ll.dscrtree.legend,
                lang          : ll.dscrtree,
                treeHeight    : this.treeHeight,
                width         : this.width,
                treeNodes     : this.treeNodes
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
