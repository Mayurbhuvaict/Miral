import Plugin from 'src/plugin-system/plugin.class';

export default class homeSliderJquery extends Plugin {

    /**
     * Plugin constructor. Finds the necessary elements from the DOM and starts the plugin.
     *
     * @constructor
     * @returns {void}
     */

    init() {
       // this.callPopup = document.querySelector('.responsive-kohlkramer');
        this._registerEvents();
    }
    _registerEvents(){
       // alert("apli");
        $('.responsive').slick({
            //alert("dnfdsk");
            dots: false,
            prevArrow: $('.prev'),
            nextArrow: $('.next'),
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1

                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });


    }
}

