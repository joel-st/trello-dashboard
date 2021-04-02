/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

(function ($) {
  $(function () {
    function integrationTest() {
      var $apiTestMetabox = $('#tvptd-options-trello-integration-api-test');
      var $cronMetabox = $('#tvptd-options-trello-integration-cron');

      if ($apiTestMetabox.length && tvpTdVars) {
        jQuery.ajax({
          type: "GET",
          url: tvpTdVars['ajaxUrl'],
          data: {
            action: 'tvptd-trello-integration-test'
          }
        }).success(function (response) {
          console.log(response);
          var parsedMember = JSON.parse(response.member);
          var parsedOrganization = JSON.parse(response.organization);
          $('input#acf-tvptd-options-trello-integration-organization-id').val(parsedOrganization.id);
          $apiTestMetabox.removeClass('testing');
          $apiTestMetabox.addClass('connected');
          $apiTestMetabox.find('.spinner').remove();
          $apiTestMetabox.find('.label').html(tvpTdVars.i18n.trelloIntegrationTestConnectedAs + ' <strong>' + parsedMember.username + '</strong>');
          $apiTestMetabox.find('figure').css('backgroundImage', 'url(' + parsedMember.avatarUrl + '/170.png)');
          $cronMetabox.find('.button').attr('disabled', false);
        }).error(function (response) {
          $apiTestMetabox.removeClass('testing');
          $apiTestMetabox.addClass('failed');
          $apiTestMetabox.find('.spinner').remove();
          $apiTestMetabox.find('.label').html('<h3>' + tvpTdVars.i18n.trelloIntegrationTestFailed + '</h3><p><strong>' + response.responseJSON.message + '</strong></p>');
          $apiTestMetabox.find('figure').remove();
        });
      }
    }

    integrationTest();
  });
})(jQuery);

/***/ }),
/* 1 */
/***/ (function(module, exports) {

(function ($) {
  $(function () {
    var $trelloMenuItem = $('#toplevel_page_' + tvpTdVars.adminOptionsSlug);
    var $trelloMenuItemTopMenu = $('#toplevel_page_' + tvpTdVars.adminOptionsSlug + ' > .menu-top');
    var $trelloActionsMenuItem = $('a[href="edit.php?post_type=' + tvpTdVars.postTypeAction + '"]').parent();
    var $trelloBoardsMenuItem = $('a[href="edit-tags.php?taxonomy=' + tvpTdVars.taxonomyBoard + '&post_type=' + tvpTdVars.postTypeAction + '"]').parent();
    var $trelloCardsMenuItem = $('a[href="edit-tags.php?taxonomy=' + tvpTdVars.taxonomyCard + '&post_type=' + tvpTdVars.postTypeAction + '"]').parent();
    var $trelloListsMenuItem = $('a[href="edit-tags.php?taxonomy=' + tvpTdVars.taxonomyList + '&post_type=' + tvpTdVars.postTypeAction + '"]').parent();

    if ($('body').hasClass('taxonomy-' + tvpTdVars.taxonomyBoard)) {
      $trelloMenuItem.add($trelloMenuItemTopMenu).removeClass('wp-not-current-submenu');
      $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-menu-open');
      $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-has-current-submenu');
      $trelloBoardsMenuItem.addClass('current');
    }

    if ($('body').hasClass('taxonomy-' + tvpTdVars.taxonomyCard)) {
      $trelloMenuItem.add($trelloMenuItemTopMenu).removeClass('wp-not-current-submenu');
      $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-menu-open');
      $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-has-current-submenu');
      $trelloCardsMenuItem.addClass('current');
    }

    if ($('body').hasClass('taxonomy-' + tvpTdVars.taxonomyList)) {
      $trelloMenuItem.add($trelloMenuItemTopMenu).removeClass('wp-not-current-submenu');
      $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-menu-open');
      $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-has-current-submenu');
      $trelloListsMenuItem.addClass('current');
    }

    if ($('body').hasClass('post-type-' + tvpTdVars.postTypeAction) && !$('body').hasClass('taxonomy-' + tvpTdVars.taxonomyCard) && !$('body').hasClass('taxonomy-' + tvpTdVars.taxonomyBoard) && !$('body').hasClass('taxonomy-' + tvpTdVars.taxonomyList)) {
      $trelloMenuItem.add($trelloMenuItemTopMenu).removeClass('wp-not-current-submenu');
      $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-menu-open');
      $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-has-current-submenu');
      $trelloActionsMenuItem.addClass('current');
    }
  });
})(jQuery);

/***/ }),
/* 2 */,
/* 3 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: ./.build/assets/scripts/admin/notification.js
var $notificationContainer = jQuery('<div id="tvptd-notification-container" class="tvptd-notification-container"></div>');
var $notificationContainerInner = jQuery('<div class="tvptd-notification-container__inner"></div>');
var $notificationContainerContentWrap = jQuery('<div class="tvptd-notification-container__content-wrap"></div>');
var $notificationContainerContent = jQuery('<div class="tvptd-notification-container__content"></div>');
var $notificationContainerMessageWrap = jQuery('<div class="tvptd-notification-container__message-wrap"></div>');
var $notificationContainerMessage = jQuery('<div class="tvptd-notification-container__message"></div>');
var $notificationContainerHead = jQuery('<div class="tvptd-notification-container__head"></div>');
var $notificationContainerTitle = jQuery('<h3 class="tvptd-notification-container__title">Notification</h3>');
var $notificationContainerClose = jQuery('<div class="tvptd-notification-container__close"></div>');
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
function addContainer() {
  jQuery('#wpcontent').prepend($notificationContainer);
}
function notify() {
  console.log('notify');
}
function loading(title) {
  $notificationContainerTitle.html(title);

  if (!title) {
    title = tvpTdVars.i18n.notificationLoading;
  }

  $notificationContainerMessage.append('<span class="spinner is-active"></span>');
  $notificationContainer.show();
}
addContainer();
/* harmony default export */ var notification = ({
  addContainer: addContainer,
  notify: notify,
  loading: loading
});
// CONCATENATED MODULE: ./.build/assets/scripts/admin/cron-metabox.js


function fetchAllTrelloData() {
  console.log('fetchAllTrelloData');
}

function fetch(request) {
  if (tvpTdVars) {
    // nf.loading('Fetching Trello Members');
    jQuery.ajax({
      type: "GET",
      url: tvpTdVars['ajaxUrl'],
      data: {
        action: 'tvptd-data-processor',
        request: request
      }
    }).success(function (response) {
      var parsed = JSON.parse(response.data);
      console.log(parsed);
    }).error(function (response) {
      console.log(response);
    });
  }
}

(function ($) {
  $(function () {
    var $fetchAllTrelloDataButton = $('#tvptd-options-trello-integration-fetch-all');
    $fetchAllTrelloDataButton.on('click', function (event) {
      event.preventDefault();
      fetchAllTrelloData();
    });
    var $fetchTrelloMembersButton = $('#tvptd-options-trello-integration-fetch-members');
    $fetchTrelloMembersButton.on('click', function (event) {
      event.preventDefault();
      fetch('addUpdateMembers');
    });
    var $fetchTrelloBoardsButton = $('#tvptd-options-trello-integration-fetch-boards');
    $fetchTrelloBoardsButton.on('click', function (event) {
      event.preventDefault();
      fetch('addUpdateBoards');
    });
  });
})(jQuery);
// EXTERNAL MODULE: ./.build/assets/scripts/admin/integration-test.js
var integration_test = __webpack_require__(0);

// EXTERNAL MODULE: ./.build/assets/scripts/admin/menu.js
var menu = __webpack_require__(1);

// CONCATENATED MODULE: ./.build/assets/scripts/admin/index.js
// import './notification.js';




/***/ })
/******/ ]);