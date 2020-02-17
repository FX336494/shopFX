<template>
    <div>
		<div class="container" style="border: none;">
			<el-row>
				<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
					<el-form ref="form" :model="form" label-width="100px">
						<el-form-item label="活动名称">
							<el-input v-model="form.title"></el-input>
						</el-form-item>
						<el-form-item label="活动介绍">
							<el-input v-model="form.intro"></el-input>
						</el-form-item>
						<el-form-item label="图片">
                            <el-image
                                style="width: 100px; height: 100px"
                                :src="form.seckill_image"
                                fit="contain">
                                <div slot="error" class="image-slot">
                                    <i class="el-icon-picture-outline"></i>
                                </div>
                            </el-image>
                            <upload  @showImg="showImg" :size="32" :uploadType="'2'" ></upload>
						</el-form-item>
                        
						<el-form-item label="状态">
                            <el-radio v-model="form.state" label="1">开启</el-radio>
                            <el-radio v-model="form.state" label="0">关闭</el-radio>
						</el-form-item>                        

                        <el-form-item label="">
                            <el-button :loading="ifload" type="primary" @click="saveData">保存</el-button>
                        </el-form-item>

					</el-form>
				</el-col>
			</el-row>
		</div>

		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
	import loading from '@/components/utils/loading';
    import upload from '@/components/utils/upload';
	export default{
		components:{loading,upload},
		data() {
			return {
				ifload:true,
				form:{
                    id:0,
                    title:'',
                    intro:'',
                    state:'1',
                    seckill_image:'https://via.placeholder.com/150',
                },
			}
		},
		created() {
			this.$nextTick(()=>{
				this.getData();
			})
		},
		methods: {
			getData() {
                this.ifload = true;
                this.$post_('promotion/seckill/get_activity',{},(res)=>{
                    if(res.code==0 && res.data){
                        this.form.id = res.data.id;
                        this.form.title = res.data.title;
                        this.form.state = res.data.state;
                        this.form.intro = res.data.intro;
                        this.form.seckill_image = res.data.seckill_image;
                    }
                    this.ifload = false;
                })

			},

            //上传图片
            showImg(url) {
                this.form.seckill_image = url;
            },

            //保存数据
            saveData() {
                this.$post_('promotion/seckill/edit_activity',this.form,(res)=>{
                    if(res.code=='0'){
                        this.$message.success(res.msg);
                        this.getData();
                    }else{
                        this.$message.error(res.msg);
                    }
                })
            }
		}
	}
</script>

<style scoped="scoped">
</style>
