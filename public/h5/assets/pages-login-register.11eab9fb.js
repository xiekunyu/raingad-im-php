import{_ as e,q as a,s as o,x as s,O as l,o as t,c as r,w as n,i,f as m,h as d,t as u,r as c,v as g,I as p,y as f,a9 as h}from"./index-88b4c777.js";import{p as _}from"./package.175629ee.js";const F=a(o);const b=e({data:()=>({regForm:{username:"",password:"",repass:"",code:""},forget:!1,packData:_,globalConfig:F.globalConfig}),watch:{forget(e){e&&(this.regForm.password="123456")}},mounted(){},methods:{sendCode(){if(!this.regForm.username)return s({title:"请输入账号！",icon:"none"}),!1;let e={account:this.regForm.username,type:2};this.$api.LoginApi.sendCode(e).then((e=>{s({title:e.msg,icon:"none"})}))},login(e){return""==this.regForm.username?(s({title:"请输入账号！",icon:"none"}),!1):""==this.regForm.password&&this.regForm.password.length<6&&this.regForm.password>16?(s({title:"请输入6-16位密码！",icon:"none"}),!1):this.regForm.password!=this.regForm.repass?(s({title:"两次密码输入不相同！",icon:"none"}),!1):void this.$api.LoginApi.register(this.regForm).then((e=>{0==e.code&&setTimeout((()=>{l({url:"/pages/login/index"})}),2e3)}))}}},[["render",function(e,a,o,s,l,_){const F=c("cu-custom"),b=i,w=g,x=p,k=f,C=h;return t(),r(b,null,{default:n((()=>[m(F,{bgColor:"bg-gradual-blue",isBack:!0},{backText:n((()=>[])),content:n((()=>[d("账号注册")])),_:1}),m(b,{style:{height:"100rpx"}}),m(b,{class:"padding im-flex im-rows im-justify-content-center mb-10"},{default:n((()=>[m(b,{class:"im-flex im-rows im-justify-content-center"},{default:n((()=>[m(w,{class:"login-logo",src:l.globalConfig.sysInfo.logo??l.packData.logo,mode:"fixWidth"},null,8,["src"])])),_:1})])),_:1}),m(b,{class:"im-flex im-rows im-justify-content-center"},{default:n((()=>[d(u(l.globalConfig.sysInfo.name??l.packData.name),1)])),_:1}),m(C,null,{default:n((()=>[m(b,{class:"cu-form-group margin-top"},{default:n((()=>[m(b,{class:"title"},{default:n((()=>[d("账号")])),_:1}),m(x,{placeholder:"账号",maxlength:"32",name:"input",modelValue:l.regForm.username,"onUpdate:modelValue":a[0]||(a[0]=e=>l.regForm.username=e)},null,8,["modelValue"])])),_:1}),m(b,{class:"cu-form-group"},{default:n((()=>[m(b,{class:"title"},{default:n((()=>[d("验证码")])),_:1}),m(x,{placeholder:"请输入验证码",maxlength:"6",name:"input",modelValue:l.regForm.code,"onUpdate:modelValue":a[1]||(a[1]=e=>l.regForm.code=e)},null,8,["modelValue"]),m(k,{class:"cu-btn bg-blue shadow",onClick:_.sendCode},{default:n((()=>[d("发送验证码")])),_:1},8,["onClick"])])),_:1}),m(b,{class:"cu-form-group"},{default:n((()=>[m(b,{class:"title"},{default:n((()=>[d("密码")])),_:1}),m(x,{placeholder:"请输入密码",maxlength:"32",type:"password",name:"input",modelValue:l.regForm.password,"onUpdate:modelValue":a[2]||(a[2]=e=>l.regForm.password=e)},null,8,["modelValue"])])),_:1}),m(b,{class:"cu-form-group"},{default:n((()=>[m(b,{class:"title"},{default:n((()=>[d("重复密码")])),_:1}),m(x,{placeholder:"请重复输入密码",maxlength:"32",type:"password",name:"input",modelValue:l.regForm.repass,"onUpdate:modelValue":a[3]||(a[3]=e=>l.regForm.repass=e)},null,8,["modelValue"])])),_:1})])),_:1}),m(b,{class:"flex flex-direction im-login-btn"},{default:n((()=>[m(k,{class:"cu-btn lg bg-blue",onClick:a[4]||(a[4]=e=>_.login())},{default:n((()=>[d("注册")])),_:1})])),_:1}),m(b,{class:"footer-version c-999"},{default:n((()=>[d(u(l.globalConfig.sysInfo.name??l.packData.name)+" for "+u(l.packData.version),1)])),_:1})])),_:1})}],["__scopeId","data-v-f67a31c8"]]);export{b as default};
