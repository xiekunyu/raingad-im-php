import{_ as t,u as a,r as e,a as s,o as l,c as n,w as c,n as i,b as o,F as u,d,e as r,f as m,g,h as f,t as h,i as p,j as _,k as b,s as x,l as y,m as C,p as k,q as T,v as I,x as v,y as w,z as j}from"./index-88b4c777.js";import{e as P,_ as S}from"./emoji.6a421766.js";import{r as N}from"./uni-app.es.4b5979f9.js";import M from"./pages-contacts-index.291ad948.js";const L=t({name:"message-list",props:{msgs:{type:Array,default:function(){return[]}},btnWidth:{type:Number,default:320}},data:()=>({msgsIn:[],damping:.29,moveIndex:-1,x:0,oX:0,scY:!0,btnWidthpx:160,touchStart:!1,modalName:null,listTouchStart:0,listTouchDirection:null,emojiMap:[],chatStatus:!0,paddingB:0}),created:function(){this.init(this.msgs),this.btnWidthpx=-1*a(this.btnWidth)+2;let t=[];P.forEach((function(a){let e=a.children;e.length>0&&e.forEach((function(a){let e=a.name,s=a.src;t[e]=s}))})),this.emojiMap=t,this.paddingB=this.inlineTools},watch:{msgs:function(t){this.init(t)}},methods:{init:function(t){this.moveIndex=-1,this.msgsIn=t.filter((t=>t.lastContent))},scrolltolower:function(){},emojiToHtml(t){if(!t)return;let a=this.emojiMap;return t.replace(/\[!(\w+)\]/gi,(function(t,e){var s=e;return a[s]?'<img class=\'mr-5\' style="width:18px;height:18px" emoji-name="'.concat(e,'" src="').concat(a[s],'" />'):"[!".concat(e,"]")}))},ListTouchStart(t){this.listTouchStart=t.touches[0].pageX},ListTouchMove(t){let a=t.touches[0].pageX-this.listTouchStart;Math.abs(a)>100&&a<0?this.listTouchDirection="left":this.listTouchDirection="right"},ListTouchEnd(t){"left"==this.listTouchDirection?(this.modalName=t.currentTarget.dataset.target,this.chatStatus=!1):this.modalName=null,this.listTouchDirection=null},openChat(t,a){this.chatStatus?this.$emit("itemTap",t,a):this.chatStatus=!0},from_time(t){return this.$util.fromTime(t)},btnTap(t,a){this.$emit("btnTap",t,a)}}},[["render",function(t,a,b,x,y,C){const k=p,T=e("Tags"),I=_,v=N(s("mp-html"),S),w=e("Empty");return l(),n(k,{class:"im-message-list"},{default:c((()=>[y.msgsIn.length>0?(l(),n(k,{key:0,class:"cu-list menu-avatar",style:i({paddingBottom:y.paddingB+"px"})},{default:c((()=>[(l(!0),o(u,null,d(y.msgsIn,((t,a)=>(l(),n(k,{class:r(["cu-item",[y.modalName=="move-box-"+a?"move-cur":"",1==t.is_top?"top-contacts":""]]),key:a,onTouchstart:C.ListTouchStart,onTouchmove:C.ListTouchMove,onTouchend:C.ListTouchEnd,onClick:e=>C.openChat(a,t),"data-target":"move-box-"+a},{default:c((()=>[m(k,{class:"cu-avatar round lg",style:i([{backgroundImage:"url("+t.avatar+")"}])},null,8,["style"]),m(k,{class:"content"},{default:c((()=>[m(k,{class:"text-grey"},{default:c((()=>[1==t.is_group?(l(),n(T,{key:0,text:"群聊",size:"mini"})):g("",!0),m(k,{class:"text-overflow",style:{width:"80%"}},{default:c((()=>[f(h(t.displayName),1)])),_:2},1024)])),_:2},1024),m(k,{class:"im-flex im-justify-content-start im-align-items-start",style:{height:"50rpx"}},{default:c((()=>[m(k,{class:"text-gray text-sm"},{default:c((()=>[t.unread>0&&0==t.is_notice?(l(),n(I,{key:0},{default:c((()=>[f("["+h(t.unread)+"条未读] ",1)])),_:2},1024)):g("",!0)])),_:2},1024),m(v,{content:C.emojiToHtml(t.lastContent),class:"im-flex text-gray text-sm text-overflow no-click"},null,8,["content"])])),_:2},1024)])),_:2},1024),m(k,{class:"action"},{default:c((()=>[m(k,{class:"text-grey text-xs"},{default:c((()=>[f(h(C.from_time(t.lastSendTime)),1)])),_:2},1024),t.unread>0&&t.is_notice?(l(),n(k,{key:0,class:"cu-tag round bg-red sm"},{default:c((()=>[f(h(t.unread),1)])),_:2},1024)):g("",!0),0==t.is_notice?(l(),n(k,{key:1,class:"c-999"},{default:c((()=>[m(I,{class:"cuIcon-musicforbidfill"})])),_:1})):g("",!0)])),_:2},1024),m(k,{class:"move"},{default:c((()=>[1==t.is_top?(l(),n(k,{key:0,class:"bg-grey",onClick:a=>C.btnTap(0,t)},{default:c((()=>[f("取消置顶")])),_:2},1032,["onClick"])):(l(),n(k,{key:1,class:"bg-blue",onClick:a=>C.btnTap(0,t)},{default:c((()=>[f("置顶聊天")])),_:2},1032,["onClick"])),0==t.is_group?(l(),n(k,{key:2,class:"bg-red",onClick:a=>C.btnTap(1,t)},{default:c((()=>[f("删除")])),_:2},1032,["onClick"])):g("",!0)])),_:2},1024)])),_:2},1032,["class","onTouchstart","onTouchmove","onTouchend","onClick","data-target"])))),128))])),_:1},8,["style"])):(l(),n(w,{key:1,noDatatext:"暂无聊天",textcolor:"#999"}))])),_:1})}],["__scopeId","data-v-b465aba9"]]),B=b(x),{contacts:W}=y(B);const A=t({components:{messageList:L},data:()=>({navCurrent:0,msgs:W,mainHeight:500,pageLoading:!0}),methods:{btnTap:function(t,a){0==t?(a.is_top=0==a.is_top?1:0,this.$api.msgApi.setChatTopAPI({id:a.id,is_top:a.is_top,is_group:a.is_group}).then((t=>{0==t.code&&B.updateContacts(a)}))):1==t&&C({title:"确定要删除吗?",success:t=>{t.confirm&&this.$api.msgApi.delChatAPI({id:a.id,is_group:a.is_group}).then((t=>{0==t.code&&B.deleteContacts(a)}))}})},itemTap:function(t,a){B.unread-=a.unread;let e=this.msgs;e[t].unread=0,B.initContacts(e),k({url:"/pages/message/chat?id="+a.id})}}},[["render",function(t,a,s,i,o,u){const d=e("messageList"),r=p;return l(),n(r,null,{default:c((()=>[m(d,{msgs:o.msgs,onItemTap:u.itemTap,onBtnTap:u.btnTap},null,8,["msgs","onItemTap","onBtnTap"])])),_:1})}],["__scopeId","data-v-f7a43db7"]]),$=""+new URL("user-card-bg-ba5b09d7.jpg",import.meta.url).href,D=T();const E=t({data:()=>({isCard:!0,userInfo:D.userInfo,paddingB:0}),created:function(){this.paddingB=this.inlineTools},methods:{IsCard(t){this.isCard=t.detail.value}}},[["render",function(t,a,e,s,g,b){const x=I,y=p,C=_;return l(),n(y,{style:i({paddingBottom:g.paddingB+"px"})},{default:c((()=>[m(y,{class:"im-friend-header"},{default:c((()=>[m(y,{class:"im-friend-bg"},{default:c((()=>[m(x,{class:"im-friend-image",src:$,mode:"widthFix"})])),_:1}),m(y,{class:"im-user im-flex im-justify-content-start align-center"},{default:c((()=>[m(C,{class:"text-white mr-5"},{default:c((()=>[f(h(g.userInfo.realname),1)])),_:1}),m(x,{class:"radius-10",style:{width:"120rpx",height:"120rpx"},src:g.userInfo.avatar,mode:"widthFix"},null,8,["src"])])),_:1})])),_:1}),m(y,{class:"cu-card dynamic no-card"},{default:c((()=>[m(y,{class:"cu-item shadow"},{default:c((()=>[m(y,{class:"cu-list menu-avatar"},{default:c((()=>[m(y,{class:"cu-item"},{default:c((()=>[m(y,{class:"cu-avatar round lg",style:{"background-image":"url(https://api.multiavatar.com/raingad3.png?apikey=zdvXV3W4MjwhP9)"}}),m(y,{class:"content flex-sub"},{default:c((()=>[m(y,null,{default:c((()=>[f("凯尔")])),_:1}),m(y,{class:"text-gray text-sm flex justify-between"},{default:c((()=>[f(" 2019年12月3日 ")])),_:1})])),_:1})])),_:1})])),_:1}),m(y,{class:"text-content"},{default:c((()=>[f(" 折磨生出苦难，苦难又会加剧折磨，凡间这无穷的循环，将有我来终结！ ")])),_:1}),m(y,{class:r(["grid flex-sub padding-lr",g.isCard?"col-3 grid-square":"col-1"])},{default:c((()=>[(l(!0),o(u,null,d(g.isCard?9:1,((t,a)=>(l(),n(y,{class:r(["bg-img",g.isCard?"":"only-img"]),style:{"background-image":"url(https://ossweb-img.qq.com/images/lol/web201310/skin/big10006.jpg)"},key:a},null,8,["class"])))),128))])),_:1},8,["class"]),m(y,{class:"text-gray text-sm text-right padding"},{default:c((()=>[m(C,{class:"cuIcon-attentionfill margin-lr-xs"}),f(" 10 "),m(C,{class:"cuIcon-appreciatefill margin-lr-xs"}),f(" 20 "),m(C,{class:"cuIcon-messagefill margin-lr-xs"}),f(" 30 ")])),_:1}),m(y,{class:"cu-list menu-avatar comment solids-top"},{default:c((()=>[m(y,{class:"cu-item"},{default:c((()=>[m(y,{class:"cu-avatar round",style:{"background-image":"url(https://api.multiavatar.com/raingad5.png?apikey=zdvXV3W4MjwhP9)"}}),m(y,{class:"content"},{default:c((()=>[m(y,{class:"text-grey"},{default:c((()=>[f("莫甘娜")])),_:1}),m(y,{class:"text-gray text-content text-df"},{default:c((()=>[f(" 凯尔，你被自己的光芒变的盲目。 ")])),_:1}),m(y,{class:"bg-grey padding-sm radius margin-top-sm text-sm"},{default:c((()=>[m(y,{class:"flex"},{default:c((()=>[m(y,null,{default:c((()=>[f("凯尔：")])),_:1}),m(y,{class:"flex-sub"},{default:c((()=>[f("妹妹，你在帮他们给黑暗找借口吗?")])),_:1})])),_:1})])),_:1}),m(y,{class:"margin-top-sm flex justify-between"},{default:c((()=>[m(y,{class:"text-gray text-df"},{default:c((()=>[f("2018年12月4日")])),_:1}),m(y,null,{default:c((()=>[m(C,{class:"cuIcon-appreciatefill text-red"}),m(C,{class:"cuIcon-messagefill text-gray margin-left-sm"})])),_:1})])),_:1})])),_:1})])),_:1}),m(y,{class:"cu-item"},{default:c((()=>[m(y,{class:"cu-avatar round",style:{"background-image":"url(https://api.multiavatar.com/raingad2.png?apikey=zdvXV3W4MjwhP9)"}}),m(y,{class:"content"},{default:c((()=>[m(y,{class:"text-grey"},{default:c((()=>[f("凯尔")])),_:1}),m(y,{class:"text-gray text-content text-df"},{default:c((()=>[f(" 妹妹，如果不是为了飞翔，我们要这翅膀有什么用? ")])),_:1}),m(y,{class:"bg-grey padding-sm radius margin-top-sm text-sm"},{default:c((()=>[m(y,{class:"flex"},{default:c((()=>[m(y,null,{default:c((()=>[f("莫甘娜：")])),_:1}),m(y,{class:"flex-sub"},{default:c((()=>[f("如果不能立足于大地，要这双脚又有何用?")])),_:1})])),_:1})])),_:1}),m(y,{class:"margin-top-sm flex justify-between"},{default:c((()=>[m(y,{class:"text-gray text-df"},{default:c((()=>[f("2018年12月4日")])),_:1}),m(y,null,{default:c((()=>[m(C,{class:"cuIcon-appreciate text-gray"}),m(C,{class:"cuIcon-messagefill text-gray margin-left-sm"})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["style"])}],["__scopeId","data-v-2da58bb3"]]),X=T();const z=t({data:()=>({loginStore:X}),onShow(){},methods:{logout(){this.$api.LoginApi.logout({}).then((t=>{0==t.code&&X.logout()}))},openWeb(t){k({url:"/pages/mine/webview?src=https://im.raingad.com"})},showSetting(){v({title:"请在web端进行资料设置",icon:"none"})}}},[["render",function(t,a,e,s,o,u){const d=p,r=_,g=w;return l(),n(d,null,{default:c((()=>[m(d,{class:"padding flex justify-start bg-white mb-10"},{default:c((()=>[m(d,{class:"cu-avatar lg radius mr-15",style:i([{backgroundImage:"url("+o.loginStore.userInfo.avatar+")"}])},null,8,["style"]),m(d,{class:"im-flex im-justify-content-start im-columns"},{default:c((()=>[m(d,{class:"mb-5 f-18 mb-10"},{default:c((()=>[f(h(o.loginStore.userInfo.realname),1)])),_:1}),m(d,{class:"text-gray mb-10"},{default:c((()=>[f(h(o.loginStore.userInfo.account),1)])),_:1}),m(d,{class:"text-gray"},{default:c((()=>[f(h(o.loginStore.userInfo.email),1)])),_:1})])),_:1})])),_:1}),m(d,{class:"cu-list menu"},{default:c((()=>[m(d,{class:"cu-item",onClick:u.showSetting},{default:c((()=>[m(d,{class:"content"},{default:c((()=>[m(r,{class:"cuIcon-settings text-green"}),m(r,null,{default:c((()=>[f("通用设置")])),_:1})])),_:1}),m(d,{class:"action"},{default:c((()=>[m(r,{class:"text-grey cuIcon-right"})])),_:1})])),_:1},8,["onClick"]),m(d,{class:"cu-item",onClick:a[0]||(a[0]=t=>u.openWeb(1))},{default:c((()=>[m(d,{class:"content"},{default:c((()=>[m(r,{class:"cuIcon-album text-green"}),m(r,null,{default:c((()=>[f("关于IM")])),_:1})])),_:1}),m(d,{class:"action"},{default:c((()=>[m(r,{class:"text-grey cuIcon-right"})])),_:1})])),_:1})])),_:1}),m(d,{class:"padding flex flex-direction"},{default:c((()=>[m(g,{class:"cu-btn bg-red lg",onClick:a[1]||(a[1]=t=>u.logout())},{default:c((()=>[f("退出登录")])),_:1})])),_:1})])),_:1})}]]),F=b(x),q=T(x),{unread:G,sysUnread:H}=y(F);const U=t({components:{message:A,contacts:M,compass:E,mine:z},data:()=>({globalConfig:q.globalConfig,PageCur:"message",PageName:"消息",TabCur:0,modelName:!1,navList:[{name:"message",title:"消息",notice:G},{name:"contacts",title:"通讯录",notice:H},{name:"compass",title:"探索",notice:0},{name:"mine",title:"我的",notice:0}]}),onShow(){},mounted(){j(),this.initContacts()},methods:{openModel(t){this.modelName=t},closeModel(){this.modelName=!1},NavChange:function(t){this.PageCur=t.name,this.PageName=t.title},showContacts(){1==this.TabCur?this.TabCur=0:this.TabCur=1},initContacts(){this.$api.msgApi.initContacts().then((t=>{let a=[],e=0;this.allMsg=t.data,t.data.forEach((t=>{t.lastContent&&(e+=t.unread),a.push(t)})),F.sysUnread=t.count,F.unread=e,F.initContacts(a)}))},addGroup(){k({url:"/pages/message/group/addGroup?type=1"})},addFriend(){k({url:"/pages/contacts/search"})},addNew(){1==this.globalConfig.sysInfo.runMode?this.addGroup():this.openModel("add")},search(){const t="message"==this.PageCur?1:2;k({url:"/pages/index/search?type="+t})}}},[["render",function(t,a,s,i,b,x){const y=_,C=p,k=e("cu-custom"),T=e("message"),v=e("contacts"),w=e("compass"),j=e("mine"),P=I;return l(),n(C,null,{default:c((()=>[m(k,{bgColor:"bg-white"},{backText:c((()=>["message"==b.PageCur||"contacts"==b.PageCur?(l(),n(C,{key:0,class:"f-20 ml-10 mr-10",onClick:a[0]||(a[0]=t=>x.search())},{default:c((()=>[m(y,{class:"cuIcon-search",style:{"margin-left":"-10px"}})])),_:1})):g("",!0)])),content:c((()=>[f(h(b.PageName),1)])),right:c((()=>["contacts"==b.PageCur?(l(),n(C,{key:0,class:"f-20 ml-10 mr-10",onClick:a[1]||(a[1]=t=>x.showContacts())},{default:c((()=>[m(y,{class:r(["f-24",b.TabCur?"cuIcon-peoplelist":"cuIcon-friend"])},null,8,["class"])])),_:1})):g("",!0),"message"==b.PageCur?(l(),n(C,{key:1,class:"f-20 ml-10 mr-10",onClick:a[2]||(a[2]=t=>x.addNew())},{default:c((()=>[m(y,{class:"cuIcon-add f-28"})])),_:1})):g("",!0)])),_:1}),m(C,null,{default:c((()=>["message"==b.PageCur?(l(),n(T,{key:0})):g("",!0),"contacts"==b.PageCur?(l(),n(v,{key:1,TabCur:b.TabCur},null,8,["TabCur"])):g("",!0),"compass"==b.PageCur?(l(),n(w,{key:2})):g("",!0),"mine"==b.PageCur?(l(),n(j,{key:3})):g("",!0)])),_:1}),m(C,{class:"cu-bar tabbar bg-white shadow foot"},{default:c((()=>[(l(!0),o(u,null,d(b.navList,((t,a)=>(l(),n(C,{class:"action",onClick:a=>x.NavChange(t),key:a,"data-cur":"message"},{default:c((()=>[m(C,{class:"cuIcon-cu-image"},{default:c((()=>[m(P,{src:"/static/image/tabbar/"+[t.name]+[b.PageCur==t.name?"-active":""]+".svg"},null,8,["src"]),t.notice>0?(l(),n(C,{key:0,class:"cu-tag badge"},{default:c((()=>[f(h(t.notice),1)])),_:2},1024)):g("",!0)])),_:2},1024),m(C,{class:r(b.PageCur==t.name?"text-green":"text-black")},{default:c((()=>[f(h(t.title),1)])),_:2},1032,["class"])])),_:2},1032,["onClick"])))),128))])),_:1}),m(C,{class:r(["cu-modal bottom-modal","add"==b.modelName?"show":""])},{default:c((()=>[m(C,{class:"cu-dialog"},{default:c((()=>[m(C,{class:"manage-content"},{default:c((()=>[m(C,{class:"cu-list menu bg-white"},{default:c((()=>[m(C,{class:"cu-item",onClick:a[3]||(a[3]=t=>x.addFriend())},{default:c((()=>[m(C,{class:"content padding-tb-sm"},{default:c((()=>[m(y,{class:"cuIcon-friendadd"}),m(y,null,{default:c((()=>[f("添加朋友")])),_:1})])),_:1})])),_:1}),m(C,{class:"cu-item",onClick:a[4]||(a[4]=t=>x.addGroup())},{default:c((()=>[m(C,{class:"content padding-tb-sm"},{default:c((()=>[m(y,{class:"cuIcon-friend"}),m(y,null,{default:c((()=>[f("创建群聊")])),_:1})])),_:1})])),_:1}),m(C,{class:"parting-line-5"}),m(C,{class:"cu-item",onClick:a[5]||(a[5]=t=>b.modelName="")},{default:c((()=>[m(C,{class:"content padding-tb-sm"},{default:c((()=>[m(y,{class:"c-red"},{default:c((()=>[f("取消")])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["class"])])),_:1})}]]);export{U as default};