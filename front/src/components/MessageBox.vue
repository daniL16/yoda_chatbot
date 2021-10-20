<template>
  <section>
    <div class="chat-box">
      <ul class="message-list">
        <li
            class="message-item"
            v-for="(message,key) in messagesThread"
            :key="key"
        >
         <Message :message="message" :key="key"></Message>
        </li>
      </ul>
      <span v-if="waitingResponse"> writing... </span>
    </div>
    <div class="chat-inputs">
      <input
          type="text"
          v-on:keyup.enter="sendMessage"
          v-model="new_message"
          placeholder="Type your message"
      />
      <button v-on:click="sendMessage">Send!</button>
      <button v-on:click="clearConservation">Clear</button>
    </div>
  </section>
</template>

<script>
import api from "../api.js";
import Message from '../components/Message';

export default {
    name: 'MessageBox',
    components: {Message},
    data: () => ({
      new_message: '',
      messagesThread: [],
      conversationToken: '',
      waitingResponse : false
    }),
    methods:{
      checkForm(){
        let valid = this.new_message !== '';
        if(!valid){
          alert('Please, enter any message.');
        }
        return valid;
      },
      sendMessage(){
        if(this.checkForm()) {
          this.waitingResponse = true;
          this.messagesThread.push(this.new_message);
          api.sendMessage(this.new_message, this.conversationToken).then(res => {
            let answer = JSON.parse(res);
            this.messagesThread.push(answer.response_message);
            this.conversationToken = answer.session_token;
            localStorage.setItem('conversationToken', this.conversationToken);
            this.new_message = '';
            this.waitingResponse = false;
          }).catch(() => {
            this.waitingResponse = false;
            alert('Sorry! Something didn\'t work as it should, try again young padawan.')
          })
        }
      },
      clearConservation(){
        this.messagesThread = []
        this.conversationToken = '';
      }
    },
    mounted(){
      if(localStorage.getItem('messages')){
          this.messagesThread = localStorage.getItem('messages').split(',')
      }
      if(localStorage.getItem('conversationToken')){
        this.conversationToken = localStorage.getItem('conversationToken')
      }
    },
    watch:{
      messagesThread: function (messages){
        localStorage.messages = messages
      }
    }

  }
</script>

<style>

.message-list {
  display: flex;
  flex-direction: column;
  list-style-type: none;
}

.chat-box {
  padding: 1em;
  overflow: auto;
  max-width: 500px;
  margin: 0 auto 1em auto;
}

.message-item {
  padding-bottom: 1.5em;
}


</style>