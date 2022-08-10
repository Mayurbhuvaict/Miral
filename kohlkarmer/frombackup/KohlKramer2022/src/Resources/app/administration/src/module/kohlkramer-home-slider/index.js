import './page/kohlkramer-home-slider-list';
import './page/kohlkramer-home-slider-detail';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

import './blocks/kohlkramer-home-page-slider';
import './elements/kohlkramer-home-page-slider';

const {Module} = Shopware;

Module.register('kohlkramer-home-slider', {
    type: 'plugin',
    name: 'kohlkramerHomeSlider',
    title: 'kohlkramer-home-slider.general.mainMenuItemGeneral',
    description: 'kohlkramer-home-slider.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'default-shopping-paper-bag-product',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'kohlkramer-home-slider-list',
            path: 'list'
        },
        create: {
            component: 'kohlkramer-home-slider-detail',
            path: 'create',
            meta: {
                parentPath: 'kohlkramer.home.slider.list'
            }
        },
        detail: {
            component: 'kohlkramer-home-slider-detail',
            path: 'detail/:id?',
            props: {
                default: (route) => ({kohlkramerHomeSliderId: route.params.id})
            },
            meta: {
                parentPath: 'kohlkramer.home.slider.list'
            }
        }
    },

    navigation: [{
        label: 'kohlkramer-home-slider.general.mainMenuItemGeneral',
        path: 'kohlkramer.home.slider.list',
        parent: 'sw-marketing',
        position: 110
    }]
});
