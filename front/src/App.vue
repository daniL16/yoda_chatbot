<template>
  <div id="app">
    <input type="text" id="message" v-model="message">
    <button @click="sendMessage"> Submit</button>
    <p> {{ answer }}</p>
  </div>
</template>

<script>

import api from '@/api.js'
import {setCookie} from "@/utils.js";

export default {
  name: 'App',
  data(){
    return{
      message: '',
      answer: '',
      sessionToken: ''
  }},
  methods:{
      sendMessage(){
         api.sendMessage(this.message).then(res => {
          this.answer = res.data.answer
          setCookie('conversationToken',res.data.sessionToken,1)
        })
    }
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
