import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'kohlkramer-home-page-slider',
    label: 'kohlkramer-home-slider.block.blockLabel',
    category: 'kohlkramer-theme',
    component: 'sw-cms-block-kohlkramer-home-page-slider',
    previewComponent: 'sw-cms-preview-kohlkramer-home-page-slider',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed'
    },
    slots: {
        kohlkramerHomePageSlider: 'kohlkramer-home-page-slider',
    }
});

