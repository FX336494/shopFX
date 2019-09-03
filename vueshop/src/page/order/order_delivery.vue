<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
		<scroll ref="scroll" @scroll="scroll"  :listenScroll="true" :probeType="3" :BSstyle="BSstyle">
			<div class="content">
				<div class="title">
					<span>{{extend.e_name}}</span>
					<span>{{extend.shopping_code}}</span>
				</div>

				<div class="list">
					<div class="item" v-for="item in data.Traces">
						<div>{{item.AcceptTime}}</div>
						<div>{{item.AcceptStation}}</div>
					</div>
				</div>

			</div>
		</scroll>
	</div>
</template>

<script type="text/javascript">
import topTile from '@/components/common/top_title'
import scroll from '@/components/common/scroll'
import {formatDate} from '@/components/js/common'
export default{
	components: {
      	topTile,
      	scroll
	},
	data() {
		return {
			headerName:'物流追踪',
			BSstyle:"position:absolute;top:40px;bottom:0px;left:0px;width:100%;overflow:hidden;background:#f0f0f0;",
			orderId:0,
			data:[],
			extend:[],
		}
	},
	created() {
		this.orderId = this.$route.query.order_id;
		this.$nextTick(()=>{
			this._init_data();
		})
	},
	methods:{
		_init_data() {
			this.$post_('order/order/search_deliver',{order_id:this.orderId},(res)=>{
				console.log(res);
				if(res.code=='0'){
					this.data = res.data;
					this.extend = res.extend;
				}else{

				}
			});
		},
		scroll() {

		}
	},
	filters:{
        formatDate(time){
            let date = new Date(time);
            return formatDate(date,'yyyy-MM-dd hh:mm');
        }
    }
}
</script>

<style type="text/css" scoped>
	.content{
		width: 100%;
	}
	.content .title{
		width: 100%;
		line-height: 35px;
		font-size: 12px;
		background: #fff;
		text-align: left;
		text-indent: 10px;
	}
	.list{
		width: 100%;
		margin-top: 5px;
	}
	.list .item{
		width: 100%;
		background: #fff;
		text-align: left;
		text-indent: 10px;
		padding:5px;
		border-bottom: 1px solid #f0f0f0;
	}
</style>
