// based on http://www.mojowill.com/developer/using-the-new-wordpress-3-5-media-manager-in-your-plugin-or-theme/
//requires Jquery
var wp__media = ( function($){
	var m;
	var op = {
		classN : 'wp__media-frame',
		multiple : true,
		library : '',//empty for all types || images only library : { type : 'image'}
	};
	return {
			// el = object/element 
			// p = params
		mu : function( el , p){
			// If the frame already exists, re-open it.
			var p =  $.extend({}, op, p);//combine op/p

			if ( m ) {
				m.open();
				return;
			}

			classN = (p.classN) ? p.classN + ' ': '';
			
			m = wp.media.frames.m = wp.media({
				//Create our media frame
				className: classN + 'media-frame',
				frame: 'select', //Allow Select Only
				multiple: p.multiple, //Disallow/Allow Multiple selections
				//Only allow images
				library: p.library,
			});

			m.on('select', function(){
				var media_attch = m.state().get('selection').toJSON();
				
				wp__media._func(p.func , media_attch , el );//we send it to the _add where 
				
			});

			m.open();

		},
		_func : function(f , m , e){
			t = this;
			f.apply(t , [m , e]);
		},

	};

})(jQuery);// end of WP Media Module , 3.5+
