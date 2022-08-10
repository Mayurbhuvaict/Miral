import template from './sw-cms-el-kohlkramer-home-page-slider.html.twig';
import './sw-cms-el-kohlkramer-home-page-slider.scss';

const {Component, Mixin} = Shopware;

Shopware.Component.register('sw-cms-el-kohlkramer-home-page-slider', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('kohlkramer-home-page-slider');
        },
    }
});
