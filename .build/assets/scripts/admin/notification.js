let $notificationContainer = jQuery('<div id="tvptd-notification-container" class="tvptd-notification-container"></div>');
let $notificationContainerInner = jQuery('<div class="tvptd-notification-container__inner"></div>');
let $notificationContainerContentWrap = jQuery('<div class="tvptd-notification-container__content-wrap"></div>');
let $notificationContainerContent = jQuery('<div class="tvptd-notification-container__content"></div>');
let $notificationContainerMessageWrap = jQuery('<div class="tvptd-notification-container__message-wrap"></div>');
let $notificationContainerMessage = jQuery('<div class="tvptd-notification-container__message"></div>');

let $notificationContainerHead = jQuery('<div class="tvptd-notification-container__head"></div>');
let $notificationContainerTitle = jQuery('<h3 class="tvptd-notification-container__title">Notification</h3>');

let $notificationContainerClose = jQuery('<div class="tvptd-notification-container__close"></div>');
$notificationContainerClose.on('click', function () {
    $notificationContainerTitle.text('');
    $notificationContainer.hide();
});

$notificationContainerHead.append($notificationContainerTitle);
$notificationContainerHead.append($notificationContainerClose);

$notificationContainerContent.append($notificationContainerHead);
$notificationContainerMessageWrap.append($notificationContainerMessage);
$notificationContainerContent.append($notificationContainerMessageWrap);

$notificationContainer.append($notificationContainerInner);
$notificationContainerInner.append($notificationContainerContentWrap);
$notificationContainerContentWrap.append($notificationContainerContent);

export function addContainer() {
    jQuery('#wpcontent').prepend($notificationContainer);
}

export function notify() {
    console.log('notify');
}

export function loading(title) {
    $notificationContainerTitle.html(title);
    if(!title) {
        title = tvpTdVars.i18n.notificationLoading;
    }
    $notificationContainerMessage.append('<span class="spinner is-active"></span>');
    $notificationContainer.show();
}

addContainer();

export default { addContainer, notify, loading }