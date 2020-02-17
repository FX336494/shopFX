<template>
    <div>
	 	<div class="container" style="border: none;margin-top: -60px;">
            <el-form ref="form" :model="form" label-width="100px">
                <el-form-item label="商品分类">
                    <el-select style="width:120px;" v-model="form.gc_id1" placeholder="请选择" @change="classChange">
                        <el-option
                          v-for="item in class1"
                          :key="item.id"
                          :label="item.category_name"
                          :value="item">
                        </el-option>
                    </el-select>

                    <el-select  style="width:120px;"  v-show="class2.length" v-model="form.gc_id2"  @change="classChange1" placeholder="请选择">
                        <el-option
                          v-for="item in class2"
                          :key="item.id"
                          :label="item.category_name"
                          :value="item">
                        </el-option>
                    </el-select>
                    <el-select style="width:120px;"  v-show="class3.length" v-model="form.gc_id3"  @change="classChange2" placeholder="请选择">
                        <el-option
                          v-for="item in class3"
                          :key="item.id"
                          :label="item.category_name"
                          :value="item">
                        </el-option>
                    </el-select>
                    <span class="add" @click="addClass">添加分类</span>
                </el-form-item>
                <el-form-item >
                    <el-button @click="reset">重置</el-button>
                    <el-button type="primary" @click="confirm">确 定</el-button>
                </el-form-item>
            </el-form>

        </div>
    </div>
</template>

<script type="text/javascript">
	import loading from '@/components/utils/loading';
	import upload from '@/components/utils/upload';
	export default{
		components:{loading,upload},
		data() {
			return{
                form:{
                    gc_id:0,
                    gc_id1:'',
                    gc_id2:'',
                    gc_id3:'',
                },
				class1:[],
				class2:[],
				class3:[],
				curClass:[],
            }
        },
        created() {
            this.reset();
            this.getClass(0,0);
        },
        methods:{
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
					}
				})
			},
			//选择分类
			classChange(item) {
                this.form.gc_id = item.id;
				this.getClass(item.id,1);
			},
			classChange1(item) {
                this.form.gc_id = item.id;
				this.getClass(item.id,2);
			},
			classChange2(item) {
                this.form.gc_id = item.id;
				this.getClass(item.id,3);
			},
            addClass() {
                this.$router.push('/page/goods/class_list');
            },
            confirm() {
                console.log(this.form);
                this.$emit('chooseEmit',this.form);
            },
            reset() {
                this.form.gc_id1= '';
                this.form.gc_id2= '';
                this.form.gc_id3= '';
				this.class2 = [];
				this.class3 = [];
            },
        }
    }
</script>

<style>
    .add{
        color:#0080FF;
        cursor: pointer;
    }
</style>
