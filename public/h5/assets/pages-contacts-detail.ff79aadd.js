import{_ as e,l as t,s as a,u as l,m as s,q as i,aa as n,D as c,p as d,o,c as u,w as r,j as f,g,i as m,n as _,t as b,h as k,d as h,F as x,f as y,r as p,k as C,A as I}from"./index-6d49e736.js";const N=t(a),w=l(a);s(N);const F=e({data:()=>({modelName:"",detail:{},userInfo:w.userInfo,globalConfig:w.globalConfig}),computed:{},onLoad(e){this.$api.msgApi.getUserInfo({user_id:e.id}).then((e=>{0==e.code&&(this.detail=e.data)}))},methods:{sendMsg(e){i({url:"/pages/message/chat?id="+e.user_id})},sex:e=>["女","男","未知"][e]||"未知",callPhone(){n({phoneNumber:this.detail.account})},calling(e){if(N.webrtcLock)return c({title:"其他终端正在通话中",icon:"none"});this.modelName="";let t=this.$util.getUuid();i({url:"/pages/message/call?msg_id="+t+"&type="+e+"&status=1&id="+this.detail.user_id+"&name="+this.detail.realname+"&avatar="+encodeURI(this.detail.avatar)})},delFriend(){d({title:"确定要删除该好友吗？",success:e=>{if(e.confirm){let e={id:this.detail.user_id};this.$api.friendApi.delFriend(e).then((t=>{0==t.code&&N.deleteContacts(e)}))}}})},addFriend(){d({title:"请输入验证信息",editable:!0,success:e=>{if(e.confirm){if(""==e.content)return c({title:"请输入备注！",icon:"error"});this.$api.friendApi.addFriend({user_id:this.detail.user_id,remark:e.content}).then((e=>{0==e.code&&c({title:e.msg,icon:"none"})}))}}})},setNickname(){let e=this.detail.friend.friend_id??"";if(!this.detail.friend)return c({title:"无法设置",icon:"error"});d({title:"请输入备注信息",editable:!0,success:t=>{if(t.confirm){if(""==t.content)return c({title:"请输入好友备注！",icon:"error"});this.$api.friendApi.setNickname({friend_id:e,nickname:t.content}).then((e=>{0==e.code&&(this.detail.friend.nickname=t.content,c({title:e.msg,icon:"none"}))}))}}})}}},[["render",function(e,t,a,l,s,i){const n=p("cu-custom"),c=f,d=C,N=I;return o(),u(c,null,{default:r((()=>[g(n,{bgColor:"bg-white",isBack:!0},{backText:r((()=>[])),content:r((()=>[m("个人信息")])),_:1}),g(c,{class:"padding flex justify-start align-center"},{default:r((()=>[g(c,{class:"cu-avatar lg radius mr-15",style:_("background-image:url("+s.detail.avatar+")")},null,8,["style"]),g(c,{class:"im-flex im-justify-content-start im-columns"},{default:r((()=>[g(c,{class:"mb-5"},{default:r((()=>[m(b(s.detail.realname),1)])),_:1}),g(c,{class:"text-gray"},{default:r((()=>[m(b(s.detail.account),1)])),_:1})])),_:1})])),_:1}),g(c,{class:"cu-list menu"},{default:r((()=>[2==s.globalConfig.sysInfo.runMode&&s.detail.friend&&s.userInfo.user_id!=s.detail.user_id?(o(),u(c,{key:0,class:"cu-item",onClick:i.setNickname},{default:r((()=>[g(c,{class:"content"},{default:r((()=>[g(d,{class:"cuIcon-edit text-green"}),g(d,null,{default:r((()=>[m("备注")])),_:1})])),_:1}),g(c,{class:"action"},{default:r((()=>[g(d,{class:"text-grey text-sm"},{default:r((()=>[m(b(s.detail.friend.nickname||"未设置"),1)])),_:1}),g(d,{class:"text-grey text-sm ml-5 cuIcon-write"})])),_:1})])),_:1},8,["onClick"])):k("",!0),g(c,{class:"cu-item"},{default:r((()=>[g(c,{class:"content"},{default:r((()=>[g(d,{class:"cuIcon-mail text-green"}),g(d,null,{default:r((()=>[m("邮箱")])),_:1})])),_:1}),g(c,{class:"action"},{default:r((()=>[g(d,{class:"text-grey text-sm"},{default:r((()=>[m(b(s.detail.email??"raingad@foxmail.com"),1)])),_:1})])),_:1})])),_:1}),g(c,{class:"cu-item"},{default:r((()=>[g(c,{class:"content"},{default:r((()=>[g(d,{class:"cuIcon-safe text-green"}),g(d,null,{default:r((()=>[m("性别")])),_:1})])),_:1}),g(c,{class:"action"},{default:r((()=>[g(d,{class:"text-grey text-sm"},{default:r((()=>[m(b(i.sex(s.detail.sex)),1)])),_:1})])),_:1})])),_:1}),s.globalConfig.sysInfo.ipregion?(o(),u(c,{key:1,class:"cu-item"},{default:r((()=>[g(c,{class:"content"},{default:r((()=>[g(d,{class:"cuIcon-location text-green"}),g(d,null,{default:r((()=>[m("IP")])),_:1})])),_:1}),g(c,{class:"action"},{default:r((()=>[s.detail.last_login_ip?(o(),u(d,{key:0,class:"text-grey text-sm"},{default:r((()=>[m(b(s.detail.last_login_ip||"未知")+" （"+b(s.detail.location||"未知")+"）",1)])),_:1})):(o(),u(d,{key:1,class:"text-grey text-sm"},{default:r((()=>[m("未知")])),_:1}))])),_:1})])),_:1})):k("",!0)])),_:1}),s.userInfo.user_id!=s.detail.user_id?(o(),h(x,{key:0},[1==s.globalConfig.sysInfo.runMode||s.detail.friend?(o(),u(c,{key:0,class:"padding flex flex-direction"},{default:r((()=>[g(N,{class:"cu-btn bg-green mt-10 lg",onClick:t[0]||(t[0]=e=>i.sendMsg(s.detail))},{default:r((()=>[m("发消息")])),_:1}),s.detail.account?(o(),u(N,{key:0,class:"cu-btn bg-blue mt-10 lg",onClick:t[1]||(t[1]=e=>i.callPhone())},{default:r((()=>[m("打电话")])),_:1})):k("",!0),parseInt(s.globalConfig.chatInfo.webrtc)?(o(),u(N,{key:1,class:"cu-btn bg-grey mt-10 lg",onClick:t[2]||(t[2]=e=>s.modelName="callRtc")},{default:r((()=>[m("音视频通话")])),_:1})):k("",!0),2==s.globalConfig.sysInfo.runMode?(o(),u(N,{key:2,class:"cu-btn bg-red mt-10 lg",onClick:t[3]||(t[3]=e=>i.delFriend())},{default:r((()=>[m("删除好友")])),_:1})):k("",!0)])),_:1})):k("",!0),2!=s.globalConfig.sysInfo.runMode||s.detail.friend?k("",!0):(o(),u(c,{key:1,class:"padding flex flex-direction"},{default:r((()=>[g(N,{class:"cu-btn bg-green lg",onClick:t[4]||(t[4]=e=>i.addFriend())},{default:r((()=>[m("加好友")])),_:1})])),_:1}))],64)):k("",!0),g(c,{class:y(["cu-modal bottom-modal","callRtc"==s.modelName?"show":""]),onClick:t[8]||(t[8]=e=>s.modelName="")},{default:r((()=>[g(c,{class:"cu-dialog"},{default:r((()=>[g(c,{class:"manage-content"},{default:r((()=>[g(c,{class:"cu-list menu bg-white"},{default:r((()=>[g(c,{class:"cu-item",onClick:t[5]||(t[5]=e=>i.calling(0))},{default:r((()=>[g(c,{class:"content padding-tb-sm"},{default:r((()=>[g(d,{class:"cuIcon-dianhua"}),g(d,null,{default:r((()=>[m("语音通话")])),_:1})])),_:1})])),_:1}),g(c,{class:"cu-item",onClick:t[6]||(t[6]=e=>i.calling(1))},{default:r((()=>[g(c,{class:"content padding-tb-sm"},{default:r((()=>[g(d,{class:"cuIcon-record"}),g(d,null,{default:r((()=>[m("视频通话")])),_:1})])),_:1})])),_:1}),g(c,{class:"parting-line-5"}),g(c,{class:"cu-item",onClick:t[7]||(t[7]=e=>s.modelName="")},{default:r((()=>[g(c,{class:"content padding-tb-sm"},{default:r((()=>[g(d,{class:"c-red"},{default:r((()=>[m("取消")])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1})])),_:1},8,["class"])])),_:1})}]]);export{F as default};
