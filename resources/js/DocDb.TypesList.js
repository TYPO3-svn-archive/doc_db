/**
 * @author  : Laurent Cherpit
 * @version : $Id: DocDb.TypesList.js 111 2009-11-29 05:21:00Z lcherpit $
 */
/**
 * Global app
 * @namespace DocDb
 */
Ext.ns( 'DocDb' );


/*
 * Class Types multiselectList
 */
DocDb.TypesList = Ext.extend( Ext.ux.form.MultiSelect, {
  
  border : false
  
  ,initComponent : function( ) {
    
    var config = {
      name     : 'type'
      ,plugins : ['msgbus']
      ,store   : new Ext.data.DirectStore({
        root    : 'result'
        ,fields : [{name:'id', type: 'int'},{name:'type', type:'string'}]
        ,baseParams : {
          sort : 'type'
          ,dir : 'ASC'
        }
        ,remoteSort :true
        ,directFn   : DocDb.model_type.get
      }) // eo store
      ,valueField   : 'id'
      ,displayField : 'type'
      ,simpleSelect : false
    }; // eo config object

    // apply config
    Ext.apply(this, Ext.apply(this.initialConfig, config));
 
    DocDb.TypesList.superclass.initComponent.apply(this, arguments);
    
    this.store.on('load',function() {
      
      var recMixCol = this.store.query( 'id', 0 ),
      index     = this.store.indexOfId( recMixCol.keys[0] ),
      record    = this.store.getAt( index );
      // change label All
      record.data.type = this.labelAll;
      // replace
      this.store.removeAt( index );
      this.store.insert( index, record );
      // select first node : All
      this.setValue('0');
      
    }
    ,this
    ); // eo method on

    this.on( 'reset', function( obj ) {

        this.store.load( );
    }); // eo function on reset

    this.on( 'change', function( obj, val ) {

      // publish topic to msgbus
      this.publish('doccb.type.selected', {obj: this, val: val});
    }
    ,this
    ,{buffer: 800, stopEvent: true}
    ); // eo function onChange and puplish msgbus

  } // eo function initComponent
  
  
  ,onRender: function( ) {

    DocDb.TypesList.superclass.onRender.apply( this, arguments );
    
    // subscribe to msgbus topic
    this.subscribe( 'doccb.owner.selected', {fn: this.getRelatedTypes, single: false} );
  } // eo function onRender
  
  ,afterRender: function( ) {

    DocDb.TypesList.superclass.afterRender.apply( this, arguments );
    
    this.store.load();
  } // eo function afterRender
  
  ,getRelatedTypes: function( sub, msg ) {
    
    if( msg.val.length < 1 ) {
      
      this.store.load( );
      
    } else {
  
      this.store.load( {params:{ownerfk:msg.val}} );
    }
  } // eo function getRelatedTypes
}); // eo extend
 
Ext.reg( 'typeslist', DocDb.TypesList );
