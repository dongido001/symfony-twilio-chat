<template>
    <div class="channels-container">
        <div class="channels">
            <div
                class="channel"
                v-for="channel in channels"
                v-bind:key="channel.id"
                v-on:click="showMessages(channel)"
                v-bind:class="[(active_channel == channel.uniqueName) ? 'active' : '']"
            >
                {{ channel.uniqueName }}
            </div>
        </div>

        <div class="input-container">
        <input
            class="channel-input"
            type="text"
            placeholder="Add a channel"
            v-model="channel_name"
            v-on:keyup.enter="addChannel"
        >
        </div>
    </div>
</template>

<script>
   export default {
       props: {
           channels: Array,
       },
       data() {
           return {
               channel_name: "",
               active_channel: null
           }
       },
       methods: {
           addChannel() {
               this.$emit('new-channel', this.channel_name);
               this.channel_name = "";
           },
           showMessages(channel) {
               this.active_channel = channel.uniqueName;
               this.$emit('show-message', channel);
           }
       }
   }
</script>

<style scoped>
 .channels-container {
   display: grid;
   grid-template-areas:
       "channels"
       "input";
   grid-template-rows: 1fr 40px;
   border-left: 1px solid rgb(48, 13, 79);
 }
 .channels {
   overflow-y: scroll;
   max-height: -webkit-fill-available;
   grid-area: channels;
 }
 .channel {
   padding: 8px;
   margin: 3px;
   background: azure;
   cursor: pointer;
 }
 .channel:hover {
   background: rgb(66, 85, 85);
   color: white;
 }
  .active {
   background: rgb(66, 85, 85);
   color: white;
 }
 .input-container {
   grid-area: input;
   bottom: 0;
 }
 .channel-input {
   width: 100%;
   height: 100%;
   border-radius: 2px;
   padding: 10px 8px;
   border: 1px solid darkgray;
   font-size: 16px;
   box-sizing: border-box;
  }
</style>