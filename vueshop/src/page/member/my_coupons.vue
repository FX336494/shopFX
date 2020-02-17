<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<div class="content">

        <div class="list" v-show="coupons.length">
          <div class="item" v-for="(item,index) in coupons" :key="index">

            <div class="row money-row">
              <div class="money" v-show="item.type=='1'"><span>{{item.coupons_money}}</span>元</div>
              <div class="money" v-show="item.type=='2'"><span>{{item.discount}}</span>折</div>
              <div class="desc">{{item.coupons_desc}}</div>

            </div>

            <div class="row desc-row">
              <div class="name">{{item.coupons_name}}</div>
              <div class="time">{{item.time_desc}}</div>
              <div class="time">{{item.type_text}}</div>
            </div>

            <router-link v-show="item.coupon_state=='1'" tag="div" to="/page/index" class="row op">
              去使用
            </router-link>
            <div class="row op used" v-show="item.coupon_state!='1'">{{item.btn_desc}}</div>

          </div>
        </div>
        <div v-show="!coupons.length">暂无优惠券</div>
		</div>
    <loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
	import topTile from '@/components/common/top_title'
  import loading from '@/components/common/loading'
	export default {
		components: {
			topTile,
      loading
		},
		data() {
			return {
				headerName: '我的优惠券',
				ifload: false,
        coupons:[],
			}
		},
    created() {
      this.$nextTick(()=>{
        this.getData();
      })
    },
    methods:{

    //获取数据
      getData() {
        this.ifload = true;
        this.$post_('goods/coupons/coupons_list',{},(res)=>{
          console.log(res);
          this.ifload = false;
          this.coupons = res.data;
        });
      },

      //选中
      CheckCoupons(item) {
        this.curCoupon = item;
        if(item.id){
          this.curCouponId = item.id;
          this.nouse = false;
        }else{
          this.curCouponId = 0;
          this.nouse = true;
        }
      },

      //完成
      finished() {
        this.$emit('checkCoupon',this.curCoupon);
      }
    }
	}
</script>

<style type="text/css" scoped>
	.wrapper {
		position: absolute;
		top: 0px;
		bottom:0px;
		left: 0px;
		width: 100%;
		overflow: auto;
		background: #f0f0f0;
	}

	.wrapper .content {
		width: 100%;
	}
  .content .nouse{
    width: 100%;
  }
  .content .nouse span{
    width: 80%;
    margin: 20px auto;
    background: #fff;
    display: inline-block;
    height: 40px;
    line-height: 40px;
    border-radius: 10px;
  }
  .content .nouse .active{
     background: #FF0036;
     color:#fff;
  }

  .content .list{
    font-size: 12px;
  }
  .content .list .item{
    display: flex;
    background: #fff;
    width: 98%;
    margin: 10px auto;
    border-radius: 10px;
  }
  .content .list .item .row{
    flex: 1;
    /* padding: 20px 0px; */
    height: 80px;
  }
  .content .list .item .money-row{
    color:#FF0036;
    text-align: center;
    padding-top: 10px;
    flex: 3;
  }
  .content .list .item .desc-row{
    text-align: left;
    padding-top: 20px;
    padding-left: 15px;
    flex: 4;
  }
  .content .list .item .desc-row .name{
    font-size: 14px;
    font-weight: bold;
  }
  .content .list .item .money-row .money span{
    font-size: 24px;
  }

  .content .list .item .op{
     flex: 2;
    line-height: 80px;
    background: #FF0036;
    color:#fff;
    font-size: 13px;
    letter-spacing: 2px;
  }
  .content .list .item .used{
    background: #ddd;
    color:#555;
  }

</style>
