import axios from 'axios'

//接口域名
export let base_url = 'http://shopfx.mobile.com/v1/'  

export function post_(url,data,callback){
    data.auth_key = sessionStorage.auth_key?sessionStorage.auth_key:''
    var qs = require("querystring")
    data = qs.stringify(data);
	axios.post(base_url+url,data).then((response)=>{
        if(response.data.code == '404'){
            sessionStorage.clear();
            location.replace("#/page/member/login")
            return
        }
		callback(response.data)
	})
}  

//post_请求
export function get_(url,data,callback){
    data.key = sessionStorage.auth_key?sessionStorage.auth_key:''
    var qs = require("querystring")
    data = qs.stringify(data)
    axios.get(base_url+url,data).then((response)=>{

        callback(response.data)
    })
}

export function ajax_upload(url,params,act)
{ 
    $.ajax({
        url:base_url+url,
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


//时间戳转换
export function formatDate(date,fmt){
    if(/(y+)/.test(fmt)){
        fmt = fmt.replace(RegExp.$1, (date.getFullYear()+'').substr(4-RegExp.$1.length));
    }
    let o = {
        'M+': date.getMonth()+1,
        'd+': date.getDate(),
        'h+': date.getHours(),
        'm+': date.getMinutes(),
        's+': date.getSeconds()
    }
    for(let k in o){    
        let str = o[k]+'';
        if(new RegExp(`(${k})`).test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length===1)?str:padLeftZero(str));
        }
    }
    return fmt;
};

function padLeftZero(str){
    return ('00'+str).substr(str.length);
}



//提取数组中有值相同的对象
export function sortArr(arr, str,ifsort) {
    var _arr = [],
        _t = [],
        // 临时的变量
        _tmp;
 
    if(ifsort){
        // 按照特定的参数将数组排序将具有相同值得排在一起
        arr = arr.sort(function(a, b) {
            var s = a[str],
                t = b[str];
     
            return s < t ? 1 : -1;
        });
    }
    
 
    if ( arr.length ){
        _tmp = arr[0][str];
    }
     // console.log( arr );
     // 将相同类别的对象添加到统一个数组 1 2 1
    for (var i in arr) {
        if ( arr[i][str] === _tmp ){
            _t.push( arr[i] );
        } else {
            _tmp = arr[i][str];
            _arr.push( _t );
            _t = [arr[i]];
        }
    }
    // 将最后的内容推出新数组
    _arr.push( _t );
    return _arr;
}

//过滤数组中的值相同项
export function unique(arr,str1,str2){
    var tmp = new Array();
    if(str2){
        for(var i in arr){
            if(arr[i]!=str1 && arr[i]!=str2){
                tmp.push(arr[i]);
            }
        }
    }else{
        for(var i in arr){
            if(arr[i]!=str1){
                tmp.push(arr[i]);
            }
        }
    }
   
    return tmp;
}