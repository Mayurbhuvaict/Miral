import template from './kohlkramer-home-slider-detail.html.twig';

const {Component, Mixin} = Shopware;
const {Criteria, EntityCollection} = Shopware.Data;
const utils = Shopware.Utils;
const {mapPropertyErrors} = Shopware.Component.getComponentHelper();
const {hasOwnProperty, cloneDeep} = Shopware.Utils.object;


Component.register('kohlkramer-home-slider-detail', {
    template,
    inject: ['repositoryFactory', 'configService', 'acl', 'feature'],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder'),
        Mixin.getByName('position')
    ],

    metaInfo() {
        return {
            title: this.$createTitle(this.kohlkramerDataTitle)
        };
    },

    shortcuts: {
        'SYSTEMKEY+S': {
            active() {
                return this.acl.can('kohlkramerData.editor');
            },
            method: 'onClickSave',
        },
        ESCAPE: 'onCancel',
    },

    props: {
        kohlkramerHomeSliderId: {
            type: String, required: false, default: null
        },
    },

    data() {
        return {
            kohlkramerData: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            mediaId: null,
            isCreateMode: false,
        };
    },

    computed: {

        isTitleRequired() {
            return Shopware.State.getters['context/isSystemDefaultLanguage'];
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        kohlkramerHomeSliderRepository() {
            return this.repositoryFactory.create('KohlKramer_home_slider');
        },

        kohlkramerDataTitle() {
            return this.placeholder(this.kohlkramerData, 'title', this.kohlkramerHomeSliderId ? '' : this.$tc('kohlkramer-home-slider.detail.textHeadline'));
        },

        uploadTag() {
            return `kohlkramer-home-slider-detail--${this.kohlkramerData.id}`;
        },

        tooltipSave() {
            const systemKey = this.$device.getSystemKey();

            return {
                message: `${systemKey} + S`,
                appearance: 'light',
            };
        },

        tooltipCancel() {
            return {
                message: 'ESC',
                appearance: 'light',
            };
        },
        mediaItem() {
            return this.kohlkramerData !== null ? this.kohlkramerData.mediaId : null;
        },

    },

    created() {
        this.createdComponent();
    },

    watch: {
        'kohlkramerData.media.id'() {

            if (!this.kohlkramerData.mediaId) {
                return;
            }
            this.setMediaItem({targetId: this.kohlkramerData.media.id});
        },

        kohlkramerHomeSliderId() {
            this.createdComponent();
        }
    },

    methods: {
        createdComponent() {

            if (!this.kohlkramerHomeSliderId) {
                if (!Shopware.State.getters['context/isSystemDefaultLanguage']) {
                    Shopware.State.commit('context/resetLanguageToDefault');
                }
            }
            this.kohlkramerData = this.kohlkramerHomeSliderRepository.create(Shopware.Context.api);
            if (this.kohlkramerHomeSliderId) {
                this.getkohlkramerHomeSlider();
            }
        },

        getkohlkramerHomeSlider() {
            this.isLoading = true;
            const criteria = new Criteria();
            criteria.getAssociation('media');
            return this.kohlkramerHomeSliderRepository
                .get(this.$route.params.id, Shopware.Context.api, criteria)
                .then((entity) => {
                    this.kohlkramerData = entity;
                    this.isLoading = false;
                });
        },


        onClickSave() {
            this.isLoading = true;

            this.kohlkramerHomeSliderRepository
                .save(this.kohlkramerData, Shopware.Context.api)
                .then(() => {
                    if (this.kohlkramerHomeSliderId) {
                        this.getkohlkramerHomeSlider();
                    } else {
                        this.$router.push({name: 'kohlkramer.home.slider.detail', params: {id: this.kohlkramerData.id}});
                    }
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch(() => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('kohlkramer-home-slider.detail.errorTitle'),
                    message: this.$t('kohlkramer-home-slider.detail.errorMessage')
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        },

        abortOnLanguageChange() {
            return this.kohlkramerHomeSliderRepository.hasChanges(this.kohlkramerData);
        },

        saveOnLanguageChange() {
            return this.kohlkramerHomeSliderRepository.save(this.kohlkramerData, Shopware.Context.api);
        },
        onCancel() {
            this.$router.push({name: 'kohlkramer.home.slider.list'});
        },

        onUploadMedia(media) {
            this.$emit('media-upload', {targetId: mediaId.targetId});
        },

        onRemoveMedia() {
            this.$emit('media-remove');
        },

        onOpenMedia() {
            this.$emit('media-open');
        },

        setMediaItem({targetId}) {

            this.mediaRepository.get(targetId).then((response) => {
                this.mediaId = response;

            });
            this.kohlkramerData.mediaId = targetId;

        },

        onDropMedia(mediaItem) {
            this.setMediaItem({targetId: mediaItem.id});
        },

        openMediaModal() {
            this.showMediaModal = true;
        },


        setMediaFromSidebar(media) {
            this.kohlkramerData.mediaId = media.id;
        },

        onUnlinkLogo() {
            this.mediaId = null;

        },

        openMediaSidebar() {
            this.$refs.mediaSidebarItem.openContent();
        },

    }
});
