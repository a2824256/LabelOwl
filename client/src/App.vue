<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App',
  created () {
    this.localSocket()
  },
  methods: {
    localSocket () {
      let that = this
      if ('WebSocket' in window) {
        console.log('您的浏览器支持 WebSocket!')

        that.ws = new WebSocket('ws://127.0.0.1:2346')
        that.global.setWs(that.ws)
        that.ws.onopen = function () {
          console.log('open')
          that.ws.send('hello')
        }
        that.ws.onmessage = function (e) {
          console.log(e.data)
        }
        that.ws.onerror = function (e) {
          console.log(e)
        }
        that.ws.onclose = function () {
          // 关闭 websocket
          console.log('连接已关闭...')
          setTimeout(() => {
            that.localSocket()
          }, 2000)
        }
      } else {
        // 浏览器不支持 WebSocket
        console.log('您的浏览器不支持 WebSocket!')
      }
    }
  }
}
</script>

<style>
/*#app {*/
/*  font-family: 'Avenir', Helvetica, Arial, sans-serif;*/
/*  -webkit-font-smoothing: antialiased;*/
/*  -moz-osx-font-smoothing: grayscale;*/
/*  text-align: center;*/
/*  color: #2c3e50;*/
/*  margin-top: 60px;*/
/*}*/
* {
  margin: 0;
  padding: 0;
}
</style>
