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
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ 2:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: ./.build/assets/scripts/public/cookie.js
function get(name) {
  var result = document.cookie.match("(^|[^;]+)\\s*" + name + "\\s*=\\s*([^;]+)");
  return result ? result.pop() : '';
}
function set(cookieKey, cookieValue, expirationDays) {
  var expiryDate = '';

  if (expirationDays) {
    var date = new Date();
    date.setTime("".concat(date.getTime()).concat(expirationDays || 30 * 24 * 60 * 60 * 1000));
    expiryDate = "; expiryDate=\" ".concat(date.toUTCString());
  }

  document.cookie = "".concat(cookieKey, "=").concat(cookieValue || '').concat(expiryDate, "; path=/");
}
function update() {
  console.log('updateCookie');
}
function remove(name) {
  set(name, '', -1);
}
/* harmony default export */ var cookie = ({
  get: get,
  set: set,
  update: update,
  remove: remove
});
// CONCATENATED MODULE: ./.build/assets/scripts/public/trello.js
// trello authentication setup with trello client.js class
function initSignup() {
  var $signUpWithTrello = $('#tvptd-signup-with-trello');

  var authenticationSuccess = function authenticationSuccess() {
    console.log(tvpTdVars.i18n.trelloAuthenticationSuccess);
    location.reload();
  };

  var authenticationFailure = function authenticationFailure() {
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
/* harmony default export */ var trello = ({
  initSignup: initSignup
});
// CONCATENATED MODULE: ./.build/assets/scripts/public/dashboard.js
function loadOverview() {
  var $overview = $('#tvptd-organization-overview');
  var $overviewContent = $('#tvptd-organization-overview .tvptd__widget-content');

  if ($overview) {
    $.ajax({
      type: "GET",
      url: tvpTdVars.ajaxUrl,
      data: {
        action: 'tvptd-public-ajax-get-organization-overview'
      },
      success: function success(response) {
        var parsedResponse = JSON.parse(response);
        $overviewContent.html(parsedResponse.html);
        $overviewContent.removeClass('tvptd__widget-content--loading');
      },
      error: function error(_error) {
        $overviewContent.html('<section class="tvptd__widget-section">Oups, something went wrong wile loading the overview.</section>');
        $overviewContent.removeClass('tvptd__widget-content--loading');
      }
    });
  }
}
function loadStatistics() {
  var timeRange = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var $statistics = $('#tvptd-organization-statistics');
  var $statisticsContent = $('#tvptd-organization-statistics .tvptd__widget-content');
  var $timeRange = $('#tvptd-organization-statistics-timerange');
  $timeRange.on('change', function (event) {
    loadStatistics(event.target.value);
  });

  if (!timeRange) {
    timeRange = $timeRange.val();
  }

  if ($statistics) {
    $statisticsContent.addClass('tvptd__widget-content--loading');
    $statisticsContent.html('<div class="tvptd__spinner spinner"></div>');
    $.ajax({
      type: "GET",
      url: tvpTdVars.ajaxUrl,
      data: {
        action: 'tvptd-public-ajax-get-organization-statistics',
        timeRange: timeRange
      },
      success: function success(response) {
        var parsedResponse = JSON.parse(response);
        $statisticsContent.html(parsedResponse.html);
        $statisticsContent.removeClass('tvptd__widget-content--loading');
      },
      error: function error(_error2) {
        $statisticsContent.html('<section class="tvptd__widget-section">Oups, something went wrong wile loading the statistics.</section>');
        $statisticsContent.removeClass('tvptd__widget-content--loading');
      }
    });
  }
}
/* harmony default export */ var dashboard = ({
  loadOverview: loadOverview,
  loadStatistics: loadStatistics
});
// CONCATENATED MODULE: ./.build/assets/scripts/public/profile.js

function init() {
  var $profile = $('#tvptd-profile');
  var $logout = $('#tvptd-logout');
  $profile.on('click', function () {
    $profile.toggleClass('active');
  });
  $logout.on('click', function () {
    authentication.logout();
  });
}
/* harmony default export */ var profile = ({
  init: init
});
// CONCATENATED MODULE: ./.build/assets/scripts/public/authentication.js



function login(member, membership) {
  var $tvpTd = $('#tvptd-loading');
  $.ajax({
    type: "GET",
    url: tvpTdVars.ajaxUrl,
    data: {
      action: 'tvptd-public-ajax-login',
      nonce: tvpTdVars.nonces.login,
      member: {
        id: member.id,
        email: member.email
      },
      membership: membership
    },
    success: function success(response) {
      $.ajax({
        type: "GET",
        url: tvpTdVars.ajaxUrl,
        data: {
          action: 'tvptd-public-ajax-get-dashboard-content',
          nonce: tvpTdVars.nonces.content
        },
        success: function success(response) {
          var parsedResponse = JSON.parse(response);
          $tvpTd.replaceWith(parsedResponse.html);
          $tvpTd.attr('id', 'tvptd');
          profile.init();
          dashboard.loadOverview();
          dashboard.loadStatistics();
        }
      });
    },
    error: function error(_error) {
      alert('Oups, sorry, something went wrong. Try it again later.');
    }
  });
}
function logout() {
  Trello.deauthorize();
  $.ajax({
    type: "GET",
    url: tvpTdVars.ajaxUrl,
    data: {
      action: 'tvptd-public-ajax-logout',
      nonce: tvpTdVars.nonces.logout
    },
    success: function success(response) {
      location.reload();
    }
  });
}
/* harmony default export */ var authentication = ({
  login: login,
  logout: logout
});
// CONCATENATED MODULE: ./.build/assets/scripts/public/loading.js




(function ($) {
  $(function () {
    var $tvpTd = $('#tvptd-loading');
    var isLoading = $tvpTd.length; // ajax call to load the signup markup

    function loadSignUp() {
      $.ajax({
        type: "GET",
        url: tvpTdVars.ajaxUrl,
        data: {
          action: 'tvptd-public-ajax-get-signup-content',
          nonce: tvpTdVars.nonces.signup
        },
        success: function success(response) {
          var parsedResponse = JSON.parse(response);
          $tvpTd.replaceWith(parsedResponse.html);
          trello.initSignup();
        }
      });
    } // ajax call to load the not in organization markup


    function loadNotInOrganization() {
      $.ajax({
        type: "GET",
        url: tvpTdVars.ajaxUrl,
        data: {
          action: 'tvptd-public-ajax-get-not-in-organization-content',
          nonce: tvpTdVars.nonces.signup
        },
        success: function success(response) {
          var parsedResponse = JSON.parse(response);
          $tvpTd.replaceWith(parsedResponse.html);
          Trello.deauthorize();
        }
      });
    }

    if (isLoading) {
      // check for exisiting authentication
      var tokenInLocalStorage = localStorage.trello_token ? localStorage.trello_token : false;
      var authCookie = cookie.get(tvpTdVars.authCookie); // if token is in local storage set it for the trello client.js Trello class
      // else load the signup page

      if (tokenInLocalStorage) {
        Trello.setToken(tokenInLocalStorage); // get info about the current member if authorized
        // if not logout to remove the token in the local storage

        if (Trello.authorized()) {
          Trello.members.get('me', function (member) {
            // check if the member is in the specified trello organization
            // if not load the not in organization markup
            if (member.idOrganizations.includes(tvpTdVars.trelloOrganization)) {
              // get all the memberships from the organization and find the membership for the current member
              // if found pass both, member and membership to the login function
              Trello.get('organizations/' + tvpTdVars.trelloOrganization + '/memberships', function (memberships) {
                var match = memberships.filter(function (o) {
                  return o.idMember == member.id;
                });
                var membership = match ? match[0] : false; // if membership was not found load the not in organization markup

                if (membership) {
                  authentication.login(member, membership);
                } else {
                  loadNotInOrganization();
                }
              });
            } else {
              loadNotInOrganization();
            }
          });
        } else {
          authentication.logout();
        }
      } else {
        loadSignUp();
      }
    }
  });
})(jQuery);
// CONCATENATED MODULE: ./.build/assets/scripts/public/index.js



/***/ })

/******/ });