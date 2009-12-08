/**
 * DocDb.DocGridResultDetails
 * @extends Ext.Panel
 *
 * This is a specialized panel which is composed of both a bookgrid
 * and a bookdetail panel. It provides the glue between the two
 * components to allow them to communicate. You could consider this
 * the actual application.
 *
 */
/**
 * @author  : Laurent Cherpit
 */
Ext.ns('DocDb');
 
DocDb.DocGridResultDetails = Ext.extend(Ext.Panel, {
  // override initComponent
  initComponent: function( ) {
    // used applyIf rather than apply so user could
    // override the defaults
    Ext.applyIf( this, {
      frame: true,
      border: false,
      unstyled: true,
      layout: 'border',
      
      items: [{
        xtype          : 'gridresults',
        id             : 'gridResults',
        lang           : this.lang,
        pageSize       : parseInt( this.pageSize, 10 ),
        region         : 'north',
        height         : this.standaloneGrid ? parseInt( this.gridHeight, 10) : 0,
        standaloneGrid : this.standaloneGrid,
        split          : true,
        useSplitTips   : true
      },{
        xtype          : 'docdetail',
        itemId         : 'detailPanel',
        id             : 'detailPanel',
        region         : 'center',
        docDetail      : this.docDetail,
        LL             : this.docDetailLL
      }]
    })
    // call the superclass's initComponent implementation
    DocDb.DocGridResultDetails.superclass.initComponent.call( this );
  },
  // override initEvents
  initEvents: function() {
    // call the superclass's initEvents implementation
    DocDb.DocGridResultDetails.superclass.initEvents.call( this );

    // now add application specific events
    // notice we use the selectionmodel's rowselect event rather
    // than a click event from the grid to provide key navigation
    // as well as mouse navigation
    var docGridSm = this.getComponent( 'gridResults' ).getSelectionModel( );
    docGridSm.on( 'rowselect', this.onRowSelect, this );
  },
  // add a method called onRowSelect
  // This matches the method signature as defined by the 'rowselect'
  // event defined in Ext.grid.RowSelectionModel
  onRowSelect: function( sm, rowIdx, r ) {
    // getComponent will retrieve itemId's or id's. Note that itemId's
    // are scoped locally to this instance of a component to avoid
    // conflicts with the ComponentMgr
    var detailPanel = this.getComponent( 'detailPanel' );
    detailPanel.updateDetail( r.data );
  }
});
// register an xtype with this class
Ext.reg( 'docgridresultdetail', DocDb.DocGridResultDetails );

