/*function someClick (e) {
        console.log($('table').data('attr', 'bye').data('attr'));
    }
    
$(document).ready( function() {
    $.fx.off = true;
    $('table').attr('data-attr', 'hello');
    var els = $('.container').bind({
        'mouseenter' : function (e) {
            $('table').stop();
            $('table').delay(1000);
            $('table').css('position', 'absolute');
            $('table')
                    .animate({'left': "+=100"}, 5000)
                    .queue(function () {
                        console.log('moving finished');
                        $(this).dequeue();
                        })
                    .fadeOut('slow')
                    .fadeIn('slow');
        }
    });
    
});
  */      