<?php
include_once("BaseBarcode_class.php");
class I25Barcode extends BaseBarcode {
    //
    private $wide;      // width of wide bar or space
    private $narrow;    // width of narrow bar or space
    private $encoded;   // encoded string 1 & 0's
    private $elements;  // array of various bars, wide black, narrow black, wide white, narrow white
    //==========================================================================
    // Constructors
    //--------------------------------------------------------------------------
    public function __construct() {
        parent::__construct();  // call parent constructor
        switch (func_num_args()){
            case 1;
                $this->setName('barcode');
                $this->setValue(func_get_arg(0));
                break;
            case 2;
                $this->setName(func_get_arg(0));
                $this->setValue(func_get_arg(1));
                break;
            default:
                die ("Cannot create ".get_class($this)." object");
        }
        $this->setFactor(1);
        $this->createSymbolSet();
        $this->createElements();

        //
    } // end of constructor
    //--------------------------------------------------------------------------
    public function setValue($value){
        $this->value = (strlen($value)%2) ?  "0{$value}":$value ; //  add leading zero if value is an odd length
    }
    //--------------------------------------------------------------------------
    public function setFactor($factor){
        $this->factor    = $factor;
        $this->height    = intVal($factor * 30);
        $this->wide      = intVal($factor * 5);
        $this->narrow    = intVal($factor * 2);
    }
    //--------------------------------------------------------------------------
    public function genStyle(){
        $width = intVal($this->getWidth());
        $this->style = "
            <style>
                .barcode {
                    border:5px solid white;
                    background:white;
                    width:{$width}px;
                    text-align:center;
                    {$this->divStyle}
                }
                .ns{
                    border-left:{$this->narrow}px solid white;
                    height:{$this->height};
                }
                .nb{
                    border-left:{$this->narrow}px solid black;
                    height:{$this->height};
                }
                .ws{
                    border-left:{$this->wide}px solid white;
                    height:{$this->height};
                }
                .wb{
                    border-left:{$this->wide}px solid black;
                    height:{$this->height};
                }

            </style>
        ";
    }
    //---------------------------------------------------------------------------
    public function genHTML(){
        $this->encode();
        $this->html .= "<div class='barcode' id='{$this->name}_bc' >{$this->encoded}</div>\n";
    }
    //---------------------------------------------------------------------------
    private function createSymbolSet(){
        // 0 = narrow
        // 1 = wide
        $this->symbolSet            = array();
        $this->symbolSet['0']       = "00110";
        $this->symbolSet['1']       = "10001";
        $this->symbolSet['2']       = "01001";
        $this->symbolSet['3']       = "11000";
        $this->symbolSet['4']       = "00101";
        $this->symbolSet['5']       = "10100";
        $this->symbolSet['6']       = "01100";
        $this->symbolSet['7']       = "00011";
        $this->symbolSet['8']       = "10010";
        $this->symbolSet['9']       = "01010";
        $this->symbolSet['pre']     = "0000";
        $this->symbolSet['post']    = "100";
    }
    //--------------------------------------------------------------------------
    private function createElements(){
        $this->elements = array(array(),array());
        $this->elements[0][0] = "<span class='ns'></span>"; // Narrow Space
        $this->elements[0][1] = "<span class='nb'></span>"; // Narrow Bar
        $this->elements[1][0] = "<span class='ws'></span>"; // Wide Space
        $this->elements[1][1] = "<span class='wb'></span>"; // Wide Bar
    }
    //--------------------------------------------------------------------------
    public function getWidth(){
        //
        $ans   = ($this->narrow * 3 * strlen($this->value)) +
                 ($this->wide   * 2 * strlen($this->value)) +
                 ($this->wide   * 6) +
                 ($this->narrow * 1) +
                 10
        ;
        return $ans;
    }
    //--------------------------------------------------------------------------
    private function encode(){
        $this->encoded = '';
        //----------------------------------------------------------------------
        // interleave 2 of 5
        $code = '';
        $len = strlen($this->value);
        for ($i = 0; $i<$len; $i+=2){
            $bars   = $this->symbolSet[substr($this->value,$i+0,1)];
            $spaces = $this->symbolSet[substr($this->value,$i+1,1)];
            for ($j=0; $j<5;$j++){
                $code .= substr($bars,$j,1);
                $code .= substr($spaces,$j,1);
            }
        }
        $code =  $this->symbolSet['pre'].$code.$this->symbolSet['post'];
        //----------------------------------------
        $color = 1;  // black = 1 white = 0
        $len = strlen($code);
        for ($i = 0; $i<$len; $i++){
            $width = substr($code,$i,1);
            //$this->encoded .= ($width ? 'w':'n');
            $this->encoded .= $this->elements[$width][$color];
            $color = !$color; // toggle color
        }
    }
    //---------------------------------------------------------------------------

    //==========================================================================
} // end of class
/*

        $bc = new I25Barcode('bcx','1234567');
        //$bc->setFactor(3);
        echo $bc->getBarcode(); // squirts out style & htlm in one string


*/
?>