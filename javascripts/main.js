var SERVER_URL = 'http://localhost:8000';

$(function () {
    $('.js-amount').on('input', function () {
        var feePercent = 1,
            transactionFee = 0.0001,
            value = parseFloat($(this).val());

        value += value / 100 * feePercent;
        value += transactionFee;
        value = Number((value).toFixed(8));

        $('.js-total').val(value);
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
});