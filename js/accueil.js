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

    svg.find('.minute-hand').attr('transform', 'rotate(' + (date.minutes() * 360 / 60) + ' 80, 80)');
    svg.find('.hour-hand').attr('transform', 'rotate(' + ((date.hours() * 60 + date.minutes()) * 360 / (24 * 60)) + ' 80, 80)');
    $(clockDiv).css('background-color', color);

}

function updateClocks() {
    $('.clock').each(function() {
        updateClock(this);
    });
    setTimeout("updateClocks()", 60 * 1000);
}
$(function () {
    updateClocks();
});