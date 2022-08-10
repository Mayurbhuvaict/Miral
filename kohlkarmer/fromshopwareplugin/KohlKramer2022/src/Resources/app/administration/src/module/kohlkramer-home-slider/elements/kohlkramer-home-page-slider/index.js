import './component';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'kohlkramer-home-page-slider',
    label: 'kohlkramer-home-slider.element.elementLabel',
    component: 'sw-cms-el-kohlkramer-home-page-slider',
    previewComponent: 'sw-cms-el-kohlkramer-home-page-slider',
});

