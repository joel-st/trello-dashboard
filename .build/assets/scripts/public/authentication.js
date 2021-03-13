import cookie from './cookie';

export function login(member) {
    const $tvpTd = $('#tvp-td-loading');

    $.ajax({
        type: "GET",
        url: tvpTdVars.ajaxUrl,
        data: {
            action: 'tvptd-public-ajax-login',
            nonce: tvpTdVars.ajaxNonce,
            member: member
        },
        success: function (response) {
            $.ajax({
                type: "GET",
                url: tvpTdVars.ajaxUrl,
                data: {
                    action: 'tvptd-public-ajax-get-dashboard-content',
                    nonce: tvpTdVars.ajaxNonce
                },
                success: function (response) {
                    const parsedResponse = JSON.parse(response);
                    $tvpTd.html(parsedResponse.html);
                    $tvpTd.attr('id', 'tvp-td');
                }
            });
        },
        error: function (error) {
            // location.reload();
        }
    });
}

export function logout() {
    localStorage.removeItem('trello_token');
    cookie.remove(tvpTdVars.authCookie);
    // location.reload();
}

export default { login, logout }