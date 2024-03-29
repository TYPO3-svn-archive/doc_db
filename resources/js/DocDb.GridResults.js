/*
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.GridResults.js 199 2010-01-18 17:23:39Z lcherpit $
 */

Ext.ns('DocDb');

/*
 * Document grid
 * configured class
 */
DocDb.GridResults = Ext.extend( Ext.grid.GridPanel, {

	border : false,

    initComponent: function( ) {

            // language labels for grid
            var gll  = this.lang.grid,
            // language labels for detail preview
            dll = this.lang.docDetail,
            // date format
            dF = this.dF,
            /**
             * @todo add config option to disable XML link export
             */
            xmlExport = this.xmlExport,
            
            // row expander
            rowExpander = new Ext.ux.grid.RowExpander({
                tpl : new Ext.XTemplate(
                    '<div class="x-grid3-expander-preview">',
                        '<hr class="hr4tmpl" />',
                        '<div style="float:left;width:91%;">',
                            '<tpl if="owner.length &gt;1">',
                                    '<b>' + dll.owner + '</b> {owner}<br/>',
                            '</tpl>',
                            '<tpl if="type.length &gt;1">',
                                    '<b>' + dll.type + '</b> {type}<br/>',
                            '</tpl>',
                            '<tpl if="status.length &gt;1">',
                                    '<b>' + dll.status + '</b> {status}<br/>',
                            '</tpl>',
                            '<tpl if="dkey.length &gt;1">',
                                    '<b>' + dll.key + '</b> {dkey}<br/>',
                            '</tpl>',
                            '<tpl if="date !==0">',
                                    '<b>' + dll.date + '</b> {[this.fDate(values["date"])]}<br/>',
                            '</tpl>',
                        '</div>',
                        '<div style="float:right;width:8%;text-align:right;">',
                            '<a href="{docPageURL}" title="' + dll.link2page + '"><img class="x-docdetail-lint2page" src="' + Ext.BLANK_IMAGE_URL + '" /></a>',
                        '</div>',
                        '<div style="clear:both;"><!-- --></div>',
                        '<hr class="hr4tmpl" />',
                        '<tpl if="this.cAr(dscr)">',
                            '<div><b>{[values["dscr"].length > 1?"' + dll.relatedDscr_pl + '":"' + dll.relatedDscr + '"]}:</b>',
                            '<tpl for="dscr">',
                                    '<p>- {title}</p>',
                            '</tpl></div><hr class="hr4tmpl" />',
                        '</tpl>',
                        '<tpl if="this.cAr(pages)">',
                            '<div><b>{[values["pages"].length > 1?"' + dll.relatedPage_pl + '":"' + dll.relatedPage + '"]}:</b>',
                            '<tpl for="pages">',
                                    '<p>- <a href="{url}" title="{urltitle}">{title}</a></p>',
                            '</tpl></div><hr class="hr4tmpl" />',
                        '</tpl>',
                        '<tpl if="dAbs.length &gt;1">',
                            '<div><b>' + dll.dAbstract + ':</b>',
                                    '<p>{dAbs}</p>',
                            '</div><hr class="hr4tmpl" />',
                        '</tpl>',
                    '</div>',
                    {
                        cAr : function( array ) {

                            return array.length > 0 ? true : false;
                        }
                    },{
                        fDate : function( d ) {
                            var dt = new Date( d );
                            return dt.format( dF.detail );
                        }
                    }
                )
            }),

            filters = new Ext.ux.grid.GridFilters( {
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
            } ),

            titleRenderer = function( value, id, record ) {


                    return '<div style="white-space:normal !important;"><a class="docpreview" href="' + record.data.docPageURL + '" title="' + dll.link2preview + '">' + value + '</a></div>';
            },


            config = {
                    title            : gll.resPanel.title,
                    autoScroll       : true,
                    forceFit         : true,
                    style            : 'border-bottom:1px solid #bbb;',
                    autoExpandColumn : 'title',
                    autoSizeColumn   : true,
                    autoExpandMin    : 150,
                    autoExpandMax    : 650,
                    plugins          : this.standaloneGrid ? [rowExpander,filters] : [rowExpander,filters,new Ext.ux.plugins.HeaderButtons( )],
                    hbuttons         : this.standaloneGrid ? '' : [{
                        text             : gll.resPanel.searchAgain,
                        id               : 'btnMakeNewSearch',
                        iconCls          : 'x-btn-search'
                    }],
                    loadMask         : {
                        msg : gll.loading
                    },
                    store : new Ext.data.GroupingStore({
                        storeId: 'gridStore',
                        baseParams : {
                            start : 0,
                            limit : this.pageSize
                        },
                        autoLoad    : false,
                        remoteGroup : true, // this.gF ? true : false,
                        groupOnSort : false,
                        remoteSort  : true, // this.gF || '',
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
                            testProperty  : 'test',
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
                                {name:'pages'},
                                // abstract
                                {name: 'dAbs', type: 'string'}
                                //           ,{name:'prevH',type:'string'}
                                //           ,{name:'prevC',type:'string'}
                            ]
                        }), // eo reader
                        listeners : {
                            datachanged : {
                                fn: function(obj) {
                                    if(obj.groupField) {
                                        this.groupField = obj.groupField;
                                        this.remoteGroup = true;
                                        this.setBaseParam( 'groupBy', obj.groupField );
                                        this.setBaseParam( 'grouping', true );
                                    }
                                }
                            }
                        }
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
                            header    : gll.header.title,
                            width     : this.colsW[ 0 ],
                            sortable  : true,
                            dataIndex : 'title',
                            renderer  : {fn: titleRenderer, scope: this}
                            //,css       : 'white-space:normal !important;'
                        },{
                            header    : gll.header.date,
                            width     : this.colsW[ 1 ],
                            fixed     : true,
                            resizable : false,
                            sortable  : true,
                            renderer  : Ext.util.Format.dateRenderer( dF.row ),
                            dataIndex : 'date'
                        },{
                            header    : gll.header.owner,
                            width     : this.colsW[ 2 ],
                            sortable  : true,
                            dataIndex : 'owner',
                            renderer  : function( value, id, record ) {return '<div style="white-space:normal !important;">' + value + '</div>';}
                        },{
                            header    : gll.header.key,
                            width     : this.colsW[ 3 ],
                            fixed     : true,
                            sortable  : true,
                            dataIndex : 'dkey'
                        },{
                            header     : gll.header.type,
                            width     : this.colsW[ 4 ],
                            fixed     : true,
                            sortable  : true,
                            dataIndex : 'type',
                            renderer  : function( value, id, record ) {return '<div style="white-space:normal !important;">' + value + '</div>';}
                        },{
                            header    : gll.header.status,
                            width     : this.colsW[ 5 ],
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
                ],
                prependButtons: true,
                items: [
                    {
                        text: 'XML',
                        tooltip: {text:  gll.xmlLinkTooltip, title: gll.xmlLinkTitle},
                        menu: new Ext.menu.Menu({
                            id: 'xmlLinkMenu',
                            style: {
                                overflow: 'visible',
                                backgroundImage: 'none'
                            },
                            items: [
                                {
                                    id: 'queryBtnXmlLink',
                                    xtype: 'button',
                                    text: gll.xmlLinkBtn,
                                    listeners : {
                                        // this grid result
                                        scope: this,
                                        render : function(el) {
                                            el.addClass('x-btn-15');
                                            el.on({
                                                scope: this,
                                                click: this.onClickGetXmlLink,
                                                stopEvent: true
                                            });
                                        }
                                    }
                                },
                                {
                                    id: 'xmlLink',
                                    xtype: 'textfield',
                                    grow: true,
                                    selectOnFocus: true,
                                    validateOnBlur: false,
                                    readOnly: true,
                                    hidden: true,
                                    value: 'xml link'
                                }
                            ]
                        })
                    },
                    '-'
                ]
            });

            
            this.on( {
                scope  : this,
                render : function( ) {
                    var bbarItems = Ext.getCmp('g-p-bbar').items;
                    if(!xmlExport) {
                        bbarItems.itemAt(0).disable().hide();
                        bbarItems.itemAt(1).hide();
                    }

                    this.body.on( {
                        scope     : this,
                        click     : this.onClickLink,
                        delegate  : 'a.docpreview',
                        stopEvent : true
                    });
                }
            });

            this.store.on({
                load : function( s, r, options ) {
                    Ext.getCmp('xmlLink').hide();
                }
            });

            DocDb.GridResults.superclass.initComponent.apply( this, arguments );

	}, // eo function initComponent

    onClickGetXmlLink : function( el, e ) {

//        // this gridresult
//        console.info(Ext.StoreMgr.lookup('gridStore').baseParams);

        // el button
        el.setIconClass('loading-btn');
        // e browser event
        // send server request
        DocDb.xmllink.getLink(this.store.baseParams, function(res, e) {
            el.setIconClass('');
            if(res.success) {
                    Ext.getCmp('xmlLink').setValue( res.xmlLink ).show();
                    Ext.getCmp('xmlLinkMenu').doLayout();
            }
        });
    },

	onClickLink : function( el, a, e ) {

		// language labels for detail preview
		var dll = this.lang.docDetail,
		selM = this.getSelectionModel( );

		if (!this.win) {
            this.win = new Ext.Window({
                id              : 'docdb-previewWin',
                    bodyStyle       : 'padding:10px',
                    layout          : 'fit',
                    preventBodyReset: true,
                    unstyled        : false,
                    maximizable     : true,
                    shadow          : true,
                    width           : this.docDetail.pWinWidth,
                    height          : this.docDetail.pWinHeight,
                    closeAction     : 'hide',
                    autoScroll      : true,
                    constrainHeader : true,
                    plain           : false,
                    tbar            : [ {
                        text    : '',
                        id      : 'btnPrev',
                        iconCls : 'x-tbar-page-prev',
                        tooltip : dll.prevDoc,
                        listeners : {
                            click: function( el, e ) {

                                if( selM.hasPrevious( ) ) {
                                    selM.selectPrevious( );
                                    this.loadDetail( selM );
                                }
                            },
                            render : this.prevNextStatus,
                            scope: this
                        }
                    },'-',{
                            text    : '',
                            id      : 'btnNext',
                            iconCls : 'x-tbar-page-next',
                            tooltip : dll.nextDoc,
                            listeners : {
                                click: function( el, e ) {

                                    if( selM.hasNext( ) ) {
                                        selM.selectNext( );
                                        this.loadDetail( selM );
                                    }
                                },
                                render : this.prevNextStatus,
                                scope: this
                            }
                    },'->',{
                            text        : dll.link2page,
                            id          : 'btnExtUrl',
                            iconCls     : 'x-docdetail-lint2page',
                            iconAlign   : 'right',
                            tooltip     : dll.link2pageTooltip,
                            handler     : function( ) {
                                window.location.href = selM.getSelected( ).data.docPageURL;
                            }
                    }]
            });
		}

        this.win.setPos = function() {
            
            var scrollPos = Ext.getDoc().getScroll(),
            elPos = this.getPosition();

            this.setPagePosition(elPos[0], scrollPos.top + 20);
        };

		this.win.on( 'resize', function( ) {

            this.setPos();
		} );

		this.win.show(
				a,
				function( ) {this.win.center();this.win.setPos();},
				this
		);

		this.loadDetail( selM );

	},

	loadDetail : function( selM ) {

				if( this.win.rendered ) {

						this.win.setTitle( selM.getSelected( ).data.title );

                        var pattern = new RegExp(window.document.baseURI, 'i');
                        if(! selM.getSelected( ).data.docPageURL.match(pattern)) {

                            // @todo open in an iframe when it come from crossdomain. but better to process server side...
                            this.win.body.update( '<iframe width="100%" height="100%" src="'+selM.getSelected( ).data.docPageURL+'#content"></iframe>' );
                        
                        } else {

                            this.win.load( {
                                    url      : selM.getSelected( ).data.docPageURL,
                                    
                                    callback : function( obj, b, s ) {

                                            var bodyC = this.win.body,
                                            cont = Ext.select( '#docdb-previewWin div#' + this.docDetail.divContIdWinP +'', bodyC );

                                            bodyC.update( cont.elements[0].innerHTML ).fadeIn( {stopFx:true, duration:.5} );
                                            this.prevNextStatus( );
                                    },
                                    scope : this
                            } );
                        }
				} // eo this.win.rendered
	},

	prevNextStatus : function( ) {

		var selM = this.getSelectionModel( ),
		btnP = Ext.getCmp( 'btnPrev' ),
		btnN = Ext.getCmp( 'btnNext' );

		if( selM.hasPrevious( ) ) {
				btnP.enable( );
		} else {
				btnP.disable( );
		}

		if( selM.hasNext( ) ) {
				btnN.enable( );
		} else {
				btnN.disable( );
		}
	},

	afterRender:function( ) {
		this.store.on( 'beforeload', function( ) {

				if( this.baseParams.groupBy !== 'owner' ) {
						this.groupBy( this.baseParams.groupBy );
				}
				if( ! this.baseParams.grouping ) {
						this.groupBy( '' );
						this.clearGrouping( );
				}
		} );

		DocDb.GridResults.superclass.afterRender.apply( this, arguments );
	} // eo function afterRender
});

Ext.reg( 'gridresults', DocDb.GridResults );

// eof