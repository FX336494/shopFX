<template>
  <div id="app">

    <keep-alive>
      <router-view v-if="$route.meta.keepAlive"></router-view>
    </keep-alive>

    <router-view v-if="!$route.meta.keepAlive"></router-view>
    <tab v-if="$route.meta.istab"></tab>
  </div>
</template>

<script>
  import tab from './components/common/tab'
  export default {
    name: 'App',
    components: {
      tab
    },
    data() {
      return {
        routePath: this.$route.path,
      }
    },
    watch: {
      "$route": "get_routePath"
    },
    mounted() {
      if (this.routePath == '') {
        this.routePath = '/index'
      }
    },
    methods: {
      get_routePath() {
        this.routePath = this.$route.path
      },
    }
  }
</script>

<style>
  body {
    font-family: MicroSoft Yahei;
  }

  #app {
    font-family: 'Avenir', Helvetica, Arial, sans-serif, 'MicroSoft Yahei';
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin: 0;
    padding: 0;
  }

  .btn {
    background: #333;
  }

  ul,
  li {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  input {
    border: none;
    outline: none;
  }

  .clear {
    clear: both;
  }
</style>
