<template>
  <div id="app">
    <input type="text" id="message" v-model="message">
    <button @click="sendMessage"> Submit</button>
    <p> {{ answer }}</p>
  </div>
</template>

<script>

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
        fetch('http://localhost:250/send_message',
            {'method': 'POST',
              body: JSON.stringify({message: this.message})
            }
          ).then(res => res.json()).then(res => {
          console.log(res)
          this.answer = res.answer
          this.sessionToken = res.sessionToken
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
