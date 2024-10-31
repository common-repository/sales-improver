(function( $ ) {
    'use strict';
    var body = $('body'),
        form = body.find('.sales-improver')
    ;
    form.on('click', '.nav-tab-wrapper .nav-tab', function (e){
        var that = $(this);

        if( that.hasClass("disabled") ) {
            e.preventDefault();
            return false;
        }

        that.addClass('nav-tab-active');
        that.siblings().removeClass('nav-tab-active');

        var content = form.find('.tab-content .tab-pane.' + that.attr('for'));
        content.addClass('active');
        content.siblings().removeClass('active');
    });

    body.on('submit', '.email-section', function (e) {
    	e.preventDefault();
        var that = $(this);
        $.post(that.attr('action'), that.serialize(), function (res){
            console.log(res);
            that.find('.email-section-notice').removeClass('d-none');
        }, 'text');
    });

})( jQuery );