const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            vue$: "vue/dist/vue.runtime.esm-browser.prod",
        },
    },
    devtool: "source-map",
};
