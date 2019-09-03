<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>财务管理</el-breadcrumb-item>
                <el-breadcrumb-item>账本管理</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

		<div class="container">
			<el-tabs v-model="activeName" type="card">
				<el-tab-pane label="会员账本列表" name="list">
					<balanceList v-if="activeName=='list'"></balanceList>
				</el-tab-pane>
				<el-tab-pane label="账本明细" name="balance_log">
					<balanceLog v-if="activeName=='balance_log'"></balanceLog>
				</el-tab-pane>
				<el-tab-pane label="操作账本" name="operate">
					<balanceOp v-if="activeName=='operate'" @operateSuc="operateSuc"></balanceOp>
				</el-tab-pane>
				<el-tab-pane label="账本操作列表" name="op_list">
					<balanceOpList v-if="activeName=='op_list'"></balanceOpList>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</template>

<script type="text/javascript">
	import balanceList from '@/page/balance/balance_list';
	import balanceOpList from '@/page/balance/balance_op_list';
    import balanceOp from '@/page/balance/balance_op';
    import balanceLog from '@/page/balance/balance_log';
	export default{
		components:{balanceList,balanceOpList,balanceOp,balanceLog},
		data() {
			return {
				activeName: 'list',
			}
		},
		created() {
		},

		methods: {
            operateSuc() {
                this.activeName = 'op_list';
            }
		}
	}
</script>

<style>
</style>
