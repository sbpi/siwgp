<?php
extract($GLOBALS,EXTR_PREFIX_SAME,'local'); 
include_once($w_dir_volta.'constants.inc');
/********************************
* xPandMenu MULTI-LEVEL class
*********************************
* Creates a tree-view menu.
* The menu can be as deep as needed
* The menu items are organised in HTML unordered lists
* Container nodes can be expanded/collapsed thanks to Javascript actions
*********************************
* Patrick Brosset
* patrickbrosset@gmail.com
*********************************
* 02/2005
*********************************/



// Path to default image files (directories and documents icons) -- (use absolute URL)
define('NODE_DEFAULT_IMG',$conRootSIW.'images/Folder/folder_win.gif');
define('LEAF_DEFAULT_IMG',$conRootSIW.'images/Folder/document_win.gif');
define('NODE_DEFAULT_ALT_IMG',$conRootSIW.'images/Folder/folder_win_o.gif');
define('LEAF_DEFAULT_ALT_IMG',$conRootSIW.'images/Folder/document_win_o.gif');

// Reference to the File class for saving and loading the generated menu
//include_once 'File.php';



// Xmenu class
class XMenu {
    
    // Sub-nodes contained in this menu (references to Xnode objects)
    var $items = array();
    
    // Keeps track of the HTML code indent to use (formatting of UL and LI)
    var $indent;
    
    // Is it the first node ?
    var $first;
    
    // Used for assigning unique IDs to HTML elements (for the javascript function)
    var $treeCount;
    
    // Same for images
    var $imgCount;
    
    // Contains the generated HTML code
    var $output;



    // Constructor
    function XMenu()
    {
        $this->indent = 0;
        $this->first = true;
        $this->treeCount = 0;
        $this->imgCount = 0;
        $this->output = "";
    }
    

    
    // addItem, adds a child to this menu
    // Takes a XNode object reference as argument
    function &addItem(&$node)
    {
        $this->items[] = &$node;
        return $this->items[count($this->items) - 1];
    }
    
    
    
    // generateTree, generates the HTML code (UL list) for the dynamic tree-view
    function generateTree($root = false)
    {
        if(!$root)    $root = $this;
        
        if($this->first){
            $this->output .= $this->codeIndent()."<ul id=\"XRoot\" class=\"XtreeRoot\">\n";
            $this->first = false;
        }else{
            if ($root->uncollapsed) {
              $this->output .= $this->codeIndent()."<ul id=\"Xtree".$this->treeCount."\" class=\"Xtree\" style=\"display:block;\">\n";
            } else {
              $this->output .= $this->codeIndent()."<ul id=\"Xtree".$this->treeCount."\" class=\"Xtree\" style=\"display:none;\">\n";
            }
        }
        $this->treeCount ++;
        foreach($root->items as $myChild){
            $this->imgCount ++;
            if($myChild->img){
                $img = "<img id=\"Ximg".$this->imgCount."\" src=\"".$myChild->img."\" border=\"0\">&nbsp;";
                if($myChild->alt_img){
                    $img_js = "xSwapImg(document.getElementById('Ximg".$this->imgCount."'),'".$myChild->img."','".$myChild->alt_img."');";
                }else{
                    $img_js = "";
                }
            }else{
                $img = "";$img_js = "";
            }
            if($myChild->link){
                if (strpos(strtoupper($myChild->link),'<A')===false) {
                  $href_open = "<a href=\"".$myChild->link."\"";
                  if($myChild->target) $href_open = $href_open." target=\"".$myChild->target."\"";
                  if($myChild->attr) $href_open = $href_open." ".$myChild->attr;
                  $href_open = $href_open.">";
                  $href_close = "</a>";
                } else {
                  $href_open = $myChild->link;
                  $href_close = "";
                }
            }else{
                $href_open = "";
                $href_close = "";
            }
            if(count($myChild->items) != 0){
                $this->output .= $this->codeIndent()."<li id=\"Xnode\" class=\"Xnode\"><span onClick=\"".$img_js."xMenuShowHide(document.getElementById('Xtree".$this->treeCount."'));\">".$href_open.$img.$myChild->name.$href_close."</span></li>\n";
                $this->indent ++;
                $this->generateTree($myChild);
            }else{
                $this->output .= $this->codeIndent()."<li id=\"Xleaf\" class=\"Xleaf\"><span onClick=\"".$img_js."\">".$href_open.$img.$myChild->name.$href_close."</span></li>\n";
            }
        }
        $this->output .= $this->codeIndent()."</ul>\n";
        $this->indent --;
        
        return $this->output;        
    }
    
    
    
    // saveTree and restoreTree - thanks to Niels Fanoe (niels.f@noee.dk) for giving me the idea

    // saveTree, save the generated HTML code to a file for future use without generating again
    function saveTree($filename = "xMenuCache.html")
    {
        $file = new File();
        $file->write($this->output,$filename);
        $file->printError();
        return $filename;
    }
    
    
    
    // restoreTree, returns the previously generated HTML code stored in a file
    // Call this method STATICALLY for easier use: XMenu::restoreTree("xPandMenuCode.html");
    function restoreTree($filename = "xMenuCache.html")
    {
        $file = new File();
        $menu = $file->read($filename);
        $error = $file->getError();
        if(!empty($error))    return false;
        else    return $menu;
    }
    
    
    
    // codeIndent, only used to create a nice and readable HTML code (indents the UL and LI tags)
    function codeIndent()
    {
        $str = "";
        for($i=0;$i<$this->indent;$i++){
            $str .= "    ";
        }
        return $str;
    }


}




// XNode class: A node item in the menu
class XNode
{

    
    // Name assigned to this node (Text shown on the item)
    var $name;
    
    // Link for the item (if any)
    var $link;
    
    // Sub-items of this node
    var $items = array();
    
    // Absolute URL of this node's icon
    var $img;
    
    // Absolute URL of this node's icon (alternate, used for expanded and collapsed states)
    var $alt_img;



    // constructor
    // $name: text shown for this item
    // $link: where does this item links to when clicked (optional)
    // $target: [included] points $link frame (optional)
    // $attr: [included] additional attributes to anchor tag (optional)
    // $img and $alt_img: images displayed next to this item (absolute paths to images must be used)
    function XNode($name,$link = false,$img = LEAF_DEFAULT_IMG,$alt_img = LEAF_DEFAULT_ALT_IMG,$target = false,$attr = false,$uncollapsed = false)
    {
        $this->name = $name;
        $this->link = $link;
        $this->target = $target;
        $this->attr = $attr;
        $this->uncollapsed = $uncollapsed;
        $this->img = $img;
        $this->alt_img = $alt_img;
    }
    
    
    
    // addItem, adds a subnode under this node
    // Takes a XNode object reference as argument
    function &addItem(&$node)
    {
        if($this->img == LEAF_DEFAULT_IMG){ ($this->uncollapsed) ? $this->img = NODE_DEFAULT_ALT_IMG : $this->img = NODE_DEFAULT_IMG;}
        if($this->alt_img == LEAF_DEFAULT_ALT_IMG){$this->alt_img = NODE_DEFAULT_ALT_IMG;}
        $this->items[] = &$node;
        return $this->items[count($this->items) - 1];
    }



}

?>