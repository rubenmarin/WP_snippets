		$(document.body).on('click','a.insert-media',function(){
					textarea = $(this).parents('.wp-editor-wrap').find('textarea');   					
					mceID = textarea.attr('id');  
					wp.media.editor.insert = function(html){
						wpActiveEditor = mceID;
						return window.send_to_editor.apply( this, arguments );
					}
		});
