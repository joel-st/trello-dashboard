import cookie from './cookie';
import trello from './trello';
import auth from './authentication';

(function ($) {
    $(function () {
        const $tvpTd = $('#tvp-td-loading');
        const isLoading = $tvpTd.length;

        function loadSignUp() {
            $.ajax({
                type: "GET",
                url: tvpTdVars.ajaxUrl,
                data: {
                    action: 'tvptd-public-ajax-get-signup-content',
                    nonce: tvpTdVars.ajaxNonce,
                },
                success: function (response) {
                    const parsedResponse = JSON.parse(response);
                    $tvpTd.html(parsedResponse.html);
                    $tvpTd.attr('id', 'tvp-td-signup');
                    trello.initSignup();
                }
            });
        }

        if(isLoading) {
            const tokenInLocalStorage = localStorage.trello_token ? localStorage.trello_token : false;
            const authCookie = cookie.get(tvpTdVars.authCookie);

            if(tokenInLocalStorage) {
                Trello.setToken(tokenInLocalStorage);
                if(Trello.authorized()) {
                    if(authCookie) {
                        auth.login({ 'id': authCookie, idOrganizations: [tvpTdVars.trelloOrganization] });
                    } else {
                        Trello.members.get('me', function (member) {
                            auth.login(member);
                        });
                    }
                } else {
                    auth.logout();
                }
                // loadSignUp();
            } else {
                loadSignUp();
            }
        }
    });
})(jQuery);