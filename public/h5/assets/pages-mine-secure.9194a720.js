import{_ as e,u as t,s,x as a,B as l,q as o,o as n,c as i,w as c,j as d,g as u,i as r,t as m,h,G as f,f as g,d as p,F as _,r as w,k as C,J as x,y as b}from"./index-030df82b.js";const k=t(s);const I=e({data:()=>({userInfo:k.userInfo,globalConfig:k.globalConfig,modelName:"",code:"",account:"",newCode:"",password:"",originalPassword:"",repass:"",loading:!1,editPass:!1}),onShow(){},methods:{logout(){let e=a("client_id");this.$api.LoginApi.logout({client_id:e}).then((e=>{0==e.code&&k.logout()}))},editAcc(){if(!this.userInfo.is_auth)return l({title:"请先认证账户！",icon:"none"}),!1;this.modelName="show",this.editPass=!1},openWeb(e){o({url:"/pages/mine/webview?src=https://im.raingad.com"})},save(){if(""==this.code&&this.userInfo.is_auth)return l({title:"请输入验证码",icon:"none"}),!1;if(this.editPass){if(""==this.password||this.password.length<6||this.password.length>16)return l({title:"请输入6-16个字符串的密码",icon:"none"}),!1;if(this.password!=this.repass)return l({title:"两次密码不一致",icon:"none"}),!1;let e={password:this.password,code:this.code,originalPassword:this.originalPassword};this.$api.msgApi.editPassword(e).then((e=>{0==e.code&&(this.modelName="",this.password="",this.repass="",l({title:e.msg,icon:"none"}))}))}else{if(""==this.account)return l({title:"请输入新的账号",icon:"none"}),!1;if(""==this.newCode)return l({title:"请输入新账号的验证码",icon:"none"}),!1;let e={account:this.account,code:this.code,newCode:this.newCode};this.$api.msgApi.editAccount(e).then((e=>{0==e.code&&(this.modelName="",this.account="",this.code="",this.newCode="",l({title:"修改成功，请重新登陆",icon:"none"}))}))}},sendCode(e){let t=e?this.userInfo.account:this.account,s=this.editPass?3:4;if(""==t)return l({title:"请输入新的账号",icon:"none"}),!1;this.loading=!0,this.$api.LoginApi.sendCode({account:t,type:s}).then((e=>{l({title:e.msg,icon:"none"}),this.loading=!1}))}}},[["render",function(e,t,s,a,l,o){const k=w("cu-custom"),I=C,V=d,y=x,P=b;return n(),i(V,null,{default:c((()=>[u(k,{bgColor:"bg-gradual-green",isBack:!0},{backText:c((()=>[])),content:c((()=>[r("账号安全")])),_:1}),u(V,{class:"cu-list menu mt-10"},{default:c((()=>[u(V,{class:"cu-item",onClick:o.editAcc},{default:c((()=>[u(V,{class:"content"},{default:c((()=>[u(I,{class:"cuIcon-settings text-grey"}),u(I,null,{default:c((()=>[r("我的账号")])),_:1})])),_:1}),u(V,{class:"action"},{default:c((()=>[u(I,null,{default:c((()=>[r(m(l.userInfo.account),1)])),_:1}),u(I,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),u(V,{class:"cu-item",onClick:t[0]||(t[0]=e=>{l.modelName="show",l.editPass=!0})},{default:c((()=>[u(V,{class:"content"},{default:c((()=>[u(I,{class:"cuIcon-lock text-green"}),u(I,null,{default:c((()=>[r("修改密码")])),_:1})])),_:1}),u(V,{class:"action"},{default:c((()=>[u(I,{class:"text-grey cuIcon-right"})])),_:1})])),_:1}),l.userInfo.is_auth?h("",!0):(n(),i(V,{key:0,class:"cu-item",onClick:t[1]||(t[1]=e=>{l.modelName="show",l.editPass=!1})},{default:c((()=>[u(V,{class:"content padding-tb-sm"},{default:c((()=>[u(V,null,{default:c((()=>[u(I,{class:"cuIcon-vip text-orange ml-5"}),r(),u(I,{class:"ml-10"},{default:c((()=>[r("认证账户")])),_:1})])),_:1}),u(V,{class:"text-gray text-sm"},{default:c((()=>[u(I,{class:"cuIcon-infofill ml-5 mr-10"}),r(" 验证账户的真实性，绑定后请使用新账户来登录！")])),_:1})])),_:1}),u(V,{class:"action"},{default:c((()=>[u(I,{class:"text-grey cuIcon-right"})])),_:1})])),_:1}))])),_:1}),u(V,{class:g(["cu-modal bottom-modal","show"==l.modelName?"show":""]),onClick:t[12]||(t[12]=e=>l.modelName="")},{default:c((()=>[u(V,{class:"cu-dialog",onClick:t[11]||(t[11]=f((()=>{}),["stop"]))},{default:c((()=>[u(V,{class:"cu-bar bg-white"},{default:c((()=>[u(V,{class:"action text-gray",onClick:t[2]||(t[2]=e=>l.modelName="")},{default:c((()=>[r("取消")])),_:1}),u(V,{class:"action text-green",onClick:o.save},{default:c((()=>[r("保存")])),_:1},8,["onClick"])])),_:1}),u(V,{class:"manage-content mb-20"},{default:c((()=>[u(V,{class:"cu-list menu mt-15 bg-white"},{default:c((()=>[l.userInfo.is_auth?(n(),i(V,{key:0,class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("验证码")])),_:1}),u(y,{placeholder:"输入验证码",name:"input",modelValue:l.code,"onUpdate:modelValue":t[3]||(t[3]=e=>l.code=e)},null,8,["modelValue"]),u(P,{class:g(["cu-btn bg-green shadow cu-load",l.loading?"loading":""]),disabled:l.loading,onClick:t[4]||(t[4]=e=>o.sendCode(!0))},{default:c((()=>[r("发送验证码")])),_:1},8,["class","disabled"])])),_:1})):h("",!0),l.editPass?(n(),p(_,{key:2},[l.userInfo.is_auth?h("",!0):(n(),i(V,{key:0,class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("原密码")])),_:1}),u(y,{placeholder:"输入原来的密码",name:"input",modelValue:l.originalPassword,"onUpdate:modelValue":t[8]||(t[8]=e=>l.originalPassword=e)},null,8,["modelValue"])])),_:1})),u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("新密码")])),_:1}),u(y,{placeholder:"输入新的密码",name:"input",modelValue:l.password,"onUpdate:modelValue":t[9]||(t[9]=e=>l.password=e)},null,8,["modelValue"])])),_:1}),u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("重复新密码")])),_:1}),u(y,{placeholder:"重复输入新密码",name:"input",modelValue:l.repass,"onUpdate:modelValue":t[10]||(t[10]=e=>l.repass=e)},null,8,["modelValue"])])),_:1})],64)):(n(),p(_,{key:1},[u(V,{class:"text-gray m-15 text-left"},{default:c((()=>[u(I,{class:"cuIcon-infofill ml-5 mr-10"}),r(" 验证账户的真实性，绑定后请使用新账户来登录！ ")])),_:1}),u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("新账号")])),_:1}),u(y,{placeholder:"输入新的邮箱或者手机号",name:"input",modelValue:l.account,"onUpdate:modelValue":t[5]||(t[5]=e=>l.account=e)},null,8,["modelValue"])])),_:1}),u(V,{class:"cu-form-group text-right"},{default:c((()=>[u(V,{class:"title"},{default:c((()=>[r("新账号验证码")])),_:1}),u(y,{placeholder:"输入验证码",name:"input",modelValue:l.newCode,"onUpdate:modelValue":t[6]||(t[6]=e=>l.newCode=e)},null,8,["modelValue"]),u(P,{class:g(["cu-btn bg-green shadow cu-load",l.loading?"loading":""]),disabled:l.loading,onClick:t[7]||(t[7]=e=>o.sendCode(!1))},{default:c((()=>[r("发送验证码")])),_:1},8,["class","disabled"])])),_:1})],64))])),_:1})])),_:1})])),_:1})])),_:1},8,["class"])])),_:1})}],["__scopeId","data-v-c7ad4366"]]);export{I as default};
