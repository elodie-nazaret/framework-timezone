$(function() {
    $('#modal-connection').modal({
        show: false
    });
    
    $('.reset-form-connection').click(function() {
        $('#form-connection')[0].reset();
    });
    
    $('#button-signup').click(function() {
        var modal = $('#modal-connection');
        
        modal.find('.modal-title').text('Inscription');

        modal.find('[name="type"]').val('signup');
        modal.modal('show');
    });

    $('#button-signin').click(function() {
        var modal = $('#modal-connection');
        modal.find('.modal-title').text('Connexion');
        modal.find('[name="type"]').val('signin');
        modal.modal('show');
    });
});
