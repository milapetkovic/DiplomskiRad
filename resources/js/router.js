import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from './components/pages/Home.vue';
import About from './components/pages/About.vue';
import PropertiesIndex from './components/pages/Properties/Index.vue';
import PropertiesCreate from './components/pages/Properties/Create.vue';
import * as VueGoogleMaps from 'vue2-google-maps';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VueRouter);
Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyBB4zjf7eY2CGYKtunkOlLEq0xgKpZ61pw',
        libraries: 'places, drawing, visualization', // This is required if you use the Autocomplete plugin
        // OR: libraries: 'places,drawing'
        // OR: libraries: 'places,drawing,visualization'
        // (as you require)

        //// If you want to set the version, you can do so:
        // v: '3.26',
    },

    //// If you intend to programmatically custom event listener code
    //// (e.g. `this.$refs.gmap.$on('zoom_changed', someFunc)`)
    //// instead of going through Vue templates (e.g. `<GmapMap @zoom_changed="someFunc">`)
    //// you might need to turn this on.
    // autobindAllEvents: false,

    //// If you want to manually install components, e.g.
    //// import {GmapMarker} from 'vue2-google-maps/src/components/marker'
    //// Vue.component('GmapMarker', GmapMarker)
    //// then disable the following:
    // installComponents: true,
})

const router = new VueRouter({
    mode: 'history',
    linkExactActiveClass: 'active',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/about',
            name: 'about',
            component: About
        },
        {
            path: '/properties/index',
            name: 'properties-index',
            component: PropertiesIndex
        },
        {
            path: '/properties/create',
            name: 'properties-create',
            component: PropertiesCreate
        },
    ]
});

export default router;
