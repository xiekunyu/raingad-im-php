import{_ as e,u as o,s as l,x as a,y as t,q as n,a9 as s,aa as i,O as c,o as r,c as g,w as d,j as u,g as m,i as f,t as h,f as p,h as _,v as b,J as k,z as C,ab as F,a4 as x}from"./index-3c78000d.js";import{p as y}from"./package.1c4691c6.js";const w=o(l);const V=e({data:()=>({loginForm:{account:"",password:"",code:"",client_id:"",rememberMe:!1},forget:!1,packData:y,globalConfig:w.globalConfig}),watch:{forget(e){e&&(this.loginForm.password="123456")}},mounted(){if(this.globalConfig&&this.globalConfig.demon_mode){const e=Math.floor(19*Math.random()+2);this.loginForm.account=138e8+e,this.loginForm.password="123456"}let e=a("LoginAccount");console.log(e),e&&(this.loginForm=e)},methods:{switchChange(e){this.loginForm.rememberMe=e.detail.value},sendCode(){if(!this.loginForm.account)return t({title:"请输入账号！",icon:"none"}),!1;let e={account:this.loginForm.account,type:1};this.$api.LoginApi.sendCode(e).then((e=>{t({title:e.msg,icon:"none"})}))},register(){n({url:"/pages/login/register"})},login(){if(this.loginForm.rememberMe?s("LoginAccount",this.loginForm):i("LoginAccount"),""==this.loginForm.account)return t({title:"请输入账号！",icon:"none"}),!1;if(""==this.loginForm.password)return t({title:"请输入密码！",icon:"none"}),!1;let e=a("client_id");e&&(this.loginForm.client_id=e),this.$api.LoginApi.login(this.loginForm).then((e=>{if(0==e.code){s("authToken",e.data.authToken);let o=e.data.userInfo;this.socketIo.send({type:"bindUid",user_id:o.user_id}),w.login(o),c({url:"/pages/index/index"})}}))}}},[["render",function(e,o,l,a,t,n){const s=u,i=b,c=k,y=C,w=F,V=x;return r(),g(s,null,{default:d((()=>[m(s,{style:{height:"150rpx"}}),m(s,{class:"padding im-flex im-rows im-justify-content-center mb-10"},{default:d((()=>[m(s,{class:"im-flex im-rows im-justify-content-center"},{default:d((()=>[m(i,{class:"login-logo",src:t.globalConfig.sysInfo.logo??t.packData.logo,mode:"fixWidth"},null,8,["src"])])),_:1})])),_:1}),m(s,{class:"im-flex im-rows im-justify-content-center"},{default:d((()=>[f(h(t.globalConfig.sysInfo.name??t.packData.name),1)])),_:1}),m(w,null,{default:d((()=>[m(s,{class:"cu-form-group margin-top"},{default:d((()=>[m(s,{class:"title"},{default:d((()=>[f("账号")])),_:1}),m(c,{placeholder:"账号",maxlength:"32",name:"input",modelValue:t.loginForm.account,"onUpdate:modelValue":o[0]||(o[0]=e=>t.loginForm.account=e)},null,8,["modelValue"])])),_:1}),t.forget?(r(),g(s,{key:1,class:"cu-form-group"},{default:d((()=>[m(s,{class:"title"},{default:d((()=>[f("验证码")])),_:1}),m(c,{placeholder:"请输入验证码",maxlength:"6",name:"input",modelValue:t.loginForm.code,"onUpdate:modelValue":o[2]||(o[2]=e=>t.loginForm.code=e)},null,8,["modelValue"]),m(y,{class:"cu-btn bg-blue shadow",onClick:n.sendCode},{default:d((()=>[f("发送验证码")])),_:1},8,["onClick"])])),_:1})):(r(),g(s,{key:0,class:"cu-form-group"},{default:d((()=>[m(s,{class:"title"},{default:d((()=>[f("密码")])),_:1}),m(c,{placeholder:"请输入密码",maxlength:"32",type:"password",name:"input",modelValue:t.loginForm.password,"onUpdate:modelValue":o[1]||(o[1]=e=>t.loginForm.password=e)},null,8,["modelValue"])])),_:1}))])),_:1}),m(s,{class:"forget"},{default:d((()=>[m(s,null,{default:d((()=>[m(V,{class:p(["switch",t.loginForm.rememberMe?"checked":""]),checked:t.loginForm.rememberMe,onChange:n.switchChange,style:{transform:"scale(0.7)"}},null,8,["checked","class","onChange"]),f("记住我")])),_:1}),m(s,{class:"text-blue",onClick:o[3]||(o[3]=e=>t.forget=!t.forget)},{default:d((()=>[f(h(t.forget?"密码登陆":"忘记密码"),1)])),_:1})])),_:1}),m(s,{class:"flex flex-direction im-login-btn"},{default:d((()=>[m(y,{class:"cu-btn lg bg-blue",onClick:o[4]||(o[4]=e=>n.login())},{default:d((()=>[f("登录")])),_:1})])),_:1}),t.globalConfig&&1==t.globalConfig.sysInfo.regtype?(r(),g(s,{key:0,class:"flex flex-direction im-reg-btn"},{default:d((()=>[m(y,{class:"cu-btn lg bg-white",onClick:o[5]||(o[5]=e=>n.register())},{default:d((()=>[f("注册")])),_:1})])),_:1})):_("",!0),t.globalConfig&&t.globalConfig.demon_mode?(r(),g(s,{key:1,class:"m-20 c-666"},{default:d((()=>[m(s,{class:"f-16 remark-title mb-10"},{default:d((()=>[f("站点仅用于演示，演示账号")])),_:1}),m(s,{class:"c-999"},{default:d((()=>[f("账号：13800000002~13800000020")])),_:1}),m(s,{class:"c-999"},{default:d((()=>[f("密码：123456")])),_:1})])),_:1})):_("",!0),m(s,{class:"footer-version c-999"},{default:d((()=>[f(h(t.globalConfig.sysInfo.name??t.packData.name)+" for "+h(t.packData.version),1)])),_:1})])),_:1})}],["__scopeId","data-v-708f565a"]]);export{V as default};
