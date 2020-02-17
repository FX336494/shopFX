<template>
	<div class="wrapper">

      <div class="content">
        <i class="iconfont icon-guanbi" @click="close()"></i>
        <div class="title">优惠券</div>

        <div class="list">
          <div class="item" v-for="(item,index) in couponsData" :key="index">

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

            <div class="row op" :class="item.undraw_num==0?'draw':''" @click="getCoupons(item)">
              {{item.btn_desc}}
            </div>

          </div>
        </div>

        <div class="btn" @click="close">完成</div>

      </div>

		<loading :ifload="ifload"></loading>
		<fadeAlert :msg="msg" v-if="showAlert" @hideFade="showAlert=false" :url="url"></fadeAlert>
	</div>
</template>

<script type="text/javascript">
	import loading from '@/components/common/loading'
	import fadeAlert from '@/components/common/fadealert'
	export default {
		components: {
			loading,
			fadeAlert
		},
    props:{
      couponsData:''
    },
		data() {
			return {
				ifload: false,
				msg: '',
				showAlert: false,
				url: '',
			}
		},
    created() {
      console.log(this.couponsData);
    },
    methods:{
      //领取优惠券
      getCoupons(item) {
        if(item.undraw_num<=0) return;
        this.ifload = true;
        let params = {
          instance_id:item.instance_id,
        }
        this.$post_('goods/coupons/get_coupons',params,(res) =>{
          this.msg = res.msg;
          this.showAlert = true;
          this.ifload = false;
          if(res.code=='0')
            this.$emit('success');
        })
      },

			//关闭规格选择
			close(){
				this.$emit('closeCoupons');
			},
    }
	}
</script>

<style type="text/css" scoped>
	.wrapper{
		position: fixed;
		top: 0px;
		left: 0px;
		bottom: 0px;
		width: 100%;
		height: 100%;
		background: rgba(0,0,0,0.8);
		z-index: 100000;
	}
	.wrapper .content{
		position: fixed;
		left: 0px;
		bottom: 0px;
		width: 100%;
		height: 500px;
		font-size: 0px;
		background: #f0f0f0;
    color:000;
    border-radius: 10px 10px 0px 0px;
	}

	.content .icon-guanbi{
		position: absolute;
		right: 0px;
		top: -5px;
		font-size: 30px;
		color:#FF0036;
	}

  .content .title{
    font-size: 16px;
    height: 40px;
    line-height: 40px;
    font-weight: bold;
    letter-spacing: 2px;
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
    font-size: 30px;
  }
  .content .list .item .op{
     flex: 2;
    line-height: 80px;
    background: #FF0036;
    color:#fff;
    font-size: 13px;
    letter-spacing: 2px;
  }
  .content .list .item .draw{
    background: #ddd;
    color:#000;
  }

  .content .btn{
    position: absolute;
    bottom: 10px;
    left: 10%;
    width: 80%;
    height: 40px;
    line-height: 25px;
    background: orange;
    border-radius: 8px;
    color:#fff;
  }
</style>
