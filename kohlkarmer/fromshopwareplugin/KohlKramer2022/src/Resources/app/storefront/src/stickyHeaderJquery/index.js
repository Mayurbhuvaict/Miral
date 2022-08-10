import Plugin from 'src/plugin-system/plugin.class';

export default class stickyHeader extends Plugin {

    /**
     * Plugin constructor. Finds the necessary elements from the DOM and starts the plugin.
     *
     * @constructor
     * @returns {void}
     */
    init() {
        this._registerEvents();

    }
    _registerEvents(){
        jQuery(window).scroll(function() {
            var sticky = jQuery('.header-main'),
                scroll = $(window).scrollTop();

            if (scroll >= 200) {
                sticky.addClass('fixed'); }
            else {
                sticky.removeClass('fixed');

            }
        });
    }

}

