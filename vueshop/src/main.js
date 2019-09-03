import Vue from 'vue'
import App from './App'
import router from './router'
import axios from 'axios'
import FastClick from 'fastclick'
import $ from 'jquery'
import VueLazyload from 'vue-lazyload'
import store from '@/components/store/index'
import 'babel-polyfill'
import './assets/css/bootstrap.min.css'
import {post_} from './components/js/common.js';

Vue.config.productionTip = false

FastClick.attach(document.body)
//图片懒加载
Vue.use(VueLazyload, {
  loading: require('./assets/images/lazy.jpg')
})

//Vue.use(axios)
// Vue.prototype.$http = axios
Vue.prototype.$post_ = post_;

new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
