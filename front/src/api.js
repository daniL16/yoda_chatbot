import axios from "axios";

const api_url = 'https://yoda-chat-backend.herokuapp.com/send_message';
//const api_url = 'http://localhost:8088/send_message';

const axiosInstance = axios.create({
    headers: {
        "Access-Control-Allow-Origin": "*",
        "Content-Type" : "application/json"
    }
});


async function sendMessage(message, conversationToken, notFountAttempts){
     return axiosInstance.post(api_url,{
            message: message,
            sessionToken: conversationToken,
            notFountAttempts: notFountAttempts
     });
}


export default {
    sendMessage
}

