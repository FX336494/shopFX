<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>平台管理</el-breadcrumb-item>
                <el-breadcrumb-item>文章管理</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

		<div class="container">
			<el-tabs v-model="activeName" type="card">
				<el-tab-pane label="文章列表" name="list">
					<articleList v-if="activeName=='list'"></articleList>
				</el-tab-pane>
				<el-tab-pane label="文章分类" name="class">
					<articleClass v-if="activeName=='class'"></articleClass>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</template>

<script type="text/javascript">
	import articleList from '@/page/platform/article_list';
	import articleClass from '@/page/platform/article_class';
	export default{
		components:{articleList,articleClass},
		data() {
			return {
				activeName: 'list',
			}
		},
		created() {
		},
		methods: {
		}
	}
</script>

<style>
</style>
