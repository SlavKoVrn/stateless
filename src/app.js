import Vue from 'vue';
import Vuetify from 'vuetify';
import BootstrapVue from 'bootstrap-vue' 
import App from './App.vue';

Vue.use(Vuetify);
Vue.use(BootstrapVue);

new Vue({
    el: '#app',
    vuetify: new Vuetify(),
    render: h => h(App)
});