import template from './sw-product-detail-rentalproduct.html.twig';
import './sw-product-detail-rentalproduct.scss';

const {EntityCollection} = Shopware.Data;
const { Component, Mixin } = Shopware;
const {Criteria} = Shopware.Data;
const {mapState, mapGetters} = Shopware.Component.getComponentHelper();

Component.register('sw-product-detail-rentalproduct', {
    template,

    inject: ['repositoryFactory','acl'],
    mixins: [
        Mixin.getByName('notification'),
    ],
    data() {
        return {
            showDeleteModal: false,
            newRentalProductData: null,
            item : null,
            showContent: false
        };
    },
    metaInfo() {
        return {
            title: 'RentalProduct'
        };
    },

     computed: {
         ...mapState('swProductDetail', [
             'product'
         ]),
         ...mapGetters('swProductDetail', [
             'isLoading'
         ]),
         productRentalProductRepository() {
             return this.repositoryFactory.create('rental_product');
         },
         ruleTypeData() {
             const criteria = new Criteria(1, 500);
             criteria.addFilter(
                 Criteria.multi('OR', [
                     Criteria.contains('rule.moduleTypes.types', 'price'),
                     Criteria.equals('rule.moduleTypes', null),
                 ]),
             );
             return criteria;
         },


         dayRuleColumns() {
             const dayRuleColumns = [
                 {
                     property: 'dayStart',
                     label: 'sw-product.rentalProduct.columnFrom',
                     visible: true,
                     allowResize: true,
                     primary: true,
                     rawData: false,
                     width: '120px',
                 }, {
                     property: 'dayEnd',
                     label: 'sw-product.rentalProduct.columnTo',
                     visible: true,
                     allowResize: true,
                     primary: true,
                     rawData: false,
                     width: '120px',
                 },
                 {
                     property: 'ruleId',
                     label: 'sw-product.rentalProduct.columnRule',
                     visible: true,
                     allowResize: true,
                     width: '250px',
                     multiLine: true,
                 },
             ];
             return dayRuleColumns;
         },
     },
    created() {
        this.createdComponent();
    },
    // mounted() {
    //     this.mountedComponent();
    // },

    methods: {
         createdComponent() {
             this.newRentalProductData = new EntityCollection(
                 this.productRentalProductRepository.route,
                 this.productRentalProductRepository.entityName,
                 Shopware.Context.api,
             );

             const productId = this.$route.params.id;

             const customCriteria = new Criteria();
             customCriteria.addFilter(Criteria.equals('productId', productId));
             customCriteria.addSorting(Criteria.sort('dayStart', 'ASC'));
              this.productRentalProductRepository
                  .search(customCriteria, Shopware.Context.api)
                  .then((results) => {
                      const currentRentalProductValues = results;
                       if(currentRentalProductValues.total > 0) {
                           currentRentalProductValues.forEach(
                          result =>
                             this.newRentalProductData.push(result)
                                );
                       }
                 });
         },
        // mountedComponent() {
        //     const ruleCriteria = new Criteria(1, 500);
        //     ruleCriteria.addFilter(
        //         Criteria.multi('OR', [
        //             Criteria.contains('rule.moduleTypes.types', 'price'),
        //             Criteria.equals('rule.moduleTypes', null),
        //         ]),
        //     );
        //
        //     // if (this.canSetLoadingRules) {
        //     //     Shopware.State.commit('swProductDetail/setLoading', ['rules', true]);
        //     // }
        //     // this.ruleRepository.search(ruleCriteria).then((res) => {
        //     //     this.rules = res;
        //     //     this.totalRules = res.total;
        //     //
        //     //     Shopware.State.commit('swProductDetail/setLoading', ['rules', false]);
        //     // });
        //     //
        //     // this.isInherited = this.isChild && !this.product.prices.total;
        // },
         onAddRentalProduct() {
             const rowData = this.productRentalProductRepository.create(Shopware.Context.api);
             rowData.ruleId = null;
             rowData.productId = this.product.id;
             rowData.dayStart = 1
             rowData.dayEnd = null;
             //this.newRentalProductData.add(rowData);
             this.newRentalProductData.push(rowData);
             this.product.extensions.rentalProduct = this.newRentalProductData;
         },
         getStartDayTooltip(itemIndex, day) {
             return {
                 message: this.$tc('sw-product.rentalProduct.dayDisabledTooltip'),
                 width: 275,
                 showDelay: 200,
                 disabled: (itemIndex !== 0 || day !== 1),
             };
         },
        onDayEndChange(item, rentalGroupData) {
            // when not last price
            if (rentalGroupData.indexOf(item) + 1 !== rentalGroupData.length) {

                return;
            }
            this.createPriceRule(rentalGroupData);
        },
        createPriceRule(dayProductdata) {
            const newDayCreate = this.productRentalProductRepository.create();
            newDayCreate.ruleId = dayProductdata.ruleId;
            newDayCreate.productId = this.product.id;
            const highestEndValue = Math.max(...dayProductdata.map((dayProductdata) => dayProductdata.dayEnd));
            newDayCreate.dayStart =  highestEndValue + 1;
            newDayCreate.dayEnd = null;
            this.newRentalProductData.push(newDayCreate);
            this.product.extensions.rentalProduct = this.newRentalProductData;
        },
         onDeleteRentalProduct(){
             const currentProductId = this.$route.params.id;
             const customProductCriteria = new Criteria();
             customProductCriteria.addFilter(Criteria.equals('productId', currentProductId));
             this.productRentalProductRepository
                 .search(customProductCriteria, Shopware.Context.api)
                 .then((results) => {
                     const currentRentalProductData = results;
                     if(currentRentalProductData.total > 0) {
                         currentRentalProductData.forEach(result => {
                             this.newRentalProductData.pop(result)
                             this.product.extensions.rentalProduct.remove(result.id);
                         });

                     }
                 });
         },

        onSingleRentalProductDelete(item) {
            const matchingDayGruop = this.newRentalProductData;

            // if it is the only item in the priceRuleGroup
            if (matchingDayGruop.length <= 1) {
                this.createNotificationError({
                    message: this.$tc('sw-product.rentalProduct.deletionNotPossibleMessage'),
                });

                return;
            }

             // get actual rule index
             const actualItemIndex = matchingDayGruop.indexOf(item);

             // if it is the last item
             if (typeof item.dayEnd === 'undefined' || item.dayEnd === null) {
                 // get previous rule
                 const previousRule = matchingDayGruop[actualItemIndex - 1];

                 // set the quantityEnd from the previous rule to null
                 previousRule.dayEnd = null;
             } else {
                 // get next rule
                 const nextRule = matchingDayGruop[actualItemIndex + 1];

                 // set the quantityStart from the next rule to the quantityStart from the actual rule
                 nextRule.dayStart = item.dayStart;
             }

            // delete rule
            this.newRentalProductData.remove(item.id);
           // this.product.prices.remove(priceRule.id);

         },

     }
});
