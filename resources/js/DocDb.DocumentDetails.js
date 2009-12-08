/**
 * DocDb.DocumentDetails
 * @extends Ext.Panel
 * This is a specialized Panel which is used to show information about
 * a document.
 *
 * The class will be registered with an xtype of 'docdetail'
 */
Ext.ns('DocDb');
 
DocDb.DocumentDetails = Ext.extend(Ext.Panel, {

    border: false,
    // override initComponent to create and compile the template
    // apply styles to the body of the panel and initialize
    // html to startingMarkup

    initComponent : function( ) {

        this.tpl = new Ext.XTemplate(
            '<div style="float:left;width:91%;">',
               '<b>' + this.LL.title + '</b> <a class="docpreview" href="{docPageURL}" title="' + this.LL.link2preview + '">{title}</a><br/>',
                '<tpl if="owner.length &gt;1">',
                    '<b>' + this.LL.owner + '</b> {owner}<br/>',
                '</tpl>',
                '<tpl if="type.length &gt;1">',
                    '<b>' + this.LL.type + '</b> {type}<br/>',
                '</tpl>',
                '<tpl if="status.length &gt;1">',
                    '<b>' + this.LL.status + '</b> {status}<br/>',
                '</tpl>',
                '<tpl if="dkey.length &gt;1">',
                    '<b>' + this.LL.key + '</b> {dkey}<br/>',
                '</tpl>',
                '<tpl if="date !==0">',
                    '<b>' + this.LL.date + '</b> {[this.fDate(values["date"])]}<br/>',
                '</tpl>',
            '</div>',
            '<div style="float:right;width:8%;text-align:right;">',
                '<a href="{docPageURL}" title="' + this.LL.link2page + '"><img class="x-docdetail-lint2page" src="' + Ext.BLANK_IMAGE_URL + '" /></a>',
            '</div>',
            {
                fDate : function( d ) {
                    var dt = new Date( d );
                    return dt.format('d.m.Y');
                }
            }
        );
        
        Ext.apply( this, {
              bodyStyle : {
                background  : '#ffffff',
                padding     : '7px',
                'font-size' : '85%'
              },
              html : this.LL.helpText
        });
        // call the superclass's initComponent implementation
        DocDb.DocumentDetails.superclass.initComponent.call( this );

        this.on( {
          scope  : this,
          render : function( ) {
            this.body.on( {
                scope     : this,
                click     : this.onClickLink,
                delegate  : 'a.docpreview',
                stopEvent : true
            });
          }
        });
    }, // eo initComponent

    restoreInitText : function( ) {
        
        this.body.update( this.LL.helpText );
    },
    // add a method which updates the details
    updateDetail : function( data ) {

        this.tpl.overwrite( this.body, data );
//        this.body.highlight( '#cccccc', {block:true} );
        this.body.fadeIn( { duration: 1, block:true} );
        this.resizeContainer();
    },

    resizeContainer : function( ) {

        var gR = Ext.getCmp( 'gridResults' );
        
        if( gR.standaloneGrid ) {
            var mG = Ext.getCmp( 'mainGrid' );
        } else {
            var mG = Ext.getCmp( 'mainPanel' );
        }
        
        var dR = Ext.getCmp( 'resultsPanel' );
        var detailBoxH = this.body.dom.firstElementChild.clientHeight + 20;
      
        mG.setHeight( gR.getHeight( ) + detailBoxH );
        dR.setHeight( gR.getHeight( ) + detailBoxH );
        this.setHeight( detailBoxH );
        mG.syncSize();
    },
  
    onClickLink : function( btn, a, t ) {
    
        if (!this.win) {
            this.win = new Ext.Window({
                id          : 'docdb-previewWin',
                bodyStyle   : 'padding:13px',
                unstyled    : false,
                maximizable : true,
                shadow      : true,
                width       : this.docDetail.pWinWidth,
                height      : this.docDetail.pWinHeight,
                closeAction : 'hide',
                autoScroll  : true,
                constrain   : true,
                url         : a.href
            });
        }

        this.win.on( 'resize', function( ) {
            this.center( );
        } );

        this.win.setTitle( a.innerHTML );
        this.win.show( );
        this.win.center( );

        
        
        if( this.win.rendered ) {

            this.win.load( {
                url       : a.href,
                callback : function( o, b, i ) {

                    var cont = Ext.select( '#docdb-previewWin div#' + this.docDetail.divContIdWinP, this.win.body.dom );

//                    console.log( cont );
                    this.win.body.update( cont.elements[0].innerHTML );
                },
                scope : this
            },
            this
            );
        }
    }

});
// register the App.BookDetail class with an xtype of bookdetail
Ext.reg( 'docdetail', DocDb.DocumentDetails );
