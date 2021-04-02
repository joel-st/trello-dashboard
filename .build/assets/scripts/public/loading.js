import cookie from './cookie';
import trello from './trello';
import auth from './authentication';

(function ($) {
    $(function () {
        const $tvpTd = $('#tvptd-loading');
        const isLoading = $tvpTd.length;

        function loadSignUp() {
            $.ajax({
                type: "GET",
                url: tvpTdVars.ajaxUrl,
                data: {
                    action: 'tvptd-public-ajax-get-signup-content',
                    nonce: tvpTdVars.nonces.signup,
                },
                success: function (response) {
                    const parsedResponse = JSON.parse(response);
                    $tvpTd.replaceWith(parsedResponse.html);
                    trello.initSignup();
                }
            });
        }

        function loadNotInOrganization() {
            $.ajax({
                type: "GET",
                url: tvpTdVars.ajaxUrl,
                data: {
                    action: 'tvptd-public-ajax-get-not-in-organization-content',
                    nonce: tvpTdVars.nonces.signup,
                },
                success: function (response) {
                    const parsedResponse = JSON.parse(response);
                    $tvpTd.replaceWith(parsedResponse.html);
                    Trello.deauthorize();
                }
            });
        }

        if(isLoading) {
            const tokenInLocalStorage = localStorage.trello_token ? localStorage.trello_token : false;
            const authCookie = cookie.get(tvpTdVars.authCookie);

            if(tokenInLocalStorage) {
                Trello.setToken(tokenInLocalStorage);
                if(Trello.authorized()) {
                    Trello.members.get('me', function (member) {
                        if(member.idOrganizations.includes(tvpTdVars.trelloOrganization)) {
                            Trello.get('organizations/' + tvpTdVars.trelloOrganization + '/memberships', function (memberships) {
                                const match = memberships.filter(function (o) { return o.idMember == member.id; });
                                const membership = match ? match[0] : false;
                                if(membership) {
                                    auth.login(member, membership);
                                } else {
                                    loadNotInOrganization();
                                }
                            });
                        } else {
                            loadNotInOrganization();
                        }
                    });
                } else {
                    auth.logout();
                }
            } else {
                loadSignUp();
            }
        }
    });
})(jQuery);