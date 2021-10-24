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
      <span v-if="waitingResponse" id="writingText"> writing... </span>
    </div>
    <div class="chat-input">
      <input
          type="text"
          v-on:keyup.enter="sendMessage"
          v-model="new_message"
          placeholder="Type your message"
      />
      <button v-on:click="sendMessage" :disabled="waitingResponse">Send!</button>
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
      waitingResponse : true,
      notFoundAttempts : 0,
    }),
    methods:{
      checkValidForm(){
        // we dont allow empty messages
        let valid = this.new_message !== '';
        if(!valid){
          alert('Please, enter any message.');
        }
        return valid;
      },
      sendMessage(){
        if(this.checkValidForm()) {
          // Check "writing..." div
          this.waitingResponse = true;
          this.messagesThread.push(this.new_message);
          api.sendMessage(this.new_message, this.conversationToken, this.notFoundAttempts).then(res => {
            let answer = JSON.parse(res.data);
            this.messagesThread.push(answer.response_message);
            this.conversationToken = answer.session_token;
            // increase the counter of messages of type not_found
            if (answer.not_found_message === true){
              this.notFoundAttempts = this.notFoundAttempts + 1;
              sessionStorage.setItem('notFoundAttempts', this.notFoundAttempts)
            }
            sessionStorage.setItem('conversationToken', this.conversationToken);
            // clear message input
            this.new_message = '';
            // Hide "writing..." div
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
      // if we have a saved conversation we display it
      if(localStorage.getItem('messages')){
          this.messagesThread = localStorage.getItem('messages').split(',')
      }
      if(sessionStorage.getItem('conversationToken')){
        this.conversationToken = sessionStorage.getItem('conversationToken')
      }
    },
    watch:{
      // when a new message is send we save it in storage
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
  justify-content: center;
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

#writingText{
  padding-left: 40px;
  display: flex;
  max-width: 200px;
  justify-content: center;
}

.chat-input{
  margin: 0.4em;
}
.chat-input input{
  width: 60%;
}
</style>