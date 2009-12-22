/*
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.Descriptors.js 166 2009-12-18 17:37:47Z lcherpit $
 */
Ext.ns( 'Ext.ux' );
Ext.ux.DscrLoader = Ext.extend( Ext.tree.TreeLoader, {

    addIconRelDscr : function( node ) {
        
        if( node.relDscr ) {
            // dirty way to put additional icon
            node.text = '<img class="x-tree-node-relDscr" src="' + Ext.BLANK_IMAGE_URL + '" /> ' + node.text;

            node.qtip = '<b>Rel.:</b><br />'
            for( i = 0; i < node.relDscr.length; i++) {
                node.qtip += '<p>- ' + node.relDscr[ i ].title + '</p>';
            }
//            node.qtip = '';
        }
    },

	createNode : function( node ) {

        this.addIconRelDscr( node );

        return Ext.ux.DscrLoader.superclass.createNode.call( this, node );
    }
});

/**
 * Global app
 * @namespace DocDb
 */
Ext.ns('DocDb');

/*
 * Class Descriptors treePanel
 */
DocDb.DescriptorsTree = Ext.extend( Ext.tree.TreePanel, {
  
    border : false,

    initComponent:function( ) {
    
        var config = {
            // -35 for toolbar @todo better way
            height          : this.treeHeight.min-35,
            width           : this.width,
            bodyStyle       : 'overflow: hidden;',
            unstyled        : false,
            animate         : false,
            autoScroll      : false,
            containerScroll : true,
            rootVisible     : false,
            lines           : false,
            useArrows       : true,
            plugins         : [ 'msgbus', new Ext.ux.plugins.HeaderButtons( ) ],
            hbuttons : [{
                text          : this.lang.search,
                tooltip       : this.lang.searchTip,
                xtype         : 'button',
                id            : 'btnFormSubmit',
                iconCls       : 'x-btn-search'
                },{
                text          : this.lang.reset,
                tooltip       : this.lang.resetTip,
                xtype         : 'button',
                id            : 'btn-form-reset',
                handler       : function( btn, e ) {
                    // ownerCt:tree
                    delete this.ownerCt.loader.baseParams;
                    this.ownerCt.loader.baseParams = {};
                    
                    // ownerCt.ownerCt:form
                    this.ownerCt.ownerCt.form.reset( );

                    if( this.ownerCt.searchIsOn ) {
                        this.ownerCt.trigger.onTriggerClick( );
                    } else {
                        this.ownerCt.collapseAll( );
                    }
                }
            }]
            ,root : {
                id       : 'root',
                text     : 'Root',
                // initial childrenNodes from session
                children : this.treeNodes
            },
            loader : new Ext.ux.DscrLoader({
                directFn      : DocDb.model_descriptor.get,
                paramsAsHash : true
            }),
            searchIsOn : false,
            tbar : [
                ' ',
                {
                    xtype      : 'checkbox',
                    id         : 'chkAllDscr',
                    checked    : false,
                    boxLabel   : this.lang.all || 'All',
                    name       : 'selAll',
                    inputValue : 'selAll'
                },
                ' ',
                '-',
                ' ',
                'Combinaison',
                ' ',
                {
                    xtype      : 'radio',
                    id         : 'selTypeAnd',
                    boxLabel   : this.lang.and,
                    name       : 'selType',
                    inputValue : 'AND'
                },
                ' ',
                {
                    xtype      : 'radio',
                    id         : 'selTypeOr',
                    checked    : true,
                    boxLabel   : this.lang.or,
                    name       : 'selType',
                    inputValue : 'OR'
                },
                ' ',
                '-',
                {
                    xtype   : 'button',
                    id      : 'btnCollapse',
                    text    : '',
                    iconCls : 'x-btn-text icon-collapse-all',
                    tooltip : this.lang.clpseAll
                },
                '->',
                '-',
                this.lang.filter,
                {
                    xtype           : 'trigger',
                    id              : 'treeFilter',
                    tree            : this,
                    triggerClass    : 'x-form-clear-trigger',
                    emptyText       : this.lang.filterEmpTxt,
                    onTriggerClick  : function(){ this.onTriggerClick(); },
                    enableKeyEvents : true,
                    minLength       : 4,
                    maxLength       : 50,
                    vtype           : 'alphanumMask'
                }
            ]
        }; // eo config object

        // apply config
        Ext.apply(this, Ext.apply( this.initialConfig, config ) );
        DocDb.DescriptorsTree.superclass.initComponent.apply( this, arguments );

        this.on('beforeclick',function( node, e ) {

            node.ui.toggleCheck( );
        });
    
        // change style bubbling to parent and send state to server
        this.on( 'checkchange', function( node, state ) {

            var checked = state ? 'checked' : 'unchecked';

            if( node.isLeaf( ) ) {

                this.bubbleSetStyle( node, state );

                if( ! this.searchIsOn ) {
                    // store state
                    DocDb.model_descriptor.setSessionNode( node.id, checked );
                }
            }
        }
        ,this
        );
    
        this.on( 'beforeexpandnode', function( node, e ) {

            if( ! this.searchIsOn ) {
                DocDb.model_descriptor.setSessionNode( node.id, 'expand' );
            }
        });


        this.on( 'beforecollapsenode', function( node, e ) {

            // cascade uncheck all checked node on collapse
            Ext.each( this.getChecked( '', node ),function( obj, index ) {

                obj.ui.toggleCheck( false );
            });
//            if( ! this.searchIsOn ) {
                DocDb.model_descriptor.setSessionNode( node.id, 'collapse' );
//            }
        });
    
    
        this.on( 'expandnode', function( node ) {

            this.applyAttrToChildrenOfNode( node );

            // is rootNode
            if( node.getDepth() === 0 ) {
                // change root line style of checked nodes
                Ext.each( this.getChecked(), function( node ) {
                    this.bubbleSetStyle( node, true );
                },
                this
                ); // eo each
            } // eo getDepth root node

            node.isAlreadyExpanded = true;

            this.resizeTreePanel( );
        }
        ,this
        ,{buffer:10}
        );
    
    
        this.on( 'collapsenode', function( node ) {

            node.collapseChildNodes( true );

            this.resizeTreePanel( );
        });

        this.loader.on( 'beforeload', function( ld, node ) {

            if( this.body.isMasked( ) ) {
                this.body.unmask( );
            }
        }, this
        );
    
    
        this.loader.on( 'load', function( ld, node, response ) {

            if( this.searchIsOn ) {
                this.expandAll( );

                if( response.responseText[0].id === 'nores' ) {
                    (function( ) { this.body.mask( this.lang.noResult, 'x-mask' ); }.defer( 20, this ) );
                }
            }
            
            node.isAlreadyExpanded = false;
        }
        ,this
        ); // eo onload
    } // eo function initComponent

    /**
     * Resize tree panel to fit the height of expanded nodes.
     * size between minHeight and expanded nodes height sum.
     */
    ,resizeTreePanel : function( ) {
        
        var mP = Ext.getCmp( 'mainPanel' );
        var aS = Ext.getCmp( 'advSearch' );
        var mS = Ext.get( 'mSelect' );
        var newH  = 0;
        var treeH = ( parseInt( this.getTreeEl( ).dom.firstElementChild.clientHeight, 10 ) + Math.ceil( this.getFrameHeight( )*1.5 ) );
        
        if( treeH <= this.treeHeight.min ) {

            newH  = ( mS.getHeight( ) + this.treeHeight.min );
            treeH = this.treeHeight.min;

        } else if( treeH >= this.treeHeight.max ) {

            newH  = ( mS.getHeight( ) + ( this.treeHeight.max + Math.ceil( this.getFrameHeight( )*.5 ) ) );
            treeH = this.treeHeight.max;
            this.body.setStyle( 'overflow-y', 'auto' );

        } else {
            
            newH = ( mS.getHeight( ) + treeH );
            this.body.setStyle( 'overflow', 'hidden' );
        }

        // tree height
        this.setHeight( treeH );
        aS.setHeight( newH );
        mP.setHeight( newH );
        this.syncSize();
        mP.doLayout( );
    }
    
    // Update Tree Node quicktips
    // usage updateqt(n, n.attributes.qtip , qtitletext);
    // node   = node
    // tqt    = node.attributes.qt ( new text of quicktip)
    // newtqt = new text for the title of quicktip
    ,updateqt : function( node, tqt, newtqt ) {

        if( node.getUI( ).textNode.setAttributeNS ) {
            node.getUI( ).textNode.setAttributeNS( 'ext', 'qtip', tqt );
            if( newtqt ) {
                node.getUI( ).textNode.setAttributeNS( 'ext', 'qtitle', newtqt );
            }
        } else {
            node.getUI( ).textNode.setAttribute( 'ext:qtip', tqt );
            if( newtqt ) {
                node.getUI( ).textNode.setAttribute( 'ext:qtitle', newtqt );
            }
        }
    } // eo function updateqt
    
    
    // Set styles to parents nodes when check state change
    ,bubbleSetStyle : function( node, state ) {

        node.bubble( function( node ) {

            if( state ) {
                node.ui.addClass( 'xtree-node-checked' );
            } else {
                if( this.getChecked( '', node.parentNode ).length < 1 || this.getChecked( '', node ).length < 1 ) {
                    node.ui.removeClass( 'xtree-node-checked' );
                }
            }
        }
        ,this
        ); // eo bubble
    }

 
    ,applyAttrToChildrenOfNode : function( node ) {


        if( node.firstChild && node.firstChild.isLeaf( ) && ! node.isAlreadyExpanded ) {

            // Put text in tip when it is too wide and addClass bubbling to parent
            node.eachChild( function( obj, index ) {
                
                var atext  = obj.ui.elNode.lastElementChild;
                var tWidth = ( atext.offsetLeft+atext.offsetWidth );

                if( tWidth > ( this.width-20 ) ) {

                    this.updateqt( obj, obj.qtip + '<br />' + obj.attributes.text );
                }
            } // eo func
            ,this
            ); // eo each
        } // eo if
    } // eo function applyAttrToChildrenOfNode
    
    
    ,disableBodyTree : function( el, chkState ) {

        var mP  = Ext.getCmp( 'mainPanel' );
        var sTA = Ext.getCmp( 'selTypeAnd' );
        var sTO = Ext.getCmp( 'selTypeOr' );

        if( chkState ) {

            sTA.disable( );
            sTO.disable( );
            this.trigger.disable( );
            this.btClpsAll.disable( );
            this.collapseAll( );
            this.body.mask( this.lang.allDscrSelected, 'x-mask' );

        } else {

            sTA.enable( );
            sTO.enable( );
            this.trigger.enable( );
            this.btClpsAll.enable( );
            this.body.unmask( );
        }// end if
    }
  
  
  
    ,onRender : function( ) {

        // copy initial children attr. to
        var AttrChildren = function( val ) {
            this.val = val;
        };

        DocDb.DescriptorsTree.superclass.onRender.apply( this, arguments );

        // show fired events
        // Ext.util.Observable.capture(Ext.getCmp('dsrcTree'),console.info);

        // ref to cmp
        this.chkAll    = Ext.getCmp( 'chkAllDscr' );
        this.btClpsAll = Ext.getCmp( 'btnCollapse' );
        this.trigger   = Ext.getCmp( 'treeFilter' );

        // copy root static session children
        this.origRootChildren = new AttrChildren( this.getRootNode( ).attributes.children );


        // subscribe to msgbus topic
        this.subscribe( 'doccb.owner.selected', {fn: this.getRelatedDescriptors, single: false} );
        this.subscribe( 'doccb.type.selected', {fn: this.getRelatedDescriptors, single: false} );
        this.subscribe( 'doccb.status.selected', {fn: this.getRelatedDescriptors, single: false} );

    } // eo function onRender


    ,afterRender:function( domC ) {

        DocDb.DescriptorsTree.superclass.afterRender.apply( this, arguments );

        // Collapse all Btn
        this.btClpsAll.on( 'click', function( ) {
                this.collapseAll( );
            }
        ,this
        );

        this.chkAll.on( 'check', this.disableBodyTree, this );

        // Search filter field
        this.trigger.onTriggerClick = function( e ) {

          this.setValue( '' );
          
          var t = this.tree

          if( t.searchIsOn ) {
            
            delete t.loader.baseParams.needle;
            t.searchIsOn = false;
            t.loader.load(
                t.getRootNode( )
            );
            
            if( t.body.isMasked( )  ) {
                t.body.unmask( );
            }
          }
        } // eo function onTriggerClick

        this.trigger.on( 'blur', function( textF ) {

            if( textF.getValue( ).length < 4 && this.searchIsOn ) {

                // to exec reload init on triggerClick
                this.searchIsOn = true;
                textF.onTriggerClick( );
            }
        }
        ,this
        ); // eo onBlur

        this.trigger.on( 'keyup', function( textF, e ) {
            
            if( ( e.isNavKeyPress( ) || e.isSpecialKey( ) ) && e.getKey() !== e.BACKSPACE && e.getKey( ) !== e.ENTER ) {
              return false;
            }
            
            if( textF.validateValue( textF.getValue( ) ) ) {

                // secu. twice check.
                if( textF.getValue( ).length < 4 ) {
                    textF.markInvalid( );
                    return false;
                }

                this.loader.baseParams.needle = textF.getValue( );

                // delete initial children Nodes to allow direct query
                delete this.getRootNode( ).attributes.children;
                
                this.collapseAll( );
                // get root node with related setted params
                this.loader.load( this.getRootNode( ) );

                this.searchIsOn = true;

            } // eo if validateValue
        }
        ,this
        ,{buffer: 600}
        ); // eo onKeyup
    } // eo function afterRender


    ,getRelatedDescriptors: function( sub, msg ) {

        var conf = {};

        if( msg.val.length ) {

            if( sub === 'doccb.owner.selected' ) {

                this.loader.baseParams.ownerfk = msg.val;

                if( Ext.isDefined( this.loader.baseParams.typefk ) ) {
                  delete this.loader.baseParams.typefk;
                }
                if( Ext.isDefined( this.loader.baseParams.statusfk ) ) {
                  delete this.loader.baseParams.statusfk;
                }

            } else if( sub === 'doccb.type.selected' ) {

                this.loader.baseParams.typefk = msg.val;

                if( Ext.isDefined(this.loader.baseParams.statusfk ) ) {
                  delete this.loader.baseParams.statusfk;
                }

            } else if( sub === 'doccb.status.selected' ) {

                this.loader.baseParams.statusfk = msg.val;

            }

            // delete initial children Nodes to allow direct query
            delete this.getRootNode( ).attributes.children;

            // strore current params options
            this.loader.lastOptions = this.loader.baseParams;

            // get root node with related setted params
            this.loader.load( this.getRootNode( ) );
        }

    } // eo function getRelatedDescriptors
  
}); // eo extend
 
Ext.reg( 'descriptorstree', DocDb.DescriptorsTree );