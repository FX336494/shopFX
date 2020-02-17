<template>
    <div>
	 	<div class="container" style="border: none;">
            <el-page-header @back="backSeckill" content="秒杀商品管理" style="margin-bottom: 20px;">
            </el-page-header>
            <el-row style="margin-bottom: 10px;">
                <el-col :span="3">
                    <el-button type="success" size="mini" @click="showBox=!showBox">添加&修改商品</el-button>
                </el-col>
            </el-row>
			<el-table
				:data="list"
				border
				v-loading="ifload">
				<el-table-column
					prop="goods_image"
					label="图片"
					width="100">
					<template slot-scope="scope">
						<img :src="scope.row.goods_image" width="40px"/>
					</template>
				</el-table-column>
				<el-table-column
					prop="goods_name"
					label="商品名称"
                    align="center"
					width="120">
				</el-table-column>
				<el-table-column
					prop="seckill_price"
                    align="center"
					label="秒杀价格"
					width="160">
				</el-table-column>
				<el-table-column
					prop="goods_price"
                    align="center"
					label="普通价格"
					width="160">
				</el-table-column>
				<el-table-column
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="danger" size="mini" @click="handleDel(scope.$index, scope.row)">
						删除
						</el-button>
                    </template>
				</el-table-column>
			</el-table>

		</div>


        <!-- 删除提示框 -->
        <el-dialog title="删除提示" :visible.sync="delVisible" width="300px" center>
            <div class="del-dialog-cnt">删除不可恢复，是否确定删除？</div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="delVisible = false">取 消</el-button>
                <el-button :loading="ifload" type="primary" @click="delData">确 定</el-button>
            </span>
        </el-dialog>

        <el-dialog title="选择商品" :visible.sync="showBox" width="90%">
            <chooseGoods v-if="showBox" @chooseEmit="chooseEmit"></chooseGoods>
        </el-dialog>

 	</div>
</template>

<script type="text/javascript">
import {download} from '@/components/js/request'
import upload from '@/components/utils/upload';
import chooseGoods from '@/components/goods/choose_goods';
export default{
    components:{upload,chooseGoods},
	data() {
		return {
			ifload:true,
			list:[],
            showBox:false,

            instanceId:0,
            goodsCommonid:0,

            //当前操作对象
			curIndex:-1,
			delVisible:false,

		}
	},
	created() {
        this.instanceId = this.$route.query.instance_id;
		this.$nextTick(()=>{
			this.getData();
		})
	},

	methods:{
		getData() {
			this.ifload = true;
            let params = {
                instance_id:this.instanceId
            }
            this.$post_('promotion/seckill/instance_goods_list',params,(res) =>{
                this.ifload = false;
                if(res.code=='0'){
                    this.list = res.data;
                }else{
                    this.$message.error(res.msg);
                }


            })
		},
        //选择商品，设置价格
        chooseEmit(goods) {
            console.log(goods);
            let params = {
                instance_id : this.instanceId,
                goods : JSON.stringify(goods),
            }
            this.ifload = true;
            this.$post_('promotion/seckill/add_instance_goods',params,(res) =>{
                this.ifload = false;
                this.showBox = !this.showBox;
                if(res.code=='0'){
                    this.$message.success(res.msg);
                    this.getData();
                }else{
                    this.$message.error(res.msg);
                }
            })
        },

        //删除确认
		handleDel(index,row) {
			this.delVisible = true;
			this.goodsCommonid = row.goods_commonid;
			this.curIndex = index;
		},
		//删除
		delData() {
			this.ifload = true;
			let param = {
                goods_commonid : this.goodsCommonid,
                instance_id : this.instanceId
            };
			this.$post_('promotion/seckill/del_instance_goods',param,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.$message.success(res.msg);
					this.list.splice(this.curIndex,1);
					this.ifload = false;
					this.delVisible = false;
				}else{
					this.$message.error(res.msg);
				}
			})
		},
        backSeckill() {
            this.$router.push({path:'/page/promotion/seckill/seckill',query:{tab:'instance'}})
        }
	}
}
</script>

<style type="text/css">

</style>
