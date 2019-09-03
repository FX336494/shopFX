<template>
  	<div class="recommend" ref="recommend">
  		<topTile :name="headerName" :showBack="true"></topTile>

	    <div class="cate_list" v-for="(item,index) in list" >
		    <div class="user_detail" @click="select_it(item.address_id)">
		        <span>{{item.true_name}}</span>
		        <span style="float: right;">{{item.mob_phone}}</span>
		        <div class="name" v-html="item.area_info+item.address"></div>
		    </div>

		    <div class="changedel">
		        <li @click.stop="edit(item.address_id)">
		        	<i class="iconfont icon-xiugai-copy"></i>
		        </li>
		        <li @click.stop="showDel(item.address_id,index)" style="margin-right:30px;">
		        	<i class="iconfont icon-shanchu"></i>
		        </li>
		        <div style="clear:both;"></div>
		    </div>
      </div>

      <div class="place_set2" @click="addplece()">
        <div style="font-size: 20px;color:#bbb">+点击添加收货地址</div>
      </div>
      <alert @callback_="callback_" :content="content" :ifshow="if_show"></alert>
      <loading :ifload="ifload"></loading>
  	</div>
</template>
<script>
import topTile from '@/components/common/top_title'
import alert from '@/components/common/alert'
import loading from '@/components/common/loading'
export default{
	components: {
      	topTile,
      	alert,
        loading
    },
	data (){
		return {
			headerName:"地址管理",
      ifload:false,
			list:[],
			content:'',
			if_show:false,
			delId:'',
			delIndex:'',
			fromType:1,
		}
	},
	created(){
		this.fromType = this.$route.query.type?this.$route.query.type:1;
		this.init_data()
	},
	watch:{

	},
	methods:{
		init_data(){
      this.ifload = true;
      this.$post_('order/address/address_list',{},(res)=>{
        console.log(res);
        this.list = res.data;
        this.ifload = false;
      });
		},
		showDel(id,index){
			this.content = "是否删除该地址？"
			this.if_show = true
			this.delId = id
			this.delIndex = index
		},
		del(id){
			var data = {
				address_id:id
			}
			this.$post_('order/address/delete',data,(res)=>{
        console.log(res)
      });
		},
		callback_(id){
			this.if_show = false
			if(id==1){
				this.del(this.delId)
				this.list.splice(this.delIndex,1)
			}else if(id == 0){
				return
			}
		},
		edit(id){
			this.$router.push({path:'/page/order/address_add',query:{address_id:id}})
		},
		addplece(){
			this.$router.push({path:'/page/order/address_add'})
		},
		select_it(addressId) {
			let param = {};
			param.is_default = '1';
      param.address_id = addressId;
      this.$post_('order/address/edit',param,(res)=>{
          console.log(res);
          this.$router.go(-1);
			})

		}
	}
}
</script>

<style scoped>
.place_set2{
	padding: 10px 15px;
	font-size: 15px;
	background: #fff;
	margin-top: 10px;
	text-align: center;
}
.place_set2:active{
	background: #ced0d2;
}
.cate_list{
    margin-top:5px;
    background: #fff;
}
.cate_list .name{
    font-size: 14px;
    margin-left: 0px;
    /*border-bottom: 1px solid rgba(193,193,193,0.3);*/
    line-height: 30px;
    height: 30px;
    color: #666;
}
.changedel{
	padding-right:30px;
}
.changedel .icon-xiugai-copy{
	font-size: 24px;
	color:#03b9c3;
}
.changedel .icon-shanchu{
	font-size: 24px;
	color:red;
}

.changedel li{
	float: right;
	height: 30px;
	line-height: 30px;
}
.changedel span{
    font-size: 14px;
    padding-left: 5px;
}
.user_detail{
    padding: 5px 15px 10px 15px;
    color:#666;
    font-size: 16px;
}
.recommend{
	position: fixed;
	top:0;
	width: 100%;
	bottom:0;
	z-index: 999;
	text-align: left;
	background: #efeff4;
}
.recommend-content{
	height: 100%;
  	overflow: hidden;
}
.clear{
	clear:both;
}
</style>
