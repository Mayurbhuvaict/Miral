import template from './sw-category-detail-base.html.twig';
import './sw-category-detail-base.scss';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.override('sw-category-detail-base', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            selectedDynamicGroup: null,
        };
    },

    created() {
        this.repositoryDynamicGroup = this.repositoryFactory.create('nzz_dynamic_group');
        this.onSelectCheckValue()
    },

    computed:{
        nzzGroupDynamicRepository() {
            return this.repositoryFactory.create('nzz_dynamic_group');
        },
    },

    methods:{
        onSelectCheckValue(){
            let currentDynamicGroupValues = "";
            let currentCategoryId = this.category.id;
            const customDynamicGroupCriteria = new Criteria();
            customDynamicGroupCriteria.addFilter(Criteria.equals('categoryId', currentCategoryId));
            this.repositoryDynamicGroup
                .search(customDynamicGroupCriteria, Shopware.Context.api)
                .then((result) => {
                    currentDynamicGroupValues = result;
                    if(currentDynamicGroupValues.total > 0){
                        this.selectedDynamicGroup = currentDynamicGroupValues[0].productStreamId;
                    }
                });
        },

        onNzzDynamicGroupChange(item){
            if (!this.category.extensions.nzzDynamicGroupCat) {
                this.category.extensions.nzzDynamicGroupCat = this.nzzGroupDynamicRepository.create();
            }
            this.category.extensions.nzzDynamicGroupCat.categoryId = this.category.id;
            this.category.extensions.nzzDynamicGroupCat.productStreamId = item;
        }
    },
});
