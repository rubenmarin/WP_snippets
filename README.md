WP_snippets
===========


using the media_upload.js
```javascript
//JS
	$(document).on( 'click' , 'input.button' , function (e){
		var self = $(this);
		wp__media.mu( self , 
			{
			multiple : false,
			func : function( media , el){
				//WRITE your own function here :) 
				
				//el = element clicked => in wp__media.mu(self, {}) self = el
				
				media = media[0];//object is passed in array, 0 to get object
				//console.log(media);
				// assuming that the input field is in the parent div.box
				
				el.parent('div.box').find('input.myimgfield').val(media.url);
			},
		});
	});	
```

using the winresize.cycle.js

slector : value must be string. selector separated by comma [example]  selector : '#slideshow , #slideshow1 , #newsfeed'
```javascript
//JS

var pauseCycle = cyclePS({
	selector : '#slideshow',
	pauseWidth : 640,
});

```
