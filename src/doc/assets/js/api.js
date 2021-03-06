$(document).ready(function () {
    $('.json').each(function (i, block) {
        hljs.highlightBlock(block);
    });

    var clipboard = new Clipboard('.btn');

    clipboard.on('success', function (e) {
        e.clearSelection();
    });

    $('#execute').on('click', function () {
        function show(data) {
            $('.response-block .json').text(JSON.stringify(data, null, 4));
        }

        $(this).text('Executing...').prop('disabled', true);

        var ajaxTime = new Date().getTime();

        $.ajax({
            url        : $(this).attr('data-url'),
            method     : 'post',
            data       : $('#request-text').text(),
            contentType: 'application/json',
            success    : function (data) {
                show(data, 'success');
            },
            error       : function (data) {
                show(data['responseJSON'], 'danger');
            },
            complete    : function (data) {
                var totalTime = (new Date().getTime() - ajaxTime) / 1000;
                $('.response-status').text(' status: '+ data.status +',');
                $('.exec-time').text(totalTime);
                $('.response-wrapper').removeClass('hidden');
                $('.response-block .json').each(function (i, block) {
                    hljs.highlightBlock(block);
                });
                $('#execute').text('Execute').prop('disabled', false);
            }
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

    $('.methods-filter').focus();

    $('.methods-filter').jSearch({
        selector  : '.list-group',
        child : '.list-group-item',
        minValLength: 0,
        Before: function(){
            $('.card').data('find','');
        },
        Found : function(elem, event){
            $(elem).show();
            $(elem).parent().parent().data('find','true');
            $(elem).parent().parent().show();
        },
        NotFound : function(elem, event){
            $(elem).hide();
            if (!$(elem).parent().parent().data('find'))
                $(elem).parent().parent().hide();
        }
    });

    $('.methods-filter').jSearch({
        selector  : '.card',
        child : '.card-header',
        minValLength: 0,
        Found : function(elem, event){
            $(elem).show();
            $(elem).parent().data('find','true');
            $(elem).parent().show();
            $(elem).parent().children('.list-group').children().show()
        }
    });

});