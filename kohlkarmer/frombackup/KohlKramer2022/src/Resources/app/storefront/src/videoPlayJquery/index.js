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
        jQuery(document).on('click','.js-videoPoster',function(e) {
            e.preventDefault();
            var poster = jQuery(this);
            var wrapper = poster.closest('.js-videoWrapper');
            videoPlay(wrapper);
        });

        function videoPlay(wrapper) {
            var iframe = wrapper.find('.js-videoIframe');
            var src = iframe.data('src');
            wrapper.addClass('videoWrapperActive');
            iframe.attr('src',src);
        }
    }

}

