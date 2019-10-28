"use strict";
(function($) {
    var $modal = $('#modal-credit-note-delete');

    $modal.on('show.bs.modal', function(event){
        var url = $(event.relatedTarget).data('ajax-url');

        $modal.find('form').attr('action', url);
    });

})(jQuery);

(function($){
    var $modal = $('#modal-credit-note');
    var currentRequest;
    var lastAjaxUrl;
    // fix bug bootstrap 3 and select2
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    /****** Modal methods ******/
    $modal.loaderOff = function(){
        $modal.find('.modal-loader').addClass('hidden');
        $modal.find('.modal-body').removeClass('hidden');
    };

    $modal.loaderOn = function(){
        $modal.find('.modal-loader').removeClass('hidden');
        $modal.find('.modal-body').addClass('hidden');
    };

    $modal.reset = function(){
        $modal.hideError();
        $modal.loaderOn();
    };

    $modal.loadAjax = function(event, data){
        if (typeof data === 'undefined') {
            data = {};
        }

        // kill last ajax request if not if it's not finished
        if (currentRequest) currentRequest.abort();

        var url = $(event.relatedTarget).data('ajax-url');
        if (typeof url === 'undefined') {
            url = lastAjaxUrl;
        } else {
            lastAjaxUrl = url;
            $modal.loaderOn();
        }

        if ($(event.relatedTarget).data('success-url')) {
            data['success-url'] = $(event.relatedTarget).data('success-url');
        }

        if (!data['credit-note-create[action]']) {
            data['credit-note-create[action]'] = 'view';
        }

        if ($(event.relatedTarget).data('credit-note-id')) {
            data['credit-note-create[credit_note_id]'] = $(event.relatedTarget).data('credit-note-id');
        }

        if ($(event.relatedTarget).data('order-id')) {
            data['credit-note-create[order_id]'] = $(event.relatedTarget).data('order-id');
        }

        if ($(event.relatedTarget).data('order-product-id')) {
            data['credit-note-create[order_product_quantity][' + $(event.relatedTarget).data('order-product-id') + ']'] = 1;
        }

        if ($(event.relatedTarget).data('type-id')) {
            data['credit-note-create[type_id]'] = $(event.relatedTarget).data('type-id');
        }

        if ($(event.relatedTarget).data('customer-id')) {
            data['credit-note-create[customer_id]'] = $(event.relatedTarget).data('customer-id');
        }

        // to avoid a display bug with select2
        setTimeout(function(data){
            // ajax start
            currentRequest = $.ajax({
                url: url,
                data: data,
                method: 'POST'
            });

            // ajax success
            currentRequest.done(function(data){
                $modal.loaderOff();
                $modal.find('.modal-body').html(data);
                $modal.modalReady();
            });

            // ajax error
            currentRequest.fail(function(jqXHR, textStatus){
                $modal.displayError(jqXHR, textStatus);
            });
        }, 100, data);
    };

    $modal.displayError = function(jqXHR, textStatus){
        if (jqXHR.statusText === 'abort') return;
        $modal.loaderOff();
        $modal.find('.modal-body').addClass('hidden');
        var $error = $modal.find('.modal-error').removeClass('hidden');
        $error.find('.textStatus').html(textStatus);
        $error.find('iframe').contents().find('html').html(jqXHR.responseText);
    };

    $modal.hideError = function(){
        $modal.find('.modal-error').addClass('hidden').find('iframe').contents().find('html').empty();
    };
    /****** End Modal methods ******/

    /****** Modal events ******/
    $modal.on('show.bs.modal', function(event){
        $modal.loadAjax(event);
    });

    $modal.on('hidden.bs.modal', function(){
        $modal.reset();
        lastAjaxUrl = undefined;
    });
    /****** End Modal events ******/

    $modal.modalReady = function(){
        var $form = $modal.find('.modal-body form');

        var initSelect = function($target){
            return $target.select2({
                templateResult: function(data){
                    if (!data.id) return data.text;
                    var prefix = data.element.dataset.color ? '<span class="label" style="background-color: ' + data.element.dataset.color + ';width: 50px;">&nbsp;</span>' : '';
                    return $(prefix + '<span>' + data.text + '</span>');
                },
                templateSelection: function(data){
                    var prefix = data.element.dataset.color ? '<span class="label" style="background-color: ' + data.element.dataset.color + ';width: 50px;">&nbsp;</span>' : '';
                    return $(prefix + '<span>' + data.text + '</span>');
                }
            });
        };

        var initAjaxSelect = function($target){
            return $target.select2({
                ajax: {
                    url: $target.data('url'),
                    dataType: 'json',
                    delay: 250,
                    data: function (params){
                        return {
                            q: params.term,
                            customerId: $target.data('customer-id')
                        };
                    },
                    processResults: function (data){
                        return {results: data.items};
                    },
                    error: function(jqXHR, textStatus){
                        if (jqXHR.statusText === 'abort') return;
                        $target.select2('destroy');
                        $modal.displayError(jqXHR, textStatus);
                    },
                    cache: false
                },
                minimumInputLength: 3,
                placeholder: $target.data('placeholder'),
                templateResult: function(data){
                    if (data.loading) return data.text;

                    var markup = "<div class='select2-result-repository clearfix'>";
                    markup += data.ref + ' : (' + data.firstname + ' ' + data.lastname + ')' + '</br><small>' + data.address + '</small>';
                    markup += "</div>";

                    return $(markup);
                },
                templateSelection: function(data){
                    if (data.text) {
                        return data.text;
                    }

                    return data.ref + ' : (' + data.firstname + ' ' + data.lastname + ')';
                }
            });
        };

        var $selectType = initSelect($form.find('.js-select-type'));
        var $selectStatus = initSelect($form.find('.js-select-status'));
        var $selectCustomer = initAjaxSelect($form.find('.js-select-customer'));
        var $selectInvoiceAddress = initSelect($form.find('.js-select-invoice-address'));

        var $selectOrder = $form.find('.js-select-order');
        if ($selectOrder.data('customer-id')) {
            initSelect($selectOrder);
        } else {
            initAjaxSelect($selectOrder);
        }

        var getFormData = function(data){
            var formData = $form.serializeArray();

            for (var i in formData) {
                for (var e in data) {
                    if (formData[i].name  === e) {
                        formData[i].value = data[e];
                        delete data[e];
                    }
                }
            }

            for (var e in data) {
                formData.push({
                        name: i,
                        value: data[i]
                    }
                );
            }

            return formData
        };

        $selectOrder.on('select2:select', function(event){
            $modal.loadAjax(event, getFormData({
                'credit-note-create[order_id]': event.params.data.id,
                'credit-note-create[action]': 'refresh'
            }));
        });

        $selectCustomer.on('select2:select', function(event){
            $modal.loadAjax(event, getFormData({
                'credit-note-create[customer_id]': event.params.data.id,
                'credit-note-create[action]': 'refresh'
            }));
        });

        $selectStatus.on('select2:select', function(event){
            $modal.loadAjax(event, getFormData({
                'credit-note-create[action]': 'refresh'
            }));
        });

        $selectInvoiceAddress.on('select2:select', function(event){
            $modal.loadAjax(event, getFormData({
                'credit-note-create[action]': 'refresh'
            }));
        });

        $selectType.on('select2:select', function(event){
            $modal.loadAjax(event, getFormData({
                'credit-note-create[action]': 'refresh'
            }));
        });

        $form.on('change', '.js-field-currency', function(event){
            $modal.loadAjax(event, getFormData({
                'credit-note-create[action]': 'refresh'
            }));
        });

        $form.on('change', '.js-order-product-quantity', function(event){
            $modal.loadAjax(event, getFormData({
                'credit-note-create[action]': 'refresh'
            }));
        });

        $form.on('keyup change', '.js-action-refresh', function(event){
            if ($(this).val().length) {
                refreshWithTimer($form, event);
            }
        });

        /*******************************************/
        /*********** Table Free Amount *************/
        /*******************************************/
        var $tableFreeAmount = $form.find('.js-table-free-amount');
        var templateFreeAmount = $('#template-credit-note-free-amount').html();

        $tableFreeAmount.on('click', '.js-action-add', function(event){
            event.preventDefault();
            $(this).data('key', parseInt($(this).data('key')) + 1);

            var templateFreeAmountWithKey = templateFreeAmount.replace(/\[\]/g, '[' + $(this).data('key') + ']');
            $tableFreeAmount.find('tbody').append(templateFreeAmountWithKey);

            if ($tableFreeAmount.find('tbody tr').not('.js-no-free-amount').length) {
                $tableFreeAmount.find('.js-no-free-amount').addClass('hidden');
            } else {
                $tableFreeAmount.find('.js-no-free-amount').removeClass('hidden');
            }
        });

        $tableFreeAmount.on('click', '.js-action-delete', function(event){
            event.preventDefault();
            $(this).parents('tr').remove();

            if ($tableFreeAmount.find('tbody tr').not('.js-no-free-amount').length) {
                $tableFreeAmount.find('.js-no-free-amount').addClass('hidden');
            } else {
                $tableFreeAmount.find('.js-no-free-amount').removeClass('hidden');
            }

            $modal.loadAjax(event, getFormData({
                'credit-note-create[action]': 'refresh'
            }));
        });

        $tableFreeAmount.on('change', '.js-field-tax-rule', function(event){
            $(this).parents('tr').find('.js-field-amount-without-tax').trigger('keyup');
        });

        var currentRequestFreeAmount;
        $tableFreeAmount.on('keyup', '.js-field-amount-without-tax, .js-field-amount-with-tax', function(event){
            if (currentRequestFreeAmount) currentRequestFreeAmount.abort();

            var $th = $(this), $thr = $th.parents('tr');

            currentRequestFreeAmount = $.ajax({
                url: $(this).data('url'),
                dataType: 'json',
                data: {
                    price: parseFloat($(this).val()),
                    tax_rule: parseInt($thr.find('.js-field-tax-rule').val())
                }
            });

            // ajax success
            currentRequestFreeAmount.done(function(data){
                if ($th.hasClass('js-field-amount-without-tax')) {
                    $thr.find('.js-field-amount-with-tax').val(data.result);
                } else {
                    $thr.find('.js-field-amount-without-tax').val(data.result);
                }

                refreshWithTimer(event);
            });

            // ajax error
            currentRequestFreeAmount.fail(function(jqXHR, textStatus){
                if (jqXHR.statusText === 'abort') return;
                $modal.displayError(jqXHR, textStatus);
            });
        });

        var timer = null;
        function refreshWithTimer(event) {
            if (timer !== null) {
                clearTimeout(timer);
                timer = null;
            }
            timer = setTimeout(function(){
                $modal.loadAjax(event, getFormData({
                    'credit-note-create[action]': 'refresh'
                }));
            }, 700);
        }

        /*******************************************/
        /********* End Table Free Amount ***********/
        /*******************************************/
    };
})(
    jQuery
);
