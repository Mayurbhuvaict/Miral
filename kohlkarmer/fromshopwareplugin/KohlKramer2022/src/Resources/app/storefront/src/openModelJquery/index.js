import Plugin from 'src/plugin-system/plugin.class';

export default class openModel extends Plugin {

    /**
     * Plugin constructor. Finds the necessary elements from the DOM and starts the plugin.
     *
     * @constructor
     * @returns {void}
     */

    init() {
        this.callPopup = document.querySelector('.kohlkramer-openmodel');
        this._registerEvents();
    }
    _registerEvents(){
        this.callPopup.addEventListener('mouseover', (event) => {
            //console.log(this.callPopup);
            var path = this.callPopup.getAttribute("src");
            if ($('div').hasClass('classKohlkramer')) {
                $('.classKohlkramer').remove();
            }
                $('body').after('<div class="modal fade classKohlkramer" id="openModalKohlkramer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exampleModalLabel"></h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><div class="alert"><img class="imgKohlkramer" src="" width="100%"></div></div></div></div></div>');
                $('#openModalKohlkramer').modal('show');
                $('.imgKohlkramer').attr("src",path);
        });
    }
}

