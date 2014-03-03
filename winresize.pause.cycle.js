(function(){
	this.cyclePS = ( function($){ 
		var _opts;
		function cyclePS (settings){
			cyclePS.opts(settings);
			cyclePS.run();	
		}
		cyclePS.run = function(){
			window.onresize();// calls when page loaded and when resizing
		};	
		cyclePS.opts = function(settings){
			opts = {
				selector : '',// value must be string. selector separated by comma [example]  selector : '#slideshow , #slideshow1 , #newsfeed'
				pauseWidth : 640,
			};
			opts = $.extend(true, opts , settings);
			this._opts = opts;
		};// override options + defaults
		cyclePS.pause = function(){
			
			var width = window.innerWidth;	
			opts = this._opts;
			if( opts.selector != ''){
				var slides = $(opts.selector);	
				if( width <= opts.pauseWidth ){ if(slides.length){ slides.cycle('pause'); } } else{ if(slides.length){ slides.cycle('resume'); } }	
			}
			return this;
		};
		window.onresize = function(){						
			cyclePS.pause();
		};
		return cyclePS;	
	})(jQuery);
}).call(this);
