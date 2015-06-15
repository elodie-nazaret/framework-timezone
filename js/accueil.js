function updateClockSvg(svg, date) {
    svg.find('.second-hand').attr('transform', 'rotate(' + ((180 + date.seconds() / 60 * 360) % 360) + ' 80, 80)');
    svg.find('.minute-hand').attr('transform', 'rotate(' + ((180 + date.minutes() / 60 * 360) % 360) + ' 80, 80)');
    svg.find('.hour-hand').attr('transform', 'rotate(' + ((180 + (date.hours() + date.minutes() / 60) / 12 * 360 ) % 360) + ' 80, 80)');
}

function updateClock(clockDiv) {
    var date = moment.tz(moment(), $(clockDiv).find('.clock-timezone').text());

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

    updateClockSvg($(clockDiv).find('svg'), date);

    $(clockDiv).css('background-color', color);
    $(clockDiv).find('.clock-date').text(date.format('dddd, MMMM DD, YYYY'));
    $(clockDiv).find('.clock-ampm').text(date.format('A'));

    $(clockDiv).find('.clock-digital').text(date.format('HH') + ':' + date.format('mm') + ':' + date.format('ss'));
    //$(clockDiv).find('.clock-digital-second').text(date.format('ss'));
    //$(clockDiv).find('.clock-digital-minute').text(date.format('mm'));
    //$(clockDiv).find('.clock-digital-hour').text(date.format('HH'));
}

function updateDetailClock() {
    var modal = $('#modal-details');

    if ($(modal).is(':visible')) {
        var date = moment.tz(moment(), $(modal).find('.detail-timezone').text());
        updateClockSvg($(modal).find('.detail-clock svg'), date);
    }
}

function updateClocks() {
    $('.clock').each(function() {
        updateClock(this);
    });

    updateDetailClock();
    setTimeout("updateClocks()", 1000);
}

$(function () {
    updateClocks();

    $('#clocks').sortable({
        tolerance: 'pointer',
        stop: function(event, ui) {
            $.ajax({
                url: 'update_order',
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

        if (viewName.hasClass('glyphicon-th')) {
            viewName.removeClass('glyphicon-th');
            viewName.addClass('glyphicon-th-list');
            viewName.attr('title', 'Passer en vue liste').tooltip('fixTitle').tooltip('show');
        } else {
            viewName.removeClass('glyphicon-th-list');
            viewName.addClass('glyphicon-th');
            viewName.attr('title', 'Passer en vue grille').tooltip('fixTitle').tooltip('show');
        }
    });

    $('#button-switch-clock').click(function () {
        var clockName = $('#next-clock-name');

        $('.clock-analog, .clock-ampm, .clock-digital').toggle();

        if (clockName.hasClass('glyphicon-time')) {
            clockName.removeClass('glyphicon-time');
            clockName.html('<span style="font-family: ds-digi; font-size: 1em">12:00</span>');
            clockName.attr('title', 'Passer en horloge digitale').tooltip('fixTitle').tooltip('show');
        } else {
            clockName.addClass('glyphicon-time');
            clockName.html('');
            clockName.attr('title', 'Passer en horloge analogique').tooltip('fixTitle').tooltip('show');
        }
    });

    $('#button-refresh').click(function () {
        $('.clock').each(function() {
            var clock = $(this);
            $.ajax({
                type : 'POST',
                url : 'weather_ajax',
                dataType: 'JSON',
                data : { city : clock.find('.clock-city').text(), country : clock.find('.clock-country').text()},
                success : function(data){
                    clock.find('.clock-weather').html(data['icon']);
                    clock.find('.clock-temp').html(data['temp']);
                }
            });
        });
    });

    $('#modal-gestion').modal({
        show: false
    });

    $('#modal-details').modal({
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

    $('#search').keyup( function() {a
        var fieldValue = $(this).val().toLowerCase();

        $('#results>div').show();
        $('#results h4').each(function() {
            var text = $(this).text().toLowerCase().split(', ');

            if (text[0].indexOf(fieldValue) == -1 && text[1].indexOf(fieldValue) == -1) {
                $(this).parent().parent().parent().hide();
            }
        });
    });

    $('[data-toggle="tooltip"]').tooltip()

    $('.clock').on('click', function() {
        var clock = $(this);

        $.ajax({
            type : 'POST',
            url : 'weather_ajax',
            dataType: 'JSON',
            data : { city : clock.find('.clock-city').text(), country : clock.find('.clock-country').text()},
            success : function(data){
                $('.detail-humidity').text('Humidité : ' + data['humidity']);
                $('.detail-pressure').text('Pression : ' + data['pressure']);
                $('.detail-temp-min').text('Température min : ' + data['minTemp']);
                $('.detail-temp-max').text('Température max : ' + data['maxTemp']);
                $('.detail-wind').text('Vent : ' + data['wind']);
            }
        });

        $('.detail-city').text(clock.find('.clock-city').text());
        $('.detail-country').text(clock.find('.clock-country').text());
        $('.detail-date').text(clock.find('.clock-date').text());
        $('.detail-timezone-offset').text(clock.find('.clock-timezone-offset').text());
        $('.detail-timezone').text(clock.find('.clock-timezone').text());
        $('.detail-temp-current').text('Température actuelle : ' + clock.find('.clock-temp').text());
        $('.detail-weather').html('Météo : ' + clock.find('.clock-weather').html());
        //$('.detail-clock').html(clock.find('.clock-clock>svg').html());
        //$('.detail-clock').html('');
        $('.detail-clock').html(clock.find('.clock-clock>svg').clone().show());
        //$('.clock-clock>svg').clone().appendTo('.detail-clock');

        $('#modal-details-content').css('background-color', clock.css('background-color'));
        $('#modal-details-content').css('color', 'white');
        $('#modal-details').modal('show');
    });

    $('')
});