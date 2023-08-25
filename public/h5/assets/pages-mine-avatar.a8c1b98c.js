import{L as t,a as e,ag as i,au as a,B as h,ah as o,M as s,av as r,aw as n,ax as c,ay as l,_ as p,o as d,c as g,w as u,g as m,d as w,F as f,f as R,h as y,n as x,v,j as I,az as b,u as T,s as S,Q as M,x as C,ad as k,C as A,i as P,r as H,b as N,y as _}from"./index-4eea144c.js";import{r as z}from"./uni-app.es.b3d97fb4.js";const W=t();function $(t){if("number"==typeof t||!isNaN(Number(t)))return e(t);if("string"==typeof t){if(t.endsWith("rpx"))return $(t.replace("rpx",""));if(t.endsWith("px"))return Number(t.replace("px",""));if(t.endsWith("vw"))return Number(t.replace("vw",""))*W.screenWidth/100;if(t.endsWith("vh"))return Number(t.replace("vh",""))*W.screenHeight/100}return 0}var D=[],F=0,j=[],q=0,B=null,O=null,X="",Y=null,L=null,U=null,E=0,J={imageRect:null,cropperRect:null};function G(){var t=J.imageRect;B.setStyle({left:t.left+"px",top:t.top+"px",width:t.width+"px",height:t.height+"px",transform:"rotate("+E+"deg)"})}function Z(){var t=J.cropperRect;O.setStyle({left:t.left+"px",top:t.top+"px",width:t.width+"px",height:t.height+"px"})}function Q(t){var e=U.width*(t-1),i=U.height*(t-1);J.imageRect={width:U.width+e,height:U.height+i,left:U.left-e*j[0],top:U.top-i*j[1]}}function K(t){return t/180*Math.PI}function V(){var t=J.imageRect.width,e=J.imageRect.height,i=J.imageRect.left,a=J.imageRect.top,h=Math.sqrt(t*t+e*e),o=Math.atan(e/t)/Math.PI*180,s=E%90,r=Math.floor(E/90),n=h*Math.cos(K(o-s)),c=h*Math.sin(K(o+s));if(r%2==1){var l=n;n=c,c=l}return{width:n,height:c,left:i-(n-t)/2,top:a-(c-e)/2,dw:n-t,dh:c-e}}const tt={touchStart:function(t,e){t.preventDefault(),t.stopPropagation(),Y=t.instance;var i=t.instance.getDataset();if(X=i.type,D=t.touches,e.callMethod("onTouchStart"),2==D.length){X="image";var a=D[0].clientX,h=D[0].clientY,o=D[1].clientX,s=D[1].clientY,r=Math.pow(o-a,2)+Math.pow(s-h,2);F=Math.sqrt(r);var n=((a+o)/2-U.left)/U.width,c=((h+s)/2-U.top)/U.height;j=[n,c]}return!1},touchMove:function(t,e){if(""==X)return!1;t.preventDefault(),t.stopPropagation();var i=t.touches,a=i[0].clientX-D[0].clientX,h=i[0].clientY-D[0].clientY;if(1==D.length){if("image"===X)J.imageRect.left=U.left+a,J.imageRect.top=U.top+h,G();else if("controller"===X){var o=0;Y.hasClass("left")&&(o=-1),Y.hasClass("right")&&(o=1);var s=0;Y.hasClass("top")&&(s=-1),Y.hasClass("bottom")&&(s=1);var r=a*o,n=h*s;0!==q&&(o*s!=0?r/q>n?r=(n=r/q)*q:n=(r=n*q)/q:0==o?r=n*q:n=r/q);var c=V(),l=L.width+r,p=L.height+n,d=c.left+c.width,g=c.top+c.height;if(-1!=o)L.left+l>d&&(l=d-L.left,0!==q&&(p=l/q));else L.left-r<c.left&&(l=L.left+L.width-c.left,0!==q&&(p=l/q));if(-1!=s)L.top+p>g&&(p=g-L.top,0!==q&&(l=p*q));else L.top-n<c.top&&(p=L.top+L.height-c.top,0!==q&&(l=p*q));-1==o&&(J.cropperRect.left=L.left+L.width-l),-1==s&&(J.cropperRect.top=L.top+L.height-p),J.cropperRect.width=l,J.cropperRect.height=p,Z()}}else if(2==i.length&&2==D.length){var u=i[0].clientX-i[1].clientX,m=i[0].clientY-i[1].clientY,w=Math.pow(u,2)+Math.pow(m,2);Q((w=Math.sqrt(w))/F),G()}return!1},touchEnd:function(t,e){if("image"===X){var i=L.left,a=L.left+L.width,h=L.top,o=h+L.height,s=L.width/L.height,r=V(),n=r.width/r.height;if(r.width<L.width||r.height<L.height){var c=1;c=n<s?L.width/r.width:L.height/r.height,U.width=J.imageRect.width,U.height=J.imageRect.height,Q(c)}i<r.left&&(J.imageRect.left=i+r.dw/2),a>r.left+r.width&&(J.imageRect.left=a-r.width+r.dw/2),h<r.top&&(J.imageRect.top=h+r.dh/2),o>r.top+r.height&&(J.imageRect.top=o-r.height+r.dh/2),G()}return e.callMethod("updateData",{cropperRect:J.cropperRect,imageRect:J.imageRect}),X="",D=[],!1},changeRatio:function(t){q=t},changeRotateAngle:function(t){E=t,B&&G(),V()},changeImageRect:function(t,e,i){t&&(U=t,J.imageRect={left:t.left,top:t.top,width:t.width,height:t.height},setTimeout((function(){B=i.selectComponent(".mainContent > .image"),G()})))},changeCropper:function(t,e,i){t&&(L=t,J.cropperRect={left:t.left,top:t.top,width:t.width,height:t.height},setTimeout((function(){O=i.selectComponent(".mainContent > .cropper"),Z()})))}},et=t=>{t.$wxs||(t.$wxs=[]),t.$wxs.push("wxsModule"),t.mixins||(t.mixins=[]),t.mixins.push({beforeCreate(){this.wxsModule=tt}})},it={name:"bt-cropper",props:{imageSrc:{type:String,default:"",required:!0},mask:{type:String,default:""},containerSize:{type:Object,default:null},fileType:{type:String,default:"png"},dWidth:Number,maxWidth:{type:Number,default:2e3},ratio:{type:Number,default:0,validator:t=>"number"==typeof t&&!(t<0)},rotate:Number,showGrid:{type:Boolean,default:!1},quality:{type:Number,default:1},canvas2d:{type:Boolean,default:!1},initPosition:{type:Object,default:()=>null},autoZoom:{type:Boolean,default:!0}},data:()=>({canvasId:"bt-cropper",containerRect:null,imageInfo:null,operationHistory:[],operationIndex:0,anim:!1,timer:null,type2d:!1,pixel:1,imageRect:null,cropperRect:null,target:{width:0,height:0}}),watch:{imageSrc:{handler(t){"string"==typeof t&&""!==t?this.imageInit(t):this.imageInfo=null},immediate:!0},ratio(){0!=this.ratio&&(this.startAnim(),this.init())}},computed:{containerStyle(){return this.containerSize&&this.containerRect?{width:this.containerRect.width+"px",height:this.containerRect.height+"px"}:{}},rotateAngle(){const t=Number(this.rotate);return isNaN(t)?0:t%360}},methods:{startAnim(){this.stopAnim(),this.anim=!0,this.timer=setTimeout((()=>{this.anim=!1}),200)},stopAnim(){this.anim=!1,clearTimeout(this.timer)},imageInit(t){i({title:"载入中..."}),a({src:t,success:t=>{this.imageInfo=t,this.$nextTick((()=>{this.getContainer().then((t=>{this.containerRect=t,this.init()}))}))},fail:t=>{this.$emit("loadFail",t),h({title:"图片下载失败!",icon:"none"})},complete(t){o()}})},initCropper(){const t=this.imageInfo.width/this.imageInfo.height,e=this.containerRect.width/this.containerRect.height,i={};let a=this.ratio;0==a&&(a=this.cropperRect?this.cropperRect.width/this.cropperRect.height:1);const h={};return e>a?(h.height=.85*this.containerRect.height,h.width=h.height*a):(h.width=.85*this.containerRect.width,h.height=h.width/a),a>t?(i.width=h.width,i.height=i.width/t):(i.height=h.height,i.width=i.height*t),i.left=(this.containerRect.width-i.width)/2,i.top=(this.containerRect.height-i.height)/2,h.left=i.left+(i.width-h.width)/2,h.top=i.top+(i.height-h.height)/2,{imageRect:i,cropperRect:h}},init(){const{imageRect:t,cropperRect:e}=this.initCropper();if(this.initPosition){const i=this.imageInfo.width/t.width,{left:a,top:h,width:o,height:s}=this.initPosition;void 0!==a&&void 0!==h&&void 0!==o&&void 0!==s&&(e.width=o/i,e.height=s/i,e.left=a/i,e.top=h/i,this.$nextTick(this.zoomToFill))}this.imageRect=t,this.cropperRect=e,this.operationHistory=[{imageRect:t,cropperRect:e}],this.operationIndex=0,this.setTarget(),this.type2d=!1},setTarget(){const t=this.cropperRect.width/this.cropperRect.height;if(this.dWidth)this.target={width:this.dWidth,height:this.dWidth/(t||1)};else{const e=Math.min(this.maxWidth,this.cropperRect.width*(this.imageInfo.width/this.imageRect.width));this.target={width:e,height:e/(t||1)}}},addHistory({imageRect:t,cropperRect:e}){this.operationIndex!==this.operationHistory.length-1&&(this.operationHistory=this.operationHistory.slice(0,this.operationIndex)),this.operationHistory.push({imageRect:t,cropperRect:e}),this.operationHistory.length>10&&this.operationHistory.shift(),this.operationIndex=this.operationHistory.length-1},updateData(t){this.imageRect=t.imageRect,this.cropperRect=t.cropperRect,this.addHistory(t),this.setTarget(),this.autoZoom&&(this.timer=setTimeout((()=>{this.zoomToFill()}),600));const{imageRect:e,cropperRect:i}=t,a=e.width/this.imageInfo.width;this.$emit("change",{left:(i.left-e.left)/a,top:(i.top-e.top)/a,width:i.width/a,height:i.height/a})},getContainer(){if(null!==this.containerSize&&"object"==typeof this.containerSize){const{width:t,height:e}=this.containerSize;return Promise.resolve({width:$(t),height:$(e)})}return new Promise((t=>{s().in(this).select(".mainContent").boundingClientRect((e=>{t(e)})).exec()}))},zoomToFill(){this.startAnim();const t={...this.cropperRect};this.imageRect,this.cropperRect,this.cropperRect=this.initCropper().cropperRect;const e=this.cropperRect.width/t.width,i=t.left-this.imageRect.left,a=t.top-this.imageRect.top;this.imageRect={width:this.imageRect.width*e,height:this.imageRect.height*e,left:this.imageRect.left+(this.cropperRect.left-t.left)-(e-1)*i,top:this.imageRect.top+(this.cropperRect.top-t.top)-(e-1)*a}},onTouchStart(){this.stopAnim()},undo(){return this.operationIndex>0&&(this.operationIndex--,this.imageRect=this.operationHistory[this.operationIndex].imageRect,this.cropperRect=this.operationHistory[this.operationIndex].cropperRect,!0)},resume(){return this.operationIndex<this.operationHistory.length-1&&(this.operationIndex++,this.imageRect=this.operationHistory[this.operationIndex].imageRect,this.cropperRect=this.operationHistory[this.operationIndex].cropperRect,!0)},async drawImage(t,e,i,h,o,s){if(this.type2d)await new Promise((t=>e.onload=t)),t.drawImage(e,i*this.pixel,h*this.pixel,o*this.pixel,s*this.pixel);else{const r=await new Promise((t=>{a({src:e,success({path:e}){t(e)}})}));t.drawImage(r,i*this.pixel,h*this.pixel,o*this.pixel,s*this.pixel),await new Promise((e=>t.draw(!1,e)))}},async crop(){let t,e;if(this.$emit("cropStart"),this.setTarget(),this.type2d){const i=s().in(this);e=await new Promise((t=>i.select(".bt-canvas").node((({node:e})=>t(e))).exec())),e.width=this.target.width*this.pixel,e.height=this.target.height*this.pixel,t=e.getContext("2d")}else t=r(this.canvasId,this);const i=this.cropperRect.width/this.target.width,a=(this.cropperRect.left-this.imageRect.left)/i,h=(this.cropperRect.top-this.imageRect.top)/i;let o;this.type2d?(o=e.createImage(),o.src=this.imageSrc):o=this.imageSrc;const p=-a,d=-h,g=this.imageRect.width/i,u=this.imageRect.height/i;if(t.save(),t.translate(p+g/2,d+u/2),t.rotate(this.rotateAngle*Math.PI/180),t.translate(-(p+g/2),-(d+u/2)),await this.drawImage(t,o,p,d,g,u),t.restore(),""!==this.mask){let e,i;e=this.type2d?t.getImageData(0,0,this.target.width,this.target.height):await new Promise((t=>{n({canvasId:this.canvasId,x:0,y:0,width:this.target.width,height:this.target.height,success(e){t(e)}},this)})),t.clearRect(0,0,this.target.width,this.target.height),this.type2d?o.src=this.mask:o=this.mask,await this.drawImage(t,o,0,0,this.target.width,this.target.height),i=this.type2d?t.getImageData(0,0,this.target.width,this.target.height):await new Promise(((t,e)=>{n({canvasId:this.canvasId,x:0,y:0,width:this.target.width,height:this.target.height,success(e){t(e)}},this)})),t.clearRect(0,0,this.target.width,this.target.height);for(let t=3;t<i.data.length;t+=4){0!==i.data[t]&&(e.data[t]=0)}this.type2d?t.putImageData(e,0,0):await new Promise((t=>{c({canvasId:this.canvasId,x:0,y:0,width:e.width,height:e.height,data:e.data,complete:e=>{t(e)}},this)}))}return new Promise(((t,i)=>{const a={};this.type2d?a.canvas=e:a.canvasId=this.canvasId,l({...a,destWidth:this.target.width,destHeight:this.target.height,quality:Number(this.quality)||1,fileType:this.fileType,success:({tempFilePath:e})=>{for(var i=e.split(","),a=i[0].match(/:(.*?);/)[1],h=atob(i[1]),o=h.length,s=new Uint8Array(o),r=0;r<o;r++)s[r]=h.charCodeAt(r);var n=URL||webkitURL;t(n.createObjectURL(new Blob([s],{type:a}))),t(e)},fail(t){console.log("保存失败，错误信息：",t),i(t)}},this)}))}}};et(it);const at=p(it,[["render",function(t,e,i,a,h,o){const s=v,r=I,n=b;return d(),g(r,{class:"bt-container",style:x([o.containerStyle])},{default:u((()=>[m(r,{class:"mainContent","data-type":"image",onTouchstart:t.wxsModule.touchStart,onTouchmove:t.wxsModule.touchMove,onTouchend:t.wxsModule.touchEnd},{default:u((()=>[h.imageRect&&h.cropperRect?(d(),w(f,{key:0},[m(s,{mode:"aspectFit",src:i.imageSrc,class:R(["image",{anim:h.anim}]),rotateAngle:o.rotateAngle,"change:rotateAngle":t.wxsModule.changeRotateAngle,"change:imageRect":t.wxsModule.changeImageRect,imageRect:h.imageRect},null,8,["src","rotateAngle","change:rotateAngle","change:imageRect","imageRect","class"]),m(r,{class:R(["cropper",{anim:h.anim}]),"change:cropperRect":t.wxsModule.changeCropper,cropperRect:h.cropperRect,"change:ratio":t.wxsModule.changeRatio,ratio:i.ratio},{default:u((()=>[m(s,{class:"mask",src:i.mask},null,8,["src"]),i.showGrid?(d(),w(f,{key:0},[m(r,{class:"line row row1"}),m(r,{class:"line row row2"}),m(r,{class:"line col col1"}),m(r,{class:"line col col2"})],64)):y("",!0),m(r,{class:"controller vertical left",onTouchstart:t.wxsModule.touchStart,"data-type":"controller"},null,8,["onTouchstart"]),m(r,{class:"controller vertical right",onTouchstart:t.wxsModule.touchStart,"data-type":"controller"},null,8,["onTouchstart"]),m(r,{class:"controller horizon top",onTouchstart:t.wxsModule.touchStart,"data-type":"controller"},null,8,["onTouchstart"]),m(r,{class:"controller horizon bottom",onTouchstart:t.wxsModule.touchStart,"data-type":"controller"},null,8,["onTouchstart"]),m(r,{class:"controller left top",onTouchstart:t.wxsModule.touchStart,"data-type":"controller"},null,8,["onTouchstart"]),m(r,{class:"controller left bottom",onTouchstart:t.wxsModule.touchStart,"data-type":"controller"},null,8,["onTouchstart"]),m(r,{class:"controller right top",onTouchstart:t.wxsModule.touchStart,"data-type":"controller"},null,8,["onTouchstart"]),m(r,{class:"controller right bottom",onTouchstart:t.wxsModule.touchStart,"data-type":"controller"},null,8,["onTouchstart"])])),_:1},8,["class","change:cropperRect","cropperRect","change:ratio","ratio"])],64)):y("",!0)])),_:1},8,["onTouchstart","onTouchmove","onTouchend"]),h.type2d?(d(),g(n,{key:0,type:"2d",class:"bt-canvas",width:h.target.width,height:h.target.height},null,8,["width","height"])):(d(),g(n,{key:1,"canvas-id":h.canvasId,class:"bt-canvas",style:x({width:h.target.width+"px",height:h.target.height+"px"}),width:h.target.width*h.pixel,height:h.target.height*h.pixel},null,8,["canvas-id","style","width","height"]))])),_:1},8,["style"])}],["__scopeId","data-v-503f171e"]]),ht=T(S);const ot=p({data:()=>({loginStore:ht,globalConfig:ht.globalConfig,saved:!1}),methods:{uploadAvatar(){this.saved=!0,this.$refs.cropper.crop().then((t=>{M({url:this.$api.msgApi.uploadAvatar,filePath:t,name:"file",header:{authToken:C("authToken")},formData:{ext:"png"},success:t=>{let e=JSON.parse(t.data);if(0==e.code){h({title:e.msg,icon:"none"});let t=JSON.parse(JSON.stringify(ht.userInfo));ht.login(t),k()}},fail:t=>{this.saved=!1,console.log(t)}})})),setTimeout((()=>{this.saved=!1}),8e3)},chooseImage(t){A({count:1,sizeType:["compressed"],sourceType:["album","camera"],success:t=>{t.tempFiles.forEach((t=>{console.log(t),this.loginStore.userInfo.avatar=t.path}))}})}}},[["render",function(t,e,i,a,h,o){const s=H("cu-custom"),r=z(N("bt-cropper"),at),n=_,c=I;return d(),g(c,{class:"container"},{default:u((()=>[m(s,{bgColor:"bg-gradual-green",isBack:!0},{backText:u((()=>[])),content:u((()=>[P("修改头像")])),_:1}),m(r,{ref:"cropper",ratio:1,imageSrc:h.loginStore.userInfo.avatar},null,8,["imageSrc"]),m(c,null,{default:u((()=>[m(c,{class:"margin-tb-sm text-center"},{default:u((()=>[m(n,{class:"cu-btn round bg-orange",onClick:o.chooseImage},{default:u((()=>[P("选择图片")])),_:1},8,["onClick"]),m(n,{class:"cu-btn round bg-green ml-10",style:x(h.saved?"border: solid 1px #dbdada;":""),disabled:h.saved,onClick:o.uploadAvatar},{default:u((()=>[P("确定上传")])),_:1},8,["style","disabled","onClick"])])),_:1})])),_:1})])),_:1})}],["__scopeId","data-v-d7c095da"]]);export{ot as default};
