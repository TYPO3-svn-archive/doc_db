/*!
 * Ext JS Library 3.0.3
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/*!
 * Ext JS Library 3.0.3
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * Bulgarian Translation
 *
 * By Георги Ко�?тадинов, Калгари, Канада
 * 10 October 2007
 * By Nedko Penev 
 * 26 October 2007
 *
 * (utf-8 encoding)
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Зареждане...</div>';

if(Ext.View){
  Ext.View.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
  Ext.grid.GridPanel.prototype.ddText = "{0} избрани колони";
}

if(Ext.TabPanelItem){
  Ext.TabPanelItem.prototype.closeText = "Затвори таб";
}

if(Ext.form.Field){
  Ext.form.Field.prototype.invalidText = "�?евалидна �?тойно�?т на полето";
}

if(Ext.LoadMask){
  Ext.LoadMask.prototype.msg = "Зареждане...";
}

Date.monthNames = [
  "Януари",
  "Февруари",
  "Март",
  "�?прил",
  "Май",
  "Юни",
  "Юли",
  "�?вгу�?т",
  "Септември",
  "Октомври",
  "�?оември",
  "Декември"
];

Date.monthNumbers = {
  Jan : 0,
  Feb : 1,
  Mar : 2,
  Apr : 3,
  May : 4,
  Jun : 5,
  Jul : 6,
  Aug : 7,
  Sep : 8,
  Oct : 9,
  Nov : 10,
  Dec : 11
};

Date.dayNames = [
  "�?едел�?",
  "Понеделник",
  "Вторник",
  "Ср�?да",
  "Четвъртък",
  "Петък",
  "Събота"
];

if(Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : "OK",
    cancel : "Отмени",
    yes    : "Да",
    no     : "�?е"
  };
}

if(Ext.util.Format){
  Ext.util.Format.date = function(v, format){
    if(!v) return "";
    if(!(v instanceof Date)) v = new Date(Date.parse(v));
    return v.dateFormat(format || "d.m.Y");
  };
}

if(Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText         : "Дне�?",
    minText           : "Тази дата е преди минималната",
    maxText           : "Тази дата е �?лед мак�?ималната",
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : 'Следващ ме�?ец (Control+Right)',
    prevText          : 'Предишен ме�?ец (Control+Left)',
    monthYearText     : 'Избери ме�?ец (Control+Up/Down за преме�?тване по години)',
    todayTip          : "{0} (Spacebar)",
    format            : "d.m.y",
    okText            : "&#160;OK&#160;",
    cancelText        : "Отмени",
    startDay          : 1
  });
}

if(Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : "Страница",
    afterPageText  : "от {0}",
    firstText      : "Първа �?траница",
    prevText       : "Предишна �?траница",
    nextText       : "Следваща �?траница",
    lastText       : "По�?ледна �?траница",
    refreshText    : "Презареди",
    displayMsg     : "Показвайки {0} - {1} от {2}",
    emptyMsg       : '�?�?ма данни за показване'
  });
}

if(Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : "Минималната дължина на това поле е {0}",
    maxLengthText : "Мак�?ималната дължина на това поле е {0}",
    blankText     : "Това поле е задължително",
    regexText     : "",
    emptyText     : null
  });
}

if(Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    minText : "Минималната �?тойно�?т за това поле е {0}",
    maxText : "Мак�?ималната �?тойно�?т за това поле е {0}",
    nanText : "{0} не е валидно чи�?ло"
  });
}

if(Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : "�?едо�?тъпен",
    disabledDatesText : "�?едо�?тъпен",
    minText           : "Датата в това поле тр�?бва да е �?лед {0}",
    maxText           : "Датата в това поле тр�?бва да е преди {0}",
    invalidText       : "{0} не е валидна дата - тр�?бва да бъде във формат {1}",
    format            : "d.m.y",
    altFormats        : "d.m.y|d/m/Y|d-m-y|d-m-Y|d/m|d-m|dm|dmy|dmY|d|Y-m-d"
  });
}

if(Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : "Зареждане...",
    valueNotFoundText : undefined
  });
}

if(Ext.form.VTypes){
  Ext.apply(Ext.form.VTypes, {
    emailText    : 'Това поле тр�?бва да бъде емейл във формат "user@example.com"',
    urlText      : 'Това поле тр�?бва да бъде URL във формат "http:/'+'/www.example.com"',
    alphaText    : 'Това поле тр�?бва да �?ъдържа �?амо букви и _',
    alphanumText : 'Това поле тр�?бва да �?ъдържа �?амо букви, цифри и _'
  });
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Мол�?, въведете URL за връзката:',
    buttonTips : {
      bold : {
        title: 'Bold (Ctrl+B)',
        text: 'Удебел�?ва избрани�? тек�?т.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Italic (Ctrl+I)',
        text: 'Прави избрани�? тек�?т кур�?ив.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Underline (Ctrl+U)',
        text: 'Подчертава избрани�? тек�?т.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Уголеми тек�?та',
        text: 'Уголем�?ва размера на шрифта.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: '�?амали тек�?та',
        text: '�?амал�?ва размера на шрифта.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'Цв�?т на маркирани�? тек�?т',
        text: 'Промен�? фонови�? цв�?т на избрани�? тек�?т.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Цв�?т на шрифта',
        text: 'Промен�? цвета на избрани�? тек�?т.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Л�?во подравн�?ване',
        text: 'Подравн�?ва тек�?та на л�?во.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'Центриране',
        text: 'Центрира тек�?та.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Д�?�?но подравн�?ване',
        text: 'Подравн�?ва тек�?та на д�?�?но.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: '�?еномериран �?пи�?ък',
        text: 'Започва неномериран �?пи�?ък.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: '�?омериран �?пи�?ък',
        text: 'Започва номериран �?пи�?ък.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'Хипервръзка',
        text: 'Превръща избрани�? тек�?т в хипервръзка.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'Редактиране на кода',
        text: 'Преминаване в режим на редактиране на кода.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : "Подреди в нара�?тващ ред",
    sortDescText : "Подреди в намал�?ващ ред",
    lockText     : "Заключи колона",
    unlockText   : "Отключи колона",
    columnsText  : "Колони"
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : "Име",
    valueText  : "Стойно�?т",
    dateFormat : "d.m.Y"
  });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : "Влачете �? мишката за да промените размера.",
    collapsibleSplitTip : "Влачете �? мишката за да промените размера. Чукнете два пъти за да �?криете."
  });
}
