import template from './kohlkramer-home-slider-list.html.twig';

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('kohlkramer-home-slider-list', {
    template,

    inject: [
        'repositoryFactory',
        'numberRangeService',
        'acl',
        'feature'
    ],

    props: {
        allowEdit: {
            type: Boolean,
            required: false,
            default: true,
        },

    },

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing')
    ],

    data() {
        return {
            kohlkramerData: null,
            isLoading: false,
            showDeleteModal: false,
            total: 0,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        kohlkramerHomeSliderRepository() {
            return this.repositoryFactory.create('KohlKramer_home_slider');
        },

        columns() {
            return [{
                property: 'title',
                dataIndex: 'title',
                label: this.$t('kohlkramer-home-slider.list.columnName'),
                routerLink: 'kohlkramer.home.slider.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            },{
                    property: 'mediaId',
                    dataIndex: 'mediaId',
                    label: this.$t('kohlkramer-home-slider.list.image'),
                    inlineEdit: 'string',
                    allowResize: true
                }
            ];
        }
    },

    methods: {
        getList() {
            this.isLoading = true;
            this.kohlkramerHomeSliderRepository
                .search(new Criteria(), Shopware.Context.api)
                .then((result) => {
                    this.kohlkramerData = result;
                    this.total = result.total;
                    this.isLoading = false;
                }).catch(() => {
                this.isLoading = false;
            });
        },

        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.getList();
        },

        onDelete(id) {
            this.showDeleteModal = id;
        },

        onCloseDeleteModal() {
            this.showDeleteModal = false;
        },

        onConfirmDelete(id) {
            this.showDeleteModal = false;

            return this.kohlkramerHomeSliderRepository.delete(id).then(() => {
                this.getList();
            });
        },
    }
});


