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
                setTimeout(function () {
                    response = JSON.parse(response);
                    console.log(response);
                    $apiTestMetabox.removeClass('testing');
                    $apiTestMetabox.addClass('connected');
                    $apiTestMetabox.find('.spinner').remove();
                    $apiTestMetabox.find('.label').html('Connected as <strong>' + response.username + '</strong>');
                    $apiTestMetabox.find('figure').css('backgroundImage', 'url(' + response.avatarUrl + '/170.png)');
                }, 1000);
            }).error(function (response) {
                setTimeout(function () {
                    $apiTestMetabox.removeClass('testing');
                    $apiTestMetabox.addClass('failed');
                    $apiTestMetabox.find('.spinner').remove();
                    $apiTestMetabox.find('.label').html('<h3>Connection failed!</h3><p><strong>' + response.responseJSON.message + '</strong></p>');
                    $apiTestMetabox.find('figure').remove();
                }, 1000);
            });
        }
    }

    integrationTest();
})(jQuery);