// vim: ts=4:sw=4:nu:fdc=4:nospell
/**
  * Page Size Plugin for Paging Toolbar
  *
  * @author    rubensr, http://extjs.com/forum/member.php?u=13177
  * @see       http://extjs.com/forum/showthread.php?t=14426
  * @author    Ing. Jozef Sakalos, modified combo for editable, enter key handler, config texts
  * @date      27. January 2008
  * @version   $Id: Ext.ux.PageSizePlugin.js 11 2008-02-22 17:13:52Z jozo $
  * @package   perseus
  */
Ext.ux.PageSizePlugin = function(config) {
    Ext.apply(this, config);

    Ext.ux.PageSizePlugin.superclass.constructor.call(this, {
        store: new Ext.data.SimpleStore({
            fields: ['text', 'value'],
            data: [['5', 5], ['10', 10], ['15', 15], ['20', 20], ['25', 25], ['50', 50], ['100', 100]]
        }),
        mode: 'local',
        displayField: 'text',
        valueField: 'value',
        allowBlank: false,
        triggerAction: 'all',
        width: 50,
        maskRe: /[0-9]/
    });
};

Ext.extend(Ext.ux.PageSizePlugin, Ext.form.ComboBox, {
    beforeText:'',
    afterText:'/page',
    init: function(paging) {
        paging.on('render', this.onInitView, this);
    },

    onInitView: function(paging) {
        //paging.add('-', this.beforeText, this, this.afterText);
        // lcherpit changing rendering position
        paging.insert( 11, '-', this.beforeText, this, this.afterText);

        this.setValue(paging.pageSize);
        this.on('select', this.onPageSizeChanged, paging);
        this.on('specialkey', function(combo, e) {
            if(13 === e.getKey()) {
                this.onPageSizeChanged.call(paging, this);
            }
        });

    },

    onPageSizeChanged: function(combo) {
        this.pageSize = parseInt(combo.getRawValue(), 10);
        this.doLoad(0);
    }
});

// end of file 

