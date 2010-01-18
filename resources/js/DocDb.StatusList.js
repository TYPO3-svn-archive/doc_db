/**
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.StatusList.js 106 2009-11-27 01:56:29Z lcherpit $
 */
/**
 * Global app
 * @namespace DocDb
 */
Ext.ns('DocDb');


/**
 * Class Status multiselectList
 */
DocDb.StatusList = Ext.extend( Ext.ux.form.MultiSelect, {
  
  border       : false
  
  ,initComponent:function( ) {
    
    var config = {
      name      : 'status'
   //   ,id   : 'status-list'
      ,plugins  : ['msgbus']
      ,store    : new Ext.data.DirectStore({
        root        : 'result'
        ,fields     : [{name:'id', type: 'int'},{name:'status', type:'string'}]
        ,baseParams : {
          sort : 'status'
          ,dir : 'ASC'
        }
        ,remoteSort : true
        ,directFn   : DocDb.model_status.get
      })
      ,valueField   : 'id'
      ,displayField : 'status'
      ,simpleSelect : false
    }; // eo config object
    
    // apply config
    Ext.apply( this, Ext.apply( this.initialConfig, config ) );
 
    DocDb.StatusList.superclass.initComponent.apply( this, arguments );
    
    this.store.on( 'load', function( ) {
      
      var recMixCol = this.store.query( 'id', 0 ),
      index  = this.store.indexOfId( recMixCol.keys[0] ),
      record = this.store.getAt( index );
      // change label All
      record.data.status = this.labelAll;
      // replace
      this.store.removeAt( index );
      this.store.insert( index, record );
      // select first node : All
      this.setValue( '0' );
    }
    ,this
    ); // eo method onLoad

    // reload store
    this.on( 'reset', function( obj ) {

        this.store.load( );
    }); // eo function on reset

    this.on( 'change', function( obj, val ) {

      // publish topic to msgbus
      this.publish( 'doccb.status.selected', {obj: this, val: val} );
      }
      ,this
      ,{buffer: 800, stopEvent: true}
    ); // eo function onChange and puplish msgbus
  } // eo function initComponent
  
  ,onRender: function() {

    DocDb.OwnersList.superclass.onRender.apply( this, arguments );
    
    // subscribe to msgbus topic
    this.subscribe( 'doccb.owner.selected', {fn: this.getRelatedStatus, single: false} );
    this.subscribe( 'doccb.type.selected', {fn: this.getRelatedStatus, single: false} );
  } // eo function onRender
  
  ,afterRender:function( ) {

    DocDb.StatusList.superclass.afterRender.apply( this, arguments );

    this.store.load( );
  } // eo function afterRender
  
  ,getRelatedStatus: function( sub, msg ) {
    
    var conf = {};
    
    if( msg.val.length < 1 ) {
      
      this.store.load( );
      
    } else {
      
      if( sub === 'doccb.owner.selected' ) {
        
        conf = {params:{ownerfk:msg.val}};
        
      } else if( sub === 'doccb.type.selected' ) {
        
        if( Ext.isDefined( this.store.lastOptions.params ) ) {
          
          conf = this.store.lastOptions;
          
        } else {
          
          conf.params = {};
        }
        
        conf.params.typefk = msg.val;
      }
      
      this.store.load( conf );
    }
  } // eo function getRelatedTypes
}); // eo extend
 
Ext.reg('statuslist', DocDb.StatusList);