<template>
  <div class="recommend" ref="recommend">
    <topTile :name="headerName" showBack="true"></topTile>

    <div class="put_info">
      <span style="line-height: 35px;font-size: 14px;">姓名：</span>
      <input class="input_" type="text" v-model="name" placeholder="请输入您的姓名">
    </div>
    <div class="put_info">
      <span style="line-height: 35px;font-size: 14px;">联系电话：</span>
      <input class="input_" type="text" v-model="mobile" placeholder="请输入您的联系电话">
    </div>
    <div class="put_info" @click="toselectplace">
      <span style="line-height: 35px;font-size: 14px;">所在地区：{{palce_}}</span>
      <i class="iconfont icon-right"></i>
    </div>
    <div class="put_info">
      <span style="line-height: 35px;font-size: 14px;">详细地址：</span>
      <input class="input_" type="text" v-model="address" placeholder="街道、楼牌号等">
    </div>
    <div class="put_info">
      <span style="line-height: 35px;font-size: 14px;">设为默认：</span>
      <input @click="asd" style="text-align:left;" ref="checked" class="input_2" type="checkbox">
    </div>
    <div class="btn" @click="btn_password()">保存</div>
    <Onlyalert :content="content" :ifshow="ifshow" @Onlycallback_="Onlycallback"></Onlyalert>
    <addressSelect @closebox="closeplace" :showSelectP="showplace"></addressSelect>
  </div>
</template>
<script>
  import topTile from '@/components/common/top_title'
  import Onlyalert from '@/components/common/Only_alert'
  import addressSelect from '@/components/common/address_select'
  export default {
    components: {
      topTile,
      Onlyalert,
      addressSelect
    },
    data() {
      return {
        headerName: "添加地址",
        pay_password: "",
        content: '',
        ifshow: false,
        id: '',
        address: '',
        mobile: '',
        name: '',
        showplace: false,
        palce_: '',
        province_id: '',
        city_id: '',
        area_id: '',
        ifonlyshow: false,
        is_default: ''
      }
    },
    created() {
      this.init()
    },
    methods: {
      init() {
        this.id = this.$route.query.address_id;
        if (this.id) {
          var data = {
            address_id: this.id
          };
          this.$post_('order/address/address_info', data, (res) => {
            console.log(res)
            this.address = res.data.address;
            this.mobile = res.data.mob_phone;
            this.name = res.data.true_name;
            this.palce_ = res.data.area_info
            this.province_id = res.data.province_id;
            this.city_id = res.data.city_id;
            this.area_id = res.data.area_id;
            this.is_default = res.data.is_default;
            if (res.data.is_default >= '1') {
              this.$refs.checked.checked = true
            } else {
              this.$refs.checked.checked = false
            }
          });
        }
      },
      btn_password() {
        this.ifonlyshow = false
        var param = {};
        param.true_name = this.name;;
        param.mob_phone = this.mobile;
        param.address = this.address;
        param.province_id = this.province_id;
        param.city_id = this.city_id;
        param.area_id = this.area_id;
        param.area_info = this.palce_;
        param.is_default = this.is_default;

        var reg = /^((1)[0-9]{1}\d{9})$/;
        if (!reg.test(param.mob_phone)) {
          alert('手机格式错误');
          return;
        }
        if (this.id) {
          param.address_id = this.id
          this.$post_('order/address/edit', param, (res) => {
            console.log(res)
            this.addCallBack(res);
          })
        } else {
          this.$post_('order/address/add', param, (res) => {
            console.log(res)
            this.addCallBack(res);
          })
        }
      },
      addCallBack(res) {
        if (res.code == 0) {
          this.content = res.msg;
          this.ifshow = true
        } else if (res.code > 0) {
          this.content = res.msg
          this.ifshow = true
          this.ifonlyshow = true
        }
      },
      Onlycallback() {
        this.ifshow = false
        if (!this.ifonlyshow) {
          this.$router.back(-1)
        }
      },
      closeplace(palce_, provinceId, cityId, areaId) {
        this.showplace = false;
        this.palce_ = palce_;
        this.city_id = cityId;
        this.area_id = areaId;
        this.province_id = provinceId;
      },
      toselectplace() {
        this.showplace = true;
      },
      asd() {
        var checked = this.$refs.checked.checked
        if (checked) {
          this.is_default = 1
        } else {
          this.is_default = 0
        }
        console.log(this.is_default)
      }
    }
  }
</script>

<style scoped>
  .put_info {
    padding: 5px 15px;
    font-size: 13px;
    margin-top: 10px;
    background: #fff;
  }

  .put_info img {
    height: 20px;
    float: right;
    margin-top: 7px;
  }

  .input_ {
    float: right;
    width: 75%;
    height: 25px;
    margin-top: 5px;
  }

  .input_2 {
    width: 20px;
    height: 20px;
    float: right;
    margin-right: 70%;
    margin-top: 7px;
  }

  .btn {
    line-height: 30px;
    width: 90%;
    margin-left: 5%;
    background: #14bb41;
    text-align: center;
    color: #fff;
    margin-top: 15px;
    border-radius: 10px;
    font-size: 16px;
  }

  .recommend {
    position: fixed;
    top: 0;
    width: 100%;
    bottom: 0;
    z-index: 999;
    text-align: left;
    background: #eee;
  }

  .recommend-content,
  .slider-wrapper {
    height: 100%;
    overflow: hidden;
  }
</style>
