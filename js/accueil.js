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

    svg.find('.minute-hand').attr('transform', 'rotate(' + (180 + date.minutes() * 360 / 60) + ' 80, 80)');
    svg.find('.hour-hand').attr('transform', 'rotate(' + (180 + (date.hours() * 60 + date.minutes()) * 360 / (24 * 60)) + ' 80, 80)');
    $(clockDiv).css('background-color', color);
    $(clockDiv).find('.clock-date').text(date.format('dddd, MMMM DD, YYYY'));
    $(clockDiv).find('.clock-ampm').text(date.format('A'));
}

function updateClocks() {
    $('.clock').each(function() {
        updateClock(this);
    });
    setTimeout("updateClocks()", 60 * 1000);
}
$(function () {
    updateClocks();

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


});