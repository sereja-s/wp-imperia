;(function ($) {
    /**
     * Open modal
     */
    $(document).on('click', '[data-oneclick-order--btn]', function (e) {
        e.preventDefault();

        var $this = $(this);

        var productId = $this.attr('data-oneclick-order--product-id');
        var variationId = $('[data-product_id="' + productId + '"]').find('[name="variation_id"]').val();
        var modalUrl = $this.data('oneclick-order--btn');

        modalUrl = modalUrl ? modalUrl : $this.attr('href');
        productId = variationId && variationId !== 0 ? variationId : productId;

        $.magnificPopup.close();
        $.magnificPopup.open({
            items: {
                src: modalUrl + '&product_id=' + productId
            },
            type: 'ajax',
            showCloseBtn: false,
            modal: false
        });

    });

    /**
     * Close modal
     */
    $(document).on('click', '[data-oneclick-order--btn-close]', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });

    /**
     * Add one click order
     */
    $(document).on('submit', '[data-oneclick-order--ajax]', function (e) {
        e.preventDefault();

        var form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method') ? form.attr('method') : 'get',
            data: form.serialize(),
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            },
            beforeSend: function () {
                form.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            success: function (data) {
              $('[data-oneclick-order--ajax]').replaceWith($(data.html).find('[data-oneclick-order--ajax]'));
            }
        });
    });

    /**
     * Disable button if variation is not purchasable
     */
    $( document ).on( "show_variation", function ( event, variation ) {
        var isDisabled = variation === undefined || (variation.is_purchasable === false || variation.is_in_stock === false);
        toggleOneClickOrderButton(isDisabled, $(event.target));
    });

    /**
     * Disable button if variation is not chosen
     */
    $(document).on("woocommerce_variation_select_change", function(event){
        toggleOneClickOrderButton(true,  $(event.target));
    });

    $(document).on('reset_data', function(event){
        toggleOneClickOrderButton(true, $(event.target));
    });

    function toggleOneClickOrderButton(isDisabled, wrapper){
        var entrySummary = wrapper.closest('.entry-summary');
        var button;

        if(entrySummary.length){
            button = entrySummary.find('[data-oneclick-order--btn]');
        }else{
            button = wrapper.closest('.product').find('[data-oneclick-order--btn]');
        }
        if(isDisabled){
            button.attr('disabled', true);
        }else{
            button.attr('disabled', false);
        }
    }

})(jQuery);