(function ($) {
    $(function () {
        function integrationTest() {
            const $apiTestMetabox = $('#tvptd-options-trello-integration-api-test');
            const $cronMetabox = $('#tvptd-options-trello-integration-cron');
            if($apiTestMetabox.length && tvpTdVars) {
                jQuery.ajax({
                    type: "GET",
                    url: tvpTdVars['ajaxUrl'],
                    data: {
                        action: 'tvptd-trello-integration-test'
                    },
                }).success(function (response) {
                    console.log(response);
                    const parsedMember = JSON.parse(response.member);
                    const parsedOrganization = JSON.parse(response.organization);
                    $('input#acf-tvptd-options-trello-integration-organization-id').val(parsedOrganization.id);
                    $apiTestMetabox.removeClass('testing');
                    $apiTestMetabox.addClass('connected');
                    $apiTestMetabox.find('.spinner').remove();
                    $apiTestMetabox.find('.label').html(tvpTdVars.i18n.trelloIntegrationTestConnectedAs + ' <strong>' + parsedMember.username + '</strong>');
                    $apiTestMetabox.find('figure').css('backgroundImage', 'url(' + parsedMember.avatarUrl + '/170.png)');
                    $cronMetabox.find('.button').attr('disabled', false);
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