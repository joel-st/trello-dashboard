(function ($) {
    function integrationTest() {
        $apiTestMetabox = $('#tvptd-options-trello-integration-api-test');
        if($apiTestMetabox.length && tvp_td_vars) {
            $.ajax({
                type: "GET",
                url: tvp_td_vars['ajax_url'],
                data: {
                    action: 'tvptd-options-trello-integration-integration-test'
                },
            }).success(function (response) {
                response = JSON.parse(response);
                console.log(response);
                $apiTestMetabox.removeClass('testing');
                $apiTestMetabox.addClass('connected');
                $apiTestMetabox.find('.spinner').remove();
                $apiTestMetabox.find('.label').html('Connected as <strong>' + response.username + '</strong>');
                $apiTestMetabox.find('figure').css('backgroundImage', 'url(' + response.avatarUrl + '/170.png)');
            })
        }
    }

    integrationTest();
})(jQuery);