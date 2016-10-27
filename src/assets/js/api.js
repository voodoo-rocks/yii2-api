$(document).ready(function () {
    $('.json').each(function (i, block) {
        hljs.highlightBlock(block);
    });

    var clipboard = new Clipboard('.btn');

    clipboard.on('success', function (e) {
        e.clearSelection();
    });

    $('#execute').on('click', function () {
        function show(data, status) {
            $('.response-block .json').text(JSON.stringify(data, null, 4));
            $('.response-block').removeClass('panel-default').addClass('panel-' + status);
            $('.response-block .btn').removeClass('btn-default').addClass('btn-' + status);
        }

        $.ajax({
            url        : $(this).attr('data-url'),
            method     : 'post',
            contentType: 'application/json'
        })
            .success(function (data) { show(data, 'success');})
            .fail(function (data) {show(data['responseJSON'], 'danger');})
            .always(function () {

                $('.response-block').removeClass('hidden');
                $('.response-block .json').each(function (i, block) {
                    hljs.highlightBlock(block);
                });
            });
    });
});