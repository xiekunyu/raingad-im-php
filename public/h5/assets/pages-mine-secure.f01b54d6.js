import{_ as e,u as t,s as a,x as s,q as l,B as o,o as n,c as d,w as c,j as i,g as u,i as r,t as m,G as h,f as g,d as f,F as p,r as _,k as w,J as C,y as b}from"./index-4eea144c.js";const k=t(a);const x=e({data:()=>({userInfo:k.userInfo,globalConfig:k.globalConfig,modelName:"",code:"",account:"",newCode:"",password:"",repass:"",loading:!1,editPass:!1}),onShow(){},methods:{logout(){let e=s("client_id");this.$api.LoginApi.logout({client_id:e}).then((e=>{0==e.code&&k.logout()}))},openWeb(e){l({url:"/pages/mine/webview?src=https://im.raingad.com"})},save(){if(""==this.code)return o({title:"请输入验证码",icon:"none"}),!1;if(this.editPass){if(""==this.password||this.password.length<6||this.password.length>16)return o({title:"请输入6-16个字符串的密码",icon:"none"}),!1;if(this.password!=this.repass)return o({title:"两次密码不一致",icon:"none"}),!1;let e={password:this.password,code:this.code};this.$api.msgApi.editPassword(e).then((e=>{0==e.code&&(this.modelName="",this.password="",this.repass="",o({title:e.msg,icon:"none"}))}))}else{if(""==this.account)return o({title:"请输入新的账号",icon:"none"}),!1;if(""==this.newCode)return o({title:"请输入新账号的验证码",icon:"none"}),!1;let e={account:this.account,code:this.code,newCode:this.newCode};this.$api.msgApi.editAccount(e).then((e=>{0==e.code&&(this.modelName="",this.account="",this.code="",this.newCode="",o({title:"修改成功，请重新登陆",icon:"none"}))}))}},sendCode(e){let t=e?this.userInfo.account:this.account,a=this.editPass?3:4;if(""==t)return o({title:"请输入新的账号",icon:"none"}),!1;this.loading=!0,this.$api.LoginApi.sendCode({account:t,type:a}).then((e=>{o({title:e.msg,icon:"none"}),this.loading=!1}))}}},[["render",function(e,t,a,s,l,o){const k=_("cu-custom"),x=w,V=i,I=C,y=b;return n(),d(V,null,{default:c((()=>[u(k,{bgColor:"bg-gradual-green",isBack:!0},{backText:c((()=>[])),content:c((()=>[r("账号安全")])),_:1}),u(V,{class:"cu-list menu mt-10"},{default:c((()=>[u(V,{class:"cu-item",onClick:t[0]||(t[0]=e=>{l.modelName="show",l.editPass=!1})},{default:c((()=>[u(V,{class:"content"},{default:c((()=>[u(x,{class:"cuIcon-settings text-grey"}),u(x,null,{default:c((()=>[r("我的账号")])),_:1})])),_:1}),u(V,{class:"action"},{default:c((()=>[u(x,null,{default:c((()=>[r(m(l.userInfo.account),1)])),_:1}),u(x,{class:"text-grey cuIcon-right"})])),_:1})])),_:1}),u(V,{class:"cu-item",onClick:t[1]||(t[1]=e=>{l.modelName="show",l.editPass=!0})},{default:c((()=>[u(V,{class:"content"},{default:c((()=>[u(x,{class:"cuIcon-lock text-green"}),u(x,null,{default:c((()=>[r("修改密码")])),_:1})])),_:1}),u(V,{class:"action"},{default:c((()=>[u(x,{class:"text-grey cuIcon-right"})])),_:1})])),_:1})])),_:1}),u(V,{class:g(["cu-modal bottom-modal","show"==l.modelName?"show":""]),onClick:t[11]||(t[11]=e=>l.modelName="")},{default:c((()=>[u(V,{class:"cu-dialog",onClick:t[10]||(t[10]=h((()=>{}),["stop"]))},{default:c((()=>[u(V,{class:"cu-bar bg-white"},{default:c((()=>[u(V,{class:"action text-gray",onClick:t[2]||(t[2]=e=>l.modelName="")},{default:c((()=>[r("取消")])),_:1}),u(V,{class:"action text-green",onClick:o.save},{default:c((()=>[r("保存")])),_:1},8,["onClick"])])),_:1}),u(V,{class:"manage-content mb-20"},{default:c((()=>[u(V,{class:"cu-list menu mt-15 bg-white"},{default:c((()=>[u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("验证码")])),_:1}),u(I,{placeholder:"输入验证码",name:"input",modelValue:l.code,"onUpdate:modelValue":t[3]||(t[3]=e=>l.code=e)},null,8,["modelValue"]),u(y,{class:g(["cu-btn bg-green shadow cu-load",l.loading?"loading":""]),disabled:l.loading,onClick:t[4]||(t[4]=e=>o.sendCode(!0))},{default:c((()=>[r("发送验证码")])),_:1},8,["class","disabled"])])),_:1}),l.editPass?(n(),f(p,{key:1},[u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("新密码")])),_:1}),u(I,{placeholder:"输入新的密码",name:"input",modelValue:l.password,"onUpdate:modelValue":t[8]||(t[8]=e=>l.password=e)},null,8,["modelValue"])])),_:1}),u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("重复新密码")])),_:1}),u(I,{placeholder:"重复输入新密码",name:"input",modelValue:l.repass,"onUpdate:modelValue":t[9]||(t[9]=e=>l.repass=e)},null,8,["modelValue"])])),_:1})],64)):(n(),f(p,{key:0},[u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("新账号")])),_:1}),u(I,{placeholder:"输入新的账号",name:"input",modelValue:l.account,"onUpdate:modelValue":t[5]||(t[5]=e=>l.account=e)},null,8,["modelValue"])])),_:1}),u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("新账号验证码")])),_:1}),u(I,{placeholder:"输入验证码",name:"input",modelValue:l.newCode,"onUpdate:modelValue":t[6]||(t[6]=e=>l.newCode=e)},null,8,["modelValue"]),u(y,{class:g(["cu-btn bg-green shadow cu-load",l.loading?"loading":""]),disabled:l.loading,onClick:t[7]||(t[7]=e=>o.sendCode(!1))},{default:c((()=>[r("发送验证码")])),_:1},8,["class","disabled"])])),_:1})],64))])),_:1})])),_:1})])),_:1})])),_:1},8,["class"])])),_:1})}],["__scopeId","data-v-c9fc87df"]]);export{x as default};