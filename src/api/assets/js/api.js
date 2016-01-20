$(document).ready(function () {
    $('.json').each(function (i, block) {
        hljs.highlightBlock(block);
    });

    $(document)

        .on('click', '.btn-api-call', function () {
            var timeBegin = new Date();

            var blockRequest = $('#' + $(this).attr('data-request'));
            var blockResponse = $('#' + $(this).attr('data-response')).removeClass('bg-danger bg-success');

            var callButton = $(this).button('loading');

            $.ajax({
                    method: "POST",
                    url   : $(this).attr('data-url'),
                    data  : blockRequest.text()
                })
                .done(function () {
                    $('.status').addClass('label-success');
                })
                .fail(function () {
                    $('.status').addClass('label-danger');
                })
                .always(function (data, status) {
                    $('.status').text(status);
                    $('.execution-time').text(new Date() - timeBegin);
                    blockResponse.text(JSON.stringify(data, null, 4));
                    blockResponse.each(function (i, block) {
                        hljs.highlightBlock(block);
                    });

                    callButton.button('reset');
                });
        })

        .on('click', '.toggle-properties', function (event) {
            $(this)
                .toggleClass('open')
                .next('.list-json')
                .slideToggle(100);
        })

        .on('click', '.run-tests', function (event) {
            $('.testable').each(function () {
                var $block = $(this);

                var $url = $(this).attr('data-url');
                var $action = $(this).attr('data-action');
                var $controller = $(this).attr('data-controller');

                $.get($url, {controller: $controller, action: $action})
                    .done(function () {
                        $block.addClass('list-group-item-success');
                    })
                    .fail(function () {
                        $block.addClass('list-group-item-danger');
                    })
            })
        })

        .on('mousedown', '.toggle-properties', function () {
            return false
        })

        .on('click', '.node-remover', function (event) {
            $(this).parent().nextUntil('br').each(function () {
                $(this).remove();
            });
            $(this).parent().next('br').remove();
            $(this).parent().remove();
        });
});