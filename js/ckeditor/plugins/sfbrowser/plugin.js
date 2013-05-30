/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function()
{
	CKEDITOR.plugins.add( 'sfbrowser',
	{
		init : function( editor )
		{
			var lang = editor.lang;
			var mainDocument = CKEDITOR.document,
				mainWindow = mainDocument.getWindow();

			editor.addCommand( 'sfbrowser',
				{
					exec : function()
					{
						function addFiles(aFiles){
							for(var i in aFiles){
								editor.insertHtml("<img src=\"/"+aFiles[i].file+"\" />");
							};						
						}
						$.sfb({select:addFiles,plugins:['filetree','imageresize'],allow:['jpeg','png','gif','jpg','JPG','JPEG','PNG','GIF'],swfupload:true,preview:true,bgcolor:'#CEE9F4',bgalpha:.8});
					}
				} );

			editor.ui.addButton( 'SFbrowser',
				{
					label : 'Файловый менеджер',
					command : 'sfbrowser',
                                        icon: this.path + 'images/maximizeme.png'
				} );

		}
	} );
})();
