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
      waitingResponse : false,
      notFoundAttempts : 0,
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
          api.sendMessage(this.new_message, this.conversationToken, this.notFoundAttempts).then(res => {
            let answer = JSON.parse(res.data);
            this.messagesThread.push(answer.response_message);
            this.conversationToken = answer.session_token;
            if (answer.not_found_message === true){
              this.notFoundAttempts = this.notFoundAttempts + 1;
              sessionStorage.setItem('notFoundAttempts', this.notFoundAttempts)
            }
            sessionStorage.setItem('conversationToken', this.conversationToken);
            this.new_message = '';
            this.waitingResponse = false;
          }).catch((error) => {
            console.log(error)
            this.waitingResponse = false;
            alert('Sorry! Something didn\'t work as it should, try again young padawan.')
          })
        }
      },
      clearConservation(){
        this.messagesThread = []
        this.conversationToken = '';
        this.notFoundAttempts = 0;
      }
    },
    mounted(){
      if(localStorage.getItem('messages')){
          this.messagesThread = localStorage.getItem('messages').split(',')
      }
      if(sessionStorage.getItem('conversationToken')){
        this.conversationToken = sessionStorage.getItem('conversationToken')
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
  padding: 0.5em;
  width: 45%;
  border-radius: 10px;
  background: #F1F0F0;
  margin-bottom: 0.8em;
}

.chat-inputs{
  display: flex;
  width: 90%;
  justify-content: center;
}

#writingText{
  padding: 0.2em;
  display: flex;
  width: 45%;
  justify-content: center;
}
</style>