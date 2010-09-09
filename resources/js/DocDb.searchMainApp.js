/*
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.searchMainApp.js 208 2010-03-13 04:52:54Z lcherpit $
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
    border   : true,

    initComponent : function( ) {

        // get var from prototype
        var sVar = this.statvar,
        ll = this.lang,
        
        config = {
            renderTo   : sVar.RENDER_TO,
            width      : sVar.mainPWidth,
            height     : sVar.mSelHeight + sVar.treeHeight.min,
            formHeight : sVar.formHeight,
            gridHeight : sVar.gridHeight,
            gSortInfo  : sVar.gridParams,
            items:[{
                xtype          : 'gridresults',
                id             : 'gridResults',
                lang           : ll,
                docDetail      : sVar.docDetail,
                dF             : sVar.gridParams.dF,
                colsW          : sVar.gridParams.colsW,
                pageSize       : parseInt( sVar.PAGESIZE, 10 ),
                gF             : sVar.gridParams.groupBy,
                region         : 'north',
                width          : sVar.mainPWidth,
                height         : 0,
                standaloneGrid : false,
                xmlExport      : sVar.xmlExportEnable,
                hidden         : true
            },{
                xtype         : 'searchform',
                id            : 'advSearch',
                lang          : ll,
                width         : sVar.mainPWidth,
                height        : sVar.formHeight,
                columnHeight  : sVar.mSelHeight,
                mSelPadding   : {inner:8,outer:5,top:5},
                treeHeight    : sVar.treeHeight,
                treeNodes     : sVar.nodes,
                collapsed     : true

            }] // end items of mainPanel
        };
  
    // apply config
    Ext.apply( this, Ext.apply( this.initialConfig, config ) );

    // call parent
    DocDb.mainPanel.superclass.initComponent.apply( this, arguments );

    this.btnSubmit = Ext.getCmp( 'btnFormSubmit' );
    this.btnSubmit.on( 'click', this.getFormAllVal );

    this.btnBackToForm = Ext.getCmp( 'btnMakeNewSearch' );
    this.btnBackToForm.on( 'click', function( ) {
//        var g = Ext.getCmp( 'gridResults' );
        this.toggleGrid( false );
//        g.getSelectionModel( ).clearSelections( true );
//        this.gSortInfo = g.store.sortInfo;
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

        var form = Ext.getCmp( 'advSearch' ),
        tree = Ext.getCmp( 'dsrcTree' ),
        grid = Ext.getCmp( 'gridResults' ),
        mP   = Ext.getCmp( 'mainPanel' ),
        gS,p,selNodes,selDscrIds,loadObj;

        if( form.getForm( ).isValid( ) ) {

            gS = grid.store;
            // get gridParams used to set grouping or not
            p = mP.statvar.gridParams;
            
            selNodes = tree.getChecked( );
            selDscrIds = '';
            loadObj = {};

            Ext.copyTo( p, form.getForm( ).getValues( ), 'owner,type,selType,status' );

            p.selNodes = '';
            // get nodes Id-s
            Ext.each( selNodes, function( node ) {
                if( selDscrIds.length > 0 ) {
                    selDscrIds += ',';
                }
                selDscrIds += node.id;
            });

            if( selDscrIds.length ) {
                // add selected descriptors tree nodes
                p.selNodes = selDscrIds;
            }

            Ext.iterate( p, function( k, v ) {
                if( k !== 'colsW' && k !== 'dF' ) {

//                    console.info(k);
                    // set store baseParams, like that those are keep on sort change or when the store is reloaded
                    gS.setBaseParam( k, v );
                }
            },
            this
            );

            // set default sort Info
            gS.setDefaultSort( mP.gSortInfo.field, mP.gSortInfo.direction );
            
            loadObj.callback = function( ) {

                mP.toggleGrid( true );
                mP.body.unmask( );

                mP.el.fadeIn( {
                    endOpacity: 1,
                    easing: 'easeOut',
                    duration: 0.6,
                    block: true
                } );
            };

            // load result in gridPanel
            gS.load( loadObj );

            if( tree.body.isMasked( ) ) {
                tree.chkAll.setValue( false );
            }

            mP.body.mask( mP.lang.form.searchRun, 'x-mask-loading' );

            delete selNodes, selDscrIds, p, loadObj;
        } // eo form isValid
    }, // eo function getFormAllVal

    toggleGrid : function( openGrid ) {

        var grid  = Ext.getCmp( 'gridResults' ),
        gridBbar  = Ext.getCmp( 'g-p-bbar' ),
        mP        = Ext.getCmp( 'mainPanel' ),
        advSearP  = Ext.getCmp( 'advSearch' ),
        tree      = Ext.getCmp( 'dsrcTree' ),
        searchPos = advSearP.getPosition( ),
        searchX   = Math.ceil( searchPos[0] ),
        searchY   = Math.ceil( searchPos[1] );

        if( openGrid ) {

            grid.setHeight( mP.gridHeight );
            mP.setHeight( mP.gridHeight );

            grid.show( );
            gridBbar.show( );
            mP.doLayout( );
            advSearP.collapse( );

        } else {

            advSearP.el.setStyle( {'opacity':'0'} );
            grid.getSelectionModel( ).clearSelections( true );
            this.gSortInfo = grid.store.sortInfo;

            gridBbar.hide( );
            grid.hide( );

            advSearP.setPagePosition( searchX,( searchY-mP.gridHeight ) )
            grid.setHeight( 0 );
            advSearP.expand( );

            advSearP.on( 'expand', function( p ) {

                tree.resizeTreePanel( );
                ( function( ) {
                    p.el.fadeIn({
                        endOpacity: 1,
                        easing: 'easeOut',
                        duration: 1,
                        stopFx: 1
                    } );
                }.defer( 10 ) );
            } );
            
        }
    }, // eo toggle grid

    /*
     * get all selected treeNodes and formFields values
     * and prepare request to load grid result
     * and on grid beforeload exec togglegrid
     */
    setBaseParams : function( ) {

        // grid result store
        var gS  = Ext.getCmp( 'gridResults' ).store,
        mP      = Ext.getCmp( 'mainPanel' ),
        lMask   = Ext.get( 'loading-mask' ),
        loadObj = {},
        // params
        p = this.statvar.gridParams;

        Ext.iterate( p, function( k, v ) {
                if( k !== 'colsW' && k !== 'dF' ) {
                    // set store baseParams, like that those are keep on sort change or when the store is reloaded
                    gS.setBaseParam( k, v );
                }
            },
            this
        );

        gS.setDefaultSort( p.field, p.direction );
        delete p;

        loadObj.callback = function( ) {

                mP.toggleGrid( true );
                Ext.fly( 'loading' ).remove( );
                lMask.fadeOut( {duration: 1, remove:true} );
            };
        // load result in gridPanel
        gS.load( loadObj );

    } // eo function setBaseParams

}); // end extend DocDb.mainPanel

// register xtype
Ext.reg( 'mainpanel', DocDb.mainPanel );


// application main entry point
DocDb.initMain = function( ) {

    Ext.QuickTips.init({
        showDelay    : 100,
        dismissDelay : 0,
        shadow       : true
    });

    // Notice that Direct requests will batch together if they occur
    // within the enableBuffer delay period (in milliseconds).
    // Slow the buffering down from the default of 10ms to 100ms
    Ext.app.REMOTING_API.namespace = DocDb;
    Ext.app.REMOTING_API.enableBuffer = 100;
    Ext.app.REMOTING_API.id = 'docdb-direct';
    Ext.Direct.addProvider( Ext.app.REMOTING_API );

    
    // create and show main app Panel
    var sApp = new DocDb.mainPanel( );

    ( function( ) { Ext.get( 'loading-mask' ).setHeight( sApp.getHeight( ) ); }.defer( 60 ) );

    ( function( ) { sApp.setBaseParams(); }.defer( 75 ) );
}

// eof