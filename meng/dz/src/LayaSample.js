document.write('<script src="../include/js/jquery.min.js"></script>')
document.write('<script src="../include/plugin/layer/layer.js"><\/script>')
document.write('<script src="../include/js/global.js"><\/script>')

var Loader = laya.net.Loader;
var Handler = laya.utils.Handler;
var WebGL =laya.webgl.WebGL;
var TipManager = laya.ui.TipManager;
//var Image = laya.Image;
var Label = laya.ui.Label;
var dz_img_ads="";
var productIndex = 0;
var curCanvasId = 0;
var rotationOldX;
var rotationOldY;
function MengUI()
{
	var Event = laya.events.Event;
	MengUI.super(this);

	this.sceneColor = "#ffffff";
	this.selectIpPanel.visible = false;
	this.confirmPanel.visible = false;
	this.pageNum = 1;
	this.countPage = 0


	this.selectProduct.selectIndex = 0;
	this.selectProduct.selectEnable = true;
	this.selectProduct.selectHandler = new Handler(this, onProductChange);

	function onProductChange()
	{
		// alert("selectProduct:"+this.selectProduct.selectedLabel +  this.selectProduct.selectedIndex + ":"+productIndex);

		console.log("selectProduct:"+this.selectProduct.selectedLabel +  this.selectProduct.selectedIndex + ":"+productIndex);
		if(this.selectProduct.selectedIndex > 0 && this.selectProduct.selectedIndex != productIndex)
		{
			createScript("/asycom.php?ipLabel="+this.selectProduct.selectedLabel);
		}
	}


	// this.partList.selectIndex = 0;
        //监听列表
    this.partList.selectEnable = true;

	//this.partList.mouseHandler = new Handler(this,onPartChange);
	this.partList.selectHandler = new Handler(this, onPartChange);
	function onPartChange(index)
	{
		//if(e.type == Event.CLICK)
        //{
		console.log("partindex111---:"+index);
			console.log("partindex:"+this.partList.length);
			console.log("partindex:"+this.partList.array[index].image);
			this.baseImage.skin = this.partList.array[index].image;
			this.maskImage.skin = this.partList.array[index].maskImage;
			this.curCanvasId = this.partList.array[index].cavasID;
		// alert(this.curCanvasId);
		//debugger;
		console.log("cavansid:"+this.partList.array[index].cavasID);
		console.log("baseImage.skin:"+this.baseImage.skin);
		console.log("maskImage.skin:"+this.maskImage.skin);
		//}
	}
	//初始化右侧填充图片列表
	var arr = [];
		/**for (var i = 0; i < 10; i++) {
			arr.push({image:"http://127.0.0.1/mmshop/data/attachment/2017-08/20170803151225a.jpg"});
		}**/

		//给list赋值更改list的显示
	this.drawImageList.array = arr;

	this.drawImageList.selectIndex = 0;
        //监听列表
    this.drawImageList.selectEnable = true;
    this.drawImageList.selectHandler = new Handler(this, onDrawImageChange);
	//this.drawImageList.mouseHandler = new Handler(this,onDrawImage);
	this.drawImageList.renderHandler = new Handler(this, rederDrawImage);
	//function onDrawImage(e,index)
	//{
		//this.drawImageList.array[index].toolTip = "11111";
	//}

/*	function rederDrawImage(cell,index)
	{
		var imageSrc =this.drawImageList.array[index].image;
		this.drawImageList.array[index].toolTip = Handler.create(this,showTips,[imageSrc],false);
	}*/

	function rederDrawImage(cell,index)
	{
		if(index == this.drawImageList.selectedIndex)
		{
			this.drawImageList.array[index].selectImg.visible = true;
			console.log(this.drawImageList.array[index]);
			this.drawImageList.array[index].selectImg.skin="comp/select.png";
		}
		else
		{
			this.drawImageList.array[index].selectImg.visible = false;
			this.drawImageList.array[index].selectImg.skin="";
		}
		var imageSrc =this.drawImageList.array[index].image;
		this.drawImageList.array[index].toolTip = Handler.create(this,showTips,[imageSrc],false);
	}

	//替换填充图片
	function onDrawImageChange()
	{
		// debugger;
		this.drawImage.bid = this.drawImageList.array[this.drawImageList.selectedIndex].brand_id;
		console.log('品牌id:------'+this.drawImage.bid);
		console.log("index:"+this.drawImageList.selectedIndex);
		console.log("index:"+this.drawImage);
		this.drawImage.skin = this.drawImageList.array[this.drawImageList.selectedIndex].image;

	}


	this.selectIP.selectIndex = 0;
	this.selectIP.selectEnable = true;
	this.selectIP.selectHandler = new Handler(this, onIpChange);
	//初始化ip图片列表
	var iparr = [];
	/**	for (var i = 0; i < 20; i++) {
			iparr.push({imagebg:"comp/bg_black.png",image: "comp/bg.png",check:"false",label:"海贼王"});
		}
		**/
	function onIpChange()
	{
		console.log("selectIP:"+this.selectIP.selectedLabel);
		console.log("pageNum:"+this.pageNum);
		if(this.selectIP.selectedIndex > 0)
		{
			this.selectIpPanel.visible = true;
			createScript("/asycom.php?act=ipselect&&ipLabel="+this.selectIP.selectedLabel+"&&p="+this.pageNum);
		}
		this.selectIP.selectedIndex = 0;
		//script.src = "http://127.0.0.1/mmshop/asycom.php?categoryId=3";

	}


	this.ipImageList.array = iparr;

	this.ipImageList.selectIndex = 0;
        //监听列表
    this.ipImageList.selectEnable = true;
    //this.ipImageList.selectHandler = new Handler(this, onIpImageChange);
	this.ipImageList.renderHandler = new Handler(this, updateIpItem);
	this.ipImageList.mouseHandler = new Handler(this,onIpImageChange);
	function updateIpItem(cell,index)
	{

		var data = this.ipImageList.array[index];
		var check = cell.getChildByName("check");
		//check.on(Event.CLICK,alert(1));
		//console.log("ipindex:"+check.selected);
		//根据isCheck的值，确定当前check组件是否为勾选状态（可以避免出现其他多余的选中状态）
		if(data.check=="true")
		{
			check.selected=true;
		}
		else
		{
			check.selected=false;
		}

	}
	function onIpImageChange(e,index)
	{
		console.log("ipindex change:"+this.ipImageList.array.length);
		//this.arr = [];
		if(e.type == Event.CLICK)
        {
			//this.drawImageList.array = [];
			//console.log("ipindex:"+this.ipImageList.array[index].check);
			if(this.ipImageList.array[index].check == "true")
			{
				this.ipImageList.array[index].check ="false";
				var temp = [];
				for(var i=0;i<arr.length;i++)
				{
						//将非选中状态的条目数据存储起来
						if(!(this.ipImageList.array[index].image == arr[i].image))
						{
							temp.push({image:arr[i].image,selectImg:"comp/select.png",brand_id:arr[i].brand_id});
						}
				}
				// this.arr = null;
				arr = temp;
			}else
			{
				this.ipImageList.array[index].check ="true";
				for(var i=0;i<arr.length;i++)
				{
					//将非选中状态的条目数据存储起来
					if((this.ipImageList.array[index].image == arr[i].image))
					{
						return;
					}
				}
				arr.push({
					image: this.ipImageList.array[index].image,
					selectImg: "comp/select.png",
					brand_id: this.ipImageList.array[index].brand_id
				});

			}
		}
	}

	//翻页ipLeft,ipInput,ipRight,ipGo
	this.ipLeft.on(Event.CLICK, this, onIpLeft);
	// this.ipInput.on(Event.CLICK, this, onIpInput);
	this.ipRight.on(Event.CLICK, this, onIpRight);
	this.ipGo.on(Event.CLICK, this, onIpGo);

	function onIpLeft(){

		if(this.pageNum <= 1){
			return false;
		}
		this.pageNum -= 1;
		this.ipInput.text = this.pageNum;
		createScript("/asycom.php?act=ipselect&&ipLabel="+this.selectIP.selectedLabel+"&&p="+this.pageNum);
	}

	function onIpRight(){
		if(this.pageNum >= this.countPage || this.countPage == 0){
			return false;
		}
		this.pageNum += 1;
		this.ipInput.text = this.pageNum;
		createScript("/asycom.php?act=ipselect&&ipLabel="+this.selectIP.selectedLabel+"&&p="+this.pageNum);
	}
	function onIpGo(){
		if(parseInt(this.ipInput.text)>= this.countPage){
			return false;
		}
		createScript("/asycom.php?act=ipselect&&ipLabel="+this.selectIP.selectedLabel+"&&p="+parseInt(this.ipInput.text));
	}


	this.ipConfirm.on(Event.CLICK, this, onIpConfirm);
	this.ipCancel.on(Event.CLICK, this, onIpCancel);
	function onIpConfirm()
	{
		//console.log("checkbox:"+this.ipImageCheck);

		this.selectIpPanel.visible = false;
		this.drawImageList.array = arr;
	}

	function onIpCancel()
	{
		this.selectIpPanel.visible = false;
	}
	this.drawImage.on(Event.MOUSE_DOWN, this, onMouseDown,["image"]);
	this.on(Event.MOUSE_UP, this, onMouseUP);


	var firstX = 0;
	var firstY = 0;
	var firstImageX =this.drawImage.x;
	var firstImageY =this.drawImage.y;
	var up = false;
	function onMouseDown(type)
	{

		up = false;

		firstX = this.mouseX;
		firstY = this.mouseY;

		console.log("fx:"+firstX +"mousex:"+this.mouseX+"dix:"+this.drawImage.x+"diw:"+this.drawImage.width);
		console.log("fx:"+firstX +"mousex:"+this.mouseX+"signx:"+this.signLabel.x+"signw:"+this.signLabel.width);
		this.drawImage.on(Event.MOUSE_MOVE, this, onMove,[type]);
		this.signLabel.on(Event.MOUSE_MOVE, this, onMove,[type]);
		this.maskImage.alpha = 0.5;

	}

	function onMove(type)
	{
		if(up)
		{
			console.log("movetype:"+type);
			return;
		}

		if(firstX != this.mouseX)
		{
			if(type == "image")
			{
				this.drawImage.x +=  this.mouseX - firstX;
			}
			if(type == "sign")
			{
				this.signLabel.x +=  this.mouseX - firstX;
			}


			firstX = this.mouseX;
		}
		if(firstY != this.mouseY )
		{
			if(type == "image")
			{
				this.drawImage.y +=  this.mouseY - firstY;
			}
			if(type == "sign")
			{
				this.signLabel.y +=  this.mouseY - firstY;
			}

			firstY = this.mouseY;
		}
	}


	function onMouseUP()
	{
		up = true;
		this.maskImage.alpha = 1;
	}


	//this.signBtn.on(Event.CLICK, this, onSignBtnClick);
	this.signInput.on(Event.BLUR,this,updateSign);
	this.signLabel.on(Event.MOUSE_DOWN, this, onMouseDown,["sign"]);

	function updateSign()
	{
		this.signLabel.text = this.signInput.text;
	}

	this.createBtn.on(Event.CLICK, this, onCreateBtnClick);
	function onCreateBtnClick()
	{
		var width = this.drawImage.width*this.drawImage.scaleX;
		var height = this.drawImage.height*this.drawImage.scaleX;
		if(this.drawImage.rotation == 180){
			rotationOldX = this.drawImage.x-width*0.5;
			rotationOldY = this.drawImage.y-height*0.5;
		}
		if(this.drawImage.rotation == 0){
			rotationOldX = this.drawImage.x-width*0.5;
			rotationOldY = this.drawImage.y-height*0.5;
		}
		if(this.drawImage.rotation == 90 || this.drawImage.rotation == 270){
			console.log('this.drawImage:'+this.drawImage.x);
			rotationOldX = this.drawImage.x-height*0.5
			rotationOldY = this.drawImage.y-width*0.5
			console.log('this.rotationOldX:'+rotationOldX);
			console.log('this.rotationOldY:'+rotationOldY);
		}
		var drawImage = this.drawImage.skin;
		var baseImage = this.baseImage.skin;
		var maskImage = this.maskImage.skin;
		// debugger;
		var dscaleX = this.drawImage.scaleX;
		var dscaleY = this.drawImage.scaleY;
		var basex = this.baseImage.x;
		var basey = this.baseImage.y;
		var fontSize = this.signLabel.fontSize
		var fontColor = this.signColor.selectedColor
		var rotation = this.signLabel.rotation
		var fontText = this.signLabel.text
		var fontx = this.signLabel.x
		var fonty = this.signLabel.y
		var imageRotation = this.drawImage.rotation
		// return false;
		createScript("/asycom.php?act=createImage&&di="+drawImage+"&&imageRotation="+imageRotation+"&&bi="+baseImage+"&&mi="+maskImage+"&&x="+rotationOldX+"&&y="+rotationOldY+"&&basex="+basex+"&&basey="+basey+"&&fontsize="+fontSize+"&&fontColor="+encodeURIComponent(fontColor)+"&&rotation="+rotation+"&&fontText="+fontText+"&&dscalex="+dscaleX+"&&dscaley="+dscaleY+"&&fontx="+fontx+'&&fonty='+fonty);

	}

	this.initBtn.toolTip="还原";
	this.enlargeBtn.toolTip = "放大";
	this.reduceBtn.toolTip="缩小";
	this.shareBtn.toolTip = "旋转"
	this.initBtn.on(Event.CLICK, this, onInitBtnClick);
	this.enlargeBtn.on(Event.CLICK, this, onEnlargeBtnClick);
	this.reduceBtn.on(Event.CLICK, this, onReduceBtnClick);
	this.shareBtn.on(Event.CLICK, this, onShareBtnClick);
	this.makeBtn.on(Event.CLICK, this, onMakeBtn);
	this.makeCancel.on(Event.CLICK, this, onMakeCancel);

	function onShareBtnClick()
	{
		//点击对图片进行旋转，每次点击+90，达到360即为起始0度
		if(this.drawImage.rotation < 360)
		{
			this.drawImage.rotation += 90;
		}
		else
		{
			this.drawImage.rotation = 0;
		}
	}
	
	function onInitBtnClick()
	{
		this.drawImage.x = firstImageX;
		this.drawImage.y = firstImageY;
		this.drawImage.scaleX = 1;
		this.drawImage.scaleY = 1;
	}

	function onEnlargeBtnClick()
	{
		if(this.drawImage.scaleX <2)
		{
			this.drawImage.scaleX += 0.2;
			this.drawImage.scaleY += 0.2;
		}
		else
		{
			this.drawImage.scaleX = 2;
			this.drawImage.scaleY = 2;
		}
	}

	function onReduceBtnClick()
	{
		console.log("当前图片scale " + this.drawImage.scaleX);
		console.log("当前图片scale anchorX " + this.drawImage.anchorX);
		if(this.drawImage.scaleX >0.1)
		{
			this.drawImage.scaleX -= 0.1;
			this.drawImage.scaleY -= 0.1;
		}
		else
		{
			this.drawImage.scaleX = 0.1;
			this.drawImage.scaleY = 0.1;
		}
	}

	function onMakeCancel(){
		this.confirmPanel.visible=false;
	}

	function onMakeBtn(){
		// var url = 'http://www.9000meng.com/index.php?mod=product&act=dz_add_pro';
		var url = 'http://localhost/index.php?mod=product&act=dz_add_pro';
		var post = {};
		post.name = this.title.text;
		post.text = this.ideaText.text;
		post.category_id = meng.selectProduct.category_id;//GetQueryString("category_id");
		/*获取图片组成参数 start*/
		post.drawImage = this.drawImage.skin;
		post.baseImage = this.baseImage.skin;
		post.maskImage = this.maskImage.skin;
		post.dscalex = this.drawImage.scaleX;
		post.dscaley = this.drawImage.scaleY;
		post.dix = this.drawImage.x;
		post.diy = this.drawImage.y;
		console.log('ipX '+post.dix)
		console.log('ipY '+post.diy)
		post.basex = this.baseImage.x;
		post.basey = this.baseImage.y;
		post.fontx = this.signLabel.x;
		post.fonty = this.signLabel.y;
		post.fontSize = this.signLabel.fontSize;
		post.fontColor = this.signColor.selectedColor;
		post.rotation = this.signLabel.rotation;
		post.fontText = this.signLabel.text;
		post.pesubmit = 1;
		post.logo = dz_img_ads;
		post.angleIndex = this.signFontAngle.selectedIndex;
		post.brand_id = this.drawImage.bid;
		post.canvasId = this.curCanvasId;
		post.ip_rotate = meng.drawImage.rotation;
		//return false;
		/*获取图片组成参数 end*/
		if(post.name == "" || post.text == ""){
			pe_tip('请填写作品标题或设计理念');
			return false;
		}
		 $.ajax({
			 url:url,
			 data:post,
			 type:'post',
			 dataType:'json',
			 success:function(res){
			 	if(res.result){
					// window.parent.location.href='http://www.9000meng.com/index.php/product/'+res.pid;
					window.parent.location.href='http://localhost/index.php/product/'+res.pid;
				}else{
					pe_tip(res.show)
				}
			 }
		 })
	}

	this.rightTab.selectedIndex = 0;
	this.rightTab.selectHandler = new Handler(this, onTabSelect);
	function onTabSelect(index)
	{
		console.log("当前选择的标签页索引为 " + index);
		this.signPanel.visible = false;
		switch(index)
		{
			case 0:
				//createScript("/asycom.php?categoryId=2");
			break;
			case 1:
				this.signPanel.visible = true;
			break;
			default:
			break;

		}
	}
	this.drawImage.blendMode = "multiply";

	function showTips(name)
	{
		var dialog = new Laya.Image(name);
		dialog.width = 100;
		dialog.height = 100;
		tip.showDislayTip(dialog);
	}

	this.signColor.changeHandler = new Handler(this, onChangeColor);
	function onChangeColor()
	{
		console.log(this.signColor.selectedColor);
		this.signLabel.color = this.signColor.selectedColor;
	}
	this.signFontColor.selectHandler = new Handler(this, onSelectFontColor);
	function onSelectFontColor()
	{
		/**
		赤色【RGB】255, 0, 0 【CMYK】 0, 100, 100, 0  
		橙色【RGB】 255, 165, 0 【CMYK】0, 35, 100, 0  
		黄色【RGB】255, 255, 0 【CMYK】0, 0, 100, 0  
		绿色【RGB】0, 255, 0 【CMYK】100, 0, 100, 0  
		青色【RGB】0, 127, 255 【CMYK】100, 50, 0, 0  
		蓝色【RGB】0, 0, 255 【CMYK】100, 100, 0, 0  
		紫色【RGB】139, 0, 255 【CMYK】45, 100, 0, 0
		**/
		var colorarr = ['#000000','#ffffff','#FF0000','#FFA500','#FFFF00','#00FF00','#007FFF','#0000FF','#8B00FF'];
	
		this.signLabel.color = colorarr[this.signFontColor.selectedIndex];
		console.log(this.signLabel.color);
	}
	this.signFontSize.selectHandler = new Handler(this, onSelectFontSize);
	function onSelectFontSize()
	{
		this.signLabel.fontSize = this.signFontSize.selectedLabel;
		console.log(this.signLabel.fontSize);
	}

	this.signFontAngle.selectHandler = new Handler(this, onSelectFontAngle);
	function onSelectFontAngle()
	{
		//console.log(parseInt(this.signFontAngle.selectedLabel));
		var rota = parseInt(this.signFontAngle.selectedLabel.replace(/(^\s*)|(\s*$)/g, ""));
		this.signLabel.rotation = rota;
		console.log(this.signLabel.rotation);
	}
}



Laya.class(MengUI, "MengUI", mengUI);


Laya.init(1200, 600);
var meng = null;
Laya.stage.bgColor ="white";
Laya.loader.load("res/atlas/comp.json", Handler.create(this, onAssetLoaded), null, Loader.ATLAS);
var tip=new TipManager();
function onAssetLoaded()
{
	if(GetQueryString("pid")){
		var url = "/asycom.php?category_id="+GetQueryString("category_id")+"&&pid="+GetQueryString("pid")
	}else{
		if(GetQueryString("brand_pic_id")){
			var url = "/asycom.php?category_id="+GetQueryString("category_id")+"&&brand_pic_id="+GetQueryString("brand_pic_id")
		}else{
			var url = "/asycom.php?category_id="+GetQueryString("category_id")
		}
	}
	meng = new MengUI();
	Laya.stage.addChild(meng);
	createScript(url);
}



function createScript(url)
{
	var script = Laya.Browser.createElement("script");
	script.src = url;
	Laya.Browser.document.body.appendChild(script);

}



function indexComplete(data)
{
	console.log("get data from server");
	meng.selectProduct.labels = "请选择产品";
	var partArr = [];

	for(var i=0;i<data['category'].length;i++)
	{
		//console.log("category_name"+data['category'][i].category_name);
		meng.selectProduct.labels += ","+ data['category'][i].category_name;

		if(data['curid'] == data['category'][i].category_id || data['curname'] == data['category'][i].category_name)
		{

			meng.curCategoryid = data['category'][i].category_id;
			meng.curCanvasId = data['canvasList'][0].canvas_id;
			// for (var j = 0; j < data['category'][i].models.length; j++) {
				for (var k = 0; k < data['canvasList'].length; k++) {
					var image = '/'+data['canvasList'][k].canvas_model;
					var maskImage = '/'+data['canvasList'][k].canvas_mask;
					var cId =  data['canvasList'][k].canvas_id;
					console.log("JSONP执行到这里image111:"+ image + cId);
					partArr.push({image:image,cavasID:cId,maskImage:maskImage});
				}

			// }
			productIndex = i+1;
			//console.log("JSONP执行到这里meng.productIndex:"+  data['category'][i]['models'][0].canvas_model);
			meng.baseImage.skin = "/"+data['category'][i]['models'][0].canvas_model;
			meng.maskImage.skin = "/"+data['category'][i]['models'][0].canvas_mask;
			if(data['curpic'] != null)
			{
				meng.drawImage.skin = "/"+data['curpic'];
				var arr =[];
				arr.push({image:"/"+data['curpic'],selectImg:"comp/select.png"});
				meng.drawImageList.array = arr;
			}
		}
	}
	console.log("JSONP执行到这里category_id:"+ meng.selectProduct.labels);
	meng.selectProduct.selectedIndex = productIndex;
	meng.selectIP.labels = "请选择素材";
	for(var i=0;i<data['brand'].length;i++)
	{
		meng.selectIP.labels += ","+data['brand'][i].brand_name;
		console.log("JSONP执行到这里"+ data['brand'][i].brand_name);
	}
	meng.selectIP.selectedIndex = 0;
	console.log("JSONP执行到这里partlength"+ partArr.length);
	meng.partList.array = partArr;
	//meng.drawImage.skin = "/"+data['curpic'];
	meng.drawImage.bid = data.brand_id;
	console.log("JSONP执行到这里brand_id"+meng.drawImage.brand_id);
	if(data.dz_info){
		//从商品购买页跳过来
		meng.drawImage.skin = "/"+data.dz_info.ip_img_ads;
		//meng.drawImage.skin = "/"+data['curpic'];//"/data/attachment/brand/pic/19.jpg";
		// meng.baseImage.skin = '/'+data.dz_info.base_img_ads;
		// meng.maskImage.skin = '/'+data.dz_info.mask_img_ads;
		console.log(meng.drawImage.skin);
		console.log(data.dz_info.ip_coordinate.split(',')[0]);
		console.log(data.dz_info.ip_coordinate.split(',')[1]);

		meng.drawImage.x = data.dz_info.ip_coordinate.split(',')[0]*1;
		meng.drawImage.y = data.dz_info.ip_coordinate.split(',')[1]*1;
		meng.drawImage.rotation = data.dz_info.ip_rotate*1;
		meng.drawImage.scaleX = data.dz_info.ip_scale.split(',')[0]*1;
		meng.drawImage.scaleY = data.dz_info.ip_scale.split(',')[1]*1;
		meng.baseImage.x = data.dz_info.base_coordinate.split(',')[0]*1;
		meng.baseImage.y = data.dz_info.base_coordinate.split(',')[1]*1;

		meng.signLabel.x = data.dz_info.f_coordinate.split(',')[0]*1;
		meng.signLabel.y = data.dz_info.f_coordinate.split(',')[1]*1;
		meng.signColor.selectedColor = data.dz_info.f_color;
		meng.signFontSize.selectedLabel = data.dz_info.f_size;
		meng.signFontAngle.selectedIndex = data.dz_info.f_rotation.split(',')[1]*1;
		meng.signLabel.fontSize = data.dz_info.f_size;
		meng.signLabel.rotation = data.dz_info.f_rotation.split(',')[0]*1;
		meng.signLabel.text = data.dz_info.f_content;
		meng.signInput.text = data.dz_info.f_content
		// console.log(meng.signFontAngle.selectedLabel);
	}
}

function ipSelect(data)
{
	meng.countPage = data.countpage;
	meng.ipImageList.array = [];
	for(var i=0;i<data['piclist'].length;i++)
	{
		console.log("JSONP执行到这里"+ data.piclist[i].brand_pic_addr);
		var picaddr = '/'+data.piclist[i].brand_pic_addr;
		meng.ipImageList.array.push({imagebg:"comp/bg_black.png",image:picaddr,check:"false",label:"海贼王",brand_id:data.piclist[i].brand_pic_bid});
		//console.log("JSONP执行到这里"+ data['piclist'][i].brand_pic_addr);
	}
}

function createImage(data)
{
	if(!data.result){
		pe_tip(data.show);
		return false;
	}

	meng.confirmPanel.visible=true;
	dz_img_ads =data.image
	// meng.productImage.skin = 'http://www.9000meng.com/'+data.image;
	meng.productImage.skin = 'http://localhost/'+data.image;
}
