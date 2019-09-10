<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>商品管理</el-breadcrumb-item>
                <el-breadcrumb-item>商品列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
    
	 	<div class="container">
			<div class="op">
				<el-button type="primary" @click="addGoods">添加商品</el-button>
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
					prop="category_name"
					label="分类"
					width="180">
				</el-table-column>	
				<el-table-column
					prop="goods_price"
					label="价格"
					width="180">
				</el-table-column>											
				<el-table-column
					label="操作">
                    <template slot-scope="scope">
                        <el-button type="text" icon="el-icon-edit" @click="eidtGoods(scope.row)">
						编辑
						</el-button>
                        <el-button type="text" icon="el-icon-delete" class="red" @click="handleDel(scope.$index, scope.row)">
						删除
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
		
        <!-- 删除提示框 -->
        <el-dialog title="删除提示" :visible.sync="delVisible" width="300px" center>
            <div class="del-dialog-cnt">删除不可恢复，是否确定删除？</div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="delVisible = false">取 消</el-button>
                <el-button :loading="ifload" type="primary" @click="delGoods">确 定</el-button>
            </span>
        </el-dialog>		
		
 	</div>
</template>

<script type="text/javascript">
export default{
	data() {
		return {
			ifload:true,
			list:[],

			page:1,
			page_size:10, 
			pages:0,
			
			delCommonGoodsId:0,
			delIndex:-1,
			delVisible:false,
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
			let params = {page:this.page,page_size:this.page_size}
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
		addGoods() {
			this.$router.push({path:'/page/goods/goods_edit',query:{goods_commonid:0}});
		},
		eidtGoods(goods) {
			this.$router.push({path:'/page/goods/goods_edit',query:{goods_commonid:goods.goods_commonid}});
		},
		handleDel(index,goods) {
			this.delVisible = true;
			this.delCommonGoodsId = goods.goods_commonid;
			this.delIndex = index;
		},
		delGoods() {
			this.ifload = true;
			let param = {goods_commonid:this.delCommonGoodsId};
			this.$post_('goods/goods/del_goods',param,(res) => {
				console.log(res);
				if(res.code=='0'){
					this.$message.success(res.msg);
					this.list.splice(this.delIndex,1);
					this.ifload = false;
					this.delVisible = false;
				}else{
					this.$message.error(res.msg);
				}
			})
		}

	}
}
</script>

<style type="text/css">
    .red{
        color: #ff0000;
    }
</style>