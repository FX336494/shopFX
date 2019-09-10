<template>
  <div class="wrapper">
    <div class="item upload">
      <i class="iconfont icon-upload" :class="opacity?'show':'hid'"></i>
      <input type="file" accept="image/*" @change="setImg($event)" />
    </div>


    <div class="imgBox" v-if="showBox">
      <div class="op">
        <button class="sure" @click="cropImage">确定</button>
        <button class="cancel" @click="canel_">取消</button>
      </div>
      <vue-cropper ref='cropper' :guides="true" :view-mode="2" drag-mode="crop" :auto-crop-area="0.5"
        :min-container-width="250" :min-container-height="180" :background="true" :rotatable="true" :src="imgSrc" alt="Source Image"
        :aspectRatio="aspectRatio" :img-style="{ 'width': '90%' }">
      </vue-cropper>
    </div>
    <loading :ifload="ifload" :title="loadingTitle"></loading>
  </div>
</template>

<script type="text/javascript">
  import {
    post_,
    ajax_upload
  } from '@/components/js/common.js'
  import loading from '@/components/common/loading'
  import VueCropper from 'vue-cropperjs';
  export default {
    props: {
      uploadType: {
        type: String,
        default: '1'
      }, //1 需要截图  2不需要
      aspectRatio: {
        type: Number,
        default: 1
      },
      opacity: {
        type: Number,
        default: 1
      }, //是否是显示上传框 1显示 0不显示
    },
    components: {
      scroll,
      VueCropper,
      loading
    },
    data() {
      return {
        imgSrc: '',
        cropImg: '',
        showBox: false,
        loadingTitle: '正在上传，请耐心等待',
        ifload: false,
        imgUrl: '',
      }
    },
    methods: {
      setImg(e) {
        const file = e.target.files[0];
        if (!file.type.includes('image/')) {
          alert('请选择图片');
          return;
        }
        if (this.uploadType == '2') {
          this.uploadImg(file);
          return true;
        }

        //if (typeof FileReader === 'function') {
        const reader = new FileReader();
        reader.onload = (event) => {
          this.imgSrc = event.target.result;
          this.$refs.cropper.replace(event.target.result);
        };
        reader.readAsDataURL(file);
        this.showBox = true;
        //} else {
        //alert('Sorry, FileReader API not supported');
        //}
      },


      //获取截取后的图片
      cropImage() {
        // get image data for post processing, e.g. upload or setting image src
        this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
        // console.log(this.cropImg);

        let filename = Math.random().toString(36).substr(2) + '.jpg';
        let file = this.baseToFile(this.cropImg, filename);
        this.uploadImg(file);

      },
      uploadImg(file) {
        this.ifload = true;
        var formdata = new FormData();
        formdata.append('file', file);
        console.log(file);
        ajax_upload('common/upload', formdata, (res) => {
          console.log(res);
          this.imgUrl = res.data.url;
          this.ifload = false;
          this.$emit('showImg', this.imgUrl);
          this.showBox = false;
        });
      },

      //base64转file
      baseToFile(base64, filename) {
        var arr = base64.split(','),
          mime = arr[0].match(/:(.*?);/)[1],
          bstr = atob(arr[1]),
          n = bstr.length,
          u8arr = new Uint8Array(n);
        while (n--) {
          u8arr[n] = bstr.charCodeAt(n);
        }
        var blobObj = new Blob([u8arr], {
          type: mime
        });

        blobObj.lastModifiedDate = new Date();
        blobObj.name = filename;
        return blobObj;
      },

      canel_() {
        this.showBox = false;
      }

    }
  }
</script>

<style type="text/css" scoped>
  .wrapper {
    z-index: 99999;
  }

  .wrapper .upload {
    position: relative;
    overflow: hidden;
    width: 64px;
    display: inline-block;
  }

  .wrapper .upload input {
    position: absolute;
    top: 0px;
    left: 0px;
    font-size: 64px;
    vertical-align: middle;
    opacity: 0;
    filter: alpha(opacity=0);
  }

  .wrapper .upload .hid {
    opacity: 0;
    filter: alpha(opacity=0);
    /* IE */
    -moz-opacity: 0;
    /* 老版Mozilla */
    -khtml-opacity: 0;
    /* 老版Safari */
    opacity: 0;
    /* 支持opacity的浏览器*/
  }

  .wrapper .item .iconfont {
    font-size: 64px;
  }

  .imgBox {
    position: fixed;
    top: 0px;
    left: 0px;
    bottom: 0px;
    background: rgba(0, 0, 0, 0.9);
    width: 100%;
    z-index: 9999;
    /*display: none;*/
  }

  .imgBox .op {
    width: 100%;
    height: 40px;
    background: #fff;
    position: relative;
  }

  .imgBox .op .sure {
    position: absolute;
    right: 0px;
    top: 0px;
    width: 100px;
    height: 40px;
    line-height: 40px;
    background: green;
    color: #fff;
    border: none;
  }

  .imgBox .op .cancel {
    position: absolute;
    left: 0px;
    top: 0px;
    width: 100px;
    height: 40px;
    line-height: 40px;
    background: red;
    color: #fff;
    border: none;
  }
</style>
