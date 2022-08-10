import './component';
import './preview';
import CMS
    from "../../../../../../../../../../../../../platform/src/Administration/Resources/app/administration/src/module/sw-cms/constant/sw-cms.constant";

Shopware.Service('cmsService').registerCmsBlock({
    name: 'kohlkramer-theme-image-text-bubble',
    label: 'KohlKramer-theme.blocks.kohlkramer-theme.imageTextBubble.label',
    category: 'kohlkramer-theme',
    component: 'sw-cms-block-kohlkramer-theme-image-text-bubble',
    previewComponent: 'sw-cms-preview-kohlkramer-theme-image-text-bubble',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        'center-image': {
            type: 'image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                    minHeight: { source: 'static', value: '300px' },
                },
                data: {
                    media: {
                        value: CMS.MEDIA.previewPlant,
                        source: 'default',
                    },
                },
            },
        },
        'center-text': {
            type: 'text',
            default: {
                config: {
                    content: {
                        source: 'static',
                        value: `
                        <h2 style="text-align: center;">Lorem Ipsum dolor</h2>
                        <p style="text-align: center;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                        sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                        sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
                        `.trim(),
                    },
                },
            },
        },
    },
});
