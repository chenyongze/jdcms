<?php
class page{
	 
	private  $each_disNums;//每页显示的条目数
	private  $nums;//总条目数
	private  $current_page;//当前被选中的页
	private  $sub_pages;//每次显示的页数
	private  $pageNums;//总页数
	private  $page_array = array();//用来构造分页的数组
	private  $subPage_link;//每个分页的链接
	private  $id;//每个分页的链接
	/*
	__construct是SubPages的构造函数，用来在创建类的时候自动运行.
	@$each_disNums   每页显示的条目数
	@nums     		  总条目数
	@current_num     当前被选中的页
	@sub_pages       每次显示的页数
	@subPage_link    每个分页的链接
	*/
	function __construct($each_disNums,$nums,$current_page,$sub_pages,$subPage_link,$id){
		$this->id=intval($id);
		$this->each_disNums=intval($each_disNums);
		$this->nums=intval($nums);
		if(!$current_page){
			$this->current_page=1;
		}else{
			$this->current_page=intval($current_page);
		}
		$this->sub_pages=intval($sub_pages);
		$this->pageNums=ceil($nums/$each_disNums);
		$this->subPage_link=$subPage_link;
// 		$this->subPageCss();
	}
	
	function __destruct(){
		unset($each_disNums);
		unset($nums);
		unset($current_page);
		unset($sub_pages);
		unset($pageNums);
		unset($page_array);
		unset($subPage_link);
		unset($subPage_type);
	}

	 
	/*
	 用来给建立分页的数组初始化的函数。
	*/
	function initArray(){
		for($i=0;$i<$this->sub_pages;$i++){
			$this->page_array[$i]=$i;
		}
		return $this->page_array;
	}
	 
	 
	/*
	 construct_num_Page该函数使用来构造显示的条目
	即：1 2 3 4 5
	*/
	function construct_num_Page(){
		if($this->pageNums < $this->sub_pages){
			$current_array=array();
			for($i=0;$i<$this->pageNums;$i++){
				$current_array[$i]=$i+1;
			}
		}else{
			$current_array=$this->initArray();
			if($this->current_page <= 3){
				for($i=0;$i<count($current_array);$i++){
					$current_array[$i]=$i+1;
				}
			}elseif ($this->current_page <= $this->pageNums && $this->current_page > $this->pageNums - $this->sub_pages + 1 ){
				for($i=0;$i<count($current_array);$i++){
					$current_array[$i]=($this->pageNums)-($this->sub_pages)+1+$i;
				}
			}else{
				for($i=0;$i<count($current_array);$i++){
					$current_array[$i]=$this->current_page-2+$i;
				}
			}
		}

		return $current_array;
	}
	
	 	 
	/*
	 构造分页
	100条记录 1/5页 上一页  下一页 1 2 3 4 5 
	*/
	function subPageCss(){
		$subPageCssStr="";
		$subPageCssStr.='<span class="totalRows">'.$this->nums."</span> 条记录 ";
		$subPageCssStr.='<span class="nowPage">'.$this->current_page."</span>/".$this->pageNums." 页 ";
	
		if($this->current_page > 1){
			if ($this->current_page == 2){
				$prewPageUrl=$this->subPage_link.$this->id.'.html';
				$subPageCssStr.="<a href='$prewPageUrl'>上一页</a>";
			}else{
				$prewPageUrl=$this->subPage_link.$this->id.'-'.($this->current_page-1).'.html';
				$subPageCssStr.="<a href='$prewPageUrl'>上一页</a>";
			}	
		}
		
		if($this->current_page < $this->pageNums){
			$nextPageUrl=$this->subPage_link.$this->id.'-'.($this->current_page+1).'.html';
			$subPageCssStr.="<a href='$nextPageUrl'>下一页</a>";
		}
		
		$a=$this->construct_num_Page();
		for($i=0;$i<count($a);$i++){
			$s=$a[$i];
			if($s == $this->current_page ){
				$subPageCssStr.="<a class='current'>".$s."</a>";
			}else{
				if ($s > 1){
					$url=$this->subPage_link.$this->id.'-'.$s.'.html';
					$subPageCssStr.="<a href='$url'>".$s."</a>";
				}else {
					$url=$this->subPage_link.$s.'.html';
					$subPageCssStr.="<a href='$url'>".$s."</a>";
				}

			}
		}
		return $subPageCssStr;
	}
}
?>