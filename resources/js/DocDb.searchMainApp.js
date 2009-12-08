/*
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.searchMainApp.js 158 2009-12-07 02:35:24Z lcherpit $
 */
/**
 * Global app
 * @namespace DocDb
 */
Ext.namespace( 'DocDb' );


DocDb.mainPanel = Ext.extend( Ext.Panel, {

    id     : 'mainPanel',
    layout : 'vbox',
    layoutConfig: {
        align : 'left',
        pack  : 'start'
    },
    border : true,

    initComponent : function( ) {
    
        var config = {
            renderTo   : this.statvar.RENDER_TO,
            width      : this.statvar.mainPWidth,
            height     : this.statvar.mSelHeight + this.statvar.treeHeight.min,
            formHeight : this.statvar.formHeight,
            gridHeight : this.statvar.gridHeight,
            // store height of result panel here to animate later
            resPHeight : this.statvar.resPHeight,
            items:[{
                xtype          : 'docgridresultdetail',
                id             : 'resultsPanel',
                standaloneGrid : false,
                pageSize       : this.statvar.PAGESIZE,
                lang           : this.lang.grid,
                width          : this.statvar.mainPWidth,
                height         : 0,
                docDetail      : this.statvar.docDetail,
                docDetailLL    : this.lang.docDetail

            },{

                xtype         : 'searchform',
                id            : 'advSearch',
                lang          : this.lang,
                width         : this.statvar.mainPWidth,
                height        : this.statvar.formHeight,
                columnHeight  : this.statvar.mSelHeight,
                mSelPadding   : {inner:8,outer:5,top:5},
                treeHeight    : this.statvar.treeHeight,
                treeNodes     : this.statvar.nodes

            }] // end items of mainPanel
        }
  
    // apply config
    Ext.apply( this, Ext.apply( this.initialConfig, config ) );

    // call parent
    DocDb.mainPanel.superclass.initComponent.apply( this, arguments );

    this.btnSubmit = Ext.getCmp( 'btnFormSubmit' );
    this.btnSubmit.on( 'click', this.getFormAllVal );

    this.btnBackToForm = Ext.getCmp( 'btnMakeNewSearch' );
    this.btnBackToForm.on( 'click', function( ) {

        this.toggleGrid( false );
        var docGridSm = Ext.getCmp( 'gridResults' ).getSelectionModel( );
        docGridSm.clearSelections( true );
        Ext.getCmp( 'detailPanel' ).restoreInitText();
    },
    this
    );

    }, // end initComponent
    
  /*
   * get all selected treeNodes and formFields values
   * and prepare request to load grid result
   * and on grid beforeload exec togglegrid
   */
    getFormAllVal : function( btn, e ) {

        var form       = Ext.getCmp( 'advSearch' );
        var tree       = Ext.getCmp( 'dsrcTree' );
        var gridResult = Ext.getCmp( 'gridResults' );
        var mainPanel  = Ext.getCmp( 'mainPanel' );

        if( form.getForm( ).isValid( ) ) {

            // get gridParams used to set grouping or not
            var params = mainPanel.statvar.gridParams;

            var selNodes = tree.getChecked( ),
                selDscrIds = '',
                loadObj = {};

            Ext.copyTo( params, form.getForm( ).getValues( ), 'owner,type,selType,status' );

            params.selNodes = '';
            // get nodes Id-s
            Ext.each( selNodes, function( node ) {
                if( selDscrIds.length > 0 ) {
                    selDscrIds += ',';
                }
                selDscrIds += node.id;
            });

            if( selDscrIds.length ) {
                // add selected descriptors tree nodes
                params.selNodes = selDscrIds;
            }

            Ext.iterate( params, function( k, v ) {
                // set store baseParams, like that those are keep on sort change or when the store is reloaded
                gridResult.store.setBaseParam( k, v );
            },
            this
            );
            
            loadObj.callback = function( ) {

                mainPanel.toggleGrid( true );
                mainPanel.body.unmask( );
                mainPanel.body.fadeIn( {
                    endOpacity: 0,
                    easing: 'easeOut',
                    duration: .7
                } );
            };

            // load result in gridPanel
            gridResult.store.load( loadObj );

            if( tree.body.isMasked( ) ) {
                tree.chkAll.setValue( false );
            }

            mainPanel.body.mask( mainPanel.lang.form.searchRun, 'x-mask-loading' );

            delete selNodes, selDscrIds, params, loadObj;
        } // eo form isValid
    } // eo function getFormAllVal

    ,toggleGrid : function( openGrid ) {

        var resultsP  = Ext.getCmp( 'resultsPanel' );
        var grid      = Ext.getCmp( 'gridResults' );
        var gridBbar  = Ext.getCmp( 'g-p-bbar' );
        var panel     = Ext.getCmp( 'mainPanel' );

        var advSearP  = Ext.getCmp( 'advSearch' );
        var searchPos = advSearP.getPosition( );
        var searchX   = Math.ceil( searchPos[0] );
        var searchY   = Math.ceil( searchPos[1] );

        if( openGrid ) {

            resultsP.setHeight( panel.resPHeight );
            grid.setHeight( panel.gridHeight );
            panel.setHeight( panel.resPHeight );

            grid.show( );
            gridBbar.show( );
            panel.doLayout( );

            resultsP.doLayout();
            advSearP.collapse( );

        } else {

            gridBbar.hide( );
            grid.hide( );

            resultsP.setHeight( 0 );

            advSearP.setPagePosition( searchX,( searchY-panel.resPHeight ) )
            grid.setHeight( 0 );

            advSearP.body.setStyle( 'opacity', 1 );
            advSearP.expand( );
            advSearP.body.fadeIn({
                endOpacity: 0,
                easing: 'easeOut',
                duration: .7
            } );
            (function( ){ Ext.getCmp( 'dsrcTree' ).resizeTreePanel( ) }.defer( 10 ) );
        }
    } // eo toggle grid
}); // end extend DocDb.mainPanel

// register xtype
Ext.reg( 'mainpanel', DocDb.mainPanel );




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

    // create and show main app Panel
    var searchMainApp = new DocDb.mainPanel( );

    searchMainApp.show( );

    // mask when init framework
    var lMask = Ext.get( 'loading-mask' );
    (function( ) { lMask.setHeight( searchMainApp.getHeight() ) }.defer( 10 ) );
    
     setTimeout( function( ) {
        Ext.fly( 'loading' ).remove( );
        lMask.fadeOut( {duration: 1, remove:true} );
        }, 250
    );
 
}); // eo function onReady

// eof