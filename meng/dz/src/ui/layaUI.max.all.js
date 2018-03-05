var CLASS$=Laya.class;
var STATICATTR$=Laya.static;
var View=laya.ui.View;
var Dialog=laya.ui.Dialog;
var mengUI=(function(_super){
		function mengUI(){
			
		    this.selectProduct=null;
		    this.selectIP=null;
		    this.productPanel=null;
		    this.baseImage=null;
		    this.drawImage=null;
		    this.maskImage=null;
		    this.signLabel=null;
		    this.initBtn=null;
		    this.enlargeBtn=null;
		    this.shareBtn=null;
		    this.reduceBtn=null;
		    this.partList=null;
		    this.selectProductPanel=null;
		    this.productPanelCloseBtn=null;
		    this.productList=null;
		    this.image1=null;
		    this.label1=null;
		    this.preBtn=null;
		    this.nextBtn=null;
		    this.drawImageList=null;
		    this.createBtn=null;
		    this.signPanel=null;
		    this.signInput=null;
		    this.signFontSize=null;
		    this.signFontAngle=null;
		    this.signColor=null;
		    this.signFontColor=null;
		    this.rightTab=null;
		    this.selectIpPanel=null;
		    this.maskPanel=null;
		    this.ipImageList=null;
		    this.ipConfirm=null;
		    this.ipCancel=null;
		    this.ipLeft=null;
		    this.ipRight=null;
		    this.ipInput=null;
		    this.ipGo=null;
		    this.confirmPanel=null;
		    this.productImage=null;
		    this.makeBtn=null;
		    this.makeCancel=null;
		    this.title=null;
		    this.ideaText=null;

			mengUI.__super.call(this);
		}

		CLASS$(mengUI,'ui.mengUI',_super);
		var __proto__=mengUI.prototype;
		__proto__.createChildren=function(){
		    			View.regComponent("Text",laya.display.Text);

			laya.ui.Component.prototype.createChildren.call(this);
			this.createView(mengUI.uiView);
		}
		mengUI.uiView={"type":"View","props":{"width":1200,"height":600},"child":[{"type":"ComboBox","props":{"y":39,"x":79,"width":100,"var":"selectProduct","skin":"comp/new_com.png","sizeGrid":"0,0,0,0","selectedIndex":0,"scrollBarSkin":"comp/vscroll.png","labels":"选择产品,鞋子,衣服"}},{"type":"ComboBox","props":{"y":39,"x":228,"width":100,"var":"selectIP","skin":"comp/new_com.png","sizeGrid":"0,0,0,0","selectedIndex":0,"scrollBarSkin":"comp/vscroll.png","labels":"选择IP,星纪元,阴阳师"}},{"type":"Panel","props":{"y":69,"x":15,"width":861,"var":"productPanel","pivotY":2,"pivotX":1,"height":530},"child":[{"type":"Panel","props":{"y":18,"x":226,"width":500,"height":500},"child":[{"type":"Image","props":{"y":0,"x":0,"var":"baseImage"}},{"type":"Image","props":{"y":299,"x":244,"var":"drawImage","skin":"/dz/comp/fullcard_b_45640.jpg","anchorY":0.5,"anchorX":0.5}},{"type":"Image","props":{"y":0,"x":0,"var":"maskImage"}},{"type":"Text","props":{"y":8,"x":253,"var":"signLabel","fontSize":25,"font":"SimHei","color":"#000000"}}]},{"type":"Button","props":{"y":109,"x":806,"var":"initBtn","skin":"comp/r.png"}},{"type":"Button","props":{"y":161,"x":806,"var":"enlargeBtn","skin":"comp/d.png"}},{"type":"Button","props":{"y":265,"x":806,"var":"shareBtn","skin":"comp/s.png"}},{"type":"Button","props":{"y":209,"x":806,"var":"reduceBtn","skin":"comp/x.png"}},{"type":"List","props":{"y":11,"x":34,"width":127,"var":"partList","vScrollBarSkin":"comp/vscroll_n.png","spaceY":5,"spaceX":40,"repeatX":1,"height":516},"child":[{"type":"Box","props":{"y":4,"x":7,"width":100,"renderType":"render","height":100},"child":[{"type":"Image","props":{"y":0,"x":0,"width":100,"skin":"comp/bg_black.png","height":100,"gray":true,"alpha":0.2}},{"type":"Image","props":{"y":0,"x":0,"width":100,"skin":"comp/bg_black.png","name":"image","height":100,"alpha":0.5}},{"type":"Label","props":{"y":-4,"x":-7,"visible":false,"name":"cavasID"}}]}]}]},{"type":"Panel","props":{"y":95,"x":79,"width":183,"visible":false,"var":"selectProductPanel","height":480},"child":[{"type":"Button","props":{"y":1,"x":154,"var":"productPanelCloseBtn","skin":"comp/btn_close.png"}},{"type":"List","props":{"y":35,"x":3,"width":177,"var":"productList","vScrollBarSkin":"comp/vscroll.png","spaceY":5,"spaceX":5,"repeatY":10,"repeatX":3,"height":382},"child":[{"type":"Box","props":{"y":3,"x":-9,"renderType":"render"},"child":[{"type":"Image","props":{"y":2,"x":5,"width":50,"var":"image1","skin":"comp/image.png","height":40}},{"type":"Label","props":{"y":47,"x":5,"var":"label1","text":"$1001`","color":"#e5110d"}}]}]},{"type":"Button","props":{"y":427,"x":27,"width":20,"var":"preBtn","skin":"comp/button.png","label":"<","height":20}},{"type":"Button","props":{"y":427,"x":88,"width":20,"var":"nextBtn","skin":"comp/button.png","label":">","height":20}}]},{"type":"List","props":{"y":78,"x":880,"width":278,"var":"drawImageList","vScrollBarSkin":"comp/vscroll_n.png","spaceY":20,"spaceX":10,"repeatX":3,"height":444},"child":[{"type":"Box","props":{"y":20,"x":13,"renderType":"render"},"child":[{"type":"Image","props":{"y":-10,"x":-4,"width":77,"skin":"comp/bg_black.png","name":"image","height":77,"alpha":0.5}},{"type":"Image","props":{"y":-10,"x":-4,"width":77,"visible":false,"skin":"comp/select.png","name":"selectImg","height":77}}]}]},{"type":"Button","props":{"y":533,"x":882,"width":278,"var":"createBtn","skin":"comp/confirm.png","height":50}},{"type":"Panel","props":{"y":78,"x":880,"width":280,"visible":false,"var":"signPanel","height":444},"child":[{"type":"Image","props":{"y":-1,"x":-1,"width":280,"skin":"comp/p_bg.png","height":445}},{"type":"TextInput","props":{"y":75,"x":107,"width":129,"var":"signInput","type":"text","text":"请输入个性签名","skin":"comp/new_input.png","height":32,"fontSize":14,"font":"Microsoft YaHei","align":"left"}},{"type":"ComboBox","props":{"y":237,"x":110,"var":"signFontSize","skin":"comp/new_com.png","selectedIndex":0,"scrollBarSkin":"comp/vscroll.png","labels":"26,28,30,32,34,36,38,40,40,42,44,46,48,50"}},{"type":"ComboBox","props":{"y":310,"x":110,"var":"signFontAngle","skin":"comp/degree.png","sizeGrid":"0,0,0,0","selectedIndex":0,"scrollBarSkin":"comp/vscroll.png","labels":"      0°,      45°,      90°,     135°,     180°,     225°,     270°,     315°,     360°"}},{"type":"ColorPicker","props":{"y":143,"x":123,"visible":false,"var":"signColor","skin":"comp/colorPicker.png"}},{"type":"Label","props":{"y":166,"x":25,"text":"签名颜色：","fontSize":15,"font":"Microsoft YaHei","align":"left"}},{"type":"Label","props":{"y":309,"x":25,"text":"旋转角度：","fontSize":15,"font":"Microsoft YaHei"}},{"type":"Label","props":{"y":237,"x":25,"text":"字体大小：","fontSize":15,"font":"Microsoft YaHei"}},{"type":"Label","props":{"y":81,"x":25,"text":"签名内容：","fontSize":15,"font":"Microsoft YaHei"}},{"type":"ComboBox","props":{"y":166,"x":110,"var":"signFontColor","skin":"comp/new_com.png","selectedIndex":0,"scrollBarSkin":"comp/vscroll.png","labels":"黑色,白色,红色,橙色,黄色,绿色,青色,蓝色,紫色"}}]},{"type":"Tab","props":{"y":27,"x":880,"width":279,"var":"rightTab","height":50,"direction":"horizontal"},"child":[{"type":"Radio","props":{"y":-2,"x":0,"stateNum":2,"skin":"comp/btn_tab0.png","name":"item0"}},{"type":"Radio","props":{"y":-2,"x":93,"stateNum":2,"skin":"comp/btn_tab1.png","name":"item1"}},{"type":"Radio","props":{"y":-2,"x":186,"stateNum":2,"skin":"comp/btn_tab2.png","name":"item2"}}]},{"type":"Panel","props":{"y":0,"x":0,"width":1200,"visible":false,"var":"selectIpPanel","height":600,"gray":false},"child":[{"type":"Image","props":{"y":0,"x":0,"width":1200,"var":"maskPanel","skin":"comp/mask.png","height":600,"alpha":0.5}},{"type":"Image","props":{"y":31,"x":230,"width":650,"skin":"comp/bg_sc.png","height":550}},{"type":"List","props":{"y":78,"x":265,"width":586,"var":"ipImageList","spaceY":10,"spaceX":15,"repeatY":4,"repeatX":5,"height":441},"child":[{"type":"Box","props":{"y":3,"x":12,"renderType":"render"},"child":[{"type":"Image","props":{"y":5,"x":3,"width":95,"skin":"comp/bg_black.png","name":"image","height":95,"alpha":0.5}},{"type":"CheckBox","props":{"y":4,"x":66,"skin":"comp/check.png","sizeGrid":"0,0,0,0","name":"check"}}]}]},{"type":"Button","props":{"y":533,"x":760,"var":"ipConfirm","skin":"comp/ipconfirm.png"}},{"type":"Button","props":{"y":37,"x":835,"var":"ipCancel","skin":"comp/btn_close.png"}},{"type":"Button","props":{"y":544,"x":391,"width":20,"var":"ipLeft","skin":"comp/left.png"}},{"type":"Button","props":{"y":545,"x":454,"width":20,"var":"ipRight","skin":"comp/right.png"}},{"type":"TextInput","props":{"y":544,"x":412,"width":40,"var":"ipInput","text":"1","skin":"comp/new_input.png","align":"center"}},{"type":"Button","props":{"y":544,"x":496,"var":"ipGo","skin":"comp/go.png"}},{"type":"Label","props":{"y":42,"x":254,"text":"请选择素材","fontSize":20,"font":"Microsoft YaHei","bold":true}}]},{"type":"Panel","props":{"y":0,"x":0,"width":1200,"visible":false,"var":"confirmPanel","height":600},"child":[{"type":"Image","props":{"y":0,"x":0,"width":1200,"skin":"comp/mask.png","height":600,"alpha":0.5}},{"type":"Image","props":{"y":39,"x":170,"width":900,"skin":"comp/bg_sc.png","height":550}},{"type":"Image","props":{"y":168,"x":234,"width":300,"var":"productImage","skin":"/dz/comp/fullcard_b_45640.jpg","height":300}},{"type":"Button","props":{"y":506,"x":932,"var":"makeBtn","skin":"comp/save.png"}},{"type":"Button","props":{"y":50,"x":1027,"var":"makeCancel","skin":"comp/btn_close.png"}},{"type":"TextInput","props":{"y":208,"x":646,"width":400,"var":"title","skin":"comp/input2.png","height":40,"align":"center"}},{"type":"Label","props":{"y":171,"x":643,"text":"作品标题：","fontSize":18,"font":"Microsoft YaHei"}},{"type":"Label","props":{"y":269,"x":645,"text":"设计理念：","fontSize":18,"font":"Microsoft YaHei"}},{"type":"TextArea","props":{"y":317,"x":646,"width":400,"var":"ideaText","skin":"comp/input3.png","pivotY":3,"pivotX":-2,"padding":"10,0,0,5","multiline":true,"height":145}},{"type":"Label","props":{"y":52,"x":190,"text":"保存设计作品","fontSize":20,"font":"Microsoft YaHei","bold":true}},{"type":"Image","props":{"y":128,"x":593,"skin":"comp/bg_s.png","height":400}}]}]};
		return mengUI;
	})(View);