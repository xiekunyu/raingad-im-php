import{_ as t,o as e,c as a,w as s,a as l,F as n,r as i,n as c,b as o,d as u,t as r,e as d,i as m,f as g,u as f,s as h,g as p,h as _,$ as b,j as x,k as C,l as y,m as k,p as v,q as I,v as T,x as w,y as S,z as j,A as P,B as N,C as A,D as M,E as L,G as B}from"./index-8b7e04f1.js";import{e as H,_ as q}from"./emoji.1e96a6c7.js";import{r as D}from"./uni-app.es.3e1f311a.js";import{s as $}from"./status.652d8781.js";import E from"./pages-contacts-index.cf6e39f7.js";import{s as F}from"./scan.471a4de6.js";const W=t({name:"im-tab",components:{},props:{values:{type:Array,default:function(){return[]}},height:{type:Number,default:72}},data:()=>({active:0,itemHeight:48}),created:function(){this.itemHeight=this.height-16},methods:{changeItem(t,e){this.active=e,this.$emit("change",t,e)}}},[["render",function(t,f,h,p,_,b){const x=m,C=g;return e(),a(C,{class:"tab-main im-flex im-justify-content-start im-align-items-center bg-gray",style:o({height:h.height+"rpx",borderRadius:h.height/2+"rpx"})},{default:s((()=>[(e(!0),l(n,null,i(h.values,((t,l)=>(e(),a(C,{class:c(["tab-item",_.active==l?"active":""]),onClick:e=>b.changeItem(t,l),style:o({height:_.itemHeight+"rpx",borderRadius:_.itemHeight/2+"rpx",lineHeight:_.itemHeight-8+"rpx"})},{default:s((()=>[u(r(t.name)+" ",1),t.count>0?(e(),a(x,{key:0},{default:s((()=>[u(r(t.count>99?"99+":t.count),1)])),_:2},1024)):d("",!0)])),_:2},1032,["class","onClick","style"])))),256))])),_:1},8,["style"])}],["__scopeId","data-v-659a6aea"]]),X=f(h),{contacts:z,unread:Q,msgAt:G}=p(X),R=_(h),{multiport:U}=p(R);const V=t({components:{statusPoint:$,imTab:W},data:()=>({navCurrent:0,msgs:z,pageLoading:!0,multiport:U,socketStatus:!0,damping:.29,moveIndex:-1,btnWidthpx:160,touchStart:!1,modalName:null,listTouchStart:0,listTouchDirection:null,emojiMap:[],chatStatus:!0,paddingB:0,msgAt:X.msgAt,appSetting:R.appSetting,globalConfig:R.globalConfig,active:0,triggered:!0,values:[{id:1,name:"所有",count:0},{id:2,name:"未读",count:Q},{id:3,name:"@我",count:G}]}),computed:{msgsIn(){let t=this.active,e=[];return e=1==t?this.msgs.filter((t=>t.unread>0)):2==t?this.msgs.filter((t=>t.is_at>0)):this.msgs.filter((t=>t.lastContent)),e}},mounted(){this.moveIndex=-1,b("socketStatus",(t=>{t||(this.multiport=!1),this.socketStatus=t}))},created:function(){this.btnWidthpx=-1*x(this.btnWidth)+2;let t=[];H.forEach((function(e){let a=e.children;a.length>0&&a.forEach((function(e){let a=e.name,s=e.src;t[a]=s}))})),this.emojiMap=t,this.paddingB=this.inlineTools},methods:{initContacts(){C("initContacts",!0),this.triggered=!0,setTimeout((()=>{this.triggered=!1}),1e3)},changeChat(t,e){this.active=e},btnTap:function(t,e){0==t?(e.is_top=0==e.is_top?1:0,this.$api.msgApi.setChatTopAPI({id:e.id,is_top:e.is_top,is_group:e.is_group}).then((t=>{0==t.code&&X.updateContacts(e)}))):1==t?y({title:"确定要删除吗?",success:t=>{t.confirm}}):2==t&&(e.is_notice=0==e.is_notice?1:0,this.$api.msgApi.isNoticeAPI({id:e.id,is_notice:e.is_notice,is_group:e.is_group}).then((t=>{0==t.code&&X.updateContacts(e)})))},reconnect(){k({title:"重连中..."}),this.socketIo.connectSocketInit({type:"ping"}),setTimeout((()=>{v()}),1500)},emojiToHtml(t){if(!t)return;let e=this.emojiMap;return t.replace(/\[!(\w+)\]/gi,(function(t,a){var s=a;return e[s]?'<img class=\'mr-5\' style="width:18px;height:18px" emoji-name="'.concat(a,'" src="').concat(e[s],'" />'):"[!".concat(a,"]")}))},ListTouchStart(t){this.listTouchStart=t.touches[0].pageX},ListTouchMove(t){let e=t.touches[0].pageX-this.listTouchStart;Math.abs(e)>100&&e<0?this.listTouchDirection="left":this.listTouchDirection="right"},ListTouchEnd(t){"left"==this.listTouchDirection?(this.modalName=t.currentTarget.dataset.target,this.chatStatus=!1):this.modalName=null,this.listTouchDirection=null},openChat(t,e){this.chatStatus?I({url:"/pages/message/chat?id="+e.id,animationType:"slide-in-right"}):this.chatStatus=!0},from_time(t){return this.$util.timeFormat(t)}}},[["render",function(t,f,h,p,_,b){const x=w("im-tab"),C=g,y=w("statusPoint"),k=D(S("mp-html"),q),v=m,I=w("Empty");return e(),a(C,null,{default:s((()=>[T(C,{class:"pr-10 pl-10 text-gray bg-white im-flex im-space-between im-align-items-center cu-bar fixed",style:o([{top:t.CustomBar+"px"}])},{default:s((()=>[T(x,{class:"mr-10",values:_.values,onChange:b.changeChat},null,8,["values","onChange"]),_.multiport&&_.socketStatus?(e(),a(C,{key:0,class:"im-flex im-justify-content-start im-align-items-center"},{default:s((()=>[T(C,{class:"iconfont icon-web f-16 ml-5"}),T(C,{class:"f-14 ml-5"},{default:s((()=>[u("电脑在线")])),_:1})])),_:1})):d("",!0),_.socketStatus?d("",!0):(e(),a(C,{key:1,class:"socket-status pd-5 im-flex justify-between im-align-items-center radius-10 im-flex1"},{default:s((()=>[T(C,{class:"cuIcon-infofill text-red f-18"}),T(C,{class:"c-666 f-12"},{default:s((()=>[u("通信断开")])),_:1}),T(C,{class:"cuIcon-refresh",onClick:f[0]||(f[0]=t=>b.reconnect())})])),_:1}))])),_:1},8,["style"]),T(C,{class:"im-message-list",style:{"margin-top":"100rpx"}},{default:s((()=>[b.msgsIn.length>0?(e(),a(C,{key:0,class:"cu-list menu-avatar",style:o({paddingBottom:_.paddingB+"px"})},{default:s((()=>[(e(!0),l(n,null,i(b.msgsIn,((t,l)=>(e(),a(C,{class:c(["cu-item second",[_.modalName=="move-box-"+l?"move-cur":"",1==t.is_top?"top-contacts":""]]),key:l,onTouchstart:b.ListTouchStart,onTouchmove:b.ListTouchMove,onTouchend:b.ListTouchEnd,onClick:e=>b.openChat(l,t),"data-target":"move-box-"+l},{default:s((()=>[T(C,{class:c(["cu-avatar lg",_.appSetting.circleAvatar?"round":"radius"]),style:o([{backgroundImage:"url("+t.avatar+")"}])},null,8,["class","style"]),T(C,{class:"content"},{default:s((()=>[T(C,{class:"c-333"},{default:s((()=>[t.is_online&&0==t.is_group&&1==_.globalConfig.chatInfo.online?(e(),a(y,{key:0,type:"success"})):d("",!0),T(C,{class:"text-overflow f-16",style:{width:"80%"}},{default:s((()=>[u(r(t.displayName),1)])),_:2},1024)])),_:2},1024),T(C,{class:"im-flex im-justify-content-start im-align-items-start lh-20x",style:{height:"50rpx",overflow:"hidden"}},{default:s((()=>[t.is_at?(e(),a(C,{key:0,class:"text-red f-12 mr-5"},{default:s((()=>[u("[有"+r(t.is_at)+"人@我] ",1)])),_:2},1024)):d("",!0),T(k,{content:b.emojiToHtml(t.lastContent),class:"im-flex f-12 text-gray text-overflow no-click"},null,8,["content"])])),_:2},1024)])),_:2},1024),T(C,{class:"action"},{default:s((()=>[T(C,{class:"text-grey text-xs"},{default:s((()=>[u(r(b.from_time(t.lastSendTime)),1)])),_:2},1024),t.unread>0?(e(),a(C,{key:0,class:c(["cu-tag round sm",t.is_notice?"bg-red":"bg-notremind"])},{default:s((()=>[u(r(t.unread),1)])),_:2},1032,["class"])):d("",!0),0==t.is_notice&&0==t.unread?(e(),a(C,{key:1,class:"c-999"},{default:s((()=>[T(v,{class:"cuIcon-musicforbidfill"})])),_:1})):d("",!0)])),_:2},1024),T(C,{class:"move second"},{default:s((()=>[1==t.is_top?(e(),a(C,{key:0,class:"bg-grey",onClick:e=>b.btnTap(0,t)},{default:s((()=>[u("取消置顶")])),_:2},1032,["onClick"])):(e(),a(C,{key:1,class:"bg-blue",onClick:e=>b.btnTap(0,t)},{default:s((()=>[u("置顶聊天")])),_:2},1032,["onClick"])),1==t.is_notice?(e(),a(C,{key:2,class:"bg-orange",onClick:e=>b.btnTap(2,t)},{default:s((()=>[u("免扰")])),_:2},1032,["onClick"])):(e(),a(C,{key:3,class:"bg-orange",onClick:e=>b.btnTap(2,t)},{default:s((()=>[u("取消免扰")])),_:2},1032,["onClick"]))])),_:2},1024)])),_:2},1032,["class","onTouchstart","onTouchmove","onTouchend","onClick","data-target"])))),128))])),_:1},8,["style"])):(e(),a(I,{key:1,noDatatext:"暂无聊天",textcolor:"#999"}))])),_:1})])),_:1})}],["__scopeId","data-v-0aa478a5"]]),J=""+new URL("user-card-bg-ba5b09d7.jpg",import.meta.url).href,K=_();const O=t({data:()=>({isCard:!0,userInfo:K.userInfo,paddingB:0}),created:function(){this.paddingB=this.inlineTools},methods:{IsCard(t){this.isCard=t.detail.value}}},[["render",function(t,d,f,h,p,_){const b=j,x=g,C=m;return e(),a(x,{style:o({paddingBottom:p.paddingB+"px"})},{default:s((()=>[T(x,{class:"im-friend-header"},{default:s((()=>[T(x,{class:"im-friend-bg"},{default:s((()=>[T(b,{class:"im-friend-image",src:J,mode:"widthFix"})])),_:1}),T(x,{class:"im-user im-flex im-justify-content-start align-center"},{default:s((()=>[T(C,{class:"text-white mr-5"},{default:s((()=>[u(r(p.userInfo.realname),1)])),_:1}),T(b,{class:"radius-10",style:{width:"120rpx",height:"120rpx"},src:p.userInfo.avatar,mode:"widthFix"},null,8,["src"])])),_:1})])),_:1}),T(x,{class:"m-10 text-center"},{default:s((()=>[u("此页面是静态模板！")])),_:1}),T(x,{class:"cu-card dynamic no-card"},{default:s((()=>[T(x,{class:"cu-item shadow"},{default:s((()=>[T(x,{class:"cu-list menu-avatar"},{default:s((()=>[T(x,{class:"cu-item"},{default:s((()=>[T(x,{class:"cu-avatar round lg",style:{"background-image":"url(https://api.multiavatar.com/raingad3.png?apikey=zdvXV3W4MjwhP9)"}}),T(x,{class:"content flex-sub"},{default:s((()=>[T(x,null,{default:s((()=>[u("凯尔")])),_:1}),T(x,{class:"text-gray text-sm flex justify-between"},{default:s((()=>[u(" 2019年12月3日 ")])),_:1})])),_:1})])),_:1})])),_:1}),T(x,{class:"text-content"},{default:s((()=>[u(" 折磨生出苦难，苦难又会加剧折磨，凡间这无穷的循环，将有我来终结！ ")])),_:1}),T(x,{class:c(["grid flex-sub padding-lr",p.isCard?"col-3 grid-square":"col-1"])},{default:s((()=>[(e(!0),l(n,null,i(p.isCard?9:1,((t,s)=>(e(),a(x,{class:c(["bg-img",p.isCard?"":"only-img"]),style:{"background-image":"url(https://ossweb-img.qq.com/images/lol/web201310/skin/big10006.jpg)"},key:s},null,8,["class"])))),128))])),_:1},8,["class"]),T(x,{class:"text-gray text-sm text-right padding"},{default:s((()=>[T(C,{class:"cuIcon-attentionfill margin-lr-xs"}),u(" 10 "),T(C,{class:"cuIcon-appreciatefill margin-lr-xs"}),u(" 20 "),T(C,{class:"cuIcon-messagefill margin-lr-xs"}),u(" 30 ")])),_:1}),T(x,{class:"cu-list menu-avatar comment solids-top"},{default:s((()=>[T(x,{class:"cu-item"},{default:s((()=>[T(x,{class:"cu-avatar round",style:{"background-image":"url(https://api.multiavatar.com/raingad5.png?apikey=zdvXV3W4MjwhP9)"}}),T(x,{class:"content"},{default:s((()=>[T(x,{class:"text-grey"},{default:s((()=>[u("莫甘娜")])),_:1}),T(x,{class:"text-gray text-content text-df"},{default:s((()=>[u(" 凯尔，你被自己的光芒变的盲目。 ")])),_:1}),T(x,{class:"bg-grey padding-sm radius margin-top-sm text-sm"},{default:s((()=>[T(x,{class:"flex"},{default:s((()=>[T(x,null,{default:s((()=>[u("凯尔：")])),_:1}),T(x,{class:"flex-sub"},{default:s((()=>[u("妹妹，你在帮他们给黑暗找借口吗?")])),_:1})])),_:1})])),_:1}),T(x,{class:"margin-top-sm flex justify-between"},{default:s((()=>[T(x,{class:"text-gray text-df"},{default:s((()=>[u("2018年12月4日")])),_:1}),T(x,null,{default:s((()=>[T(C,{class:"cuIcon-appreciatefill text-red"}),T(C,{class:"cuIcon-messagefill text-gray margin-left-sm"})])),_:1})])),_:1})])),_:1})])),_:1}),T(x,{class:"cu-item"},{default:s((()=>[T(x,{class:"cu-avatar round",style:{"background-image":"url(https://api.multiavatar.com/raingad2.png?apikey=zdvXV3W4MjwhP9)"}}),T(x,{class:"content"},{default:s((()=>[T(x,{class:"text-grey"},{default:s((()=>[u("凯尔")])),_:1}),T(x,{class:"text-gray text-content text-df"},{default:s((()=>[u(" 妹妹，如果不是为了飞翔，我们要这翅膀有什么用? ")])),_:1}),T(x,{class:"bg-grey padding-sm radius margin-top-sm text-sm"},{default:s((()=>[T(x,{class:"flex"},{default:s((()=>[T(x,null,{default:s((()=>[u("莫甘娜：")])),_:1}),T(x,{class:"flex-sub"},{default:s((()=>[u("如果不能立足于大地，要这双脚又有何用?")])),_:1})])),_:1})])),_:1}),T(x,{class:"margin-top-sm flex justify-between"},{default:s((()=>[T(x,{class:"text-gray text-df"},{default:s((()=>[u("2018年12月4日")])),_:1}),T(x,null,{default:s((()=>[T(C,{class:"cuIcon-appreciate text-gray"}),T(C,{class:"cuIcon-messagefill text-gray margin-left-sm"})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["style"])}],["__scopeId","data-v-f6597acf"]]),Y=_(h);const Z=t({data:()=>({loginStore:Y,globalConfig:Y.globalConfig,appSetting:Y.appSetting}),onShow(){},methods:{logout(){let t=P("client_id");this.$api.LoginApi.logout({client_id:t}).then((t=>{0==t.code&&Y.logout()}))},about(){if(this.globalConfig&&this.globalConfig.demon_mode){I({url:"/pages/mine/webview?src="+"https://im.raingad.com"})}else I({url:"/pages/mine/about"})},showSetting(){I({url:"/pages/mine/setting"})},showsecure(){I({url:"/pages/mine/secure"})},editInfo(){I({url:"/pages/mine/profile"})},scan(){F.scanQr()},openQr(){I({url:"/pages/index/qrcode"})}}},[["render",function(t,l,n,i,f,h){const p=g,_=m,b=N;return e(),a(p,null,{default:s((()=>[T(p,{class:"padding flex im-space-between im-align-items-center bg-white mb-10"},{default:s((()=>[T(p,{class:"flex justify-start bg-white",onClick:l[0]||(l[0]=t=>h.editInfo())},{default:s((()=>[T(p,{class:c(["cu-avatar lg mr-15",f.appSetting.circleAvatar?"round":"radius"]),style:o([{backgroundImage:"url("+f.loginStore.userInfo.avatar+")"}])},null,8,["class","style"]),T(p,{class:"im-flex im-justify-content-start im-columns"},{default:s((()=>[T(p,{class:"mb-5 f-18 mb-10 im-flex im-align-items-center"},{default:s((()=>[T(p,{class:"c-333"},{default:s((()=>[u(r(f.loginStore.userInfo.realname),1)])),_:1}),T(p,{class:c(["cu-tag ml-10 round light",f.loginStore.userInfo.is_auth?"bg-orange":"bg-grey"])},{default:s((()=>[u(r(f.loginStore.userInfo.is_auth?"已认证":"未认证"),1)])),_:1},8,["class"])])),_:1}),T(p,{class:"text-gray mb-10"},{default:s((()=>[u(r(f.loginStore.userInfo.account),1)])),_:1})])),_:1})])),_:1}),T(p,{class:"cuIcon-qrcode f-24 text-gray",onClick:h.openQr},null,8,["onClick"])])),_:1}),T(p,{class:"cu-list menu"},{default:s((()=>[T(p,{class:"cu-item",onClick:h.scan},{default:s((()=>[T(p,{class:"content"},{default:s((()=>[T(_,{class:"cuIcon-scan text-blue"}),T(_,null,{default:s((()=>[u("扫一扫")])),_:1})])),_:1}),T(p,{class:"action"},{default:s((()=>[T(_,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),T(p,{class:"cu-item",onClick:h.showSetting},{default:s((()=>[T(p,{class:"content"},{default:s((()=>[T(_,{class:"cuIcon-settings text-grey"}),T(_,null,{default:s((()=>[u("通用设置")])),_:1})])),_:1}),T(p,{class:"action"},{default:s((()=>[T(_,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),T(p,{class:"cu-item",onClick:h.showsecure},{default:s((()=>[T(p,{class:"content"},{default:s((()=>[T(_,{class:"cuIcon-safe text-orange"}),T(_,null,{default:s((()=>[u("账号安全")])),_:1})])),_:1}),T(p,{class:"action"},{default:s((()=>[T(_,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),f.globalConfig.demon_mode?(e(),a(p,{key:0,class:"cu-item",onClick:l[1]||(l[1]=t=>h.about())},{default:s((()=>[T(p,{class:"content"},{default:s((()=>[T(_,{class:"cuIcon-info text-green"}),T(_,null,{default:s((()=>[u("关于IM")])),_:1})])),_:1}),T(p,{class:"action"},{default:s((()=>[T(_,{class:"text-grey cuIcon-right"})])),_:1})])),_:1})):d("",!0)])),_:1}),T(p,{class:"padding flex flex-direction"},{default:s((()=>[T(b,{class:"cu-btn bg-red lg",onClick:l[2]||(l[2]=t=>h.logout())},{default:s((()=>[u("退出登录")])),_:1})])),_:1})])),_:1})}]]),tt=f(h),et=_(h),{unread:at,sysUnread:st}=p(tt);const lt=t({components:{message:V,contacts:E,compass:O,mine:Z},data(){let t=[{name:"message",title:"消息",notice:at},{name:"contacts",title:"通讯录",notice:st}],e={name:"compass",title:"探索",notice:0};et.globalConfig&&et.globalConfig.demon_mode&&t.push(e);return t.push({name:"mine",title:"我的",notice:0}),{globalConfig:et.globalConfig,PageCur:"message",PageName:"消息",TabCur:0,modelName:!1,navList:t}},mounted(){A(),this.initContacts(),b("socketStatus",(t=>{t&&(console.log("触发了一次"),this.initContacts())})),M("initContacts"),b("initContacts",(t=>{this.initContacts()}))},methods:{closeModel(){this.modelName=!1},scan(){F.scanQr()},NavChange:function(t){this.PageCur=t.name,this.PageName=t.title},showContacts(){1==this.TabCur?this.TabCur=0:this.TabCur=1},initContacts(){this.modelName="",this.$api.msgApi.initContacts().then((t=>{tt.sysUnread=t.count,tt.initContacts(t.data)}))},addGroup(){I({url:"/pages/index/userSelection?type=1"})},addFriend(){I({url:"/pages/contacts/search"})},search(){const t="message"==this.PageCur?1:2;I({url:"/pages/index/search?type="+t})}}},[["render",function(t,o,f,h,p,_){const b=m,x=g,C=w("cu-custom"),y=w("message"),k=w("contacts"),v=w("compass"),I=w("mine"),S=j;return e(),a(x,null,{default:s((()=>[T(C,{bgColor:"bg-white"},{backText:s((()=>["message"==p.PageCur||"contacts"==p.PageCur?(e(),a(x,{key:0,class:"f-20 ml-10 mr-10",onClick:o[0]||(o[0]=t=>_.search())},{default:s((()=>[T(b,{class:"cuIcon-search",style:{"margin-left":"-10px"}})])),_:1})):d("",!0)])),content:s((()=>[u(r(p.PageName),1)])),right:s((()=>["contacts"==p.PageCur&&p.globalConfig&&p.globalConfig.demon_mode?(e(),a(x,{key:0,class:"f-20 ml-10 mr-10",onClick:o[1]||(o[1]=t=>_.showContacts())},{default:s((()=>[T(b,{class:c(["f-24",p.TabCur?"cuIcon-peoplelist":"cuIcon-friend"])},null,8,["class"])])),_:1})):d("",!0),"message"==p.PageCur?(e(),a(x,{key:1,class:"f-20 ml-10 mr-10",onClick:o[2]||(o[2]=t=>p.modelName="add")},{default:s((()=>[T(b,{class:"cuIcon-add f-28"})])),_:1})):d("",!0)])),_:1}),T(x,null,{default:s((()=>[L(T(y,null,null,512),[[B,"message"==p.PageCur]]),L(T(k,{TabCur:p.TabCur},null,8,["TabCur"]),[[B,"contacts"==p.PageCur]]),L(T(v,null,null,512),[[B,"compass"==p.PageCur]]),L(T(I,null,null,512),[[B,"mine"==p.PageCur]])])),_:1}),T(x,{class:"cu-bar tabbar bg-white shadow foot"},{default:s((()=>[(e(!0),l(n,null,i(p.navList,((t,l)=>(e(),a(x,{class:"action",onClick:e=>_.NavChange(t),key:l,"data-cur":"message"},{default:s((()=>[T(x,{class:"cuIcon-cu-image"},{default:s((()=>[T(S,{src:"/static/image/tabbar/"+[t.name]+[p.PageCur==t.name?"-active":""]+".svg"},null,8,["src"]),t.notice>0?(e(),a(x,{key:0,class:"cu-tag badge"},{default:s((()=>[u(r(t.notice),1)])),_:2},1024)):d("",!0)])),_:2},1024),T(x,{class:c(p.PageCur==t.name?"text-green":"text-black")},{default:s((()=>[u(r(t.title),1)])),_:2},1032,["class"])])),_:2},1032,["onClick"])))),128))])),_:1}),T(x,{class:c(["cu-modal bottom-modal","add"==p.modelName?"show":""]),onClick:o[8]||(o[8]=t=>p.modelName="")},{default:s((()=>[T(x,{class:"cu-dialog"},{default:s((()=>[T(x,{class:"cu-list menu bg-white"},{default:s((()=>[T(x,{class:"cu-item",onClick:o[3]||(o[3]=t=>{_.initContacts()})},{default:s((()=>[T(x,{class:"content padding-tb-sm"},{default:s((()=>[T(b,{class:"cuIcon-refresh"}),T(b,null,{default:s((()=>[u("更新消息列表")])),_:1})])),_:1})])),_:1}),2==p.globalConfig.sysInfo.runMode?(e(),a(x,{key:0,class:"cu-item",onClick:o[4]||(o[4]=t=>_.addFriend())},{default:s((()=>[T(x,{class:"content padding-tb-sm"},{default:s((()=>[T(b,{class:"cuIcon-friendadd"}),T(b,null,{default:s((()=>[u("添加朋友")])),_:1})])),_:1})])),_:1})):d("",!0),T(x,{class:"cu-item",onClick:o[5]||(o[5]=t=>_.addGroup())},{default:s((()=>[T(x,{class:"content padding-tb-sm"},{default:s((()=>[T(b,{class:"cuIcon-friend"}),T(b,null,{default:s((()=>[u("创建群聊")])),_:1})])),_:1})])),_:1}),T(x,{class:"cu-item",onClick:o[6]||(o[6]=t=>_.scan())},{default:s((()=>[T(x,{class:"content padding-tb-sm"},{default:s((()=>[T(b,{class:"cuIcon-scan mr-10"}),T(b,null,{default:s((()=>[u("扫 一 扫")])),_:1})])),_:1})])),_:1}),T(x,{class:"parting-line-5"}),T(x,{class:"cu-item",onClick:o[7]||(o[7]=t=>p.modelName="")},{default:s((()=>[T(x,{class:"content padding-tb-sm"},{default:s((()=>[T(b,{class:"c-red"},{default:s((()=>[u("取消")])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["class"])])),_:1})}]]);export{lt as default};