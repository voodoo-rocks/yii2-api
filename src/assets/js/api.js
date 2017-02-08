$(document).ready(function () {
    $('.json').each(function (i, block) {
        hljs.highlightBlock(block);
    });

    var clipboard = new Clipboard('.btn');

    clipboard.on('success', function (e) {
        e.clearSelection();
    });

    $('#execute').on('click', function () {

        var $btn = $(this).button('loading')

        function show(data, status) {
            $('.response-block .json').text(JSON.stringify(data, null, 4));
            $('.response-block').removeClass('panel-default panel-danger panel-success').addClass('panel-' + status);
        }

        $.ajax({
            url: $(this).attr('data-url'),
            method: 'post',
            data: $('#request-text').text(),
            contentType: 'application/json'
        })
            .success(function (data) {
                show(data, 'success');
            })
            .fail(function (data) {
                show(data['responseJSON'], 'danger');
            })
            .always(function () {
                $btn.button('reset');

                $('.response-block').removeClass('hidden');
                $('.response-block .json').each(function (i, block) {
                    hljs.highlightBlock(block);
                });
            });
    });

    (function ($) {
        $.fn.fireChangeEvents = function () {
            return this.each(function () {
                var $this = $(this);
                var htmlOld = $this.html();
                $this.bind('focus blur', function () {
                    var htmlNew = $this.html();
                    if (htmlOld !== htmlNew) {
                        $this.trigger('change');
                        htmlOld = htmlNew;
                    }
                })
            })
        }
    })(jQuery);

    $('.editable').fireChangeEvents().on('change', function () {

        try {
            $(this).text(JSON.stringify(JSON.parse($(this).text()), null, 4));
        } catch (err) {

        }

        $(this).each(function (i, block) {
            hljs.highlightBlock(block);
        });
    });
});