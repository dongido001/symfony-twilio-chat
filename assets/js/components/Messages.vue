
<template> 
    <div class="messages-container">
        <div id="messages">
            <div
                class="chat-message"
                v-for="message in messages" 
                v-bind:key="message.id"
                v-bind:class="[(message.author == userData.email) ? 'to-right' : 'to-left']"
            >
                {{ message.body }}
            </div>
        </div>
        
        <div class="input-container">
            <input
            class="chat-input" 
            type="text"
            placeholder="Enter your message..." 
            v-model="message"
            v-on:keyup.enter="addMessage"
            >
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            messages: Array,
            userData: Object
        },
        data() {
            return {
                message: ""
            }
        },
        methods: {
            addMessage() {
                this.$emit('new-message', this.message);
                this.message = "";
            }
        }
    }
</script>

<style scoped>
    .messages-container {
        display: grid;
        grid-template-areas:
            "messages"
            "input";
        grid-template-rows: 1fr 40px;
        border-left: 1px solid rgb(48, 13, 79); ;
    }
    .chat-message {
        width: 70%;
        margin-bottom: 8px;
        padding: 5px;
    }
    .to-left {
        background: rgb(191, 202, 204);
        color: rgb(39, 37, 37);
        float: left;
    }
    .to-right {
        background: rgb(48, 13, 79);
        color: white;
        float: right;
    }
    .input-container {
        grid-area: input;
    }
    .chat-input {
        width: 100%; 
        height: 100%;
        border-radius: 2px;
        padding: 10px 8px;
        border: 1px solid darkgray;
        font-size: 16px;
        box-sizing: border-box;
    }
    #messages {
        overflow-y: scroll; 
        max-height: -webkit-fill-available;
    }
</style>
