<template>
    <div>
	 	<div class="container" style="border: none;margin-top: -60px;">
			<div class="search" style="margin-bottom: 10px;">
				<el-row type="flex"  justify="space-between">
					<el-col :span="20">
						<el-input v-model="search.goods_name" placeholder="商品名称" clearable></el-input>
					</el-col>
					<el-col :span="4">
						<el-button :loading="ifload" type="primary" @click="searchRes" icon="el-icon-search">搜索</el-button>
					</el-col>
				</el-row>
			</div>


			<el-table
				:data="list"
				border
				v-loading="ifload"
				style="width: 100%">
				<el-table-column
					prop="goods_commonid"
					label="商品ID"
					width="80">
				</el-table-column>
				<el-table-column
					prop="goods_image"
					label="商品图片"
					width="80">
					<template slot-scope="scope">
						<img :src="scope.row.goods_image" width="32px"/>
					</template>
				</el-table-column>
				<el-table-column
					prop="goods_name"
					label="商品名称"
					width="220">
				</el-table-column>
				<el-table-column
					prop="goods_price"
					label="价格"
					width="140">
				</el-table-column>
				<el-table-column
					label="操作">
                    <template slot-scope="scope">
                        <el-button v-show="isPrice==1" type="text" icon="el-icon-edit" @click="choseGoods(scope.row)">
						选择此商品
						</el-button>
                        <el-button v-show="isPrice==0" type="text" icon="el-icon-edit" @click="confirmGoods(scope.row)">
						选择此商品s
						</el-button>
                    </template>
				</el-table-column>
		  </el-table>

            <div class="pagination">
                <el-pagination background
					@current-change="handleCurrentChange"
					layout="prev, pager, next"
					:total="pages"
					:page-size="page_size">
                </el-pagination>
            </div>
	 	</div>

        <!-- 设置产品价格 -->
        <el-dialog :modal-append-to-body="false" title="设置商品价格" :visible.sync="showPriceBox" width="40%">
            <el-form :model="form" style="margin-top: -20px;">
                <el-form-item :label="item.goods_name" label-width="100" v-for="(item,i) in curGoodsSku" :key="i">
                    <el-input v-model="item.goods_price" style="display: inline-block;width: 220px;"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="showPriceBox = false">取 消</el-button>
                <el-button type="primary" @click="setPrice">确 定</el-button>
            </div>
        </el-dialog>




 	</div>
</template>

<script type="text/javascript">
export default{
    props:{
        type:{type:Number,default:1},
        isPrice:{type:Number,default:1}, //是否要设置价格
    },
	data() {
		return {
			ifload:true,
			list:[],
            search:{
                goods_name:'',
                promotion_type:0,
            },
			page:1,
			page_size:8,
			pages:0,

            showPriceBox:false,
            form:{

            },
            curGoodsSku:{},

		}
	},
	created() {
		this.$nextTick(()=>{
			this.getData();
		})
	},
	computed:{
	},
	methods:{
		getData() {
			let params = {
                page : this.page,
                page_size : this.page_size,
                search : JSON.stringify(this.search),
            }
			this.$post_('goods/goods/goods_list',params,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.list = res.data;
					this.pages = Number(res.extend.pages);
					console.log(this.pages);
					this.ifload = false;
				}
			});
		},
        // 分页导航
        handleCurrentChange(val) {
            this.page = val;
            this.getData();
        },
		//搜索
		searchRes() {
			this.page = 1;
			this.getData();
		},

        //选择此商品
        choseGoods(goods) {
            let params = {
                goods_commonid:goods.goods_commonid,
            }
            if(this.type==1){
                this.$post_('goods/goods/get_goods',params,(res) => {
                    console.log(res);
                    this.showPriceBox = !this.showPriceBox;
                    if(res.code=='0'){
                        this.curGoodsSku = res.data;
                    }
                });
            }
        },
        //设置拼团产品价格
        setPrice() {
            console.log(this.curGoodsSku);
            this.$emit('chooseEmit',this.curGoodsSku);
        },
        confirmGoods(goods) {
            this.$emit('chooseEmit',goods);
        }
	}
}
</script>

<style type="text/css">
</style>
