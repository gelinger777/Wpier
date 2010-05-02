/**
 * Simple Carousel
 * Copyright (c) 2010 Tobias Zeising, http://www.aditu.de
 * Licensed under the MIT license
 * Version 0.2
 */
(function($){
$.fn.simplecarousel = function( params ) {
    // set config
    var defaults = {
        width: 700,
        height: 500,
        next: false,
        prev: false,
        vertical: false,
        auto: false,
        fade: false,
        current: 0,
        items: 0,
        slidespeed: 600,
        visible: 1
    };
    var config = $.extend(defaults, params);
    
    // configure carousel ul and li
    var ul = $(this);
    var li = ul.children('li');
    
    config.items = li.length;
    
    var height = config.height;
    var width = config.width;
    if(config.visible>1) {
        if(config.vertical)
            height = height*config.visible;
        else
            width = width*config.visible;
    }
    
    ul.wrap('<div class="carousel-frame" style="width:'+width+'px;height:'+height+'px;overflow:hidden">');
    if(!config.vertical) {
        ul.width(config.items*config.width);
        ul.height(config.height);
    } else {
        ul.width(config.width);
        ul.height(config.items*config.height);
    }
    ul.css('overflow','hidden');
    
    li.each(function(i,item) {
        $(item).width(config.width);
        $(item).height(config.height);
        if(!config.vertical)
            $(item).css('float','left');
    });
    
    // loads 2 previous and next images
    var loadimage = function() {
        var current = ul.children('li:nth-child('+config.current+')');
        for(var i = -2; i<=2; i++) {
            var current = config.current + i;
            if(current<0)
                current = config.items + current;
            var preload = ul.children('li:nth-child('+current+')');
            var img = preload.children('img');
            img.attr('src', img.attr('ref'));
        }
    }
    
    // function for sliding the carousel
    var slide = function(dir, click) {
        if(typeof click == "undefined" & config.auto==false)
            return;
    
        if(dir=="next") {
            config.current += config.visible;
            if(config.current>=config.items)
                config.current = 0;
        } else {
            config.current -= config.visible;
            if(config.current<0)
                config.current = (config.visible==1) ? config.items-1 : config.items-config.visible+(config.visible-(config.items%config.visible));
        }
        
        // fade
        if(config.fade!=false) {
            ul.fadeOut(config.fade, function() {
                ul.css({marginLeft: -1.0*config.current*config.width});
                ul.fadeIn(config.fade);
            });
            
        // slide
        } else {
            if(!config.vertical)
                ul.animate( {marginLeft: -1.0*config.current*config.width}, config.slidespeed );
            else
                ul.animate( {marginTop: -1.0*config.current*config.height}, config.slidespeed );
        }
        
        loadimage();
        
        if(typeof click != "undefined")
            config.auto = false;
        
        if(config.auto!=false)
            setTimeout(function() {
                slide('next');
            }, config.auto);
    }
    
    // set event handler for next and prev
    if(config.next!=false)
        config.next.click(function() {
            slide('next',true);
        });
        
        
    if(config.prev!=false)
        config.prev.click(function() {
            slide('prev',true);
        });
    
    // start auto sliding
    if(config.auto!=false)
        setTimeout(function() {
            slide('next');
        }, config.auto);
}
})(jQuery);