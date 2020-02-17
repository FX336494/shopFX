<template>
    <div v-loading="ifload">
		<el-row>
			<el-col :xs="24" :sm="20" :md="20" :lg="20" :xl="20">
				<el-form ref="form" :model="form" label-width="120px">

					<el-form-item :label="item.name?item.name:'默认'" v-for="(item,index) in data" :key="index">
						<el-row>
							<el-col class="col" :span="4" v-for="(images,i) in item.images" :key="i">
								<div class="image">
									<img :src="images.image_url" width="60%"/>
									<i class="el-icon-delete" @click="delImg(index,i)"></i>
								</div>
								<div class="extra">
									<el-input placeholder="排序"
										size="mini"
										class="sort"
										v-model="data[index]['images'][i]['sort']">
									<i slot="prefix" class="el-input__icon el-icon-sort"></i>
									</el-input>
								</div>
							</el-col>
							<el-col :span="3" v-show="data[index].images.length<5">
								<div class="upload">
									<upload
										:size='32'
                                        :uploadType="'2'"
										:params="{index:index}"
										@showImg="showImg">
									</upload>
								</div>

							</el-col>
						</el-row>
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
	import {baseUrl} from '@/components/js/request.js';
	import upload from '@/components/utils/upload';
    import loading from '@/components/utils/loading';
	export default{
		components:{upload,loading},
		props:{
			goodsCommonid:{type:Number,default:0},
		},
		data() {
			return {
				ifload:false,
				form:{},
				data:[],
			}
		},
		created() {
			this.form.goods_commonid = this.goodsCommonid;
			this.$nextTick(() =>{
				this._initGoodsImages();
			})
		},
		methods:{
			_initGoodsImages() {
				this.ifload = true;
				let param = {goods_commonid:this.goodsCommonid};
				this.$post_('goods/goods/get_goods_images',param,(res) => {
					console.log(res);
					this.ifload = false;
					this.data = res.data;
				})
			},
			//显示图片
			showImg(imgUrl,val) {
				console.log(this.data);
				this.data[val.index].images.push({image_url:imgUrl});
            },
			//删除图片
			delImg(index,i) {
				this.data[index]['images'].splice(i,1);
			},

			save() {
				// console.log(this.data);
				this.ifload = true;
				let param = {
					data:JSON.stringify(this.data),
					goods_commonid:this.goodsCommonid,
				};
				this.$post_('goods/goods/save_images',param,(res) => {
					console.log(res);
					this.ifload = false;
					if(res.code=='0'){
						this.$message.success(res.msg);
					}else{
						this.$message.error(res.msg);
					}
				})
			},
		},
	}
</script>

<style scoped="scoped">
	.col{
		/* background: yellow; */
		height: 150px;
	}
	.image{
		position: relative;
		height: 100px;
		/* background: red; */
		overflow: hidden;
		text-align: center;
		padding: 5px 10px;
	}
	.image img{
		width: 100%;
		border:1px solid #f0f0f0;

	}
	.image .el-icon-delete{
		position: absolute;
		right: 3px;
		top: 0px;
		color:red;

	}
	.upload{
		/* background: #006666; */
		line-height: 50px;
		vertical-align: middle;
		text-align: center;
	}
	.extra{
		text-align: center;
	}
	.extra .sort{
		width: 60%;

	}

</style>
