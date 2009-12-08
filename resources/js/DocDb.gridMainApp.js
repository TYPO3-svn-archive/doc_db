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
    
        var config = {
            renderTo   : this.statvar.RENDER_TO,
            
            width      : parseInt( this.statvar.mainPWidth, 10 ),
            height     : parseInt( this.statvar.resPHeight, 10 ),
//            gridHeight : this.statvar.gridHeight,
//            // stor height of result panel here to animate later
//            resPHeight : this.statvar.resPHeight,
            items:[{
                xtype          : 'docgridresultdetail',
                id             : 'resultsPanel',
                standaloneGrid : true,
                pageSize       : this.statvar.PAGESIZE,
                lang           : this.lang.grid,
                width          : this.statvar.mainPWidth,
                height         : this.statvar.resPHeight,
                gridHeight     : this.statvar.gridHeight,
                docDetail      : this.statvar.docDetail,
                docDetailLL    : this.lang.docDetail

            }] // end items of mainPanel
        }
  
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

        var gridResult = Ext.getCmp( 'gridResults' );

        var params = this.statvar.gridParams;

        Ext.iterate(params, function( k, v ) {
            // set store baseParams, like that those are keep on sort change or when the store is reloaded
            gridResult.store.setBaseParam( k, v );
        },
        this
        );

        // load result in gridPanel
        gridResult.store.load();
    } // eo function setBaseParams


}); // end extend DocDb.mainGrid

// register xtype
Ext.reg( 'maingrid', DocDb.mainGrid );


// application main entry point
Ext.onReady( function( ) {
 
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

    // mask when init framework
     setTimeout( function( ) {
        Ext.fly( 'loading' ).remove( );
        Ext.get( 'loading-mask' ).fadeOut( {duration: 1, remove:true} );
        }, 250
    );

    // create and show main app Panel
    var gridMainApp = new DocDb.mainGrid( );

    gridMainApp.show( );
    
    (function( ) { gridMainApp.setBaseParams(); }.defer( 10 ));
//    gridMainApp.doLayout();

    

}); // eo function onReady

// eof