<template>
    <div>
		<div class="container" style="border: none;">
            <el-page-header @back="backPintuan" content="优惠券编辑" style="margin-bottom: 20px;">
            </el-page-header>
			<el-row>
				<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
					<el-form ref="form" :model="form" label-width="100px">
						<el-form-item label="优惠券名称">
							<el-input v-model="form.coupons_name"></el-input>
						</el-form-item>
						<el-form-item label="优惠券描述">
							<el-input v-model="form.coupons_desc"></el-input>
						</el-form-item>
						<el-form-item label="领取方式">
							<el-select v-model="form.use_type" placeholder="请选择" @change="changeType">
                                <el-option
                                    v-for="item in getTypeText"
                                    :key="item.val"
                                    :label="item.name"
                                    :value="item.val"
                                >
                                </el-option>
                            </el-select>
						</el-form-item>
						<el-form-item label="优惠券图片">
                            <el-image
                                style="width: 100px; height: 100px"
                                :src="form.coupons_image"
                                fit="contain">
                                <div slot="error" class="image-slot">
                                    <i class="el-icon-picture-outline"></i>
                                </div>
                            </el-image>
                            <upload  @showImg="showImg" :size="32" :uploadType="'2'" ></upload>
						</el-form-item>

						<el-form-item label="优惠券类型">
                            <el-radio v-model="form.type" label="1">满减券</el-radio>
                            <el-radio v-model="form.type" label="2">折扣券</el-radio>
						</el-form-item>

						<el-form-item label="消费金额">
							<el-input v-model="form.consume_amount" type="number"></el-input>
						</el-form-item>

						<el-form-item label="优惠券面额" v-show="form.type=='1'">
							<el-input v-model="form.coupons_money" type="number"></el-input>
						</el-form-item>

						<el-form-item label="优惠折扣" v-show="form.type=='2'">
							<el-input v-model="form.discount" type="number"></el-input>
						</el-form-item>

						<el-form-item label="优惠券类型">
                            <el-radio v-model="form.time_type" label="1">时间段</el-radio>
                            <el-radio v-model="form.time_type" label="2">领取N天后</el-radio>
						</el-form-item>

						<el-form-item label="开始时间" v-show="form.time_type=='1'">
                            <div class="block">
                                <el-date-picker
                                  v-model="form.start_time"
                                  type="datetime"
                                  value-format="yyyy-MM-dd HH:mm:ss"
                                  placeholder="选择日期时间">
                                </el-date-picker>
                            </div>
						</el-form-item>
						<el-form-item label="结束时间" v-show="form.time_type=='1'">
                            <div class="block">
                                <el-date-picker
                                  v-model="form.end_time"
                                  type="datetime"
                                  value-format="yyyy-MM-dd HH:mm:ss"
                                  placeholder="选择日期时间">
                                </el-date-picker>
                            </div>
						</el-form-item>

						<el-form-item label="有效天数" v-show="form.time_type=='2'">
							<el-input v-model="form.useful_day" type="number" min="0"></el-input>
						</el-form-item>

						<el-form-item label="发放总数">
							<el-input v-model="form.total_nums" type="number"></el-input>
						</el-form-item>
						<el-form-item label="每人限领数">
							<el-input v-model="form.limit_nums" type="number"></el-input>
						</el-form-item>

						<el-form-item label="类型">
							<el-select v-model="form.use_type" placeholder="请选择" @change="changeType">
                                <el-option
                                    v-for="item in useTypeText"
                                    :key="item.val"
                                    :label="item.name"
                                    :value="item.val"
                                >
                                </el-option>
                            </el-select>
						</el-form-item>
                        <el-form-item label="当前选择" v-show="form.use_type>1 && typeMsg" >
                            <span>{{typeMsg}}</span>
                        </el-form-item>

						<el-form-item label="状态">
                            <el-radio v-model="form.state" label="1">有效</el-radio>
                            <el-radio v-model="form.state" label="0">失效</el-radio>
						</el-form-item>

                        <el-form-item label="">
                            <el-button :loading="ifload" type="primary" @click="saveData">保存</el-button>
                        </el-form-item>

					</el-form>
				</el-col>
			</el-row>
		</div>

		<loading :ifload="ifload"></loading>
        <el-dialog title="选择商品" :visible.sync="showBox" width="90%">
            <chooseGoods v-if="showBox" @chooseEmit="chooseEmit" :isPrice="0"></chooseGoods>
        </el-dialog>
        <el-dialog title="选择分类" :visible.sync="showClsBox" width="60%">
            <chooseClass @chooseEmit="classEmit"></chooseClass>
        </el-dialog>
	</div>
</template>

<script type="text/javascript">
	import loading from '@/components/utils/loading';
    import upload from '@/components/utils/upload';
    import chooseGoods from '@/components/goods/choose_goods';
    import chooseClass from '@/components/goods/choose_class';
	export default{
		components:{loading,upload,chooseGoods,chooseClass},
		data() {
			return {
				ifload:false,
				form:{
                    instance_id:0,
                    coupons_name:'',
                    coupons_desc:'',
                    get_type:'1',
                    type:'1',
                    start_time:'',
                    end_time:'',
                    coupons_money:'0',
                    discount:0,
                    total_nums:10,
                    limit_nums:1,
                    consume_amount:'0',
                    use_type:1,
                    time_type:'1',
                    useful_day:0,
                    type_id:0,
                    state:'1',
                    coupons_image:'https://via.placeholder.com/150',
                },
                typeMsg:'',
                useTypeText:[{val:1,name:'通用券'},{val:2,name:'分类下可用'},{val:3,name:'单品可用'}],
                getTypeText:[{val:1,name:'免费领取'}],
                showBox:false,
                showClsBox:false,
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
                if(this.form.instance_id<=0) return;
                this.ifload = true;
                let params = {
                    instance_id : this.form.instance_id
                }
                this.$post_('promotion/coupons/get_instance',params,(res)=>{
                    if(res.code==0 && res.data){
                        this.form.instance_id = res.data.instance_id;
                        this.form.coupons_name = res.data.coupons_name;
                        this.form.coupons_desc = res.data.coupons_desc;
                        this.form.get_type = res.data.get_type;
                        this.form.type = res.data.type;
                        this.form.coupons_image = res.data.coupons_image;
                        this.form.start_time = res.data.start_time;
                        this.form.end_time = res.data.end_time;
                        this.form.coupons_money = res.data.coupons_money;
                        this.form.discount = res.data.discount;
                        this.form.time_type = res.data.time_type;
                        this.form.useful_day = res.data.useful_day;
                        this.form.total_nums = res.data.total_nums;
                        this.form.limit_nums = res.data.limit_nums;
                        this.form.consume_amount = res.data.consume_amount;
                        this.form.use_type = Number(res.data.use_type);
                        this.form.type_id = res.data.type_id;
                        this.form.state = res.data.state;
                        this.typeMsg = res.data.type_msg;
                    }
                    this.ifload = false;
                })

			},

            //上传图片
            showImg(url) {
                this.form.coupons_image = url;
            },

            changeType() {
                console.log(this.form.use_type);
                this.typeMsg = '';
                if(this.form.use_type=='3'){
                    this.showBox = !this.showBox;
                }else if(this.form.use_type=='2'){
                    this.showClsBox = !this.showClsBox;
                }else{
                    this.form.type_id = 0;
                }
            },
            //选择商品
            chooseEmit(goods) {
                this.showBox = false;
                console.log(goods);
                this.form.type_id = goods.goods_commonid;
                this.typeMsg = goods.goods_name;
            },
            //选择分类
            classEmit(classObj) {
                this.showClsBox = false;
                if(!classObj.gc_id) return ;
                this.form.type_id = classObj.gc_id;
                let msg = '';
                if(classObj.gc_id1){
                    msg = classObj.gc_id1.category_name;
                }
                if(classObj.gc_id2){
                    msg += '> '+classObj.gc_id2.category_name;
                }
                if(classObj.gc_id3){
                    msg += '> '+classObj.gc_id3.category_name;
                }
                this.typeMsg = msg;
                console.log(classObj);
            },

            //保存数据
            saveData() {
                let params = {
                    data:JSON.stringify(this.form)
                }
                this.ifload = true;
                this.$post_('promotion/coupons/edit_coupons_instance',params,(res)=>{
                    if(res.code=='0'){
                        this.$message.success(res.msg);
                        this.$router.push({path:'/page/promotion/coupons/coupons'})
                    }else{
                        this.$message.error(res.msg);
                    }
                    this.ifload = false;
                })
            },
            backPintuan() {
                this.$router.push({path:'/page/promotion/coupons/coupons'})
            }
		}
	}
</script>

<style scoped="scoped">
</style>
