function updateClock(clockDiv) {
    var date = moment.tz(moment(), $(clockDiv).find('.clock-timezone').text());
    var svg = $(clockDiv).find('svg');

    var hour = date.hours();
    var color = '';
    if (hour >= 8 && hour < 10 || hour >= 18 && hour < 20) {
        color = '#DD7632';
    }
    else if (hour >= 10 && hour < 12 || hour >= 16 && hour < 18) {
        color = '#E7A235';
    }
    else if (hour >= 12 && hour < 16) {
        color = '#EEB934';
    }
    else if (hour >= 6 && hour < 8 || hour >= 20 && hour < 22) {
        color = '#776CC6';
    }
    else if (hour >= 4 && hour < 6 || hour >= 22 && hour < 24) {
        color = '#5B52AA';
    }
    else {
        color = '#413D93';
    }

    svg.find('.second-hand').attr('transform', 'rotate(' + ((180 + date.seconds() / 60 * 360) % 360) + ' 80, 80)');
    svg.find('.minute-hand').attr('transform', 'rotate(' + ((180 + date.minutes() / 60 * 360) % 360) + ' 80, 80)');
    svg.find('.hour-hand').attr('transform', 'rotate(' + ((180 + (date.hours() + date.minutes() / 60) / 12 * 360 ) % 360) + ' 80, 80)');

    $(clockDiv).css('background-color', color);
    $(clockDiv).find('.clock-date').text(date.format('dddd, MMMM DD, YYYY'));
    $(clockDiv).find('.clock-ampm').text(date.format('A'));

    $(clockDiv).find('.clock-digital-second').text(date.format('ss'));
    $(clockDiv).find('.clock-digital-minute').text(date.format('mm'));
    $(clockDiv).find('.clock-digital-hour').text(date.format('HH'));
}

function updateClocks() {
    $('.clock').each(function() {
        updateClock(this);
    });
    setTimeout("updateClocks()", 1000);
}

$(function () {
    updateClocks();

    $('#clocks').sortable({
        tolerance: 'pointer',
        stop: function(event, ui) {
            $.ajax({
                url: './update_order.php',
                data: {
                    clockOrder: $(ui.item).index() + 1,
                    clockId: $(ui.item).find('.clock-id').text()
                },
                method: 'POST'
            });
        }
    });

    $('#button-switch-view').click(function() {
        var clocks = $('.clock');
        var viewName = $('#next-view-name');

        clocks.toggleClass('clock-grid clock-list');
        clocks.toggleClass('col-xs-6 col-sm-4 col-xs-12');

        if (viewName.text() == 'liste') {
            viewName.text('grille');
        } else {
            viewName.text('liste');
        }
    });

    $('#button-switch-clock').click(function () {
        var clockName = $('#next-clock-name');

        $('.clock-analog, .clock-ampm, .clock-digital').toggle();

        if (clockName.text() == 'digitale') {
            clockName.text('analogique');
        } else {
            clockName.text('digitale');
        }
    });

    $('#modal-gestion').modal({
        show: false
    });

    $('#button-manage').click(function() {
        var modal = $('#modal-gestion');
        modal.modal('show');
    });

    $('.reset-form-gestion').click(function() {
        $('#form-gestion')[0].reset();
    });


    $('#modal-create-clock').modal({
        show: false
    });

    $('#button-create-clock').click(function () {
        $('#modal-create-clock').modal('show');
    });

    $('#search').keyup( function(){
        $field = $(this);
        $('#results').html('');
        $('#ajax-loader').removeClass('hidden');

        if( $field.val().length > 1 )
        {
            $.ajax({
                type : 'POST',
                url : 'search.php',
                data : { search : $(this).val()},
                success : function(data){
                    $('#ajax-loader').addClass('hidden');
                    $('#results').html(data);
                }
            });
        } else if ($field.val().length == 0) {
            $.ajax({
                type : 'POST',
                url : 'search.php',
                data : { search : null},
                success : function(data){
                    $('#ajax-loader').addClass('hidden');
                    $('#results').html(data);
                }
            });
        }
    });

    $('.clock').on('click', function() {
        var modal = $('#modal-gestion');
        modal.modal('show');
    });
});