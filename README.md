WP_snippets
===========


for media_upload
//JS
	$(document).on( 'click' , 'input.button' , function (e){
		var self = $(this);
		wp__media.mu( self , 
			{
			multiple : false,
			func : function( media , el){
				//el = element clicked => in wp__media.mu(self, {}) self = el
				media = media[0];//object is passed in array, 0 to get object
				//console.log(media);
				// assuming that the input field is in the parent div.box
				el.parent('div.box').find('input.myimgfield').val(media.url);
			},
		});
	});	
