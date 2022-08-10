/* View */
import './view/sw-product-detail-rentalproduct';

/* Page */
import './page/sw-product-detail';

/* Component */
//import './component/sw-product-upselling-form';
//import './component/sw-product-up-selling-assignment';

/* Snippet */
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';


Shopware.Module.register('sw-product-detail-tab-rentalproduct', {
    snippets: {
        'de-DE': deDE,
        'en-GB': enGB,
    },

    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.product.detail') {
            currentRoute.children.push({
                name: 'sw.product.detail.rentalproduct',
                path: '/sw/product/detail/:id/rentalproduct',
                component: 'sw-product-detail-rentalproduct',
                meta: {
                    parentPath: "sw.product.index"
                }
            });
        }
        next(currentRoute);
    }
});
