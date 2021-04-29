// trello authentication setup with trello client.js class
export function initSignup() {
    const $signUpWithTrello = $('#tvptd-signup-with-trello');

    var authenticationSuccess = function () {
        console.log(tvpTdVars.i18n.trelloAuthenticationSuccess);
        location.reload();
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
}

export default { initSignup }