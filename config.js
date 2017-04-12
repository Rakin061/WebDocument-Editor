/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
 
 
 //CKEDITOR.plugins.addExternal('table','/path/to/nid_fomrs/','plugin.js');

//CKEDITOR.dialog.addUIElement( 'text', textBuilder );

 


CKEDITOR.editorConfig = function( config ) {
	
	// %REMOVE_START%
	// The configuration options below are needed when running CKEditor from source files.
	config.plugins = 'dialogui,dialog,about,a11yhelp,dialogadvtab,basicstyles,bidi,blockquote,clipboard,button,panelbutton,panel,floatpanel,colorbutton,colordialog,templates,menu,contextmenu,copyformatting,div,resize,toolbar,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,flash,floatingspace,listblock,richcombo,font,format,horizontalrule,htmlwriter,iframe,wysiwygarea,image,indent,indentblock,indentlist,smiley,justify,menubutton,language,link,list,liststyle,magicline,maximize,newpage,pagebreak,pastetext,pastefromword,preview,print,removeformat,save,selectall,showblocks,showborders,sourcearea,specialchar,scayt,stylescombo,tab,undo,wsc';
	   //config.removePlugins= 'table,tabletools';
      config.extraPlugins = 'docprops,timestampmp,lineheight,forms,table,tabletools,devtools';
	  //config.removePlugins= 'tabletools';
	  config.allowedContent= true;
	

	  //config.removePlugins = 'forms'; 
	 // config.extraPlugins = 'forms';
	
	
	
	
	
	
	
	config.skin = 'office2013';
	
	config.toolbarCanCollapse = true;
	config.toolbarGroupCycling = true;
	
	config.forcePasteAsPlainText = true;
	// %REMOVE_END%

	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};



 CKEDITOR.on( 'dialogDefinition', function( evt ) {
	var editor = evt.editor,
		dialog = evt.data,
		onOk = dialog.definition.onOk;
	
	// Modify only table dialog.
	if ( dialog.name !== 'table' ) {
		return;
	}
	
	// onOk callback is called when someone click "Ok" button;
	// in most cases it contains most of the logic of the dialog.
	dialog.definition.onOk = function() {
		onOk.call( this );
		
		setTimeout( function( evt ) {
			// When new table is inserted, the cursor is put
			// inside the first cell, so to get all cells
			// we must get the table element
			// from the selected celland fetch all cells
			// from it.
			var cells = CKEDITOR.plugins.tabletools.getSelectedCells( editor.getSelection() )[ 0 ].getAscendant( 'table' ).find( 'td, th' );
                         
                        //alert("Salman"+contents.elements.children.children.default
                                //);
			//alert("salman");			
			for ( var i = 0; i < cells.count(); i++ ) {
				//cells.getItem( i ).setStyle( 'width', '250px' );
			}
		}, 1 );
	};
} );

//CKEDITOR.replace( 'editor1' );

