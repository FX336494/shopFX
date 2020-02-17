<template>
    <div>
		<div class="container" style="border: none;">
            <el-page-header @back="backPintuan" content="实例编辑" style="margin-bottom: 20px;">
            </el-page-header>
			<el-row>
				<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
					<el-form ref="form" :model="form" label-width="100px">
						<el-form-item label="实例名称">
							<el-input v-model="form.title"></el-input>
						</el-form-item>
						<el-form-item label="图片">
                            <el-image
                                style="width: 100px; height: 100px"
                                :src="form.image"
                                fit="contain">
                                <div slot="error" class="image-slot">
                                    <i class="el-icon-picture-outline"></i>
                                </div>
                            </el-image>
                            <upload  @showImg="showImg" :size="32" :uploadType="'2'" ></upload>
						</el-form-item>
						<el-form-item label="开始时间">
                            <div class="block">
                                <el-date-picker
                                  v-model="form.start_time"
                                  type="datetime"
                                  value-format="yyyy-MM-dd HH:mm:ss"
                                  placeholder="选择日期时间">
                                </el-date-picker>
                            </div>
						</el-form-item>
						<el-form-item label="结束时间">
                            <div class="block">
                                <el-date-picker
                                  v-model="form.end_time"
                                  type="datetime"
                                  value-format="yyyy-MM-dd HH:mm:ss"
                                  placeholder="选择日期时间">
                                </el-date-picker>
                            </div>
						</el-form-item>
						<el-form-item label="预告时间">
                            <div class="block">
                                <el-date-picker
                                  v-model="form.trailer_time"
                                  type="datetime"
                                  value-format="yyyy-MM-dd HH:mm:ss"
                                  placeholder="选择日期时间">
                                </el-date-picker>
                            </div>
						</el-form-item>
						<el-form-item label="开团人数">
							<el-input v-model="form.min_nums"></el-input>
						</el-form-item>
						<el-form-item label="成团时限">
							<el-input v-model="form.time_limit" placeholder="在规定时间内完成拼团 单位 分"></el-input>
						</el-form-item>

						<el-form-item label="状态">
                            <el-radio v-model="form.state" label="1">开启</el-radio>
                            <el-radio v-model="form.state" label="0">关闭</el-radio>
						</el-form-item>

						<el-form-item label="活动结束后">
                            <el-radio v-model="form.end_goods_status" label="1">变成普通产品</el-radio>
                            <el-radio v-model="form.end_goods_status" label="2">下架</el-radio>
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
				ifload:false,
				form:{
                    instance_id:0,
                    title:'',
                    start_time:'',
                    end_time:'',
                    trailer_time:'',
                    min_nums:2,
                    time_limit:'',
                    state:'1',
                    end_goods_status:'1',
                    image:'https://via.placeholder.com/150',
                },
			}
		},
		created() {
            this.form.instance_id = this.$route.query.id;
			this.$nextTick(()=>{
				this.getData();
			})
		},
		methods: {
			getData() {
                this.ifload = true;
                let params = {
                    instance_id : this.form.instance_id
                }
                this.$post_('promotion/pintuan/get_instance',params,(res)=>{
                    if(res.code==0 && res.data){
                        this.form.instance_id = res.data.instance_id;
                        this.form.title = res.data.title;
                        this.form.image = res.data.image;
                        this.form.start_time = res.data.start_time;
                        this.form.end_time = res.data.end_time;
                        this.form.trailer_time = res.data.trailer_time;
                        this.form.state = res.data.state;
                        this.form.min_nums = res.data.min_nums;
                        this.form.time_limit = res.data.time_limit;
                        this.form.end_goods_status = res.data.end_goods_status;
                    }
                    this.ifload = false;
                })

			},

            //上传图片
            showImg(url) {
                this.form.image = url;
            },

            //保存数据
            saveData() {
                let params = {
                    data:JSON.stringify(this.form)
                }
                this.ifload = true;
                this.$post_('promotion/pintuan/edit_pintuan_instance',params,(res)=>{
                    if(res.code=='0'){
                        this.$message.success(res.msg);
                        this.$router.push({path:'/page/promotion/pintuan/pintuan',query:{tab:'instance'}})
                    }else{
                        this.$message.error(res.msg);
                    }
                    this.ifload = false;
                })
            },
            backPintuan() {
                this.$router.push({path:'/page/promotion/pintuan/pintuan',query:{tab:'instance'}})
            }
		}
	}
</script>

<style scoped="scoped">
</style>
