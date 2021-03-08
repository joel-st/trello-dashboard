import nf from './notification';

function fetchAllTrelloData() {
    if(tvp_td_vars) {
        nf.loading('Fetching Trello Data');
        // jQuery.ajax({
        //     type: "GET",
        //     url: tvp_td_vars['ajax_url'],
        //     data: {
        //         action: 'tvptd-trello-get-from-organization',
        //         request: 'members',
        //     },
        // }).success(function (response) {
        //     // const parsed = JSON.parse(response.data);
        //     console.log(response);
        // }).error(function (response) {
        //     console.log(response);
        // });
    }
}

(function ($) {
    $(function () {
        const $fetchAllTrelloDataButton = $('#tvptd-options-trello-integration-fetch-all-trello-data');
        $fetchAllTrelloDataButton.on('click', function (event) {
            event.preventDefault();
            fetchAllTrelloData();
        });
    });
})(jQuery);