import './bootstrap';
import { Fancybox } from "@fancyapps/ui/dist/fancybox/fancybox.esm.js"
Fancybox.bind("[data-fancybox]", {});

import {createApp} from 'vue';
import { createStore } from 'vuex';

import App from './App.vue'

const app = createApp(App)
// шина событий
import eventBus from './plugins/event-bus';
app.use(eventBus);

// ********** store start
import store from "./stores";
app.use(store)
// ********** store end


app.mount("#app");

// app.component('TodoDeleteButton', TodoDeleteButton)
