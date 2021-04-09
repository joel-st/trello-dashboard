import cookie from './cookie';
import dashboard from './dashboard';
import profile from './profile';

export function login(member, membership) {
    const $tvpTd = $('#tvptd-loading');

    $.ajax({
        type: "GET",
        url: tvpTdVars.ajaxUrl,
        data: {
            action: 'tvptd-public-ajax-login',
            nonce: tvpTdVars.nonces.login,
            member: {
                id: member.id,
                email: member.email,
            },
            membership: membership
        },
        success: function (response) {
            $.ajax({
                type: "GET",
                url: tvpTdVars.ajaxUrl,
                data: {
                    action: 'tvptd-public-ajax-get-dashboard-content',
                    nonce: tvpTdVars.nonces.content
                },
                success: function (response) {
                    const parsedResponse = JSON.parse(response);
                    $tvpTd.replaceWith(parsedResponse.html);
                    $tvpTd.attr('id', 'tvptd');
                    profile.init();
                    dashboard.loadOverview();
                    dashboard.loadStatistics();
                }
            });
        },
        error: function (error) {
            alert('Oups, sorry, something went wrong. Try it again later.');
        }
    });
}

export function logout() {
    Trello.deauthorize();
    $.ajax({
        type: "GET",
        url: tvpTdVars.ajaxUrl,
        data: {
            action: 'tvptd-public-ajax-logout',
            nonce: tvpTdVars.nonces.logout
        },
        success: function (response) {
            location.reload();
        }
    });
}

export default { login, logout }