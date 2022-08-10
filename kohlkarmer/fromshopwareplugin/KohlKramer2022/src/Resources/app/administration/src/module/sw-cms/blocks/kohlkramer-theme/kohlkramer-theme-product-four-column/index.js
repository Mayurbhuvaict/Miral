import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'kohlkramer-theme-product-four-column',
    label: 'KohlKramer-theme.blocks.kohlkramer-theme.productFourColumn.label',
    category: 'kohlkramer-theme',
    component: 'sw-cms-block-kohlkramer-theme-product-four-column',
    previewComponent: 'sw-cms-preview-kohlkramer-theme-product-four-column',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        left: 'kohlkramer-theme-product-box',
        center: 'kohlkramer-theme-product-box',
        right: 'kohlkramer-theme-product-box',
        rights: 'kohlkramer-theme-product-box',
    },
});
