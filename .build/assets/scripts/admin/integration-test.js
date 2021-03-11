(function ($) {
    $(function () {
        function integrationTest() {
            const $apiTestMetabox = $('#tvptd-options-trello-integration-api-test');
            if($apiTestMetabox.length && tvpTdVars) {
                jQuery.ajax({
                    type: "GET",
                    url: tvpTdVars['ajax_url'],
                    data: {
                        action: 'tvptd-trello-integration-test'
                    },
                }).success(function (response) {
                    const parsed = JSON.parse(response.data);
                    $apiTestMetabox.removeClass('testing');
                    $apiTestMetabox.addClass('connected');
                    $apiTestMetabox.find('.spinner').remove();
                    $apiTestMetabox.find('.label').html(tvpTdVars.i18n.trelloIntegrationTestConnectedAs + ' <strong>' + parsed.username + '</strong>');
                    $apiTestMetabox.find('figure').css('backgroundImage', 'url(' + parsed.avatarUrl + '/170.png)');
                }).error(function (response) {
                    $apiTestMetabox.removeClass('testing');
                    $apiTestMetabox.addClass('failed');
                    $apiTestMetabox.find('.spinner').remove();
                    $apiTestMetabox.find('.label').html('<h3>' + tvpTdVars.i18n.trelloIntegrationTestFailed + '</h3><p><strong>' + response.responseJSON.message + '</strong></p>');
                    $apiTestMetabox.find('figure').remove();
                });
            }
        }

        integrationTest();
    });
})(jQuery);