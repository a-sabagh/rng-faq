jQuery(document).ready(function ($) {
    var select2_node = $(".faq-select2");
    if (typeof FAQ_Object != "undefined" && select2_node.length) {
        var ajaxUrl = FAQ_Object.ajaxUrl;
        /* search product */
        $(".faq-select2").select2({
            data: {
                id: '-1', // the value of the option
                text: 'Select an option'
            },
            minimumInputLength: 3,
            ajax: {
                url: ajaxUrl,
                type: "post",
                dataType: 'json',
                data: function (params) {
                    return {
                        s: params.term
                    };
                },
                processResults: function (response) {
                    console.log(response);
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    }
});