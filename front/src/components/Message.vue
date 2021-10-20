<template>
  <span class="message">{{message}}</span>
</template>

<script>
 export default {
   props:{
     message:{
       type: String,
       default: () => ''
     }
   }
 }
</script>

<style>
.message {
  width: 45%;
  border-radius: 10px;
  padding: .5em;
  font-size: 10pt;
  background: #F1F0F0;
  color: black;
}
</style>