var SERVER_URL = 'http://146.185.145.189';

$(function () {
    $('.js-amount').on('input', function () {
        var feePercent = 1,
            transactionFee = 0.0001,
            value = parseFloat($(this).val()),
            $totalInput = $(this).parents('form').find('.js-total'),
            $receiverInput = $(this).parents('form').find('.js-total-receiver'),
            receiverValue;

        receiverValue = value - transactionFee * 2 - (value / 100 * feePercent);

        value = Number((value).toFixed(8));
        receiverValue = Number((receiverValue).toFixed(8));

        $totalInput.val(value);
        $receiverInput.val(receiverValue);
    });

    $('.js-amount').each(function (k, el) {
        $(el).trigger('input');
    });

    $('.js-balance').each(function (k, el) {
        updateBalance($(el));
    });

    function updateBalance($el) {
        var label = $el.parents('.tab-pane').attr('id'),
            $balance = $el.find('.balance'),
            $pending = $el.find('.pending');

        $.get(SERVER_URL + '?action=balance&label=' + label, function (data) {
            $balance.text(data.balance);
            $pending.text(data.pending);
        });
    }

    $('form').submit(function (e) {
        e.preventDefault();

        if (!$(this).data('sending')) {
            $(this).data('sending', true);

            $.ajax({
                type: 'post',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function (data) {
                    $('.js-balance').each(function (k, el) {
                        updateBalance($(el));
                    });
                },
                error: function () {
                    location.reload();
                }
            });
        }
    });
});
