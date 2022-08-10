import template from './sw-product-detail.html.twig';

const {Criteria} = Shopware.Data;

Shopware.Component.override('sw-product-detail', {
    template,

    data() {
        return {
            showRentalTab: false,
        };
    },
    created() {
        this.setRentalTabCondition();
    },
    watch:{
        'product' :{
            handler() {
                this.setRentalTabCondition();
            }
        }
    },
    computed: {
        productCriteria() {
            const criteria = this.$super('productCriteria');
            criteria.getAssociation('rentalProduct')
            return criteria;
        },
        productPriceDataRepository() {
            return this.repositoryFactory.create('product');
        },
    },
    methods: {
        setRentalTabCondition() {
            const productId = this.$route.params.id;
            const customCriteria = new Criteria();
            customCriteria.addFilter(Criteria.equals('id', productId));
            customCriteria.addAssociation('prices');
            this.productPriceDataRepository
                .search(customCriteria, Shopware.Context.api)
                .then((results) => {
                    if(results[0].prices.length > 0) {
                        this.showRentalTab = true;
                    }
                    else {
                        this.showRentalTab = false;
                    }
                });
        }
    }
});
