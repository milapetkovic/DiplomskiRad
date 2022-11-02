import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from './components/pages/Home.vue';
import About from './components/pages/About.vue';
import PropertiesIndex from './components/pages/Properties/Index.vue';
import PropertiesCreate from './components/pages/Properties/Create.vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import Autocomplete from '@trevoreyre/autocomplete-vue'
import '@trevoreyre/autocomplete-vue/dist/style.css'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faMagnifyingGlass } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import ByCounties from "./components/pages/Properties/ByCounties.vue";
import ByAmenities from "./components/pages/Properties/ByAmenities.vue";

import Detail from "./components/pages/Properties/Detail.vue";

library.add(faMagnifyingGlass)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VueRouter);
Vue.use(Autocomplete);

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
        {
            path: '/properties/by-counties',
            name: 'by-counties',
            component: ByCounties
        },
        {
            path: '/properties/by-amenities',
            name: 'by-amenities',
            component: ByAmenities
        },
        {
            path: '/properties/detail/:id',
            name: 'properties-detail',
            component: Detail
        }
    ]
});

export default router;
