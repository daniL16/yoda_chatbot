<template>
  <div id="app">
    <MessageBox></MessageBox>
  </div>
</template>

<script>

import MessageBox from "./components/MessageBox";

export default {
  name: 'App',
  components: {MessageBox},
  created(){
    document.title = 'Yoda Chat'
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
