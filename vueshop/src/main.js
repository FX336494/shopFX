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
import * as cusFilter from './components/js/filters.js';

Vue.config.productionTip = false

FastClick.attach(document.body)
//图片懒加载
Vue.use(VueLazyload, {
  loading: require('./assets/images/lazy.jpg')
})

Vue.prototype.$post_ = post_;
// 导出的是对象，可以直接通过key和value来获得过滤器的名和过滤器的方法
Object.keys(cusFilter).forEach(key => {
    Vue.filter(key, cusFilter[key])
})

new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
