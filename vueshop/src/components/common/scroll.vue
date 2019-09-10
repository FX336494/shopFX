<template>
  <div ref="wrapper" class="bs" :style="BSstyle">
    <slot></slot>
  </div>
</template>

<script>
  import BScroll from 'better-scroll'

  export default {
    props: {
      BSstyle:{
        type:String
      },
      probeType: {
        type: Number,
        default: 1
      },
      click: {
        type: Boolean,
        default: true
      },
      listenScroll: {
        type: Boolean,
        default: false
      },
      data: {
        type: Array,
        default: null
      },
      num:'',
      obj:{},
      ifloaded:false,
      scrollToEndFlag:{
        type: Number,
        default: 1
      },
      bounce:{
        type: Boolean,
        default: false
      }
    },
    mounted() {
      setTimeout(() => {
        this._initScroll()
      }, 20)
    },
    methods: {
      _initScroll() {
        if (!this.$refs.wrapper) {
          return
        }
        this.scroll = new BScroll(this.$refs.wrapper, {
          probeType: this.probeType,
          click: this.click
        })
        if (this.listenScroll) {
          let me = this
          this.scroll.on('scroll', (pos) => {
            me.$emit('scroll', pos,this.scroll.maxScrollY)
          }) 
          this.scroll.on('scrollStart', (pos,maxScrollY) => {
            me.$emit('scrollStart',this.scroll,this.scroll.maxScrollY)
          })
          this.scroll.on('scrollEnd', (pos,maxScrollY) => {
            me.$emit('scrollEnd',this.scroll.y,this.scroll.maxScrollY)
          })
          this.scroll.on('touchend', (pos,maxScrollY) => {
            me.$emit('touchend',pos,this.scroll.maxScrollY)
          })
        }

        console.log(this.scroll);
      },
      disable() {
        this.scroll && this.scroll.disable()
      },
      enable() {
        this.scroll && this.scroll.enable()
      },
      refresh() {
        this.scroll && this.scroll.refresh()
      },
      scrollTo() {
        this.scroll && this.scroll.scrollTo.apply(this.scroll, arguments)
      },
      scrollToElement() {
        this.scroll && this.scroll.scrollToElement.apply(this.scroll, arguments)
      }
    },
    watch: {
      data() {
        setTimeout(() => {
          this.refresh()
          this.$emit('getheight',this.scroll.maxScrollY)
        }, 100)
      },
      num() {
        setTimeout(() => {
          this.refresh()
        }, 100)
      },
      obj(){
        setTimeout(() => {
          this.refresh()
        }, 100)
      },
      ifloaded(){
        setTimeout(() => {
          this.refresh()
        }, 100)
      },
      scrollToEndFlag(){
        setTimeout(() => {
          this._initScroll()
          console.log(this.scroll.maxScrollY)
          this.scroll.scrollTo(0,this.scroll.maxScrollY)
        }, 100) 
      }
    }
  }
</script>

<style scoped>
  .bs{

  }
</style>
