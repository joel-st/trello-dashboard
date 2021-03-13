import nf from './notification';

function fetchAllTrelloData() {
    console.log('fetchAllTrelloData');
    // if(tvpTdVars) {
    //     console.log('fetchAllTrelloData');
    // }
}

function fetchTrelloMembers() {
    if(tvpTdVars) {
        // nf.loading('Fetching Trello Members');
        jQuery.ajax({
            type: "GET",
            url: tvpTdVars['ajaxUrl'],
            data: {
                action: 'tvptd-data-processor',
                request: 'addUpdateMembers',
            },
        }).success(function (response) {
            const parsed = JSON.parse(response.data);
            console.log(parsed);
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