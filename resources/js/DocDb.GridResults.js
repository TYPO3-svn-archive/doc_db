/*
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.GridResults.js 145 2009-12-06 16:57:40Z lcherpit $
 */

Ext.ns('DocDb');

/*
 * Document grid
 * configured class 
 */
DocDb.GridResults = Ext.extend( Ext.grid.GridPanel, {
  
  border : false,
  
    initComponent:function( ) {
    
        // row expander
        var rowExpander = new Ext.ux.grid.RowExpander({
            tpl : new Ext.XTemplate(
                '<div class="x-grid3-expander-preview">',
                    '<tpl if="this.cAr(dscr)">',
                        '<div><b>Related descriptor{[values["dscr"].length > 0?"s":""]}:</b>',
                        '<tpl for="dscr">',
                            '<p>- {dtitle}</p>',
                        '</tpl></div><hr class="hr4tmpl" />',
                    '</tpl>',
                    '<tpl if="this.cAr(pages)">',
                        '<div><b>Related page{[values["dscr"].length > 0?"s":""]}:</b>',
                        '<tpl for="pages">',
                            '<p>- <a href="{pUrl}" title="{pTitle}">{pTitle}</a></p>',
                        '</tpl></div><hr class="hr4tmpl" />',
                    '</tpl>',
                '</div>',
                {
                    cAr : function( array ) {

                        return array.length > 0 ? true : false;
                    }
                }
            )
        });

        var filters = new Ext.ux.grid.GridFilters( {
            encode: true, // json encode the filter query
            local: false,   // remote filtering
            filters : [{
                type      : 'string',
                dataIndex : 'title'
            },{
                type      : 'string',
                dataIndex : 'owner'
            },{
                type      : 'date',
                dataIndex : 'date'
            },{
                type      : 'string',
                dataIndex : 'type'
            }, {
                type      : 'string',
                dataIndex : 'status'
            }]
        } );
    

    var config = {
        title            : this.lang.resPanel.title,
        height           : this.standaloneGrid ? this.height : 0,
        autoScroll       : true,
        forceFit         : true,
        autoExpandColumn : 'title',
        autoSizeColumn   : true,
        autoExpandMin    : 160,
        autoExpandMax    : 400,
        plugins          : this.standaloneGrid ? [rowExpander,filters] : [rowExpander,filters,new Ext.ux.plugins.HeaderButtons( )],
        hbuttons         : this.standaloneGrid ? '' : [{
            text          : this.lang.resPanel.searchAgain,
            id            : 'btnMakeNewSearch',
            iconCls       : 'x-btn-search'
        }],
        loadMask         : {
            msg : this.lang.loading
        },
        store : new Ext.data.GroupingStore({
            baseParams : {
                start : 0,
                limit : 10
            },
            autoLoad    : false,
            remoteGroup : true,
            groupOnSort : false,
            remoteSort  : true,
            groupField  : 'owner',
            sortInfo    : {
                field     : 'title',
                direction : 'ASC'
            },
            proxy       : new Ext.data.DirectProxy({
                paramsAsHash : true,
                directFn     : DocDb.model_document.get
            }),
            reader : new Ext.data.JsonReader({
                root          : 'rows',
                idProperty    : 'uid',
                totalProperty : 'totalCount',
                fields : [
                    {name:'uid', type:'int'},
                    {name:'title', type:'string'},
                    {name:'docPageURL', type:'string'},
                    {name:'date', type: 'date', dateFormat: 'timestamp'},
                    {name:'owner', type:'string'},
                    {name:'dkey', type:'string'},
                    {name:'type', type:'string'},
                    {name:'status',type:'string'},
                    {name:'dscr'},
                    {name:'pages'}
                    //           ,{name:'prevH',type:'string'}
                    //           ,{name:'prevC',type:'string'}
                ]
            }) // eo reader
        }), // eo groupingStore
        view : new Ext.grid.GroupingView({
            forceFit          : false,
            hideGroupedColumn : true,
            showGroupName     : false,
            groupTextTpl      : '{text}'
        }),
        cm         : new Ext.grid.ColumnModel([
            //new Ext.grid.RowNumberer()
            rowExpander,
            {
                id        : 'title',
                header    : this.lang.header.title,
                width     : 250,
                sortable  : true,
                dataIndex : 'title',
                renderer  : function( value, id, record ) { return '<div style="white-space:normal !important;">' + value + '</div>';}
                //,css       : 'white-space:normal !important;'
            },{
                header    : this.lang.header.date,
                width     : 60,
                fixed     : true,
                resizable : false,
                sortable  : true,
                renderer  : Ext.util.Format.dateRenderer( 'd.m.y' ),
                dataIndex : 'date'
            },{
                header    : this.lang.header.owner,
                width     : 130,
                sortable  : true,
                dataIndex : 'owner',
                renderer  : function( value, id, record ) { return '<div style="white-space:normal !important;">' + value + '</div>';}
            },{
                header    : this.lang.header.key,
                width     : 35,
                fixed     : true,
                sortable  : true,
                dataIndex : 'dkey'
            },{
                header     : this.lang.header.type,
                width     : 130,
                fixed     : true,
                sortable  : true,
                dataIndex : 'type',
                renderer  : function( value, id, record ) { return '<div style="white-space:normal !important;">' + value + '</div>';}
            },{
                header    : this.lang.header.status,
                width     : 60,
                fixed     : true,
                sortable  : true,
                dataIndex : 'status'
            }
        ])
    }; // eo config object

    // apply config
    Ext.apply( this, Ext.apply( this.initialConfig, config ) );
    
    this.bbar = new Ext.PagingToolbar({
        id          : 'g-p-bbar',
        pageSize    : this.pageSize,
        store       : this.store,
        displayInfo : true,
        hidden      : false,
        autoShow    : true,
        plugins:[
            new Ext.ux.PageSizePlugin({
                editable       : false,
                forceSelection : true
            })
        ]
    });

//    console.info( this.bbar );
    
//    this.plugins = standaloneGrid ? [rowExpander] : [rowExpander, new Ext.ux.plugins.HeaderButtons( )];

    DocDb.GridResults.superclass.initComponent.apply( this, arguments );
    
  } // eo function initComponent
 
  
//  ,onRender : function() {
//    
//    // cut title to column with

//  }
  ,afterRender:function() {
    this.store.on('beforeload',function(){

        if( this.baseParams.groupBy !== 'owner' ) {
            this.groupBy( this.baseParams.groupBy );
        }
        if( ! this.baseParams.grouping ) {
            this.groupBy( '' );
            this.clearGrouping();
        }

    })
 
    DocDb.GridResults.superclass.afterRender.apply(this, arguments);
  } // eo function afterRender
  
});
 
Ext.reg( 'gridresults', DocDb.GridResults );
