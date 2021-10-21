import axios from "axios";

const api_url = 'https://yoda-chat-backend.herokuapp.com/send_message';
//const fakeResponse = '"{\\"session_token\\":\\"eyJ0eXBlIjoiSldUIiwiYWxnIjoiSFMyNTYifQ.eyJzZXNzaW9uSWQiOiJoMm92ZXVhbjluZXJ1b245bTM2cWhvMWQwNSIsInRpbWVzdGFtcCI6MTYzNDc0MjM5MiwicHJvamVjdCI6InlvZGFfY2hhdGJvdF9lbiJ9.Ox9GzbCs3GN6wh-YAH_piNvQGtdWDSXm4wH1Ot_JXXs\\",\\"response_message\\":\\"Always pass on what you have learned. What would you like to know?\\"}"'

const axiosInstance = axios.create({
    headers: {
        "Access-Control-Allow-Origin": "*",
        "Content-Type" : "application/json"
    }
});

// function sleep(ms) {
//     return new Promise(resolve => setTimeout(resolve, ms));
// }

async function sendMessage(message, conversationToken){
    return axiosInstance.post(api_url,{
            message: message,
            sessionToken: conversationToken
        }).then(
            function (response){
                console.log(response)
            }
        )
        .catch(function (error) {
            console.log(error)
        })
}


export default {
    sendMessage
}

