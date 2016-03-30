<?php 
class Tree
{
	/*
	 *在这里需要注意的是，类需要传入一个数组。即分类的全部数据，是一个二维的数组
	 */
	private  $view;
	private  $data=array(); 
	private  $cateArray=array(); 
	private  $cateSortArray=array(); 
	
	public function __construct($dataArray)
	{
		foreach ($dataArray as $val)
		{
			$this->setNode($val['iClassID'], $val['iParentID'], $val['cClassName'], $val['cSort']);
		}
		$this->sortASC();
	}
	
	//设置树的节点
	function setNode($id, $parent, $value, $sort)
	{
		$parent = $parent?$parent:0;
		$this->data[$id] = $value;
		$this->cateArray[$id] = $parent;
		$this->cateSortArray[$id] = $sort;
	}
	
	/*
	 * 递归实现
	 * 得到id下的子树结构数组(用多维数组格式来表示树)
	 * id Internet 节点id (0表示的是根节点，是虚拟的，即没有与它对应的实际信息)
	 * return array 子树节点数组
	 */
	function getChildsTree($id=0)
	{
		$childs=array();
		foreach ($this->cateArray as $child=>$parent)
		{
			if ($parent==$id)
			{
				$childs[$child]=$this->getChildsTree($child);
			}
		}
		return $childs;
	}

	/*
	 * 递归实现
	 * 得到id节点的所有后代节点
	 * $id  
	 * return array  索引=>id,...一维数组(顺序important)
	 */
	function getChilds($id=0)
	{
		$childArray = array();
		$childs = $this->getChild($id);
		foreach ($childs as $child)
		{
			$childArray[]=$child;
			$childArray=array_merge($childArray,$this->getChilds($child));
		}
		return $childArray;
	}

	/*
	 * 得到id节点的孩子节点
	 * $id
	 * return array 索引=>id,...一维数组
	 */
	function getChild($id)
	{
		$childs=array();
		foreach ($this->cateArray as $child=>$parent)
		{
			if ($parent==$id)
			{
				$childs[]=$child;
			}
		}
		return $childs;
	}
	
	/*
	 * 递归实现
	 * 反线获得节点id的父节点
	 * $id interger 不可以为0
	 * return array 其父节点数组
	 */
	function getNodeLever($id)
	{
		$parents=array();
		if (array_key_exists($this->cateArray[$id],$this->cateArray))//它的父节点，在节点树中
		{
			$parents[]=$this->cateArray[$id];
			$parents=array_merge($parents,$this->getNodeLever($this->cateArray[$id]));
		}
		return $parents;
	}
	
	/*
	 * 根据所在层数得到n-1个前导格式化符号
	 * $id Internet 不可以取0
	 * $preStr str 填充的格式化符号
	 * return str 多个格式化符号
	 */
	function getLayer($id,$preStr='|',$link='--')
	{
		$lv = count($this->getNodeLever($id));
		return $preStr.str_repeat($link,$lv);
	}
	
	//得到id节点的信息
	function getValue ($id)
	{
		return $this->data[$id];
	}
	
	//得到id节点的信息
	function getSort ($id)
	{
		return $this->cateSortArray[$id];
	}
	
	//id降序
	function sortDES()
	{
		krsort($this->cateArray);
	}
	
	//id升序
	function sortASC()
	{
		ksort($this->cateArray);
	}
	
	//判断是否为叶子节点 
	function isLeaf($id){
		$arr = $this->getChild($id);
		if(!empty($arr)){
			return false;
		}else{
			return true;
		}
	}
	
	//下拉列表框样式，
	function select($cid = 0){
		$this->view = '';
		$category = $this->getChilds(0);
		//file_put_contents('log.txt',var_export($category,true));
		foreach ($category as $key=>$id)
		{
			if($cid == $id){
				$this->view .= "<option value='$id' selected='selected'>".$this->getLayer($id, '','└─').$this->getValue($id)."</option>";
			}
			else{
				$this->view .= "<option value='$id'>".$this->getLayer($id, '','└─').$this->getValue($id)."</option>";
			}       
		}
		return $this->view;
	}
	
	//表格样式
	function view($operate)
	{
		$this->view = '<tr>  <td>Category Name</td>  <td>Category Sort</td>	<td>Operate</td>  </tr>';
		$category = $this->getChilds(0);	
		foreach ($category as $key=>$id)
		{
			$event='onMouseOver="this.style.background=\'lightblue\'" 
			onmouseout="this.style.background=\'#ffffff\'"';
			if($this->isLeaf($id)){
				$newOperate = str_ireplace("###",$id,$operate);
			}else{
				$newOperate = '';
			}
			
			$this->view .= "<tr id='tr_".$id."' ".$event."><td>".$this->getLayer($id, '','└─').$this->getValue($id)."</td><td>".$this->getLayer($id, '','└─').$this->getSort($id)."</td><td>".$newOperate."</td></tr>";
		}
		return $this->view;
	}
}
?>