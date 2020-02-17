import axios from 'axios'

//接口域名
var postUrl = 'http://shopfx.admin.com/v1/'; //tinakj
export let baseUrl  = 'http://shopfx.admin.com/v1/';

export function post_(url,data,callback){

    data.auth_key = localStorage.getItem('key')?localStorage.getItem('key'):''
    var qs = require("querystring")
    data = qs.stringify(data);
    // console.log(data);
    axios.post(postUrl+url,data)
        .then((response)=>{
            if(response.data.code == 404){
                localStorage.clear();
                location.replace("#/Login")
                return
            }
            callback(response.data)
        })
        .catch(function (error) {
            console.log(error);
        });
}

export function ajax_upload(url,params,act)
{
    $.ajax({
        url:postUrl+url,
        data:params,
        type:'POST',
        dataType:'json',
        processData:false,
        contentType : false,
        // async:false,
        success:function(data){
            //$("#alert-bg").fadeOut(2000);
            act(data);
        },
        error:function(e){
            console.log('ajax error:',e);
            act(e.responseText);
        }
    });
}

export function download(url) {

    var iframe =document.createElement("iframe")

    iframe.style.display ="none";

    iframe.src = url;

    document.body.appendChild(iframe);
}
