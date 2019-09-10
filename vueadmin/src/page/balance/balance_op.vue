<template>
    <div>
		<div class="container" style="border: none;">
			<el-row>
				<el-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
					<el-form ref="form" :model="form" label-width="100px">
						<el-form-item label="会员">
							<el-input v-model="memberKey" @blur="getMember" placeholder="输入会员ID或是电话"></el-input>
						</el-form-item>
                        <el-form-item label="会员信息" v-show="memberInfo" style="color: red;">
                            {{this.memberInfo}}
                        </el-form-item>
						<el-form-item label="操作数量">
							<el-input v-model="form.nums" placeholder="操作数量"></el-input>
						</el-form-item>
						<el-form-item label="账本类型">
							<el-select v-model="form.balance_type">
                                <el-option
                                    v-for="(name,btype) in typeNames"
                                    :label="name"
                                    :value="btype"
                                    :key="btype"
                                ></el-option>
                            </el-select>
						</el-form-item>
						<el-form-item label="操作类型">
							<el-select v-model="form.type" placeholder="操作类型">
                                <el-option value="1" label="增加" key='1'></el-option>
                                <el-option value="0" label="减少" key='0'></el-option>
                            </el-select>
						</el-form-item>
						<el-form-item label="备注">
							<el-input v-model="form.remark" placeholder="备注" ></el-input>
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
	export default{
		components:{loading},
		data() {
			return {
				ifload:true,
                typeNames:[],
                memberKey:'',
                memberInfo:'',
				form:{
                    nums: '',
                    balance_type: '1',
                    type: '1',
                    remark: '',
                    member_id:'',
                    member_name:''
                },
			}
		},
		created() {
            this.member_id = this.$route.query.member_id;
			this.$nextTick(()=>{
				this.initData();
			})
		},
		methods: {
			initData() {
                this.ifload = true;
                let param = {};
                this.$post_('finance/balance/balance_type',param,(res) => {
                    console.log(res);
                    if(res.code=='0'){
                        this.typeNames = res.data;
                    }
                })
				this.ifload = false;
			},
            getMember() {
                this.ifload = true;
                let param = {member_key:this.memberKey};
                this.$post_('member/member/query_member',param,(res) => {
                    console.log(res);
                    if(res.code=='0' && res.data){
                        this.form.member_id = res.data.member_id;
                        this.form.member_name = res.data.member_name;
                        this.memberInfo = '姓名：'+res.data.member_name+'，ID:'+res.data.member_id;
                    }else{
                        this.memberInfo = '无此人信息';
                        this.member_id = '';
                        this.member_name = '';
                    }
                })
				this.ifload = false;
            },

            //保存数据
            saveData() {
                this.ifload = true;
                this.$post_('finance/balance/balance_op',this.form,(res) =>{
                    console.log(res);
                    this.ifload = false;
                    if(res.code=='0'){
                        this.$message.success(res.msg);
                        // this.$router.back(-1);
                        this.$emit('operateSuc');
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
