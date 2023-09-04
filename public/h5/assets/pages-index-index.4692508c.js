import{u as t,s as a,_ as s,a as e,r as l,b as n,o as c,c as i,w as o,n as u,d as r,F as d,e as g,f as m,g as f,h as p,i as h,t as _,j as b,k as C,l as x,m as y,p as k,q as I,v as T,x as v,y as w,z as S}from"./index-030df82b.js";import{e as j,_ as P}from"./emoji.ca85945e.js";import{r as N}from"./uni-app.es.ba88b45a.js";import{s as L}from"./status.66039b37.js";import M from"./pages-contacts-index.414bd332.js";import{s as A}from"./scan.b7252c68.js";const B=t(a);const $=s({name:"message-list",props:{msgs:{type:Array,default:function(){return[]}},btnWidth:{type:Number,default:320}},components:{statusPoint:L},data:()=>({msgsIn:[],damping:.29,moveIndex:-1,x:0,oX:0,scY:!0,btnWidthpx:160,touchStart:!1,modalName:null,listTouchStart:0,listTouchDirection:null,emojiMap:[],chatStatus:!0,paddingB:0,appSetting:B.appSetting,globalConfig:B.globalConfig}),created:function(){this.init(this.msgs),this.btnWidthpx=-1*e(this.btnWidth)+2;let t=[];j.forEach((function(a){let s=a.children;s.length>0&&s.forEach((function(a){let s=a.name,e=a.src;t[s]=e}))})),this.emojiMap=t,this.paddingB=this.inlineTools},watch:{msgs:function(t){this.init(t)}},methods:{init:function(t){this.moveIndex=-1,this.msgsIn=t.filter((t=>t.lastContent))},scrolltolower:function(){},emojiToHtml(t){if(!t)return;let a=this.emojiMap;return t.replace(/\[!(\w+)\]/gi,(function(t,s){var e=s;return a[e]?'<img class=\'mr-5\' style="width:18px;height:18px" emoji-name="'.concat(s,'" src="').concat(a[e],'" />'):"[!".concat(s,"]")}))},ListTouchStart(t){this.listTouchStart=t.touches[0].pageX},ListTouchMove(t){let a=t.touches[0].pageX-this.listTouchStart;Math.abs(a)>100&&a<0?this.listTouchDirection="left":this.listTouchDirection="right"},ListTouchEnd(t){"left"==this.listTouchDirection?(this.modalName=t.currentTarget.dataset.target,this.chatStatus=!1):this.modalName=null,this.listTouchDirection=null},openChat(t,a){this.chatStatus?this.$emit("itemTap",t,a):this.chatStatus=!0},from_time(t){return this.$util.timeFormat(t)},btnTap(t,a){this.$emit("btnTap",t,a)}}},[["render",function(t,a,s,e,x,y){const k=b,I=l("Tags"),T=l("statusPoint"),v=C,w=N(n("mp-html"),P),S=l("Empty");return c(),i(k,{class:"im-message-list"},{default:o((()=>[x.msgsIn.length>0?(c(),i(k,{key:0,class:"cu-list menu-avatar",style:u({paddingBottom:x.paddingB+"px"})},{default:o((()=>[(c(!0),r(d,null,g(x.msgsIn,((t,a)=>(c(),i(k,{class:m(["cu-item",[x.modalName=="move-box-"+a?"move-cur":"",1==t.is_top?"top-contacts":"",0==t.is_group?"third":"second"]]),key:a,onTouchstart:y.ListTouchStart,onTouchmove:y.ListTouchMove,onTouchend:y.ListTouchEnd,onClick:s=>y.openChat(a,t),"data-target":"move-box-"+a},{default:o((()=>[f(k,{class:m(["cu-avatar lg",x.appSetting.circleAvatar?"round":"radius"]),style:u([{backgroundImage:"url("+t.avatar+")"}])},null,8,["class","style"]),f(k,{class:"content"},{default:o((()=>[f(k,{class:"c-333"},{default:o((()=>[1==t.is_group?(c(),i(I,{key:0,text:"群聊",size:"mini"})):p("",!0),t.is_online&&0==t.is_group&&1==x.globalConfig.chatInfo.online?(c(),i(T,{key:1,type:"success"})):p("",!0),f(k,{class:"text-overflow f-16",style:{width:"80%"}},{default:o((()=>[h(_(t.displayName),1)])),_:2},1024)])),_:2},1024),f(k,{class:"im-flex im-justify-content-start im-align-items-start pt-5",style:{height:"50rpx"}},{default:o((()=>[f(k,{class:"text-gray text-sm"},{default:o((()=>[t.unread>0&&0==t.is_notice?(c(),i(v,{key:0},{default:o((()=>[h("["+_(t.unread)+"条未读] ",1)])),_:2},1024)):p("",!0)])),_:2},1024),f(w,{content:y.emojiToHtml(t.lastContent),class:"im-flex text-gray text-sm text-overflow no-click"},null,8,["content"])])),_:2},1024)])),_:2},1024),f(k,{class:"action"},{default:o((()=>[f(k,{class:"text-grey text-xs"},{default:o((()=>[h(_(y.from_time(t.lastSendTime)),1)])),_:2},1024),t.unread>0&&t.is_notice?(c(),i(k,{key:0,class:"cu-tag round bg-red sm"},{default:o((()=>[h(_(t.unread),1)])),_:2},1024)):p("",!0),0==t.is_notice?(c(),i(k,{key:1,class:"c-999"},{default:o((()=>[f(v,{class:"cuIcon-musicforbidfill"})])),_:1})):p("",!0)])),_:2},1024),f(k,{class:m(["move",0==t.is_group?"third":"second"])},{default:o((()=>[1==t.is_top?(c(),i(k,{key:0,class:"bg-grey",onClick:a=>y.btnTap(0,t)},{default:o((()=>[h("取消置顶")])),_:2},1032,["onClick"])):(c(),i(k,{key:1,class:"bg-blue",onClick:a=>y.btnTap(0,t)},{default:o((()=>[h("置顶聊天")])),_:2},1032,["onClick"])),1==t.is_notice?(c(),i(k,{key:2,class:"bg-orange",onClick:a=>y.btnTap(2,t)},{default:o((()=>[h("免扰")])),_:2},1032,["onClick"])):(c(),i(k,{key:3,class:"bg-orange",onClick:a=>y.btnTap(2,t)},{default:o((()=>[h("取消免扰")])),_:2},1032,["onClick"])),0==t.is_group?(c(),i(k,{key:4,class:"bg-red",onClick:a=>y.btnTap(1,t)},{default:o((()=>[h("删除会话")])),_:2},1032,["onClick"])):p("",!0)])),_:2},1032,["class"])])),_:2},1032,["class","onTouchstart","onTouchmove","onTouchend","onClick","data-target"])))),128))])),_:1},8,["style"])):(c(),i(S,{key:1,noDatatext:"暂无聊天",textcolor:"#999"}))])),_:1})}],["__scopeId","data-v-fe159e83"]]),W=x(a),{contacts:D}=y(W);const E=s({components:{messageList:$},data:()=>({navCurrent:0,msgs:D,mainHeight:500,pageLoading:!0}),methods:{btnTap:function(t,a){0==t?(a.is_top=0==a.is_top?1:0,this.$api.msgApi.setChatTopAPI({id:a.id,is_top:a.is_top,is_group:a.is_group}).then((t=>{0==t.code&&W.updateContacts(a)}))):1==t?k({title:"确定要删除吗?",success:t=>{t.confirm&&this.$api.msgApi.delChatAPI({id:a.id,is_group:a.is_group}).then((t=>{0==t.code&&W.deleteContacts(a)}))}}):2==t&&(a.is_notice=0==a.is_notice?1:0,this.$api.msgApi.setIsNotice({id:a.id,is_notice:a.is_notice,is_group:a.is_group}).then((t=>{0==t.code&&W.updateContacts(a)})))},itemTap:function(t,a){W.unread-=a.unread;let s=this.msgs;s[t].unread=0,W.initContacts(s),I({url:"/pages/message/chat?id="+a.id})}}},[["render",function(t,a,s,e,n,u){const r=l("messageList"),d=b;return c(),i(d,null,{default:o((()=>[f(r,{msgs:n.msgs,onItemTap:u.itemTap,onBtnTap:u.btnTap},null,8,["msgs","onItemTap","onBtnTap"])])),_:1})}],["__scopeId","data-v-1a193281"]]),F=""+new URL("user-card-bg-ba5b09d7.jpg",import.meta.url).href,X=t();const q=s({data:()=>({isCard:!0,userInfo:X.userInfo,paddingB:0}),created:function(){this.paddingB=this.inlineTools},methods:{IsCard(t){this.isCard=t.detail.value}}},[["render",function(t,a,s,e,l,n){const p=T,x=b,y=C;return c(),i(x,{style:u({paddingBottom:l.paddingB+"px"})},{default:o((()=>[f(x,{class:"im-friend-header"},{default:o((()=>[f(x,{class:"im-friend-bg"},{default:o((()=>[f(p,{class:"im-friend-image",src:F,mode:"widthFix"})])),_:1}),f(x,{class:"im-user im-flex im-justify-content-start align-center"},{default:o((()=>[f(y,{class:"text-white mr-5"},{default:o((()=>[h(_(l.userInfo.realname),1)])),_:1}),f(p,{class:"radius-10",style:{width:"120rpx",height:"120rpx"},src:l.userInfo.avatar,mode:"widthFix"},null,8,["src"])])),_:1})])),_:1}),f(x,{class:"m-10 text-center"},{default:o((()=>[h("此页面是静态模板！")])),_:1}),f(x,{class:"cu-card dynamic no-card"},{default:o((()=>[f(x,{class:"cu-item shadow"},{default:o((()=>[f(x,{class:"cu-list menu-avatar"},{default:o((()=>[f(x,{class:"cu-item"},{default:o((()=>[f(x,{class:"cu-avatar round lg",style:{"background-image":"url(https://api.multiavatar.com/raingad3.png?apikey=zdvXV3W4MjwhP9)"}}),f(x,{class:"content flex-sub"},{default:o((()=>[f(x,null,{default:o((()=>[h("凯尔")])),_:1}),f(x,{class:"text-gray text-sm flex justify-between"},{default:o((()=>[h(" 2019年12月3日 ")])),_:1})])),_:1})])),_:1})])),_:1}),f(x,{class:"text-content"},{default:o((()=>[h(" 折磨生出苦难，苦难又会加剧折磨，凡间这无穷的循环，将有我来终结！ ")])),_:1}),f(x,{class:m(["grid flex-sub padding-lr",l.isCard?"col-3 grid-square":"col-1"])},{default:o((()=>[(c(!0),r(d,null,g(l.isCard?9:1,((t,a)=>(c(),i(x,{class:m(["bg-img",l.isCard?"":"only-img"]),style:{"background-image":"url(https://ossweb-img.qq.com/images/lol/web201310/skin/big10006.jpg)"},key:a},null,8,["class"])))),128))])),_:1},8,["class"]),f(x,{class:"text-gray text-sm text-right padding"},{default:o((()=>[f(y,{class:"cuIcon-attentionfill margin-lr-xs"}),h(" 10 "),f(y,{class:"cuIcon-appreciatefill margin-lr-xs"}),h(" 20 "),f(y,{class:"cuIcon-messagefill margin-lr-xs"}),h(" 30 ")])),_:1}),f(x,{class:"cu-list menu-avatar comment solids-top"},{default:o((()=>[f(x,{class:"cu-item"},{default:o((()=>[f(x,{class:"cu-avatar round",style:{"background-image":"url(https://api.multiavatar.com/raingad5.png?apikey=zdvXV3W4MjwhP9)"}}),f(x,{class:"content"},{default:o((()=>[f(x,{class:"text-grey"},{default:o((()=>[h("莫甘娜")])),_:1}),f(x,{class:"text-gray text-content text-df"},{default:o((()=>[h(" 凯尔，你被自己的光芒变的盲目。 ")])),_:1}),f(x,{class:"bg-grey padding-sm radius margin-top-sm text-sm"},{default:o((()=>[f(x,{class:"flex"},{default:o((()=>[f(x,null,{default:o((()=>[h("凯尔：")])),_:1}),f(x,{class:"flex-sub"},{default:o((()=>[h("妹妹，你在帮他们给黑暗找借口吗?")])),_:1})])),_:1})])),_:1}),f(x,{class:"margin-top-sm flex justify-between"},{default:o((()=>[f(x,{class:"text-gray text-df"},{default:o((()=>[h("2018年12月4日")])),_:1}),f(x,null,{default:o((()=>[f(y,{class:"cuIcon-appreciatefill text-red"}),f(y,{class:"cuIcon-messagefill text-gray margin-left-sm"})])),_:1})])),_:1})])),_:1})])),_:1}),f(x,{class:"cu-item"},{default:o((()=>[f(x,{class:"cu-avatar round",style:{"background-image":"url(https://api.multiavatar.com/raingad2.png?apikey=zdvXV3W4MjwhP9)"}}),f(x,{class:"content"},{default:o((()=>[f(x,{class:"text-grey"},{default:o((()=>[h("凯尔")])),_:1}),f(x,{class:"text-gray text-content text-df"},{default:o((()=>[h(" 妹妹，如果不是为了飞翔，我们要这翅膀有什么用? ")])),_:1}),f(x,{class:"bg-grey padding-sm radius margin-top-sm text-sm"},{default:o((()=>[f(x,{class:"flex"},{default:o((()=>[f(x,null,{default:o((()=>[h("莫甘娜：")])),_:1}),f(x,{class:"flex-sub"},{default:o((()=>[h("如果不能立足于大地，要这双脚又有何用?")])),_:1})])),_:1})])),_:1}),f(x,{class:"margin-top-sm flex justify-between"},{default:o((()=>[f(x,{class:"text-gray text-df"},{default:o((()=>[h("2018年12月4日")])),_:1}),f(x,null,{default:o((()=>[f(y,{class:"cuIcon-appreciate text-gray"}),f(y,{class:"cuIcon-messagefill text-gray margin-left-sm"})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["style"])}],["__scopeId","data-v-f6597acf"]]),z=t(a);const G=s({data:()=>({loginStore:z,globalConfig:z.globalConfig,appSetting:z.appSetting}),onShow(){},methods:{logout(){let t=v("client_id");this.$api.LoginApi.logout({client_id:t}).then((t=>{0==t.code&&z.logout()}))},about(){if(this.globalConfig&&this.globalConfig.demon_mode){I({url:"/pages/mine/webview?src="+"https://im.raingad.com"})}else I({url:"/pages/mine/about"})},showSetting(){I({url:"/pages/mine/setting"})},showsecure(){I({url:"/pages/mine/secure"})},editInfo(){I({url:"/pages/mine/profile"})},setAvatar(){I({url:"/pages/mine/avatar"})},scan(){A.scanQr()}}},[["render",function(t,a,s,e,l,n){const r=b,d=C,g=w;return c(),i(r,null,{default:o((()=>[f(r,{class:"padding flex justify-start bg-white mb-10"},{default:o((()=>[f(r,{class:m(["cu-avatar lg mr-15",l.appSetting.circleAvatar?"round":"radius"]),onClick:n.setAvatar,style:u([{backgroundImage:"url("+l.loginStore.userInfo.avatar+")"}])},null,8,["class","onClick","style"]),f(r,{class:"im-flex im-justify-content-start im-columns",onClick:a[0]||(a[0]=t=>n.editInfo())},{default:o((()=>[f(r,{class:"mb-5 f-18 mb-10 im-flex im-align-items-center"},{default:o((()=>[f(r,{class:"c-333"},{default:o((()=>[h(_(l.loginStore.userInfo.realname),1)])),_:1}),f(r,{class:m(["cu-tag ml-10 round light",l.loginStore.userInfo.is_auth?"bg-orange":"bg-grey"])},{default:o((()=>[h(_(l.loginStore.userInfo.is_auth?"已认证":"未认证"),1)])),_:1},8,["class"])])),_:1}),f(r,{class:"text-gray mb-10"},{default:o((()=>[h(_(l.loginStore.userInfo.account),1)])),_:1})])),_:1})])),_:1}),f(r,{class:"cu-list menu"},{default:o((()=>[f(r,{class:"cu-item",onClick:n.scan},{default:o((()=>[f(r,{class:"content"},{default:o((()=>[f(d,{class:"cuIcon-scan text-blue"}),f(d,null,{default:o((()=>[h("扫一扫")])),_:1})])),_:1}),f(r,{class:"action"},{default:o((()=>[f(d,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),f(r,{class:"cu-item",onClick:n.showSetting},{default:o((()=>[f(r,{class:"content"},{default:o((()=>[f(d,{class:"cuIcon-settings text-grey"}),f(d,null,{default:o((()=>[h("通用设置")])),_:1})])),_:1}),f(r,{class:"action"},{default:o((()=>[f(d,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),f(r,{class:"cu-item",onClick:n.showsecure},{default:o((()=>[f(r,{class:"content"},{default:o((()=>[f(d,{class:"cuIcon-lock text-orange"}),f(d,null,{default:o((()=>[h("账号安全")])),_:1})])),_:1}),f(r,{class:"action"},{default:o((()=>[f(d,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),f(r,{class:"cu-item",onClick:a[1]||(a[1]=t=>n.about())},{default:o((()=>[f(r,{class:"content"},{default:o((()=>[f(d,{class:"cuIcon-info text-green"}),f(d,null,{default:o((()=>[h("关于IM")])),_:1})])),_:1}),f(r,{class:"action"},{default:o((()=>[f(d,{class:"text-grey cuIcon-right"})])),_:1})])),_:1})])),_:1}),f(r,{class:"padding flex flex-direction"},{default:o((()=>[f(g,{class:"cu-btn bg-red lg",onClick:a[2]||(a[2]=t=>n.logout())},{default:o((()=>[h("退出登录")])),_:1})])),_:1})])),_:1})}]]),H=x(a),U=t(a),{unread:V,sysUnread:Q}=y(H);const R=s({components:{message:E,contacts:M,compass:q,mine:G},data(){let t=[{name:"message",title:"消息",notice:V},{name:"contacts",title:"通讯录",notice:Q}];t.push({name:"compass",title:"探索",notice:0});return t.push({name:"mine",title:"我的",notice:0}),{globalConfig:U.globalConfig,PageCur:"message",PageName:"消息",TabCur:0,modelName:!1,navList:t}},onShow(){},mounted(){S(),this.initContacts()},methods:{closeModel(){this.modelName=!1},scan(){A.scanQr()},NavChange:function(t){this.PageCur=t.name,this.PageName=t.title},showContacts(){1==this.TabCur?this.TabCur=0:this.TabCur=1},initContacts(){this.$api.msgApi.initContacts().then((t=>{let a=[],s=0;this.allMsg=t.data,t.data.forEach((t=>{t.lastContent&&(s+=t.unread),a.push(t)})),H.sysUnread=t.count,H.unread=s,H.initContacts(a)}))},addGroup(){I({url:"/pages/message/group/addGroup?type=1"})},addFriend(){I({url:"/pages/contacts/search"})},search(){const t="message"==this.PageCur?1:2;I({url:"/pages/index/search?type="+t})}}},[["render",function(t,a,s,e,n,u){const x=C,y=b,k=l("cu-custom"),I=l("message"),v=l("contacts"),w=l("compass"),S=l("mine"),j=T;return c(),i(y,null,{default:o((()=>[f(k,{bgColor:"bg-white"},{backText:o((()=>["message"==n.PageCur||"contacts"==n.PageCur?(c(),i(y,{key:0,class:"f-20 ml-10 mr-10",onClick:a[0]||(a[0]=t=>u.search())},{default:o((()=>[f(x,{class:"cuIcon-search",style:{"margin-left":"-10px"}})])),_:1})):p("",!0)])),content:o((()=>[h(_(n.PageName),1)])),right:o((()=>["contacts"==n.PageCur&&n.globalConfig&&n.globalConfig.demon_mode?(c(),i(y,{key:0,class:"f-20 ml-10 mr-10",onClick:a[1]||(a[1]=t=>u.showContacts())},{default:o((()=>[f(x,{class:m(["f-24",n.TabCur?"cuIcon-peoplelist":"cuIcon-friend"])},null,8,["class"])])),_:1})):p("",!0),"message"==n.PageCur?(c(),i(y,{key:1,class:"f-20 ml-10 mr-10",onClick:a[2]||(a[2]=t=>n.modelName="add")},{default:o((()=>[f(x,{class:"cuIcon-add f-28"})])),_:1})):p("",!0)])),_:1}),f(y,null,{default:o((()=>["message"==n.PageCur?(c(),i(I,{key:0})):p("",!0),"contacts"==n.PageCur?(c(),i(v,{key:1,TabCur:n.TabCur},null,8,["TabCur"])):p("",!0),"compass"==n.PageCur?(c(),i(w,{key:2})):p("",!0),"mine"==n.PageCur?(c(),i(S,{key:3})):p("",!0)])),_:1}),f(y,{class:"cu-bar tabbar bg-white shadow foot"},{default:o((()=>[(c(!0),r(d,null,g(n.navList,((t,a)=>(c(),i(y,{class:"action",onClick:a=>u.NavChange(t),key:a,"data-cur":"message"},{default:o((()=>[f(y,{class:"cuIcon-cu-image"},{default:o((()=>[f(j,{src:"/static/image/tabbar/"+[t.name]+[n.PageCur==t.name?"-active":""]+".svg"},null,8,["src"]),t.notice>0?(c(),i(y,{key:0,class:"cu-tag badge"},{default:o((()=>[h(_(t.notice),1)])),_:2},1024)):p("",!0)])),_:2},1024),f(y,{class:m(n.PageCur==t.name?"text-green":"text-black")},{default:o((()=>[h(_(t.title),1)])),_:2},1032,["class"])])),_:2},1032,["onClick"])))),128))])),_:1}),f(y,{class:m(["cu-modal bottom-modal","add"==n.modelName?"show":""]),onClick:a[7]||(a[7]=t=>n.modelName="")},{default:o((()=>[f(y,{class:"cu-dialog"},{default:o((()=>[f(y,{class:"manage-content"},{default:o((()=>[f(y,{class:"cu-list menu bg-white"},{default:o((()=>[2==n.globalConfig.sysInfo.runMode?(c(),i(y,{key:0,class:"cu-item",onClick:a[3]||(a[3]=t=>u.addFriend())},{default:o((()=>[f(y,{class:"content padding-tb-sm"},{default:o((()=>[f(x,{class:"cuIcon-friendadd"}),f(x,null,{default:o((()=>[h("添加朋友")])),_:1})])),_:1})])),_:1})):p("",!0),f(y,{class:"cu-item",onClick:a[4]||(a[4]=t=>u.addGroup())},{default:o((()=>[f(y,{class:"content padding-tb-sm"},{default:o((()=>[f(x,{class:"cuIcon-friend"}),f(x,null,{default:o((()=>[h("创建群聊")])),_:1})])),_:1})])),_:1}),f(y,{class:"cu-item",onClick:a[5]||(a[5]=t=>u.scan())},{default:o((()=>[f(y,{class:"content padding-tb-sm"},{default:o((()=>[f(x,{class:"cuIcon-scan mr-10"}),f(x,null,{default:o((()=>[h("扫 一 扫")])),_:1})])),_:1})])),_:1}),f(y,{class:"parting-line-5"}),f(y,{class:"cu-item",onClick:a[6]||(a[6]=t=>n.modelName="")},{default:o((()=>[f(y,{class:"content padding-tb-sm"},{default:o((()=>[f(x,{class:"c-red"},{default:o((()=>[h("取消")])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["class"])])),_:1})}]]);export{R as default};
