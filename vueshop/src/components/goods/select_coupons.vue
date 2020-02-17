<template>
	<div class="wrapper">
		<topTile :name="headerName"></topTile>
		<div class="content">
        <div class="nouse" >
          <span :class="nouse?'active':''" @click="CheckCoupons([])">不使用优惠券</span>
        </div>

        <div class="list">
          <div class="item" @click="CheckCoupons(item)" v-for="(item,index) in coupons" :key="index">

            <div class="row money-row">
              <div class="money">省<span>{{item.coupons_money}}</span></div>
            </div>

            <div class="row desc-row">
              <div class="name" v-show="item.type=='1'">
                <span>{{item.coupons_money}}</span>元优惠券
              </div>
              <div class="name" v-show="item.type=='2'">
                <span>{{item.discount}}</span>折优惠券
              </div>
            </div>

            <div class="row op">
              <i class="iconfont" :class="curCouponId==item.id?'active icon-gouxuan':'icon-gouxuan1'"></i>
            </div>

          </div>
        </div>
        <div class="footer" @click="finished">
          完成
        </div>
		</div>

		<fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :url="url"></fadeAlert>
	</div>
</template>

<script type="text/javascript">
	import topTile from '@/components/common/top_title'
	import fadeAlert from '@/components/common/fadealert'
	export default {
    props:{
      coupons:'',
    },
		components: {
			topTile,
			fadeAlert
		},
		data() {
			return {
				headerName: '选择优惠券',
				ifload: false,
				msg: '',
				showAlert: false,
				url: '',
        curCouponId:0,
        curCoupon:[],
        nouse:false,
			}
		},
    created() {
      console.log(this.coupons);
    },
    methods:{
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
		bottom: 50px;
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
    height: 60px;
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
    line-height: 60px;
    color:#000;
    font-size: 13px;
    letter-spacing: 2px;
  }
  .content .list .item .op .iconfont{
    font-size: 24px;
  }
  .content .list .item .op .active{
    color: #FF0036;
  }
  .footer{
    position: fixed;
    bottom: 0px;
    left: 0px;
    width: 100%;
    background: #FF0036;
    color:#fff;
    height: 50px;
    line-height: 50px;
    font-size: 15px;
    letter-spacing: 2px;
  }

</style>
