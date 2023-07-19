import{_ as t,o as e,c as a,w as s,f as l,b as n,d as i,F as r,i as o,S as c,e as d,h as u,t as m,Z as h,g as f,j as p,k as g,s as y,q as _,l as C,p as b,n as x,E as k,G as v,H as T,r as w,I,a0 as B,v as S}from"./index-570d8679.js";const D=t({name:"breadcrum",props:{tree:{type:Array,default:()=>({})},icon:{type:String,default:()=>"cuIcon-right"}},data:()=>({}),methods:{open(t){this.$emit("openBread",t)}}},[["render",function(t,g,y,_,C,b){const x=o,k=p,v=c;return e(),a(x,null,{default:s((()=>[l(v,{class:"folder-wap","scroll-x":!0,"scroll-left":99999999},{default:s((()=>[l(x,{class:"im-flex im-justify-content-start im-align-items-center"},{default:s((()=>[(e(!0),n(r,null,i(y.tree,((n,i)=>(e(),a(x,{class:"tab-item im-flex im-justify-content-start im-align-items-center",onClick:t=>b.open(n),key:i},{default:s((()=>[l(x,{class:d(["mar10 lz-tree-name font-color-999",{"font-color-333":i==y.tree.length-1}])},{default:s((()=>[u(m(n.name),1)])),_:2},1032,["class"]),h(t.$slots,"icon",{},(()=>[i<y.tree.length-1?(e(),a(k,{key:0,class:d(["font-color-999",[y.icon?y.icon:"cuIcon-right"]])},null,8,["class"])):f("",!0)]),!0)])),_:2},1032,["onClick"])))),128))])),_:3})])),_:3})])),_:3})}],["__scopeId","data-v-b66ec5ca"]]),L=g(y),j=_(y),{contacts:M,sysUnread:V}=C(L);const z=t({components:{breadcurm:D},name:"contacts",props:{TabCur:{type:Number,default:0}},data(){return{StatusBar:this.StatusBar,CustomBar:this.CustomBar,paddingB:this.inlineTools,tabbarH:50,hidden:!0,listCurID:"",list:[],listCur:"",total:0,scrollLeft:0,msgs:M,unread:V,globalConfig:j.globalConfig,tree:[{id:1,name:"技术部"},{id:2,name:"产品部"},{id:3,name:"信息部"}],depList:[{id:1,name:"技术部"},{id:2,name:"财务部"}],userList:[{id:1,realname:"张三",avatar:"https://api.multiavatar.com/raingad1.png?apikey=zdvXV3W4MjwhP9"},{id:2,realname:"李四",avatar:"https://api.multiavatar.com/raingad2.png?apikey=zdvXV3W4MjwhP9"}],tabList:["普通通讯录","企业通讯录"],contacts:[]}},watch:{msgs(t){this.initContacts(t)}},created(){this.listCur=this.contacts[0]},mounted(){this.initContacts(this.msgs)},methods:{initContacts(t){const e=JSON.parse(JSON.stringify(t)).filter((t=>0==t.is_group));this.total=e.length;const a=e.sort(((t,e)=>"#"===t.index?1:"#"===e.index?-1:t.index.localeCompare(e.index,"zh"))).reduce(((t,e)=>{const a=e.index,s=t.findIndex((t=>t.name===a));return-1===s?t.push({name:a,children:[e]}):t[s].children.push(e),t}),[]);this.contacts=a},openDep(t){},openDetails(t){b({url:"/pages/contacts/detail?id="+t.id})},openGroup(){b({url:"/pages/contacts/group"})},openFriend(){b({url:"/pages/contacts/friend"})},tabSelect(t){this.TabCur=t.currentTarget.dataset.id,this.scrollLeft=60*(t.currentTarget.dataset.id-1)},getCur(t){this.hidden=!1,this.listCur=this.contacts[t.target.id].name},setCur(t){this.hidden=!0,this.listCur=this.listCur},tMove(t){let e=t.touches[0].clientY,a=this.boxTop,s=this;if(e>a){let t=parseInt((e-a)/20);this.listCur=s.contacts[t].name}},tStart(){this.hidden=!1},tEnd(){this.hidden=!0,this.listCurID=this.listCur},indexSelect(t){let e=this,a=this.barHeight,s=this.contacts,l=Math.ceil(s.length*t.detail.y/a);for(let n=0;n<s.length;n++)if(l<n+1)return e.listCur=s[n].name,e.movableY=20*n,!1}}},[["render",function(t,h,g,y,_,C){const b=o,D=p,L=w("Empty"),j=c,M=I,V=w("breadcurm"),z=B,E=S;return e(),a(b,null,{default:s((()=>[0==g.TabCur?(e(),a(j,{key:0,"scroll-y":"",class:"indexes","scroll-into-view":"indexes-"+_.listCurID,style:x([{height:"calc(100vh - "+(_.CustomBar+t.inlineTools+_.StatusBar)+"px)"}]),"scroll-with-animation":!0,"enable-back-to-top":!0},{default:s((()=>[l(b,{style:{"padding-bottom":"120rpx"}},{default:s((()=>[l(b,{class:"cu-list menu mt-10"},{default:s((()=>[2==_.globalConfig.sysInfo.runMode?(e(),a(b,{key:0,class:"cu-item arrow",onClick:C.openFriend},{default:s((()=>[l(b,{class:"cu-avatar radius mr-15",style:x([{backgroundImage:"url(./static/image/invite.png)"}])},null,8,["style"]),l(b,{class:"content"},{default:s((()=>[l(D,{class:"text-grey"},{default:s((()=>[u("新邀请")])),_:1})])),_:1}),l(b,{class:"action"},{default:s((()=>[_.unread>0?(e(),a(b,{key:0,class:"cu-tag round bg-red sm"},{default:s((()=>[u(m(_.unread),1)])),_:1})):f("",!0)])),_:1})])),_:1},8,["onClick"])):f("",!0),l(b,{class:"cu-item arrow",onClick:C.openGroup},{default:s((()=>[l(b,{class:"cu-avatar radius mr-15",style:x([{backgroundImage:"url(./static/image/group.png)"}])},null,8,["style"]),l(b,{class:"content"},{default:s((()=>[l(D,{class:"text-grey"},{default:s((()=>[u("群聊")])),_:1})])),_:1})])),_:1},8,["onClick"])])),_:1}),(e(!0),n(r,null,i(_.contacts,((t,o)=>(e(),a(b,{key:o,class:d("indexItem-"+t.name),id:"indexes-"+t.name,"data-index":t.name},{default:s((()=>[l(b,{class:"padding"},{default:s((()=>[u(m(t.name),1)])),_:2},1024),l(b,{class:"cu-list menu no-padding"},{default:s((()=>[(e(!0),n(r,null,i(t.children,((t,n)=>(e(),a(b,{class:"cu-item",key:n,onClick:e=>C.openDetails(t)},{default:s((()=>[l(b,{class:"cu-avatar radius mr-15",style:x([{backgroundImage:"url("+t.avatar+")"}])},null,8,["style"]),l(b,{class:"content"},{default:s((()=>[l(b,{class:"text-grey"},{default:s((()=>[u(m(t.displayName),1)])),_:2},1024)])),_:2},1024)])),_:2},1032,["onClick"])))),128))])),_:2},1024)])),_:2},1032,["class","id","data-index"])))),128)),l(b,{class:"text-center m-20 text-grey"},{default:s((()=>[u(m(_.total)+" 个朋友",1)])),_:1}),_.contacts.length?f("",!0):(e(),a(L,{key:0,noDatatext:"暂无联系人",textcolor:"#999"}))])),_:1})])),_:1},8,["scroll-into-view","style"])):f("",!0),1==g.TabCur?(e(),a(b,{key:1,class:"cu-bar bg-white search fixed",style:x([{top:_.CustomBar+"px"}])},{default:s((()=>[l(b,{class:"search-form round"},{default:s((()=>[l(D,{class:"cuIcon-search"}),l(M,{type:"text",modelValue:t.keywords,"onUpdate:modelValue":h[0]||(h[0]=e=>t.keywords=e),placeholder:"输入搜索的关键词","confirm-type":"search"},null,8,["modelValue"])])),_:1})])),_:1},8,["style"])):f("",!0),1==g.TabCur?(e(),a(j,{key:2,"scroll-y":"",class:"indexes","scroll-into-view":"indexes-"+_.listCurID,style:x([{top:"50px",height:"calc(100vh - "+(_.CustomBar+t.inlineTools+_.StatusBar+50)+"px)"}]),"scroll-with-animation":!0,"enable-back-to-top":!0},{default:s((()=>[l(b,{class:""},{default:s((()=>[l(V,{tree:_.tree,onOpenBread:C.openDep},null,8,["tree","onOpenBread"])])),_:1}),l(b,{class:"im-department-list"},{default:s((()=>[(e(!0),n(r,null,i(_.depList,((t,n)=>(e(),a(z,{class:"im-flex im-justify-content-start im-align-items-center mt-10",onClick:e=>C.openDep(t),key:n},{default:s((()=>[l(b,{class:"im-folder-bar mr-10"},{default:s((()=>[l(D,{class:"cuIcon-file color-blue"})])),_:1}),l(b,{class:"im-list-body im-border-b"},{default:s((()=>[l(b,{class:"im-list-title word"},{default:s((()=>[u(m(t.name),1)])),_:2},1024)])),_:2},1024)])),_:2},1032,["onClick"])))),128)),(e(!0),n(r,null,i(_.userList,((t,n)=>(e(),a(z,{class:"im-flex im-justify-content-start im-align-items-center mt-10",key:t.id,url:"/pages/contacts/detail?user_id="+t.id},{default:s((()=>[l(b,{class:"im-folder-bar im-image mr-10"},{default:s((()=>[l(E,{src:t.avatar,mode:"widthFix"},null,8,["src"])])),_:2},1024),l(b,{class:"im-list-body im-border-b"},{default:s((()=>[l(b,{class:"im-list-title word"},{default:s((()=>[u(m(t.realname),1)])),_:2},1024)])),_:2},1024)])),_:2},1032,["url"])))),128)),0==_.depList.length&&0==_.userList.length?(e(),a(L,{key:0})):f("",!0)])),_:1})])),_:1},8,["scroll-into-view","style"])):f("",!0),0==g.TabCur?(e(),a(b,{key:3,class:"indexBar",style:x([{height:"calc(100vh - "+_.CustomBar+"px - 50px)"}])},{default:s((()=>[l(b,{class:"indexBar-box",onTouchstart:C.tStart,onTouchend:C.tEnd,onTouchmove:k(C.tMove,["stop"])},{default:s((()=>[(e(!0),n(r,null,i(_.contacts,((t,l)=>(e(),a(b,{class:"indexBar-item",key:l,id:l,onTouchstart:C.getCur,onTouchend:C.setCur},{default:s((()=>[u(m(t.name),1)])),_:2},1032,["id","onTouchstart","onTouchend"])))),128))])),_:1},8,["onTouchstart","onTouchend","onTouchmove"])])),_:1},8,["style"])):f("",!0),v(l(b,{class:"indexToast"},{default:s((()=>[u(m(_.listCur),1)])),_:1},512),[[T,!_.hidden]])])),_:1})}],["__scopeId","data-v-b8d4f8e9"]]);export{z as default};
