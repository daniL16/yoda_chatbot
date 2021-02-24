import {getCookie} from "./utils";
import axios from "axios";

const api_url = 'http://localhost:250/send_message';

axios.defaults.headers.get['Content-Type'] = 'application/json';

async function sendMessage(message){
    const cookieToken = getCookie('conversationToken');
    return axios.post(api_url,{
            message: message,
            sessionToken: cookieToken
        })
        .catch(function (error) {
            console.log('Error! ' + error);
        })
}


export default {
    sendMessage
}

