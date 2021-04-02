(function ($) {
    $(function () {
        const $trelloMenuItem = $('#toplevel_page_' + tvpTdVars.adminOptionsSlug);
        const $trelloMenuItemTopMenu = $('#toplevel_page_' + tvpTdVars.adminOptionsSlug + ' > .menu-top');
        const $trelloActionsMenuItem = $('a[href="edit.php?post_type=' + tvpTdVars.postTypeAction + '"]').parent();
        const $trelloBoardsMenuItem = $('a[href="edit-tags.php?taxonomy=' + tvpTdVars.taxonomyBoard + '&post_type=' + tvpTdVars.postTypeAction + '"]').parent();
        const $trelloCardsMenuItem = $('a[href="edit-tags.php?taxonomy=' + tvpTdVars.taxonomyCard + '&post_type=' + tvpTdVars.postTypeAction + '"]').parent();
        const $trelloListsMenuItem = $('a[href="edit-tags.php?taxonomy=' + tvpTdVars.taxonomyList + '&post_type=' + tvpTdVars.postTypeAction + '"]').parent();

        if($('body').hasClass('taxonomy-' + tvpTdVars.taxonomyBoard)) {
            $trelloMenuItem.add($trelloMenuItemTopMenu).removeClass('wp-not-current-submenu');
            $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-menu-open');
            $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-has-current-submenu');
            $trelloBoardsMenuItem.addClass('current');
        }
        if($('body').hasClass('taxonomy-' + tvpTdVars.taxonomyCard)) {
            $trelloMenuItem.add($trelloMenuItemTopMenu).removeClass('wp-not-current-submenu');
            $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-menu-open');
            $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-has-current-submenu');
            $trelloCardsMenuItem.addClass('current');
        }
        if($('body').hasClass('taxonomy-' + tvpTdVars.taxonomyList)) {
            $trelloMenuItem.add($trelloMenuItemTopMenu).removeClass('wp-not-current-submenu');
            $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-menu-open');
            $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-has-current-submenu');
            $trelloListsMenuItem.addClass('current');
        }

        if($('body').hasClass('post-type-' + tvpTdVars.postTypeAction) &&
            !$('body').hasClass('taxonomy-' + tvpTdVars.taxonomyCard) &&
            !$('body').hasClass('taxonomy-' + tvpTdVars.taxonomyBoard) &&
            !$('body').hasClass('taxonomy-' + tvpTdVars.taxonomyList)) {
            $trelloMenuItem.add($trelloMenuItemTopMenu).removeClass('wp-not-current-submenu');
            $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-menu-open');
            $trelloMenuItem.add($trelloMenuItemTopMenu).addClass('wp-has-current-submenu');
            $trelloActionsMenuItem.addClass('current');
        }
    });
})(jQuery);