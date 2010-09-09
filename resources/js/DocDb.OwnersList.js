/**
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.OwnersList.js 199 2010-01-18 17:23:39Z lcherpit $
 */
/**
 * Global app
 * @namespace DocDb
 */
Ext.ns('DocDb');


/**
 * Class: Owners multiselectList
 */
DocDb.OwnersList = Ext.extend( Ext.ux.form.MultiSelect, {
  
  border       :false
  
  ,initComponent:function( ) {
    
    var config = {
      name      : 'owner'
      ,plugins  : ['msgbus']
      ,store    : new Ext.data.DirectStore({
        root        : 'result'
        ,fields     : [{name:'id', type: 'int'},{name:'owner', type:'string'}]
        ,baseParams : {
          sort : 'owner'
          ,dir : 'ASC'
        }
        ,remoteSort : true
        ,directFn   : DocDb.model_owner.get
      }) // eo store
      ,valueField   : 'id'
      ,displayField : 'owner'
      ,simpleSelect : false
    }; // eo config object
    
    // apply config
    Ext.apply( this, Ext.apply( this.initialConfig, config ) );

    DocDb.OwnersList.superclass.initComponent.apply( this, arguments );

    /**
     *  Change label and localize of the record id: 0
     */
    this.store.on( 'load', function( ) {
      
      var recMixCol = this.store.query( 'id', 0 ),
      index  = this.store.indexOfId( recMixCol.keys[0] ),
      record = this.store.getAt( index );
      // change label All
      record.data.owner = this.labelAll;
      // replace
      this.store.removeAt( index );
      this.store.insert( index, record );
      // select first record : All
      this.setValue( '0' );
      
    }
    ,this ); // eo method on
    
    this.on( 'change', function( obj, val ) {

      // publish topic to msgbus
      this.publish( 'doccb.owner.selected', {obj: this, val: val} );
    }
    ,this
    ,{buffer: 800, stopEvent: true}
    ); // eo function onChange and puplish msgbus

    this.on( 'reset', function( obj ) {

      // publish topic to msgbus
      this.publish( 'doccb.owner.reset', {obj: obj} );
    }
    ,this ); // eo function on reset

    this.on( 'afterreset', function( obj ) {
      this.setValue( '0' );
    });

  } // eo function initComponent

  ,afterRender : function() {

    DocDb.OwnersList.superclass.afterRender.apply( this, arguments );

    var that = this;
    (function(){that.store.load( );}.defer(200));
 
    
  } // eo function afterRender

}); // eo extend
 
Ext.reg( 'ownerslist', DocDb.OwnersList );