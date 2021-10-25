<template>
  <span class="message" v-html="message"></span>
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
  font-size: 10pt;
  color: black;
}
</style>