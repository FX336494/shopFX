<template>
	<div class="wrapper">
		<topTile :name="headerName" :showBack="true"></topTile>
			<div class="content">
				<div class="title">{{notice.title}}</div>
				<div class="time">{{notice.update_time*1000 | formatDate}}</div>
				<div class="desc">{{notice.desc}}</div>
				<div class="info" v-html="notice.content"></div>

			</div>
      <loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
import {formatDate} from '@/components/js/common.js'
import topTile from '@/components/common/top_title'
import loading from '@/components/common/loading'
export default{
	components: {
      	topTile,
        loading
	},
	data() {
		return {
			headerName:'公告详情',
      ifload:false,
			id:'',
			notice:[],
		}
	},
	created() {
		this.id = this.$route.query.id;
		this.$nextTick(()=>{
			this._get_notice();
		})
	},
	methods:{
		_get_notice() {
      this.ifload = true;
			this.$post_('platform/article/article_detail',{id:this.id},(res)=>{
				console.log(res);
				this.notice = res.data;
			});
      this.ifload = false;
		},
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
  .wrapper{
    position:absolute;
    top:0px;
    bottom:0px;
    left:0px;
    width:100%;
    overflow:auto;
    background:#f0f0f0;
  }
	.content{
		width: 100%;
		padding: 10px;
		background: #fff;
		text-align: left;
		text-indent: 8px;
	}
	.content .title{
		font-size: 20px;
		font-weight: 500;
		line-height: 35px;
		text-align: center;
	}
	.content .time{
		font-size: 13px;
		color: #555;
	}
	.content .info{
		margin-top: 15px;
	}
  .content >>> img{
    width: 100%;
  }
</style>
