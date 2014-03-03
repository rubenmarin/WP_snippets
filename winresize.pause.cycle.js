(function(){
	this.cyclePS = ( function($){ 
		var _opts;
		var _width;
		function cyclePS (settings){
			cyclePS.opts(settings);
			cyclePS.init();	
		}
		cyclePS.init = function(){
			cyclePS.resize();
		};
		// override options + defaults (uses jquery)
		cyclePS.opts = function(settings){
			opts = {
				selector : '',// value must be string. selector separated by comma [example]  selector : '#slideshow , #slideshow1 , #newsfeed'
				pauseWidth : 640,
			};
			opts = $.extend(true, opts , settings);
			this._opts = opts;
		};
		
		cyclePS.pause = function(){
			console.log('ok!');
			var width = window.innerWidth;	
			opts = this._opts;
			if( opts.selector != ''){
				var slides = $(opts.selector);	
				if( width <= opts.pauseWidth ){ if(slides.length){ slides.cycle('pause'); } } else{ if(slides.length){ slides.cycle('resume'); } }	
			}
			return this;
		};

		cyclePS.resize = function(){
			cyclePS.deploy();
			var evnt;
			evnt = window.addEventListener || window.attachEvent;
			evnt('resize', function() {
				cyclePS.deploy();
			});
		};			
		
		cyclePS.deploy = function(){
			cyclePS.pause();	
		};

		return cyclePS;	
	})(jQuery);
}).call(this);
