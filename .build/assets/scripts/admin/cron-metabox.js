import nf from './notification';

function fetchAllTrelloData() {
    console.log('fetchAllTrelloData');
    // if(tvp_td_vars) {
    //     console.log('fetchAllTrelloData');
    // }
}

function fetchTrelloMembers() {
    if(tvp_td_vars) {
        // nf.loading('Fetching Trello Members');
        jQuery.ajax({
            type: "GET",
            url: tvp_td_vars['ajax_url'],
            data: {
                action: 'tvptd-data-processor',
                request: 'addUpdateMembers',
            },
        }).success(function (response) {
            const parsed = JSON.parse(response.data);
            console.log(response, parsed);
        }).error(function (response) {
            console.log(response);
        });
    }
}

(function ($) {
    $(function () {
        const $fetchAllTrelloDataButton = $('#tvptd-options-trello-integration-fetch-all');
        $fetchAllTrelloDataButton.on('click', function (event) {
            event.preventDefault();
            fetchAllTrelloData();
        });

        const $fetchTrelloMembersButton = $('#tvptd-options-trello-integration-fetch-members');
        $fetchTrelloMembersButton.on('click', function (event) {
            event.preventDefault();
            fetchTrelloMembers();
        });
    });
})(jQuery);