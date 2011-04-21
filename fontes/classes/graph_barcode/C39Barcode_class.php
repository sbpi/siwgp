<?php
include_once("BaseBarcode_class.php");
class C39Barcode extends BaseBarcode {
    //
    private $wide;      // width of wide bar or space
    private $narrow;    // width of narrow bar or space
    private $encoded;   // encoded string 1 & 0's
    private $elements;  // array of various bars, wide black, narrow black, wide white, narrow white
    private $msg;       // the actule barcode message
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
        $this->value = $value ; //
        $this->msg   = strtoupper('*'.$value.'*');
    }
    //--------------------------------------------------------------------------
    public function setFactor($factor){
        $this->factor    = $factor;
        $this->height    = intVal($factor * 30);
        $this->wide      = intVal($factor * 5);
        $this->narrow    = intVal($factor * 3);
    }
    //--------------------------------------------------------------------------
    public function genStyle(){
        $width = intVal($this->getWidth());
        $this->style = "
            <style>
                .barcode {
                    border:1px solid white;
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
        // added inner-charater narrow bar
        $this->symbolSet      = array();
        $this->symbolSet['0'] = '0001101000';
        $this->symbolSet['1'] = '1001000010';
        $this->symbolSet['2'] = '0011000010';
        $this->symbolSet['3'] = '1011000000';
        $this->symbolSet['4'] = '0001100010';
        $this->symbolSet['5'] = '1001100000';
        $this->symbolSet['6'] = '0011100000';
        $this->symbolSet['7'] = '0001001010';
        $this->symbolSet['8'] = '1001001000';
        $this->symbolSet['9'] = '0011001000';
        $this->symbolSet['A'] = '0011001000';
        $this->symbolSet['B'] = '0010010010';
        $this->symbolSet['C'] = '1010010000';
        $this->symbolSet['D'] = '0000110010';
        $this->symbolSet['E'] = '1000110000';
        $this->symbolSet['F'] = '0010110000';
        $this->symbolSet['G'] = '0000011010';
        $this->symbolSet['H'] = '1000011000';
        $this->symbolSet['I'] = '0010011000';
        $this->symbolSet['J'] = '0000111000';
        $this->symbolSet['K'] = '1000000110';
        $this->symbolSet['L'] = '0010000110';
        $this->symbolSet['M'] = '1010000100';
        $this->symbolSet['N'] = '0000100110';
        $this->symbolSet['O'] = '1000100100';
        $this->symbolSet['P'] = '0010100100';
        $this->symbolSet['Q'] = '0000001110';
        $this->symbolSet['R'] = '1000001100';
        $this->symbolSet['S'] = '0010001100';
        $this->symbolSet['T'] = '0000101100';
        $this->symbolSet['U'] = '1100000010';
        $this->symbolSet['V'] = '0110000010';
        $this->symbolSet['W'] = '1110000000';
        $this->symbolSet['X'] = '0100100010';
        $this->symbolSet['Y'] = '1100100000';
        $this->symbolSet['Z'] = '0110100000';
        $this->symbolSet['-'] = '0100001010';
        $this->symbolSet['.'] = '1100001000';
        $this->symbolSet[' '] = '0110001000';
        $this->symbolSet['$'] = '0101010000';
        $this->symbolSet['/'] = '0101000100';
        $this->symbolSet['+'] = '0100010100';
        $this->symbolSet['%'] = '0001010100';
        $this->symbolSet['*'] = '0100101000';
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
        $ans   = ($this->narrow * 7 * strlen($this->msg)) +
                 ($this->wide   * 3 * strlen($this->msg)) +
                 10
        ;
        return $ans;
    }
    //--------------------------------------------------------------------------
    private function encode(){
        $this->encoded = '';
        // Code 3 of 9
        $color = 1;  // black = 1 white = 0
        $len = strlen($this->msg);
        for ($i = 0; $i<$len; $i++){
            $code = $this->symbolSet[substr($this->msg,$i+0,1)];
            for ($j=0; $j<10;$j++){
                $width = substr($code,$j,1);
                $this->encoded .= $this->elements[$width][$color];
                $color = !$color; // toggle color
            }
        }
    }
    //==========================================================================
} // end of class
/*
        $bc = new c39Barcode('bcx','1234567');
        //$bc->setFactor(3);
        echo $bc->getBarcode();  // squirts out style & htlm in one string
        //$bc->exec($this); // prefered method
*/
?>