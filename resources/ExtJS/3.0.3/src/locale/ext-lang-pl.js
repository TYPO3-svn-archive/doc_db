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
 * Polish Translations
 * By vbert 17-April-2007
 * Updated by mmar 16-November-2007
 * Encoding: utf-8
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Wczytywanie danych...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
   Ext.grid.GridPanel.prototype.ddText = "{0} wybrano wiersze(y)";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Zamknij zakładkę";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Wartość tego pola jest niewłaściwa";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "Wczytywanie danych...";
}

Date.monthNames = [
    "Styczeń",
    "Luty",
    "Marzec",
    "Kwiecień",
    "Maj",
    "Czerwiec",
    "Lipiec",
    "Sierpień",
    "Wrzesień",
    "Październik",
    "Listopad",
    "Grudzień"
];

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  Sty : 0,
  Lut : 1,
  Mar : 2,
  Kwi : 3,
  Maj : 4,
  Cze : 5,
  Lip : 6,
  Sie : 7,
  Wrz : 8,
  Paź : 9,
  Lis : 10,
  Gru : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
    "Niedziela",
    "Poniedziałek",
    "Wtorek",
    "Środa",
    "Czwartek",
    "Piątek",
    "Sobota"
];

Date.getShortDayName = function(day) {
	switch(day) {
		case 0: return 'ndz';
		case 1: return 'pon';
		case 2: return 'wt';
		case 3: return 'śr';
		case 4: return 'czw';
		case 5: return 'pt';				
		case 6: return 'sob';
                default: return '';
	}
};

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Anuluj",
      yes    : "Tak",
      no     : "Nie"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "Y-m-d");
   };
}

if(Ext.DatePicker){
	Ext.apply(Ext.DatePicker.prototype, {
		startDay			: 1,
		todayText			: "Dzisiaj",
		minText				: "Data jest wcześniejsza od daty minimalnej",
		maxText				: "Data jest późniejsza od daty maksymalnej",
		disabledDaysText	: "",
		disabledDatesText	: "",
		monthNames			: Date.monthNames,
		dayNames			: Date.dayNames,
		nextText			: "Następny miesiąc (Control+StrzałkaWPrawo)",
		prevText			: "Poprzedni miesiąc (Control+StrzałkaWLewo)",
		monthYearText		: "Wybierz miesiąc (Control+Up/Down aby zmienić rok)",
		todayTip			: "{0} (Spacja)",
		format				: "Y-m-d",
		okText            	: "&#160;OK&#160;",
    	cancelText        	: "Anuluj",
    	startDay          	: 1
	});
}

if(Ext.PagingToolbar){
	Ext.apply(Ext.PagingToolbar.prototype, {
		beforePageText	: "Strona",
		afterPageText	: "z {0}",
		firstText		: "Pierwsza strona",
	    prevText		: "Poprzednia strona",
		nextText		: "Następna strona",
	    lastText		: "Ostatnia strona",
		refreshText		: "Odśwież",
	    displayMsg		: "Wyświetlono {0} - {1} z {2}",
		emptyMsg		: "Brak danych do wyświetlenia"
	});
}

if(Ext.form.TextField){
	Ext.apply(Ext.form.TextField.prototype, {
	    minLengthText	: "Minimalna ilość znaków dla tego pola to {0}",
		maxLengthText	: "Maksymalna ilość znaków dla tego pola to {0}",
	    blankText		: "To pole jest wymagane",
		regexText		: "",
	    emptyText		: null
	});
}

if(Ext.form.NumberField){
	Ext.apply(Ext.form.NumberField.prototype, {
	    minText	: "Minimalna wartość dla tego pola to {0}",
	    maxText	: "Maksymalna wartość dla tego pola to {0}",
		nanText	: "{0} to nie jest właściwa wartość"
	});
}

if(Ext.form.DateField){
	Ext.apply(Ext.form.DateField.prototype, {
	    disabledDaysText	: "Wyłączony",
	    disabledDatesText	: "Wyłączony",
		minText				: "Data w tym polu musi być późniejsza od {0}",
	    maxText				: "Data w tym polu musi być wcześniejsza od {0}",
		invalidText			: "{0} to nie jest prawidłowa data - prawidłowy format daty {1}",
	    format				: "Y-m-d",
    	altFormats    	    : "m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
	});
}

if(Ext.form.ComboBox){
	Ext.apply(Ext.form.ComboBox.prototype, {
		loadingText       : "Wczytuję...",
		valueNotFoundText : undefined
	});
}

if(Ext.form.VTypes){
	Ext.apply(Ext.form.VTypes, {
	    emailText		: 'To pole wymaga podania adresu e-mail w formacie: "nazwa@domena.pl"',
	    urlText			: 'To pole wymaga podania adresu strony www w formacie: "http:/'+'/www.domena.pl"',
		alphaText		: 'To pole wymaga podania tylko liter i _',
		alphanumText	: 'To pole wymaga podania tylko liter, cyfr i _'
	});
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Wprowadź adres URL strony:',
    buttonTips : {
      bold : {
        title: 'Pogrubienie (Ctrl+B)',
        text: 'Ustaw styl zaznaczonego tekstu na pogrubiony.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Kursywa (Ctrl+I)',
        text: 'Ustaw styl zaznaczonego tekstu na kursywę.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Podkreślenie (Ctrl+U)',
        text: 'Podkreśl zaznaczony tekst.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Zwiększ czcionkę',
        text: 'Zwiększ rozmiar czcionki.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'Zmniejsz czcionkę',
        text: 'Zmniejsz rozmiar czcionki.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'Wyróżnienie',
        text: 'Zmień kolor wyróżnienia zaznaczonego tekstu.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Kolor czcionki',
        text: 'Zmień kolor zaznaczonego tekstu.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Do lewej',
        text: 'Wyrównaj tekst do lewej.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'Wyśrodkuj',
        text: 'Wyrównaj tekst do środka.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Do prawej',
        text: 'Wyrównaj tekst do prawej.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'Lista wypunktowana',
        text: 'Rozpocznij listę wypunktowaną.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: 'Lista numerowana',
        text: 'Rozpocznij listę numerowaną.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'Hiperłącze',
        text: 'Przekształć zaznaczony tekst w hiperłącze.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'Edycja źródła',
        text: 'Przełącz w tryb edycji źródła.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.grid.GridView){
	Ext.apply(Ext.grid.GridView.prototype, {
	    sortAscText		: "Sortuj rosnąco",
	    sortDescText	: "Sortuj malejąco",
		lockText		: "Zablokuj kolumnę",
	    unlockText		: "Odblokuj kolumnę",
		columnsText		: "Kolumny"
	});
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(None)',
    groupByText    : 'Grupuj po tym polu',
    showGroupsText : 'Pokaż w grupach'
  });
}

if(Ext.grid.PropertyColumnModel){
	Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
	    nameText	: "Nazwa",
	    valueText	: "Wartość",
		dateFormat	: "Y-m-d"
	});
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
	Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
	    splitTip			: "Przeciągnij aby zmienić rozmiar.",
		collapsibleSplitTip	: "Przeciągnij aby zmienić rozmiar. Kliknij dwukrotnie aby ukryć."
	});
}
