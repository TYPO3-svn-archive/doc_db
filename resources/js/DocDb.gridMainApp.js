/*
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.app.js 116 2009-12-01 02:10:14Z lcherpit $
 */
/**
 * Global app
 * @namespace DocDb
 */
Ext.namespace( 'DocDb' );


DocDb.mainGrid = Ext.extend( Ext.Panel, {

    id     : 'mainGrid',
    layout : 'vbox',
    layoutConfig: {
        align : 'left',
        pack  : 'start'
    },
    border : true,
    
    initComponent : function( ) {

        // get var from prototype
        var sVar = this.statvar,
        ll = this.lang,

        config = {
            renderTo   : sVar.RENDER_TO,
            
            width      : parseInt( sVar.mainPWidth, 10 ),
            height     : parseInt( sVar.gridHeight, 10 ),
            items:[{
                xtype          : 'gridresults',
                id             : 'gridResults',
                lang           : ll,
                docDetail      : sVar.docDetail,
                dF             : sVar.gridParams.dF,
                colsW          : sVar.gridParams.colsW,
                pageSize       : parseInt( sVar.PAGESIZE, 10 ),
                region         : 'north',
                width          : sVar.mainPWidth,
                height         : sVar.gridHeight,
                standaloneGrid : true
            }] // end items of mainPanel
        };
  
    // apply config
    Ext.apply( this, Ext.apply( this.initialConfig, config ) );

    // call parent
    DocDb.mainGrid.superclass.initComponent.apply( this, arguments );

    }, // end initComponent
    
  /*
   * get all selected treeNodes and formFields values
   * and prepare request to load grid result
   * and on grid beforeload exec togglegrid
   */
    setBaseParams : function( ) {

        // grid result store
        var gS = Ext.getCmp( 'gridResults' ).store,

        // params
        p = this.statvar.gridParams;

        Ext.iterate( p, function( k, v ) {
                // set store baseParams, like that those are keep on sort change or when the store is reloaded
                gS.setBaseParam( k, v );
            },
            this
        );

        gS.setDefaultSort( p.field, p.direction );
        delete p;
        
        // load result in gridPanel
        gS.load( );
        
    } // eo function setBaseParams
}); // end extend DocDb.mainGrid

// register xtype
Ext.reg( 'maingrid', DocDb.mainGrid );


// application main entry point
DocDb.initMain = function( ) {
 
    Ext.QuickTips.init({
        showDelay    : 100,
        dismissDelay : 0,
        shadow       : true
    });
    //Ext.form.Field.prototype.msgTarget = 'side';

    // Notice that Direct requests will batch together if they occur
    // within the enableBuffer delay period (in milliseconds).
    // Slow the buffering down from the default of 10ms to 100ms
    Ext.app.REMOTING_API.namespace = DocDb;
    Ext.app.REMOTING_API.enableBuffer = 60;
    Ext.app.REMOTING_API.id = 'docdb-direct';
    Ext.Direct.addProvider( Ext.app.REMOTING_API );

    // create and show main app Panel
    var gridMainApp = new DocDb.mainGrid( );

    gridMainApp.show( );
    
    ( function( ) { gridMainApp.setBaseParams(); }.defer( 10 ) );
    gridMainApp.doLayout( );

    // mask when init framework
     setTimeout( function( ) {
        Ext.fly( 'loading' ).remove( );
        Ext.get( 'loading-mask' ).fadeOut( {duration: 1, remove:true} );
        }, 250
    );
} // eo function initMain

// eof