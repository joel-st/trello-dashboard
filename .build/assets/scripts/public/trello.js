(function ($) {
    const $signUpWithTrello = $('#tvptd-signup-with-trello');

    console.log(window.Trello, localStorage);

    var authenticationSuccess = function () {
        console.log('Successful authentication');
        console.log(window.Trello);
        console.log(window.Trello.token());
        console.log(window.Trello.key());
        localStorage.setItem('token', token);
        window.Trello.members.get('me', function (sss) {
            console.log(sss);
        })
    };

    var authenticationFailure = function () {
        console.log(tvpTdVars.i18n.trelloAuthenticationFailure);
    };

    $signUpWithTrello.on('click', function (event) {
        event.preventDefault();
        window.Trello.authorize({
            type: 'popup',
            name: 'TVP Trello Dashboard',
            scope: {
                read: 'true',
                write: 'false',
                account: 'true'
            },
            expiration: 'never',
            success: authenticationSuccess,
            error: authenticationFailure
        });
    });
})(jQuery);