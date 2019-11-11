<template>
    <div>
		<el-row>
			<el-col :xs="24" :sm="18" :md="18" :lg="18" :xl="18">
				<el-form ref="form" :model="form" label-width="100px">
					<el-form-item label="商品分类">
						<el-select style="width:120px;" v-model="form.gc_id1" placeholder="请选择" @change="classChange">
							<el-option
							  v-for="item in class1" 
							  :key="item.id"
							  :label="item.category_name"
							  :value="item.id">
							</el-option>
						</el-select>	
												
						<el-select  style="width:120px;"  v-show="class2.length" v-model="form.gc_id2"  @change="classChange1" placeholder="请选择">
							<el-option
							  v-for="item in class2"
							  :key="item.id"
							  :label="item.category_name"
							  :value="item.id">
							</el-option>
						</el-select>	
						<el-select style="width:120px;"  v-show="class3.length" v-model="form.gc_id3"  @change="classChange2" placeholder="请选择">
							<el-option
							  v-for="item in class3"
							  :key="item.id"
							  :label="item.category_name"
							  :value="item.id">
							</el-option>
						</el-select>																			
																		
					</el-form-item>								
						
					<el-form-item label="商品名称">
						<el-input v-model="form.goods_name" placeholder="请输入商品名称"></el-input>
					</el-form-item>
					<el-form-item label="商品价格">
						<el-input v-model="form.goods_price" placeholder="请输入商品价格"></el-input>
					</el-form-item>	
					<el-form-item label="市场价格">
						<el-input v-model="form.goods_marketprice" placeholder="请输入市场价格"></el-input>
					</el-form-item>	
					<el-form-item label="成本价">
						<el-input v-model="form.goods_costprice" placeholder="请输入成本价"></el-input>
					</el-form-item>

					<el-form-item label="商品主图">
						<div v-show="form.goods_image">
							<img :src="form.goods_image" width="128px"/>
						</div>
						<upload :size="32" :uptype="2" @showImg="showImg"></upload>
					</el-form-item>
					<el-form-item label="规格" v-show="specArr.length">
						<el-row v-for="(spec,index) in specArr" :key="index">
							<el-col :span="3">{{spec.spec_name}}</el-col>
							<el-col :span="21">
								<el-checkbox  
								v-for="(value,i) in spec.spec_value" 
								v-model="specArr[index]['spec_value'][i]['checked']"
								@change="specChange"
								:key="i">
									{{value.show_name}}
								</el-checkbox>
							</el-col>
						</el-row>
					</el-form-item>						
					<el-form-item label="规格配置" v-show="checkedSpec && checkedSpecValue">
						<el-row > 
							<el-col :span="4" v-for="(title,tindex) in checkedSpec" :key="tindex" v-if="title">
								{{title.spec_name}}
							</el-col>
							<el-col :span="4" v-show="checkedSpec.length">
								价格 <i class="iconfont icon-xiugai-copy" @click="editPrice"></i>
							</el-col>
							<el-col :span="4" v-show="checkedSpec.length">
								库存 <i class="iconfont icon-xiugai-copy" @click="editStock"></i>
							</el-col>
																					
						</el-row>
						<el-row v-for="(item,index) in checkedSpecValue" :key="index">
							<div v-for="(valItem,i) in item" :key="i">
								<el-col :span="4" v-show="valItem.spec_val">
									{{valItem.spec_val}}
								</el-col>		

								<el-col :span="4" v-show="valItem.price!=undefined">
									<el-input style="width:80px" v-model="checkedSpecValue[index][i].price"></el-input>
								</el-col>	
								<el-col :span="4" v-show="valItem.storage!=undefined">
									<el-input style="width:80px" v-model="checkedSpecValue[index][i].storage" @input="caluStorage"></el-input>
								</el-col>																																	
							</div>

						</el-row>
					</el-form-item>							
					<el-form-item label="商品库存">
						<el-input :disabled="storageDis" v-model="form.goods_storage" placeholder="请输入商品库存"></el-input>
					</el-form-item>	
					<el-form-item label="库存报警值">
						<el-input v-model="form.goods_storage_alarm" placeholder="请输入库存报警值"></el-input>
					</el-form-item>							
					<el-form-item label="运费">
						<el-input v-model="form.goods_freight" placeholder="请输入运费"></el-input>
					</el-form-item>	
					<el-form-item label="推荐商品">
						<el-radio v-model="form.goods_commend" label="1">是</el-radio>
						<el-radio v-model="form.goods_commend" label="0">否</el-radio>						
					</el-form-item>						
					<el-form-item label="商品发布">
						<el-radio v-model="form.goods_state" label="1">立即发布</el-radio>
						<el-radio v-model="form.goods_state" label="0">放入仓库</el-radio>						
					</el-form-item>	
					<el-form-item label="">
						<el-button :loading="ifload" type="primary" @click="saveGoods">保存</el-button>
					</el-form-item>
				</el-form>				
			</el-col>
		</el-row>

        <!-- 编辑弹出框 -->
        <el-dialog :title="'批量修改'+editLabel" :visible.sync="editVisible" width="30%">
			<el-row>
				<el-col :span="6" style="text-align:center;">{{editLabel}}</el-col>
				<el-col :span="18">
					<el-input v-model="eidtVal"></el-input> 
				</el-col>
			</el-row> 
            <span slot="footer" class="dialog-footer">
                <el-button @click="editVisible = false">取 消</el-button>
                <el-button type="primary" @click="saveEdit">确 定</el-button>
            </span>
        </el-dialog>		
		
		<loading :ifload="ifload"></loading>
	</div>
</template>

<script type="text/javascript">
	import loading from '@/components/utils/loading';
	import upload from '@/components/utils/upload';
	export default{
		components:{loading,upload},
		props:{
			goodsCommonid:{type:Number,default:0}, 
		},
		data() {
			return {
				ifload:true,
				form:{
					goods_commonid:0,
					gc_id:'',
					gc_id1:'',
					gc_id2:'',
					gc_id3:'',
					goods_name:'',
					goods_image:'',
					goods_price:'',
					goods_costprice:'',
					goods_marketprice:'',
					goods_storage:0,
					goods_storage_alarm:0,
					goods_state:'1',
					type_id:0,
					goods_freight:0,
					goods_commend:'',
				},
				oldGcId:0,
				class1:[],
				class2:[],
				class3:[],
				curClass:[],
				specArr:[], //组规格
				checkedSpec:[], //选中的规格 
				checkedSpecValue:[], //选中的规格值组合
				
				storageDis:false,
				editVisible:false,
				editLabel:'价格',
				eidtVal:0,
				editType:'price',
			}
		},		
		created() {
			this.$nextTick(()=>{
				this.getClass(0,0);
				this.form.goods_commonid = this.goodsCommonid;
				this._initGoodsData();
			})
		},	
		methods: {
			//获取产品初始信息
			_initGoodsData() {
				if(!this.goodsCommonid) return false;
				let param = {goods_commonid:this.goodsCommonid};
				console.log(param);
				this.$post_('goods/goods/get_goodscommon_detail',param,(res) =>{
					console.log(res);
					this.ifload = false;
					this.oldGcId = res.data.gc_id;
					this.goods_commonid = res.data.goods_commonid;
					this.form.gc_id = res.data.gc_id;
					this.form.gc_id1 = res.data.gc_id1;
					this.form.gc_id2 = res.data.gc_id2;
					this.form.gc_id3 = res.data.gc_id3;
					this.form.goods_name = res.data.goods_name;
					this.form.goods_image = res.data.goods_image;
					this.form.goods_price = res.data.goods_price;
					this.form.goods_costprice = res.data.goods_costprice;	
					this.form.goods_marketprice = res.data.goods_marketprice;
					this.form.goods_storage = res.data.goods_storage;
					this.form.goods_storage_alarm = res.data.goods_storage_alarm;
					this.form.goods_state = res.data.goods_state;
					this.form.type_id = res.data.type_id;
					this.form.goods_freight = res.data.goods_freight;
					this.form.goods_commend = res.data.goods_commend;
					this.checkedSpec = res.data.checked_spec; 
					this.checkedSpecValue = res.data.checked_spec_value;
					if(this.form.gc_id1>0) this.getClass(this.form.gc_id1,1);
					if(this.form.gc_id2>0) this.getClass(this.form.gc_id2,2);
					if(this.form.gc_id3>0) this.getClass(this.form.gc_id3,3);
					
					
				})
				
			},
			
			//获取分类
			getClass(pid,level) {
				this.ifload = true;
				this.$post_('goods/goods-class/get_class',{parent_id:pid},(res) =>{
					this.ifload = false;
					if(res.data.length){
						if(level==0){
							this.class1 = res.data;
						}else if(level==1){
							this.class2 = res.data;
						}else if(level==2){
							this.class3 = res.data;
						}		
					}else{
						//检查是否有分类规格
						this.showSpec();						
					}
				})
				
			},
            
			//显示图片
			showImg(imgUrl) {
				console.log(imgUrl);
				this.form.goods_image = imgUrl;
            },
			
			//选择分类
			classChange(gcId) {
				this.form.gc_id = gcId;
				this.getClass(gcId,1);
			},
			classChange1(gcId) {
				this.form.gc_id = gcId;
				this.getClass(gcId,2);
			},
			classChange2(gcId) {
				this.form.gc_id = gcId;
				this.getClass(gcId,3);
			},			
			
			//获取规格
			showSpec() {
				if(this.form.gc_id != this.oldGcId){
					this.checkedSpec = [];
					this.checkedSpecValue = [];
				}

				let params = {
					gc_id:this.form.gc_id,
					// goods_commonid:this.goodsCommonid,
				};
				if(this.form.gc_id==this.oldGcId){
					params.goods_commonid = this.goodsCommonid;
				}
				this.$post_('goods/goods-class/get_class_spec',params,(res) => {
					console.log(res);
					if(res.code=='0'){
						this.specArr = res.data;
						this.form.type_id = res.extend.type_id;
						// if(this.goodsCommonid>0){
						// 	this.specChange();
						// 	console.log(this.checkedSpecValue);
						// }
					}else{
						this.$message.warning(res.msg);
						this.specArr = [];
						this.form.type_id = 0;
					}
				});
			},
			//获取已选择的值
			formatSpecArr() {
				console.log(this.checkedSpec);
			},
			
			//勾选规格
			specChange() {
				let checkedSpec = [];
				console.log(this.specArr);
				this.specArr.forEach((spec,index)=>{
					let checkSpecVal = [];
					let specFlag = false;
					spec.spec_value.forEach((specVal) => {
						if(specVal.checked){
							this.$set(spec,'checked',true);
							specFlag = true;
							let valName = {
								val_id:specVal.id,
								spec_val:specVal.value_name,
								spec_id:specVal.spec_id,
							}
							checkSpecVal.push(valName);
						}
					})
					//用于取消时
					if(!specFlag){
						this.$set(spec,'checked',false);
					}
					//有选中的
					if(checkSpecVal.length){
						checkedSpec.push(checkSpecVal);
					}
					
					//获取选择的规格 
					if(spec.checked){
						let wasFlag = true;
						console.log(this.checkedSpec);
						this.checkedSpec.forEach((val,i)=>{
							if(val.spec_id==spec.spec_id){
								wasFlag = false;
							}
						})							
						if(wasFlag){
							let selectSpec = {spec_id:spec.spec_id,spec_name:spec.spec_name};
							this.checkedSpec[spec.spec_id] = selectSpec;									
						}
					}else{
						this.checkedSpec.forEach((val,i)=>{
							if(val.spec_id==spec.spec_id){
								this.checkedSpec.splice(i,1);
							}
						})
					}
				});
				console.log(checkedSpec);
				if(!checkedSpec.length){
					this.checkedSpecValue = [];
					this.storageDis = false;
					return ;
				} 
				console.log(checkedSpec);
				let res = this.descartes(checkedSpec);
				let data = [];
				
				//额外增加一些参数
				if(res[0].spec_id!=undefined){
					res.forEach((val,i)=>{ 
						val.price = 0;
						val.storage = 0;
						val = [val];
						data.push(val);
					});
					
				}else{
					res.forEach((val,i)=>{
						console.log(val);
						val.push({price:0});
						val.push({storage:0}); 
						data.push(val);
					})					
				}
				if(data.length>0){
					this.storageDis = true;
				}
				console.log(this.storageDis);
				this.checkedSpecValue = data;				
			},
			
			/**
			 * 生成笛卡尔积
			 * @returns {*}
			 */
			descartes(array) {
				if( array.length < 2 ) return array[0] || [];

				return [].reduce.call(array, function(col, set) {
					var res = [];
					col.forEach(function(c) {
						set.forEach(function(s) {
							var t = [].concat( Array.isArray(c) ? c : [c] );
							t.push(s);
							res.push(t);
						})
					});

					return res;
				});
			},							
		
			//提交
			saveGoods() {
				this.ifload = true;
				if(!this.checkedSpec[0]){
					this.checkedSpec.splice(0,1);
				}
				this.form.spec_name = JSON.stringify(this.checkedSpec);
				this.form.spec_value = JSON.stringify(this.checkedSpecValue);
				// console.log(this.specArr);return;
				this.$post_('goods/goods/add_step1',this.form,(res)=>{
					console.log(res);
					this.ifload = false;
					if(res.code=='0'){
						this.$message.success(res.msg);
						let goodsCommonid = Number(res.data.goods_commonid);
						this.$emit('baseSuccess',goodsCommonid);
					}else{
						this.$message.error(res.msg);
					}
				})
			},
			editPrice() {
				this.editVisible = true;
				this.editLabel = '价格';
				this.editType = 'price';
			},
			editStock() {
				this.editVisible = true;
				this.editLabel = '库存';
				this.editType = 'stock';
			},
			
			//批量修改价格、库存
			saveEdit() {
				// console.log(this.checkedSpecValue);
				if(this.editType=='stock'){
					this.form.goods_storage = 0;
				}
				this.checkedSpecValue.forEach((val,index)=> {
					val.forEach((v,i) =>{
						if(v.price!=undefined && this.editType=='price'){
							this.checkedSpecValue[index][i].price = this.eidtVal;
						}
						if(v.storage!=undefined && this.editType=='stock'){
							this.checkedSpecValue[index][i].storage = this.eidtVal;
							this.form.goods_storage += this.eidtVal*100/100;
						}						
					})
				})
				this.editVisible = false;
			},
			//计算各规格库存
			caluStorage() {
				this.form.goods_storage = 0;
				this.checkedSpecValue.forEach((val,index)=> {
					val.forEach((v,i) =>{
						if(v.storage!=undefined){
							this.form.goods_storage += v.storage*100/100;
						}						
					})
				})				
			}
		}
	}
</script>

<style>
</style>
