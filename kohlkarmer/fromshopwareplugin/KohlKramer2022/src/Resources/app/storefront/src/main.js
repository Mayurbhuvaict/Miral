import stickyHeader from "./stickyHeaderJquery/index.js";
import videoPlay from "./videoPlayJquery/index.js";
import openModel from "./openModelJquery/index.js";
//import homeSliderJquery from "./homeSliderJquery/index.js";

window.PluginManager.register(
    'stickyHeader',
    stickyHeader,
    '[sticky-header]'
);

window.PluginManager.register(
    'videoPlay',
    videoPlay,
    '[video-play]'
);

// window.PluginManager.register(
//     'openModel',
//     openModel,
//     '[open-Model]'
// );
window.PluginManager.register('openModel', openModel);

//console.log('SwagBasicExampleTheme JS loaded');
//section-3
//window.PluginManager.register('homeSliderJquery', homeSliderJquery);


