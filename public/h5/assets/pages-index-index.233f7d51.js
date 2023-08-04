import{u as t,s as a,_ as e,a as s,r as l,b as n,o as i,c,w as o,n as u,d,F as r,e as m,f as g,g as f,h as p,i as h,t as _,j as b,k as x,l as y,m as C,p as k,q as T,v as I,x as v,y as w,z as j,A as P}from"./index-2acfbae4.js";import{s as N,e as S,_ as M}from"./status.3d264336.js";import{r as L}from"./uni-app.es.956ef649.js";import B from"./pages-contacts-index.e8c606b1.js";const A=t(a);const W=e({name:"message-list",props:{msgs:{type:Array,default:function(){return[]}},btnWidth:{type:Number,default:320}},components:{statusPoint:N},data:()=>({msgsIn:[],damping:.29,moveIndex:-1,x:0,oX:0,scY:!0,btnWidthpx:160,touchStart:!1,modalName:null,listTouchStart:0,listTouchDirection:null,emojiMap:[],chatStatus:!0,paddingB:0,globalConfig:A.globalConfig}),created:function(){this.init(this.msgs),this.btnWidthpx=-1*s(this.btnWidth)+2;let t=[];S.forEach((function(a){let e=a.children;e.length>0&&e.forEach((function(a){let e=a.name,s=a.src;t[e]=s}))})),this.emojiMap=t,this.paddingB=this.inlineTools},watch:{msgs:function(t){this.init(t)}},methods:{init:function(t){this.moveIndex=-1,this.msgsIn=t.filter((t=>t.lastContent))},scrolltolower:function(){},emojiToHtml(t){if(!t)return;let a=this.emojiMap;return t.replace(/\[!(\w+)\]/gi,(function(t,e){var s=e;return a[s]?'<img class=\'mr-5\' style="width:18px;height:18px" emoji-name="'.concat(e,'" src="').concat(a[s],'" />'):"[!".concat(e,"]")}))},ListTouchStart(t){this.listTouchStart=t.touches[0].pageX},ListTouchMove(t){let a=t.touches[0].pageX-this.listTouchStart;Math.abs(a)>100&&a<0?this.listTouchDirection="left":this.listTouchDirection="right"},ListTouchEnd(t){"left"==this.listTouchDirection?(this.modalName=t.currentTarget.dataset.target,this.chatStatus=!1):this.modalName=null,this.listTouchDirection=null},openChat(t,a){this.chatStatus?this.$emit("itemTap",t,a):this.chatStatus=!0},from_time(t){return this.$util.timeFormat(t)},btnTap(t,a){this.$emit("btnTap",t,a)}}},[["render",function(t,a,e,s,y,C){const k=b,T=l("Tags"),I=l("statusPoint"),v=x,w=L(n("mp-html"),M),j=l("Empty");return i(),c(k,{class:"im-message-list"},{default:o((()=>[y.msgsIn.length>0?(i(),c(k,{key:0,class:"cu-list menu-avatar",style:u({paddingBottom:y.paddingB+"px"})},{default:o((()=>[(i(!0),d(r,null,m(y.msgsIn,((t,a)=>(i(),c(k,{class:g(["cu-item",[y.modalName=="move-box-"+a?"move-cur":"",1==t.is_top?"top-contacts":"",0==t.is_group?"third":"second"]]),key:a,onTouchstart:C.ListTouchStart,onTouchmove:C.ListTouchMove,onTouchend:C.ListTouchEnd,onClick:e=>C.openChat(a,t),"data-target":"move-box-"+a},{default:o((()=>[f(k,{class:"cu-avatar round lg",style:u([{backgroundImage:"url("+t.avatar+")"}])},null,8,["style"]),f(k,{class:"content"},{default:o((()=>[f(k,{class:"text-grey"},{default:o((()=>[1==t.is_group?(i(),c(T,{key:0,text:"群聊",size:"mini"})):p("",!0),f(k,{class:"text-overflow",style:{width:"80%"}},{default:o((()=>[t.is_online&&0==t.is_group&&1==y.globalConfig.chatInfo.online?(i(),c(I,{key:0,type:"success"})):p("",!0),h(" "+_(t.displayName),1)])),_:2},1024)])),_:2},1024),f(k,{class:"im-flex im-justify-content-start im-align-items-start",style:{height:"50rpx"}},{default:o((()=>[f(k,{class:"text-gray text-sm"},{default:o((()=>[t.unread>0&&0==t.is_notice?(i(),c(v,{key:0},{default:o((()=>[h("["+_(t.unread)+"条未读] ",1)])),_:2},1024)):p("",!0)])),_:2},1024),f(w,{content:C.emojiToHtml(t.lastContent),class:"im-flex text-gray text-sm text-overflow no-click"},null,8,["content"])])),_:2},1024)])),_:2},1024),f(k,{class:"action"},{default:o((()=>[f(k,{class:"text-grey text-xs"},{default:o((()=>[h(_(C.from_time(t.lastSendTime)),1)])),_:2},1024),t.unread>0&&t.is_notice?(i(),c(k,{key:0,class:"cu-tag round bg-red sm"},{default:o((()=>[h(_(t.unread),1)])),_:2},1024)):p("",!0),0==t.is_notice?(i(),c(k,{key:1,class:"c-999"},{default:o((()=>[f(v,{class:"cuIcon-musicforbidfill"})])),_:1})):p("",!0)])),_:2},1024),f(k,{class:g(["move",0==t.is_group?"third":"second"])},{default:o((()=>[1==t.is_top?(i(),c(k,{key:0,class:"bg-grey",onClick:a=>C.btnTap(0,t)},{default:o((()=>[h("取消置顶")])),_:2},1032,["onClick"])):(i(),c(k,{key:1,class:"bg-blue",onClick:a=>C.btnTap(0,t)},{default:o((()=>[h("置顶聊天")])),_:2},1032,["onClick"])),1==t.is_notice?(i(),c(k,{key:2,class:"bg-orange",onClick:a=>C.btnTap(2,t)},{default:o((()=>[h("免扰")])),_:2},1032,["onClick"])):(i(),c(k,{key:3,class:"bg-orange",onClick:a=>C.btnTap(2,t)},{default:o((()=>[h("取消免扰")])),_:2},1032,["onClick"])),0==t.is_group?(i(),c(k,{key:4,class:"bg-red",onClick:a=>C.btnTap(1,t)},{default:o((()=>[h("删除会话")])),_:2},1032,["onClick"])):p("",!0)])),_:2},1032,["class"])])),_:2},1032,["class","onTouchstart","onTouchmove","onTouchend","onClick","data-target"])))),128))])),_:1},8,["style"])):(i(),c(j,{key:1,noDatatext:"暂无聊天",textcolor:"#999"}))])),_:1})}],["__scopeId","data-v-101cefea"]]),$=y(a),{contacts:D}=C($);const E=e({components:{messageList:W},data:()=>({navCurrent:0,msgs:D,mainHeight:500,pageLoading:!0}),methods:{btnTap:function(t,a){0==t?(a.is_top=0==a.is_top?1:0,this.$api.msgApi.setChatTopAPI({id:a.id,is_top:a.is_top,is_group:a.is_group}).then((t=>{0==t.code&&$.updateContacts(a)}))):1==t?k({title:"确定要删除吗?",success:t=>{t.confirm&&this.$api.msgApi.delChatAPI({id:a.id,is_group:a.is_group}).then((t=>{0==t.code&&$.deleteContacts(a)}))}}):2==t&&(a.is_notice=0==a.is_notice?1:0,this.$api.msgApi.setIsNotice({id:a.id,is_notice:a.is_notice,is_group:a.is_group}).then((t=>{0==t.code&&$.updateContacts(a)})))},itemTap:function(t,a){$.unread-=a.unread;let e=this.msgs;e[t].unread=0,$.initContacts(e),T({url:"/pages/message/chat?id="+a.id})}}},[["render",function(t,a,e,s,n,u){const d=l("messageList"),r=b;return i(),c(r,null,{default:o((()=>[f(d,{msgs:n.msgs,onItemTap:u.itemTap,onBtnTap:u.btnTap},null,8,["msgs","onItemTap","onBtnTap"])])),_:1})}],["__scopeId","data-v-1a193281"]]),F=""+new URL("user-card-bg-ba5b09d7.jpg",import.meta.url).href,X=t();const z=e({data:()=>({isCard:!0,userInfo:X.userInfo,paddingB:0}),created:function(){this.paddingB=this.inlineTools},methods:{IsCard(t){this.isCard=t.detail.value}}},[["render",function(t,a,e,s,l,n){const p=I,y=b,C=x;return i(),c(y,{style:u({paddingBottom:l.paddingB+"px"})},{default:o((()=>[f(y,{class:"im-friend-header"},{default:o((()=>[f(y,{class:"im-friend-bg"},{default:o((()=>[f(p,{class:"im-friend-image",src:F,mode:"widthFix"})])),_:1}),f(y,{class:"im-user im-flex im-justify-content-start align-center"},{default:o((()=>[f(C,{class:"text-white mr-5"},{default:o((()=>[h(_(l.userInfo.realname),1)])),_:1}),f(p,{class:"radius-10",style:{width:"120rpx",height:"120rpx"},src:l.userInfo.avatar,mode:"widthFix"},null,8,["src"])])),_:1})])),_:1}),f(y,{class:"cu-card dynamic no-card"},{default:o((()=>[f(y,{class:"cu-item shadow"},{default:o((()=>[f(y,{class:"cu-list menu-avatar"},{default:o((()=>[f(y,{class:"cu-item"},{default:o((()=>[f(y,{class:"cu-avatar round lg",style:{"background-image":"url(https://api.multiavatar.com/raingad3.png?apikey=zdvXV3W4MjwhP9)"}}),f(y,{class:"content flex-sub"},{default:o((()=>[f(y,null,{default:o((()=>[h("凯尔")])),_:1}),f(y,{class:"text-gray text-sm flex justify-between"},{default:o((()=>[h(" 2019年12月3日 ")])),_:1})])),_:1})])),_:1})])),_:1}),f(y,{class:"text-content"},{default:o((()=>[h(" 折磨生出苦难，苦难又会加剧折磨，凡间这无穷的循环，将有我来终结！ ")])),_:1}),f(y,{class:g(["grid flex-sub padding-lr",l.isCard?"col-3 grid-square":"col-1"])},{default:o((()=>[(i(!0),d(r,null,m(l.isCard?9:1,((t,a)=>(i(),c(y,{class:g(["bg-img",l.isCard?"":"only-img"]),style:{"background-image":"url(https://ossweb-img.qq.com/images/lol/web201310/skin/big10006.jpg)"},key:a},null,8,["class"])))),128))])),_:1},8,["class"]),f(y,{class:"text-gray text-sm text-right padding"},{default:o((()=>[f(C,{class:"cuIcon-attentionfill margin-lr-xs"}),h(" 10 "),f(C,{class:"cuIcon-appreciatefill margin-lr-xs"}),h(" 20 "),f(C,{class:"cuIcon-messagefill margin-lr-xs"}),h(" 30 ")])),_:1}),f(y,{class:"cu-list menu-avatar comment solids-top"},{default:o((()=>[f(y,{class:"cu-item"},{default:o((()=>[f(y,{class:"cu-avatar round",style:{"background-image":"url(https://api.multiavatar.com/raingad5.png?apikey=zdvXV3W4MjwhP9)"}}),f(y,{class:"content"},{default:o((()=>[f(y,{class:"text-grey"},{default:o((()=>[h("莫甘娜")])),_:1}),f(y,{class:"text-gray text-content text-df"},{default:o((()=>[h(" 凯尔，你被自己的光芒变的盲目。 ")])),_:1}),f(y,{class:"bg-grey padding-sm radius margin-top-sm text-sm"},{default:o((()=>[f(y,{class:"flex"},{default:o((()=>[f(y,null,{default:o((()=>[h("凯尔：")])),_:1}),f(y,{class:"flex-sub"},{default:o((()=>[h("妹妹，你在帮他们给黑暗找借口吗?")])),_:1})])),_:1})])),_:1}),f(y,{class:"margin-top-sm flex justify-between"},{default:o((()=>[f(y,{class:"text-gray text-df"},{default:o((()=>[h("2018年12月4日")])),_:1}),f(y,null,{default:o((()=>[f(C,{class:"cuIcon-appreciatefill text-red"}),f(C,{class:"cuIcon-messagefill text-gray margin-left-sm"})])),_:1})])),_:1})])),_:1})])),_:1}),f(y,{class:"cu-item"},{default:o((()=>[f(y,{class:"cu-avatar round",style:{"background-image":"url(https://api.multiavatar.com/raingad2.png?apikey=zdvXV3W4MjwhP9)"}}),f(y,{class:"content"},{default:o((()=>[f(y,{class:"text-grey"},{default:o((()=>[h("凯尔")])),_:1}),f(y,{class:"text-gray text-content text-df"},{default:o((()=>[h(" 妹妹，如果不是为了飞翔，我们要这翅膀有什么用? ")])),_:1}),f(y,{class:"bg-grey padding-sm radius margin-top-sm text-sm"},{default:o((()=>[f(y,{class:"flex"},{default:o((()=>[f(y,null,{default:o((()=>[h("莫甘娜：")])),_:1}),f(y,{class:"flex-sub"},{default:o((()=>[h("如果不能立足于大地，要这双脚又有何用?")])),_:1})])),_:1})])),_:1}),f(y,{class:"margin-top-sm flex justify-between"},{default:o((()=>[f(y,{class:"text-gray text-df"},{default:o((()=>[h("2018年12月4日")])),_:1}),f(y,null,{default:o((()=>[f(C,{class:"cuIcon-appreciate text-gray"}),f(C,{class:"cuIcon-messagefill text-gray margin-left-sm"})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["style"])}],["__scopeId","data-v-2da58bb3"]]),q=t();const G=e({data:()=>({loginStore:q}),onShow(){},methods:{logout(){let t=v("client_id");this.$api.LoginApi.logout({client_id:t}).then((t=>{0==t.code&&q.logout()}))},openWeb(t){T({url:"/pages/mine/webview?src=https://im.raingad.com"})},showSetting(){w({title:"请在web端进行资料设置",icon:"none"})}}},[["render",function(t,a,e,s,l,n){const d=b,r=x,m=j;return i(),c(d,null,{default:o((()=>[f(d,{class:"padding flex justify-start bg-white mb-10"},{default:o((()=>[f(d,{class:"cu-avatar lg radius mr-15",style:u([{backgroundImage:"url("+l.loginStore.userInfo.avatar+")"}])},null,8,["style"]),f(d,{class:"im-flex im-justify-content-start im-columns"},{default:o((()=>[f(d,{class:"mb-5 f-18 mb-10"},{default:o((()=>[h(_(l.loginStore.userInfo.realname),1)])),_:1}),f(d,{class:"text-gray mb-10"},{default:o((()=>[h(_(l.loginStore.userInfo.account),1)])),_:1}),f(d,{class:"text-gray"},{default:o((()=>[h(_(l.loginStore.userInfo.email),1)])),_:1})])),_:1})])),_:1}),f(d,{class:"cu-list menu"},{default:o((()=>[f(d,{class:"cu-item",onClick:n.showSetting},{default:o((()=>[f(d,{class:"content"},{default:o((()=>[f(r,{class:"cuIcon-settings text-green"}),f(r,null,{default:o((()=>[h("通用设置")])),_:1})])),_:1}),f(d,{class:"action"},{default:o((()=>[f(r,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),f(d,{class:"cu-item",onClick:a[0]||(a[0]=t=>n.openWeb(1))},{default:o((()=>[f(d,{class:"content"},{default:o((()=>[f(r,{class:"cuIcon-album text-green"}),f(r,null,{default:o((()=>[h("关于IM")])),_:1})])),_:1}),f(d,{class:"action"},{default:o((()=>[f(r,{class:"text-grey cuIcon-right"})])),_:1})])),_:1})])),_:1}),f(d,{class:"padding flex flex-direction"},{default:o((()=>[f(m,{class:"cu-btn bg-red lg",onClick:a[1]||(a[1]=t=>n.logout())},{default:o((()=>[h("退出登录")])),_:1})])),_:1})])),_:1})}]]),H=y(a),U=t(a),{unread:V,sysUnread:R}=C(H);const Y=e({components:{message:E,contacts:B,compass:z,mine:G},data:()=>({globalConfig:U.globalConfig,PageCur:"message",PageName:"消息",TabCur:0,modelName:!1,navList:[{name:"message",title:"消息",notice:V},{name:"contacts",title:"通讯录",notice:R},{name:"compass",title:"探索",notice:0},{name:"mine",title:"我的",notice:0}]}),onShow(){},mounted(){P(),this.initContacts()},methods:{openModel(t){this.modelName=t},closeModel(){this.modelName=!1},NavChange:function(t){this.PageCur=t.name,this.PageName=t.title},showContacts(){1==this.TabCur?this.TabCur=0:this.TabCur=1},initContacts(){this.$api.msgApi.initContacts().then((t=>{let a=[],e=0;this.allMsg=t.data,t.data.forEach((t=>{t.lastContent&&(e+=t.unread),a.push(t)})),H.sysUnread=t.count,H.unread=e,H.initContacts(a)}))},addGroup(){T({url:"/pages/message/group/addGroup?type=1"})},addFriend(){T({url:"/pages/contacts/search"})},addNew(){1==this.globalConfig.sysInfo.runMode?this.addGroup():this.openModel("add")},search(){const t="message"==this.PageCur?1:2;T({url:"/pages/index/search?type="+t})}}},[["render",function(t,a,e,s,n,u){const y=x,C=b,k=l("cu-custom"),T=l("message"),v=l("contacts"),w=l("compass"),j=l("mine"),P=I;return i(),c(C,null,{default:o((()=>[f(k,{bgColor:"bg-white"},{backText:o((()=>["message"==n.PageCur||"contacts"==n.PageCur?(i(),c(C,{key:0,class:"f-20 ml-10 mr-10",onClick:a[0]||(a[0]=t=>u.search())},{default:o((()=>[f(y,{class:"cuIcon-search",style:{"margin-left":"-10px"}})])),_:1})):p("",!0)])),content:o((()=>[h(_(n.PageName),1)])),right:o((()=>["contacts"==n.PageCur?(i(),c(C,{key:0,class:"f-20 ml-10 mr-10",onClick:a[1]||(a[1]=t=>u.showContacts())},{default:o((()=>[f(y,{class:g(["f-24",n.TabCur?"cuIcon-peoplelist":"cuIcon-friend"])},null,8,["class"])])),_:1})):p("",!0),"message"==n.PageCur?(i(),c(C,{key:1,class:"f-20 ml-10 mr-10",onClick:a[2]||(a[2]=t=>u.addNew())},{default:o((()=>[f(y,{class:"cuIcon-add f-28"})])),_:1})):p("",!0)])),_:1}),f(C,null,{default:o((()=>["message"==n.PageCur?(i(),c(T,{key:0})):p("",!0),"contacts"==n.PageCur?(i(),c(v,{key:1,TabCur:n.TabCur},null,8,["TabCur"])):p("",!0),"compass"==n.PageCur?(i(),c(w,{key:2})):p("",!0),"mine"==n.PageCur?(i(),c(j,{key:3})):p("",!0)])),_:1}),f(C,{class:"cu-bar tabbar bg-white shadow foot"},{default:o((()=>[(i(!0),d(r,null,m(n.navList,((t,a)=>(i(),c(C,{class:"action",onClick:a=>u.NavChange(t),key:a,"data-cur":"message"},{default:o((()=>[f(C,{class:"cuIcon-cu-image"},{default:o((()=>[f(P,{src:"/static/image/tabbar/"+[t.name]+[n.PageCur==t.name?"-active":""]+".svg"},null,8,["src"]),t.notice>0?(i(),c(C,{key:0,class:"cu-tag badge"},{default:o((()=>[h(_(t.notice),1)])),_:2},1024)):p("",!0)])),_:2},1024),f(C,{class:g(n.PageCur==t.name?"text-green":"text-black")},{default:o((()=>[h(_(t.title),1)])),_:2},1032,["class"])])),_:2},1032,["onClick"])))),128))])),_:1}),f(C,{class:g(["cu-modal bottom-modal","add"==n.modelName?"show":""]),onClick:a[6]||(a[6]=t=>n.modelName="")},{default:o((()=>[f(C,{class:"cu-dialog"},{default:o((()=>[f(C,{class:"manage-content"},{default:o((()=>[f(C,{class:"cu-list menu bg-white"},{default:o((()=>[f(C,{class:"cu-item",onClick:a[3]||(a[3]=t=>u.addFriend())},{default:o((()=>[f(C,{class:"content padding-tb-sm"},{default:o((()=>[f(y,{class:"cuIcon-friendadd"}),f(y,null,{default:o((()=>[h("添加朋友")])),_:1})])),_:1})])),_:1}),f(C,{class:"cu-item",onClick:a[4]||(a[4]=t=>u.addGroup())},{default:o((()=>[f(C,{class:"content padding-tb-sm"},{default:o((()=>[f(y,{class:"cuIcon-friend"}),f(y,null,{default:o((()=>[h("创建群聊")])),_:1})])),_:1})])),_:1}),f(C,{class:"parting-line-5"}),f(C,{class:"cu-item",onClick:a[5]||(a[5]=t=>n.modelName="")},{default:o((()=>[f(C,{class:"content padding-tb-sm"},{default:o((()=>[f(y,{class:"c-red"},{default:o((()=>[h("取消")])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["class"])])),_:1})}]]);export{Y as default};