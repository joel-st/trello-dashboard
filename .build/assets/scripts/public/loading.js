import cookie from './cookie';
import trello from './trello';
import auth from './authentication';

(function ($) {
    $(function () {
        const $tvpTd = $('#tvptd-loading');
        const isLoading = $tvpTd.length;

        // ajax call to load the signup markup
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

        // ajax call to load the not in organization markup
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
            // check for exisiting authentication
            const tokenInLocalStorage = localStorage.trello_token ? localStorage.trello_token : false;
            const authCookie = cookie.get(tvpTdVars.authCookie);

            // if token is in local storage set it for the trello client.js Trello class
            // else load the signup page
            if(tokenInLocalStorage) {
                Trello.setToken(tokenInLocalStorage);

                // get info about the current member if authorized
                // if not logout to remove the token in the local storage
                if(Trello.authorized()) {
                    Trello.members.get('me', function (member) {

                        // check if the member is in the specified trello organization
                        // if not load the not in organization markup
                        if(member.idOrganizations.includes(tvpTdVars.trelloOrganization)) {

                            // get all the memberships from the organization and find the membership for the current member
                            // if found pass both, member and membership to the login function
                            Trello.get('organizations/' + tvpTdVars.trelloOrganization + '/memberships', function (memberships) {
                                const match = memberships.filter(function (o) { return o.idMember == member.id; });
                                const membership = match ? match[0] : false;

                                // if membership was not found load the not in organization markup
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