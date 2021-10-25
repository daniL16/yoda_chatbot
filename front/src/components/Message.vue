<template>
  <section>
    <span class="author">{{author}}:  </span>
    <span class="message" v-html="message"></span>
  </section>
</template>

<script>
 export default {
   props:{
     author:{
       type: String,
       default: () => ''
     },
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
.author {
  font-size: 10pt;
  color: black;
  font-weight: bold;
}
</style>