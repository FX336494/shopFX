<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i>订单管理</el-breadcrumb-item>
                <el-breadcrumb-item>订单详情</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

        <div class="container">
            <h4>基本信息</h4>
            <el-row :gutter="20">
                <el-col :span="12">
                    <el-card class="box-card" shadow="always">
                        <h3 class="title">收货人信息</h3>
                        <div class="text item">收货人姓名：{{reciverInfo['true_name']}}</div>
                        <div class="text item">收货人电话：{{reciverInfo['mob_phone']}}</div>
                        <div class="text item">收货人地址：
                            {{reciverInfo['area_info']}}
                            {{reciverInfo['address']}}
                        </div>
                        <div class="text item">用户留言：{{orderCommon.order_message}}</div>
                    </el-card>
                </el-col>
                <el-col :span="12">
                    <el-card class="box-card">
                        <el-steps :space="200" :active="order.order_state-0" finish-status="success">
                            <el-step title="待付款"></el-step>
                            <el-step title="待发货"></el-step>
                            <el-step title="已发货"></el-step>
                            <el-step title="已完成"></el-step>
                        </el-steps>
                        <el-row>
                            <el-col :span="12">
                                <div class="item">订单状态：{{order.order_state_text}}</div>
                                <div class="item">支付状态：{{order.pay_state_text}}</div>
                            </el-col>
                        </el-row>
                    </el-card>
                </el-col>
            </el-row>

            <div class="goods">
                <h4>下单商品</h4>
                <el-row v-for="(goods,i) in orderGoods" :key='i' class="item">
                    <el-col :span="4" style="text-align: center;">
                        <img :src="goods.goods_image" />
                    </el-col>
                    <el-col :span="16">
                        <div class="name">{{goods.goods_name}}</div>
                        <div class="price">单价:{{goods.goods_price}}</div>
                        <div class="num">数量:{{goods.goods_num}}</div>
                    </el-col>
                </el-row>
            </div>

            <div class="total">
                <el-row :gutter="20">
                    <el-col :span="6" :offset="18">
                        <div>运费：{{order.shipping_fee}}</div>
                        <div>商品总计：{{order.goods_amount}}</div>
                        <div class="money">总计：{{order.order_amount}}</div>
                    </el-col>
                </el-row>
            </div>

            <div class="delivery" v-show="order.order_state=='2'">
                <el-row>
                    <h4>发货设置</h4>
                    <el-col :span="14">
                        <el-input placeholder="请输入物流单号" v-model="logisticsNums" class="input-with-select">
                            <el-select v-model="expressId" slot="prepend" placeholder="请选择物流">
                                <el-option label="请选择物流" value="0">请选择物流</el-option>
                                <el-option v-for="(express,k) in expresses" :label="express.e_name" :value="express.id"
                                    :key='k'>
                                </el-option>
                            </el-select>
                            <el-button :loading="ifload" type="primary" slot="append" @click="delivery">确认发货</el-button>
                        </el-input>
                    </el-col>
                </el-row>
            </div>


        </div>


    </div>
</template>

<script type="text/javascript">
    export default {
        data() {
            return {
                ifload: true,
                order: [],
                orderCommon: [],
                reciverInfo: [],
                orderGoods: [],
                expressId: '0', //物流公司id
                logisticsNums: '', //物流单号
                expresses: [],
            }
        },
        created() {
            this.orderId = this.$route.query.order_id;
            this.$nextTick(() => {
                this.getData();
            })
        },
        computed: {},
        methods: {
            getData() {
                let params = {
                    order_id: this.orderId
                }
                this.$post_('order/order/order_detail', params, (res) => {
                    console.log(res);
                    if (res.code == '0') {
                        this.order = res.data;
                        this.orderCommon = res.data.order_common;
                        this.reciverInfo = this.orderCommon.reciver_info;
                        this.orderGoods = res.data.order_goods;
                        this.ifload = false;
                        if (this.order.order_state == '2') {
                            this.getExpress();
                        }
                    }
                });
            },
            //物流公司
            getExpress() {
                this.$post_('logistics/express/express_chose', {}, (res) => {
                    console.log(res);
                    this.expresses = res.data;
                });
            },
            //发货
            delivery() {
                let param = {
                    order_id: this.orderId,
                    express_id: this.expressId,
                    logistics_nums: this.logisticsNums
                }
                this.ifload = true;
                this.$post_('order/order/delivery', param, (res) => {
                    console.log(res);
                    this.ifload = false;
                    if (res.code == '0') {
                        this.$message.success(res.msg);
                        this.order.order_state = '3';
                    } else {
                        this.$message.error(res.msg);
                    }
                })
            }
        }
    }
</script>

<style type="text/css">
    .box-card .item {
        font-size: 13px;
        line-height: 25px;
    }

    .box-card .title {
        font-size: 15px;
    }

    .goods {
        margin-top: 30px;
    }

    .goods .item {
        border-bottom: 1px solid #f0f0f0;
        padding: 5px 0px;
    }
    .goods .item img{
        width: 80px;
        border-radius: 8px;
    }

    .goods .price,
    .goods .num {
        color: #555;
        font-size: 13px;
        line-height: 25px;
    }

    .total {
        margin-top: 10px;
        font-size: 13px;
        line-height: 25px;
    }

    .total .money {
        font-size: 20px;
        color: #F56C6C;
        line-height: 30px;
    }

    .input-with-select .el-input-group__append {
        background-color: #67C23A;
        color: #fff;
    }

    .el-select .el-input {
        width: 130px;
    }

    h4 {
        line-height: 30px;
    }
</style>
