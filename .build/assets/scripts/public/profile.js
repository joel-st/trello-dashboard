import auth from './authentication';

export function init() {
    const $profile = $('#tvptd-profile');
    const $logout = $('#tvptd-logout');

    $profile.on('click', function () {
        $profile.toggleClass('active');
    });

    $logout.on('click', function () {
        auth.logout();
    });
}

export default { init }