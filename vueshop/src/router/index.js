import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)
export default new Router({
  routes: [
    {
      path: '/',
      redirect:'/page/index'
    },
    {
      path: '/page/index',
      name: '首页',
      component:resolve =>require(['../page/index'],resolve),
      meta: {keepAlive: false,istab:true}
    },

    //会员相关
  	{
      path: '/page/member/login',
      name: '登录',
      component:resolve =>require(['../page/member/login'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/member/reg',
      name: '会员注册',
      component:resolve =>require(['../page/member/reg'],resolve),
      meta: {keepAlive: true}
    },
    {
      path: '/page/member/index',
      name: '个人中心',
      component:resolve =>require(['../page/member/index'],resolve),
      meta: {keepAlive: false,istab:true}
    },
    {
      path: '/page/member/favorites_goods',
      name: '商品收藏',
      component:resolve =>require(['../page/member/favorites_goods'],resolve),
      meta: {keepAlive: false,istab:false}
    },
    {
      path: '/page/member/browse_goods',
      name: '商品浏览足迹',
      component:resolve =>require(['../page/member/browse_goods'],resolve),
      meta: {keepAlive: false,istab:false}
    },
    {
      path: '/page/member/my_coupons',
      name: '我的优惠券',
      component:resolve =>require(['../page/member/my_coupons'],resolve),
      meta: {keepAlive: false,istab:false}
    },    

    //设置相关
    {
      path: '/page/set/index',
      name: '设置',
      component:resolve =>require(['../page/set/index'],resolve),
      meta: {keepAlive: true}
    },
    {
      path: '/page/set/edit_pass',
      name: '修改登录密码',
      component:resolve =>require(['../page/set/edit_pass'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/set/edit_info',
      name: '修改资料',
      component:resolve =>require(['../page/set/edit_info'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/set/notice',
      name: '公告列表',
      component:resolve =>require(['../page/set/notice'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/set/notice_detail',
      name: '公告详情',
      component:resolve =>require(['../page/set/notice_detail'],resolve),
      meta: {keepAlive: false}
    },



    //商品相关
    {
      path: '/page/goods/goods_detail',
      name: '商品详情',
      component:resolve =>require(['../page/goods/goods_detail'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/goods/class',
      name: '商品分类',
      component:resolve =>require(['../page/goods/class'],resolve),
      meta: {keepAlive: false,istab:true}
    },
    {
      path: '/page/goods/class_goods_list',
      name: '分类商品列表',
      component:resolve =>require(['../page/goods/class_goods_list'],resolve),
      meta: {keepAlive: false,istab:false}
    },
    {
      path: '/page/goods/search',
      name: '产品搜索',
      component:resolve =>require(['../page/goods/search'],resolve),
      meta: {keepAlive: false,istab:false}
    },
    {
      path: '/page/goods/search_list',
      name: '搜索结果',
      component:resolve =>require(['../page/goods/search_list'],resolve),
      meta: {keepAlive: false,istab:false}
    },
    {
      path: '/page/goods/goods_evaluate',
      name: '商品评价',
      component:resolve =>require(['../page/goods/goods_evaluate'],resolve),
      meta: {keepAlive: false,istab:false}
    },

    //促销相关
    {
      path: '/page/goods/promotion/pintuan',
      name: '拼团列表',
      component:resolve =>require(['../page/goods/promotion/pintuan'],resolve),
      meta: {keepAlive: false,istab:false}
    },
    {
      path: '/page/goods/promotion/seckill',
      name: '秒杀列表',
      component:resolve =>require(['../page/goods/promotion/seckill'],resolve),
      meta: {keepAlive: false,istab:false}
    },
    {
      path: '/page/goods/promotion/more_seckill',
      name: '秒杀产品列表',
      component:resolve =>require(['../page/goods/promotion/more_seckill'],resolve),
      meta: {keepAlive: false,istab:false}
    },
    {
      path: '/page/goods/promotion/more_pintuan',
      name: '拼团产品列表',
      component:resolve =>require(['../page/goods/promotion/more_pintuan'],resolve),
      meta: {keepAlive: false,istab:false}
    },    

    //订单相关
    {
      path: '/page/order/cart_list',
      name: '购物车列表',
      component:resolve =>require(['../page/order/cart_list'],resolve),
      meta: {keepAlive: false,istab:true}
    },
    {
      path: '/page/order/create_order',
      name: '生成订单',
      component:resolve =>require(['../page/order/create_order'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/order/address_list',
      name: '收货地址列表',
      component:resolve =>require(['../page/order/address_list'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/order/address_add',
      name: '收货地址添加',
      component:resolve =>require(['../page/order/address_add'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/order/order_list',
      name: '我的订单',
      component:resolve =>require(['../page/order/order_list'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/order/order_detail',
      name: '订单详情',
      component:resolve =>require(['../page/order/order_detail'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/order/order_delivery',
      name: '物流追踪',
      component:resolve =>require(['../page/order/order_delivery'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/order/evaluation',
      name: '发表评价',
      component:resolve =>require(['../page/order/evaluation'],resolve),
      meta: {keepAlive: false}
    },


    //钱包相关
    {
      path: '/page/wallet/index',
      name: '我的钱包',
      component:resolve =>require(['../page/wallet/index'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/wallet/integral',
      name: '我的积分',
      component:resolve =>require(['../page/wallet/integral'],resolve),
      meta: {keepAlive: false}
    },
    {
      path: '/page/wallet/integral_list',
      name: '积分明细',
      component:resolve =>require(['../page/wallet/integral_list'],resolve),
      meta: {keepAlive: false}
    },


  ]
})
