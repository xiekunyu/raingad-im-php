import{n as a,aq as e,d as s,T as n,G as t,ar as c}from"./index-bcce18ca.js";const i={scanQr:()=>{a({url:"/pages/index/scan"})},checkQr:i=>{i.includes(e)?(s=>{let n=s.replace(e,""),t=n.split("/"),i=t[t.length-1];c(n,{realToken:i}).then((e=>{if(0==e.code)switch(e.data.action){case"groupInfo":a({url:"/pages/message/group/info?group_id="+e.data.id});break;case"userInfo":a({url:"/pages/contacts/detail?id="+e.data.id})}}))})(i):s({title:"已识别内容",content:i,confirmText:"复制内容",success:function(a){a.confirm&&n({data:i,success:function(){t({title:"复制成功",icon:"none"})}})}})}};export{i as s};