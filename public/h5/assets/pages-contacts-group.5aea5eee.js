import{_ as a,l as s,s as t,m as e,x as l,q as n,o as c,c as o,w as r,j as i,g as u,i as d,d as g,e as m,F as p,h,k as f,r as x,n as k,t as _}from"./index-030df82b.js";e(s(t));const C=a({name:"group",data:()=>({groupList:[]}),created(){},mounted(){this.initContacts(this.msgs)},methods:{initContacts(a){const s=l("allContacts").filter((a=>1==a.is_group)).sort(((a,s)=>"#"===a.index?1:"#"===s.index?-1:a.index.localeCompare(s.index,"zh")));this.groupList=s},openDetails(a){n({url:"/pages/message/chat?id="+a.id})},search(){n({url:"/pages/index/search?type=3"})}}},[["render",function(a,s,t,e,l,n){const C=f,y=i,b=x("cu-custom"),L=x("Empty");return c(),o(y,null,{default:r((()=>[u(b,{bgColor:"bg-gradual-green",isBack:!0},{backText:r((()=>[])),content:r((()=>[d("群聊列表")])),right:r((()=>[u(y,{class:"f-20 ml-10 mr-10",onClick:s[0]||(s[0]=a=>n.search())},{default:r((()=>[u(C,{class:"cuIcon-search"})])),_:1})])),_:1}),u(y,{class:"cu-list menu-avatar no-padding"},{default:r((()=>[(c(!0),g(p,null,m(l.groupList,((a,s)=>(c(),o(y,{class:"cu-item",key:s,onClick:s=>n.openDetails(a)},{default:r((()=>[u(y,{class:"cu-avatar lg radius mr-15",style:k([{backgroundImage:"url("+a.avatar+")"}])},null,8,["style"]),u(y,{class:"content"},{default:r((()=>[u(y,{class:"c-333"},{default:r((()=>[d(_(a.displayName),1)])),_:2},1024)])),_:2},1024)])),_:2},1032,["onClick"])))),128)),l.groupList.length?h("",!0):(c(),o(L,{key:0,noDatatext:"暂无群聊",textcolor:"#999"}))])),_:1})])),_:1})}]]);export{C as default};
