<template>
  <div class="wrapper">
       <div class="chat-container">
           <div class="head">
               <div style="justify-self: center;">
                   Chatx
               </div>
           </div>
           <div class="chat-box">
               <Login
                   v-if="!authenticated"
                   v-on:authenticated="setAuthenticated"
               />
               <div class="container-chat" v-else>
                   <Channels
                      :channels="channels"
                      v-on:show-message="showMessages"
                      v-on:new-channel="addChannel"
                   />
                   <Messages
                       v-on:new-message="addMessage"
                       :messages="messages"
                       :userData="userData"
                   />
               </div>
           </div>
       </div>
  </div>
</template>

<script>
   import Login from './Login';
   import Messages from './Messages';
   import Channels from './Channels';
   const Chat = require('twilio-chat');

   let chatChannel;
   let Client;
  
   export default {
       components: {
          Messages,
          Login,
          Channels
       },
       data: function () {
           return {
             authenticated: false,
             messages: [],
             userData: {},
             token: null,
             channels: [],
             active_channel: null
           }
       },
       methods: {
         setAuthenticated(userData) {
            fetch('/token', {
                  method: "POST",
                  body: `email=${userData.email}&name=${userData.name}`,
                  headers: {'Content-Type':'application/x-www-form-urlencoded'},
            })
               .then( (response) => response.json() )
               .then( (response) => {
                  if (response.status == 'success') {
                     this.token = response.token;
                     this.userData = userData;
                     this.authenticated = true;
                     
                     this.initializeChat();
                  }
               });
         },
         initializeChat() {
            Chat.Client.create( this.token )
                  .then( (client) => {
                     client.getPublicChannelDescriptors()
                        .then( channels => {
                              this.channels = channels.state.items 
                           });

                     Client = client;
                  });
         },
         setupChannel(channel) {
            channel.decline()
                  .then( (channel) => {
                     // Then join the channel
                     channel.join().then( (channel) => {
                        chatChannel = channel; // Set it global
                        channel.getMessages().then( messages => {
                              this.messages = messages.items;
                        });

                        // Listen for new messages sent to the channel
                        channel.on('messageAdded', (message) => {
                              this.messages.push(message.state);
                        });
                     }).catch( (err) => {
                        // If there is error joining the room,
                        // get all messages on the channel
                        channel.getMessages().then( messages => {
                              this.messages = messages.items;
                        });
                     });
                  });
         },
         addMessage(message) {
            if (chatChannel) {
                  chatChannel.sendMessage(message);
            }
         },
         addChannel(uniqueName) {
            Client.createChannel({
               uniqueName: uniqueName,
               // friendlyName: 'The homie channel'
            }).then((channel) => {
                  this.channels.push(channel.state)
               });
         },
         showMessages(channel) {
            Client.getChannelByUniqueName(channel.uniqueName)
                  .then( channel => {
                     this.setupChannel(channel)
                  });
         },
       }
   }
</script>

<style scoped>
   .wrapper {
       display: grid;
       grid-template-columns: 50px 1fr 50px;
       height: 90vh;
       z-index: 100000;
       box-sizing: border-box;
       padding: 0px;
       margin: 0px;
   }
   .chat-container {
       grid-column-start: 2;
       z-index: 100000;
       box-sizing: border-box;
   }
   .head {
       padding: 9px;
       display: grid;
       background-color: rgb(48, 13, 79);
       color: white;
       border-top-left-radius: 5px;
       border-top-right-radius: 5px;
   }
   .chat-box {
       border-left: 1px solid rgb(48, 13, 79);
       border-right: 1px solid rgb(48, 13, 79);
       background: lightgray;
       height: 100%;
  }
  .container-chat {
       display: grid;
       grid-template-columns: 1fr 4fr;
       min-height: 100%;
  }
</style>