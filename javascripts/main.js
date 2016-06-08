/**
 * Created by danil on 08.06.16.
 */
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
});
