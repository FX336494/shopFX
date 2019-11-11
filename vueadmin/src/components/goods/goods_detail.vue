<template>
    <div v-loading="ifload">
		<el-row>
			<el-col :xs="24" :sm="18" :md="18" :lg="18" :xl="18">
				<el-form ref="form" :model="form" label-width="100px">
					<el-form-item label="商品描述" v-if="initFlag">
                        <editor  @getContent="getContent" :initContent="initContent"></editor>
					</el-form-item>
					<el-form-item label="">
						<el-button :loading="ifload" type="primary" @click="save">保存</el-button>
					</el-form-item>
				</el-form>
			</el-col>
		</el-row>
        <loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
	import {baseUrl,ajax_upload} from '@/components/js/request.js';
    import editor from '@/components/utils/editor';
	import loading from '@/components/utils/loading';
	export default{
		components:{editor,loading},
		props:{
			goodsCommonid:{type:Number,default:0},
		},
		data() {
			return {
				ifload:true,
				form:{
					goods_body:'',
					goods_commonid:0,
				},
                initContent:'',
                initFlag:false,
			}
		},
		created() {
			this.form.goods_commonid = this.goodsCommonid;
			this.$nextTick(()=>{
				this._init_detail();
			})
		},
		methods:{

			_init_detail() {
				if(!this.goodsCommonid) return false;
				let param = {goods_commonid:this.goodsCommonid};
				this.$post_('goods/goods/get_goods_body',param,(res) =>{
					console.log(res);
					this.ifload = false;
					this.form.goods_body = res.data.goods_body;
                    this.initContent = res.data.goods_body;
                    this.initFlag = true;
				})
			},

            //获取编辑器内容
            getContent(content) {
                this.form.goods_body = content;
            },

			save() {
				this.ifload = true;
				this.$post_('goods/goods/add_step2',this.form,(res) => {
					console.log(res);
					this.ifload = false;
					if(res.code=='0'){
						this.$message.success(res.msg);
					}else{
						this.$message.error(res.msg);
					}
				})
			}
		},
	}
</script>

<style>
	.uploader{
		display: none;
	}
    img{
        max-height: 250px;
    }
</style>
